<?php
/**
 * Created by Igor Dubiy on 16/06/2014
 */

namespace AppShed\Extensions\StorageBundle\Controller;

use AppShed\Extensions\StorageBundle\Entity\Api;
use AppShed\Extensions\StorageBundle\Entity\Data;
use AppShed\Extensions\StorageBundle\Entity\Field;
use AppShed\Extensions\StorageBundle\Entity\Filter;
use AppShed\Extensions\StorageBundle\Exception\MissingDataException;
use AppShed\Extensions\StorageBundle\Exception\NotImplementedException;
use AppShed\Extensions\StorageBundle\Form\ApiEditType;
use AppShed\Extensions\StorageBundle\Form\ApiType;
use Doctrine\ORM\EntityManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use Symfony\Component\Routing\Exception\MethodNotAllowedException;

class ApiExecuteController extends Controller
{
    /**
     * @Route("/api/{uuid}", defaults={"_format": "json"}, name="api_show")
     * @ParamConverter("api", class="AppShed\Extensions\StorageBundle\Entity\Api", options={"uuid"="uuid"})
     * @Method({"GET", "POST"})
     */
    public function showAction(Request $request, Api $api)
    {
        if (in_array($api->getAction(), [Api::ACTION_INSERT, Api::ACTION_UPDATE, Api::ACTION_DELETE]) && $request->getMethod() != 'POST') {
            throw new MethodNotAllowedException(['POST']);
        }

        $result = [];
        switch ($api->getAction()) {
            case Api::ACTION_SELECT: {
                $result = $this->selectData($api, $request);
            } break;
            case Api::ACTION_INSERT: {
                $result = $this->insertData($api, $request);
            } break;
            case Api::ACTION_UPDATE: {
                $result = $this->updateData($api, $request);
            } break;
            case Api::ACTION_DELETE: {
                $result = $this->deleteData($api, $request);
            } break;
        }
        return new JsonResponse($result, 200);
    }




    private function deleteData(Api $api, Request $request)
    {
        $result = [];

        $filters = $request->request->get('filters', '');
        $additionalFilters = $this->getAdditionalFilters($filters);
        //GET FILTERED DATA
        $storeData = $this->getDoctrine()->getManager()->getRepository('AppShedExtensionsStorageBundle:Data')->getDataForApi($api, $additionalFilters);

        if ($api->getLimit()) {
            $storeData = $this->limitResults($storeData, $api->getLimit());
        }

        $executed = 0;
        if (count($storeData)) {
            /** @var EntityManager $em */
            $em = $this->getDoctrine()->getManager();
            foreach ($storeData as $k => $value) {
                $storeData[$k] = $em->merge($storeData[$k]);
                $em->remove($storeData[$k]);
            }
            $executed++;
            $em->flush();
        }
        return ['updatedRows' => $executed];
    }


    private function updateData(Api $api, Request $request)
    {
        $result = [];
        //get this data here (before getDataForApi()) because $em->clear() make bad
        $storeColumns = $api->getStore()->getColumns();
        $appId = $api->getApp()->getAppId();

        $filters = $request->request->get('filters', '');
        $request->request->remove('filters');
        $additionalFilters = $this->getAdditionalFilters($filters);
        //GET FILTERED DATA
        $storeData = $this->getDoctrine()->getManager()->getRepository('AppShedExtensionsStorageBundle:Data')->getDataForApi($api, $additionalFilters);

        if ($api->getLimit()) {
            $storeData = $this->limitResults($storeData, $api->getLimit());
        }
        $updateData = $request->request->all();

        $executed = 0;

        if (empty($updateData)) {
            throw new MissingDataException('No data given');
        }

        foreach ($storeData as $k => $value) {
            $data = $storeData[$k]->getData();
            $newData = array_merge($data, $updateData);
            $storeData[$k]->setData($newData);
            $storeData[$k]->setColumns(array_keys($newData));
            $executed++;
        }

        $store = $api->getStore();
        $em = $this->getDoctrine()->getManager();
        //Add any new columns to the store
        $newColumns = array_diff(array_keys($updateData), $storeColumns);
        if (count($newColumns)) {
            $store->setColumns(array_merge($storeColumns, $newColumns));
        }
        $em->merge($store);
        $em->flush();

        return ['updatedRows' => $executed];
    }

