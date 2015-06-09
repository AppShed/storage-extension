<?php
/**
 * Created by mcfedr on 05/05/2014 21:31
 */

namespace AppShed\Extensions\StorageBundle\Controller;

use AppShed\Extensions\StorageBundle\Entity\Api;
use AppShed\Extensions\StorageBundle\Entity\Data;
use AppShed\Extensions\StorageBundle\Entity\Store;
use AppShed\Extensions\StorageBundle\Form\ApiType;
use AppShed\Extensions\StorageBundle\Form\StoreType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ApiController extends StorageController
{
    /**
     * @Route("/api/list", name="api_list")
     * @Method({"GET"})
     * @Template()
     */
    public function listAction(Request $request)
    {
        $app = $this->getApp($request);
        $data['apis'] = $this->getDoctrine()->getRepository('AppShedExtensionsStorageBundle:Api')->findBy(['app' => $app]);
        $data['appParams'] = $this->getExtensionAppParameters($request);
        return $data;
    }

    /**
     * @Route("/api/new", name="api_new")
     * @Method({"GET", "POST"})
     * @Template()
     */
    public function newAction(Request $request)
    {
        $app = $this->getApp($request);
        $appParams = $this->getExtensionAppParameters($request);

        $api = new Api();
        $api->setApp($app);
        $form = $this->createForm(new ApiType($app), $api, [
            'action' => $this->generateUrl('api_new', $appParams),
            'method' => 'POST'
        ]);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($api);
            $em->flush();
            return $this->redirect($this->generateUrl('api_edit', array_merge($appParams, ['uuid' => $api->getUuid()])));
        }

        $data = [
            'api' => $api,
            'form'   => $form->createView(),
            'appParams' => $appParams
        ];
        return $data;
    }

    /**
     * @Route("/api/{uuid}/edit", name="api_edit")
     * @param Api $api
     * @param $uuid
     * @ParamConverter("api", class="AppShed\Extensions\StorageBundle\Entity\Api", options={"uuid"="uuid"})
     * @Method({"GET", "POST"})
     * @return array
     * @Template()
     */
    public function editAction(Request $request, Api $api, $uuid)
    {
        $app = $this->getApp($request);
        $appParams = $this->getExtensionAppParameters($request);
        if ($api->getApp() != $app) {
            throw new NotFoundHttpException("Api with uuid $uuid not found");
        }

        $data = [
            'api' => $api,
            'appParams' => $appParams
        ];
        return $data;
    }


    /**
     * @Route("/api/{uuid}/delete", name="api_delete")
     * @param Api $api
     * @param $uuid
     * @ParamConverter("api", class="AppShed\Extensions\StorageBundle\Entity\Api", options={"uuid"="uuid"})
     * @Method({"GET", "POST"})
     * @return array
     */
    public function deleteAction(Request $request, Api $api, $uuid)
    {
        $app = $this->getApp($request);
        $appParams = $this->getExtensionAppParameters($request);
        if ($api->getApp() != $app) {
            throw new NotFoundHttpException("Api with uuid $uuid not found");
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($api);
        $em->flush();

        return $this->redirect($this->generateUrl('api_list', $appParams));
    }


    /**
     * @Route("/api/{uuid}", name="api_show")
     * @param Api $api
     * @param $uuid
     * @ParamConverter("api", class="AppShed\Extensions\StorageBundle\Entity\Api", options={"uuid"="uuid"})
     * @Method({"GET", "POST"})
     * @return JsonResponse

     */
    public function showAction(Request $request, Api $api, $uuid)
    {
        $api->getQuery();

        switch ($api->getAction()) {
            case 'Select': {
                $result = $this->selectData($api);
            } break;
            case 'Insert': {
                $result = $this->insertData($api);
            } break;
//            case 'Update': {
//                $result = $this->updateData($api);
//            } break;
//            case 'Delete': {
//                $result = $this->deleteData($api);
//            } break;
        }

        $html = '<table>';
        foreach ($result as $record) {
            $html .= '<tr>';
            foreach ($record as $col) {
                $html .= '<td>' . $col . '</td>';
            }
            $html .= '</tr>';
        }
        $html .= '</table>';



//        return new Response($html);
        return new JsonResponse($result, 200);
    }


    private function insertData(Api $api, Request $request) {
        $data = $request->request->all();
        print_r($data);
    }

    private function selectData(Api $api) {
        $storeData = $api->getStore()->getData()->getValues();
        $result = [];

        //DECODE SQL
        $sqlRaw = strtolower($api->getQuery());
        $partDelimiters = ['limit', 'order by', 'group by', 'where', 'from', 'select'];
        $sqlDecoded = [];

        foreach ($partDelimiters as $delimiter) {
            $parts = explode($delimiter, $sqlRaw);
            $sqlRaw = $parts[0];
            if (isset($parts[1])) {
                $sqlDecoded[$delimiter] = trim($parts[1]);
            }
        }

        //PREPARE RESULT COLUMNS
        $sql['select'] = [];
        if ($sqlDecoded['select'] == '*') {
            $sql['select'] = $api->getStore()->getColumns();
        } else {
            $fields = explode(',', $sqlDecoded['select']);
            foreach ($fields as $field) {
                $field = trim($field, ' "');
                $sql['select'][] = $field;
                if (strpos($field, '(')) {
                    $aggregateParts = explode('(', $field);
                    $sql['aggregate'] = [
                        'function' => trim($aggregateParts[0]),
                        'field' => trim($aggregateParts[1], ') '),
                        'resultField' => $field
                    ];
                }
            }
        }

        //PREPARE FILTERS
        $sql['where'] = [];
        if (isset($sqlDecoded['where'])) {
            $allowedOperations = ['>=', '>', '<=', '<', '=']; //order of elements is important
            $statements = explode(' and ', $sqlDecoded['where']);
            foreach ($statements as $statement) {
                foreach ($allowedOperations as $operation) {
                    if (strpos($statement, $operation) !== FALSE) {
                        $statementParts = explode($operation, $statement);
                        $sql['where'][] = [
                            'field' => trim($statementParts[0]),
                            'operation' => $operation,
                            'value' => trim($statementParts[1])
                        ];
                        break;
                    }
                }
            }
        }

        //FILTER RESULTS
        /** @var Data $row */
        foreach ($storeData as $row) {
            $data = $row->getData();
            $filterAccepted = true;
            foreach ($sql['where'] as $where) {
                if (! in_array($where['field'], $row->getColumns())) {
                    $filterAccepted = false;
                    break;
                }
                switch ($where['operation']) {
                    case '=': {
                        if ($data[$where['field']] != $where['value']) {
                            $filterAccepted = false;
                        }
                    } break;
                    case '>': {
                        if ($data[$where['field']] <= $where['value']) {
                            $filterAccepted = false;
                        }
                    } break;
                    case '>=': {
                        if ($data[$where['field']] < $where['value']) {
                            $filterAccepted = false;
                        }
                    } break;
                    case '<': {
                        if ($data[$where['field']] >= $where['value']) {
                            $filterAccepted = false;
                        }
                    } break;
                    case '<=': {
                        if ($data[$where['field']] > $where['value']) {
                            $filterAccepted = false;
                        }
                    } break;
                }
            }
            if (! $filterAccepted) {
                continue;
            }

            $record = [];
            foreach ($sql['select'] as $field) {
                $record[$field] = ((isset($data[$field])) ? ($data[$field]) : (null));
            }
            $result[] = $record;
        }

        //GROUP BY && DO AGGREGATE FUNCTIONS
        if (isset($sqlDecoded['group by']) || isset($sql['aggregate'])) {
            //GROUP BY
            if (isset($sqlDecoded['group by'])) {
                $sql['group'] = trim($sqlDecoded['group by']);
                $resultGroup = [];
                foreach ($result as $record) {
                    $groupValue = $record[$sql['group']];
                    if (isset($resultGroup[$groupValue])) {
                        $resultGroup[$groupValue][] = $record;
                    } else {
                        $resultGroup[$groupValue] = [$record];
                    }
                }
            } else {
                //just same format for aggregation
                $resultGroup = [$result];
            }

            // DO AGGREGATE FUNCTIONS (if any)
            if (isset($sql['aggregate'])) {
                foreach ($resultGroup as $key => $resultGroupRecord) {
                    $functionInputData = [];
                    foreach ($resultGroupRecord as $record) {
                        if ($record[$sql['aggregate']['field']] != null) {
                            $functionInputData[] = $record[$sql['aggregate']['field']];
                        }
                    }
                    $resultGroup[$key][0][$sql['aggregate']['resultField']] = $this->aggregateFunction($sql['aggregate']['function'], $functionInputData);
                }
            }

            $result = [];
            foreach ($resultGroup as $key => $resultGroupRecord) {
                $result[] = $resultGroupRecord[0];
            }
        }

        //ORDER RESULTS
        if (isset($sqlDecoded['order by'])) {
            $orderParts = explode(' ', $sqlDecoded['order by']);
            $sql['order'] = [
                'field' => $orderParts[0],
                'direction' => ((isset($orderParts[1])) ? ($orderParts[1]) : ('asc'))
            ];
            if ($sql['order']['direction'] == 'asc') {
                //Make different functions to decrease count IF statements inside function
                usort($result, $this->sortOrderAsc($sql['order']['field']));
            } else {
                usort($result, $this->sortOrderDesc($sql['order']['field']));
            }
        }

        //LIMIT RESULTS
        if (isset($sqlDecoded['limit'])) {
            $limitParts = explode(',', $sqlDecoded['limit']);
            if (count($limitParts) == 2) {
                $sql['limit'] = [
                    'offset' => trim($limitParts[0]),
                    'count' => trim($limitParts[1])
                ];
            } else {
                $sql['limit'] = [
                    'offset' => 0,
                    'count' => trim($limitParts[0])
                ];
            }
            $result = array_slice($result, $sql['limit']['offset'], $sql['limit']['count']);
        }
        return $result;
    }

    private function aggregateFunction($function, $input) {
        switch ($function) {
            case 'count': {
                return count($input);
            } break;
            case 'sum': {
                return array_sum($input);
            } break;
            case 'avg': {
                return array_sum($input) / count($input);
            } break;
            case 'max': {
                return max($input);
            } break;
            case 'min': {
                return min($input);
            } break;
            default: {
                return 'unknown function';
            }
        }
    }

    private function sortOrderAsc($field) {
        return function ($a, $b) use ($field) {
            return $a[$field] > $b[$field];
        };
    }

    private function sortOrderDesc($field) {
        return function ($a, $b) use ($field) {
            return $a[$field] < $b[$field];
        };
    }

    protected function getPostEditStoreRoute() {
        return 'show_api';
    }

}