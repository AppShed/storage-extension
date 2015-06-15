<?php
/**
 * Created by mcfedr on 05/05/2014 21:31
 */

namespace AppShed\Extensions\StorageBundle\Controller;

use AppShed\Extensions\StorageBundle\Entity\Data;
use AppShed\Extensions\StorageBundle\Entity\Store;
use AppShed\Extensions\StorageBundle\Form\StoreType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class TableController extends StorageController
{
    /**
     * @Route("/table/list", name="store_list")
     * @Method({"GET"})
     * @Template()
     */
    public function listAction(Request $request)
    {
        $app = $this->getApp($request);
        $data['stores'] = $this->getDoctrine()->getRepository('AppShedExtensionsStorageBundle:Store')->findBy(['app' => $app]);
        $data['appParams'] = $this->getExtensionAppParameters($request);
        return $data;
    }

    /**
     * @Route("/table/{id}", requirements={"id": "\d+"}, name="store_view")
     * @Method({"GET"})
     * @Template()
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     *
     */
    public function viewAction(Request $request, $id)
    {
        $app = $this->getApp($request);
        $store = $this->getDoctrine()->getRepository("AppShedExtensionsStorageBundle:Store")->findOneBy(['id' => $id, 'app' => $app]);
        if (!$store) {
            throw new NotFoundHttpException("Store with id $id not found");
        }

        $perPage = 100;

        $pages = ceil($store->getData()->count() / $perPage);
        $page = $request->query->getInt('page', 1);
        if ($page > $pages) {
            $page = $pages;
        }
        if ($page < 1) {
            $page = 1;
        }

        $data['store'] = $store;
        $storeData = array_slice($store->getData()->getValues(), ($page - 1) * $perPage, $perPage);

        $data['data'] = [];
        /** @var Data $record */
        foreach ($storeData as $record) {
            $data['data'][] = [
                'id' => $record->getId(),
                'data' => $record->getData()
            ];
        }

        $data['appParams'] = $this->getExtensionAppParameters($request);
        $data['page'] = $page;
        $data['pages'] = $pages;

        return $data;
    }

    /**
     * @Route("/table/new", name="store_new")
     * @Method({"GET", "POST"})
     * @Template()
     */
    public function newAction(Request $request)
    {
        $app = $this->getApp($request);
        $appParams = $this->getExtensionAppParameters($request);

        $store = new Store();
        $store->setApp($app);
        $form = $this->createForm(new StoreType(), $store, array(
            'action' => $this->generateUrl('store_new', $appParams),
            'method' => 'POST',
        ));

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($store);
            $em->flush();

            return $this->redirect($this->generateUrl('store_list', $appParams));
        }

        $data = [
            'store' => $store,
            'form'   => $form->createView(),
            'appParams' => $appParams
        ];

        return $data;
    }

    protected function getPostEditStoreRoute() {
        return 'show_data';
    }

}