    private function insertData(Api $api, Request $request)
    {
        //get this data here (before getDataForApi()) because $em->clear() make bad
        $storeColumns = $api->getStore()->getColumns();
        $appId = $api->getApp()->getAppId();

        $data = $request->request->all();
        $executed = 0;

        if (empty($data)) {
            throw new MissingDataException('No data given');
        }

        $dataO = new Data();
        $dataO->setStore($api->getStore());
        $dataO->setColumns(array_keys($data));
        $dataO->setData($data);
        $this->getDoctrine()->getManager()->persist($dataO);
        $this->getDoctrine()->getManager()->flush();

        $store = $api->getStore();
        $em = $this->getDoctrine()->getManager();
        //Add any new columns to the store
        $newColumns = array_diff(array_keys($data), $storeColumns);
        if (count($newColumns)) {
            $store->setColumns(array_merge($storeColumns, $newColumns));
        }
        $em->merge($store);
        $em->flush();
        $executed++;

        return ['updatedRows' => $executed];
    }

    private function selectData(Api $api, Request $request)
    {
        $result = [];

        $filters = $request->request->get('filters', '');
        $additionalFilters = $this->getAdditionalFilters($filters);
        //GET FILTERED DATA
        $storeData = $this->getDoctrine()->getManager()->getRepository('AppShedExtensionsStorageBundle:Data')->getDataForApi($api, $additionalFilters);

        $sql['select'] = [];
        /** @var Field $field */
        foreach ($api->getFields() as $field) {
            if ($field->getAggregate()) {
                $sql['aggregate'] = $field;
                $sql['select'][] = $field->getAggregate() . '(' . $field->getField() . ')';
            } else {
                $sql['select'][] = $field->getField();
            }
        }

        //SET DATA FIELDS BY SELECT STATEMENT
        /** @var Data $row */
        foreach ($storeData as $row) {
            $data = $row->getData();
            if (isset($sql['aggregate'])) {
                $data[$sql['aggregate']->getAggregate() . '(' . $sql['aggregate']->getField() . ')'] = 0;
            }
            foreach ($sql['select'] as $field) {
                if (!isset($data[$field])) {
                    $data[$field] = null;
                }
            }
            $result[] = $data;
        }

        //GROUP BY && DO AGGREGATE FUNCTIONS
        if ($api->getGroupField() || isset($sql['aggregate'])) {
            //GROUP BY
            if ($api->getGroupField()) {
                $resultGroup = [];
                foreach ($result as $record) {
                    $groupValue = $record[$api->getGroupField()];
                    if (isset($resultGroup[$groupValue])) {
                        $resultGroup[$groupValue][] = $record;
                    } else {
                        $resultGroup[$groupValue] = [$record];
                    }
                }
            } else {
                //just same format, for aggregation
                $resultGroup = [$result];
            }

            // DO AGGREGATE FUNCTIONS (if any)
            /** @var Field $sql['aggregate']  */
            if (isset($sql['aggregate'])) {
                $resultField = $sql['aggregate']->getAggregate() . '(' . $sql['aggregate']->getField() . ')';
                foreach ($resultGroup as $key => $resultGroupRecord) {
                    $functionInputData = [];
                    foreach ($resultGroupRecord as $record) {
                        if (isset($record[$sql['aggregate']->getField()]) && $record[$sql['aggregate']->getField()] != null) {
                            $functionInputData[] = $record[$sql['aggregate']->getField()];
                        }
                    }
                        $resultGroup[$key][0] = $this->aggregateRowsFunction($sql['aggregate']->getAggregate(), $sql['aggregate']->getField(), $resultField, $resultGroup[$key]);
                }
            }

            $result = [];
            foreach ($resultGroup as $key => $resultGroupRecord) {
                $result[] = $resultGroupRecord[0];
            }
        }

        //REMOVE USELESS ROWS
        foreach ($result as $recordKey => $recordValue) {
            $keys = array_keys($recordValue);
            foreach ($keys as $key) {
                if (! in_array($key, $sql['select'])) {
                    unset($result[$recordKey][$key]);
                }
            }
        }

        //ORDER RESULTS
        if ($api->getOrderField()) {
            $orderField = $api->getOrderField();
            if ($api->getOrderField() == Api::ORDER_AGGREGATE_FUNCTION) {
                if (isset($sql['aggregate'])) {
                    $orderField = $sql['aggregate']->getAggregate() . '(' . $sql['aggregate']->getField() . ')';
                } else {
                    $orderField = '';
                }
            }
            if ($orderField) {
                if ($api->getOrderDirection() == Api::ODRER_DIRECTION_ASC) {
                    //Make different functions to decrease count IF statements inside function
                    usort($result, $this->sortOrderAsc($orderField));
                } else {
                    usort($result, $this->sortOrderDesc($orderField));
                }
            }
        }

        //LIMIT RESULTS
        if ($api->getLimit()) {
            $result = $this->limitResults($result, $api->getLimit());
        }
        return $result;
    }

