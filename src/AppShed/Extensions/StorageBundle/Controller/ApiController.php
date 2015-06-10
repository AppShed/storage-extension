<?php
/**
 * Created by mcfedr on 05/05/2014 21:31
 */

namespace AppShed\Extensions\StorageBundle\Controller;

use AppShed\Extensions\StorageBundle\Entity\Api;
use AppShed\Extensions\StorageBundle\Entity\Data;
use AppShed\Extensions\StorageBundle\Entity\Field;
use AppShed\Extensions\StorageBundle\Form\ApiEditType;
use AppShed\Extensions\StorageBundle\Form\ApiType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
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
            $this->get('session')->getFlashBag()->add(
                'success',
                'New API created successfully'
            );
            if (in_array($api->getAction(), ['Insert'])) {
                return $this->redirect($this->generateUrl('api_list', $appParams));
            }
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

        $form = $this->createForm(new ApiEditType($api), $api, [
            'action' => $this->generateUrl('api_edit', array_merge($appParams, ['uuid' => $api->getUuid()])),
            'method' => 'POST'
        ]);

        switch ($api->getAction()) {
            case Api::ACTION_UPDATE:
            case Api::ACTION_DELETE: {
                $form
                    ->remove('fields')
                    ->remove('groupField')
                    ->remove('orderField')
                    ->remove('orderDirection');
            } break;
            case Api::ACTION_INSERT: {
                $form
                    ->remove('fields')
                    ->remove('filters')
                    ->remove('groupField')
                    ->remove('orderField')
                    ->remove('orderDirection')
                    ->remove('limit');
            } break;
        }


        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($api);
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'success',
                'Your changes were saved'
            );
            return $this->redirect($this->generateUrl('api_list', $appParams));
        }

        $data = [
            'api' => $api,
            'form'   => $form->createView(),
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
    public function showAction(Request $request, Api $api)
    {
        $store = $api->getStore()->getColumns();
        $app = $api->getApp()->getStores();


//        print_r($store->getColumns());
        switch ($api->getAction()) {
            case Api::ACTION_SELECT: {
                $result = $this->selectData($api);
            } break;
            case Api::ACTION_INSERT: {
                $result = $this->insertData($api, $request);
            } break;
            case Api::ACTION_UPDATE: {
                $result = $this->updateData($api, $request);
            } break;
            case Api::ACTION_DELETE: {
//                $result = $this->deleteData($api, $request);
            } break;
        }

//        $html = '<table>';
//        foreach ($result as $record) {
//            $html .= '<tr>';
//            foreach ($record as $col) {
//                $html .= '<td>' . $col . '</td>';
//            }
//            $html .= '</tr>';
//        }
//        $html .= '</table>';

//        return new Response($html);
        return new JsonResponse($result, 200);
    }

    private function updateData(Api $api, Request $request) {
        $result = [];

        //GET FILTERED DATA
        $storeData = $this->getDoctrine()->getManager()->getRepository('AppShedExtensionsStorageBundle:Data')->getDataForApi($api);

        if ($api->getLimit()) {
            $storeData = $this->limitResults($storeData, $api->getLimit());
        }
        $updateData = $request->request->all();

        $updated = 0;
        if (! empty($updateData)) {
            foreach ($storeData as $k => $value) {
                $data = $storeData[$k]->getData();
                $newData = array_merge($data, $updateData);
                $storeData[$k]->setData($newData);
                $storeData[$k]->setColumns(array_keys($newData));
            }

            //Add any new columns to the store
            $newColumns = array_diff(array_keys($updateData), $api->getStore()->getColumns());
            $store = $api->getStore();
            if (count($newColumns)) {
                $store->setColumns(array_merge($api->getStore()->getColumns(), $newColumns));

            }

//            $api->getStore()->setColumns(array_merge($api->getStore()->getColumns(), array_keys($updateData)));
            $this->getDoctrine()->getManager()->persist($store);
            $this->getDoctrine()->getManager()->flush();
            $updated = 1;
        }
        return ['value' => $updated];
    }

    private function insertData(Api $api, Request $request) {
        $data = $request->request->all();
        $inserted = 0;
        if (! empty($data)) {
            $dataO = new Data();
            $dataO->setStore($api->getStore());
            $dataO->setColumns(array_keys($data));
            $dataO->setData($data);
            $this->getDoctrine()->getManager()->persist($dataO);
            $this->getDoctrine()->getManager()->flush();
            $inserted = 1;
        }
        return ['value' => $inserted];
    }

    private function selectData(Api $api) {
        $result = [];

        //GET FILTERED DATA
        $storeData = $this->getDoctrine()->getManager()->getRepository('AppShedExtensionsStorageBundle:Data')->getDataForApi($api);

        $sql['select'] = [];
        /** @var Field $field */
        foreach ($api->getFields() as $field) {
            $sql['select'][] = $field->getField();
            if ($field->getAggregate()) {
                $sql['aggregate'] = $field;
            }
        }

        //SET DATA FIELDS BY SELECT STATEMENT
        /** @var Data $row */
        foreach ($storeData as $row) {
            $data = $row->getData();
            $record = [];
            foreach ($sql['select'] as $field) {
                $record[$field] = ((isset($data[$field])) ? ($data[$field]) : (null));
            }
            $result[] = $record;
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
                $resultField = $sql['aggregate']->getField();
                foreach ($resultGroup as $key => $resultGroupRecord) {
                    $functionInputData = [];
                    foreach ($resultGroupRecord as $record) {
                        if ($record[$sql['aggregate']->getArg()] != null) {
                            $functionInputData[] = $record[$sql['aggregate']->getArg()];
                        }
                    }

                    $resultGroup[$key][0][$resultField] = $this->aggregateFunction($sql['aggregate']->getAggregate(), $functionInputData);
                }
            }

            $result = [];
            foreach ($resultGroup as $key => $resultGroupRecord) {
                $result[] = $resultGroupRecord[0];
            }
        }

        //ORDER RESULTS
        if ($api->getOrderField()) {
            if ($api->getOrderDirection() == Api::ODRER_DIRECTION_ASC) {
                //Make different functions to decrease count IF statements inside function
                usort($result, $this->sortOrderAsc($api->getOrderField()));
            } else {
                usort($result, $this->sortOrderDesc($api->getOrderField()));
            }
        }

        //LIMIT RESULTS
        if ($api->getLimit()) {
            $result = $this->limitResults($result, $api->getLimit());
        }
        return $result;
    }

    private function limitResults($result, $limit = '') {
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