    private function getAdditionalFilters($filters = '')
    {
        $additionalFilters = [];
        if ($filters) {
            $allowedOperations = [Filter::FILTER_GREATER_THAN_OR_EQUALS, Filter::FILTER_GREATER_THAN, Filter::FILTER_LESS_THAN_OR_EQUALS, Filter::FILTER_LESS_THAN, Filter::FILTER_NOT_EQUALS, Filter::FILTER_EQUALS]; //order of elements is important
            $statements = explode(' and ', strtolower($filters));
            foreach ($statements as $statement) {
                foreach ($allowedOperations as $operation) {
                    if (strpos($statement, $operation) !== FALSE) {
                        $statementParts = explode($operation, $statement);
                        $filter = new Filter();
                        $filter->setCol(trim($statementParts[0]));
                        $filter->setType($operation);
                        $filter->setValue(trim($statementParts[1]));
                        $additionalFilters[] = $filter;
                    }
                }
            }
        }
        return $additionalFilters;
    }

    private function limitResults($result, $limit = '')
    {
        $limitParts = explode(',', $limit);
        if (count($limitParts) == 2) {
            $offset = trim($limitParts[0]);
            $count = trim($limitParts[1]);
        } else {
            $offset = 0;
            $count = trim($limitParts[0]);
        }
        return array_slice($result, $offset, $count);
    }

    private function aggregateRowsFunction($function, $field, $resultField, $data)
    {
        $row = $data[0];
        switch ($function) {
            case 'count':
                $hasData = false;
                $count = 0;
                foreach ($data as $record) {
                    if (isset($record[$field]) && $record[$field] != null) {
                        $count++;
                        $hasData = true;
                    }
                }
                $result = (($hasData) ? ($count) : (null));
            break;
            case 'sum':
                $hasData = false;
                $sum = 0;
                foreach ($data as $record) {
                    if (isset($record[$field]) && $record[$field] != null) {
                        $sum += $record[$field];
                        $hasData = true;
                    }
                }
                $result = (($hasData) ? ($sum) : (null));
                break;
            case 'avg':
                $hasData = false;
                $sum = 0;
                $count = 0;
                foreach ($data as $record) {
                    if (isset($record[$field]) && $record[$field] != null) {
                        $sum += $record[$field];
                        $count++;
                        $hasData = true;
                    }
                }
                $result = (($hasData) ? ($sum / $count) : (null));
                break;
            case 'max':
                $hasData = false;
                foreach ($data as $record) {
                    if (isset($record[$field]) && $record[$field] != null) {
                        if (!isset($max) || $record[$field] > $max) {
                            $max = $record[$field];
                            $row = $record;
                        }
                        $hasData = true;
                    }
                }
                $result = (($hasData) ? ($max) : (null));
                break;
            case 'min':
                $hasData = false;
                foreach ($data as $record) {
                    if (isset($record[$field]) && $record[$field] != null) {
                        if (!isset($min) || $record[$field] < $min) {
                            $min = $record[$field];
                            $row = $record;
                        }
                        $hasData = true;
                    }
                }
                $result = (($hasData) ? ($min) : (null));
                break;
            default:
                throw new NotImplementedException("Aggregate function '$function' not  implemented");

        }
        $row[$resultField] = $result;
        return $row;
    }

    private function sortOrderAsc($field)
    {
        return function ($a, $b) use ($field) {
            return $a[$field] > $b[$field];
        };
    }

    private function sortOrderDesc($field)
    {
        return function ($a, $b) use ($field) {
            return $a[$field] < $b[$field];
        };
    }
}
