<?php

namespace AppShed\Extensions\StorageBundle\Controller;

use AppShed\Extensions\StorageBundle\Entity\Data;
use AppShed\Remote\Element\Item\Text;
use AppShed\Remote\Element\Screen\Screen;
use AppShed\Remote\HTML\Remote;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;

class WriteController extends StorageController
{
    /**
     * @Route("/write/settings", name="write_settings")
     * @Template()
     * @Method({"GET", "POST"})
     */
    public function settingsAction(Request $request)
    {
        return parent::settingsAction($request);
    }

    /**
     * @Route("/write/settings/store/{id}", name="write_store_edit", requirements={"id": "new|\d+"}, defaults={"id" = "new"})
     * @Template()
     * @Method({"GET", "POST"})
     */
    public function editStoreAction($id, Request $request) {
        return parent::editStoreAction($id, $request);
    }

    protected function getPostEditStoreRoute() {
        return 'write_settings';
    }

    /**
     * @Route("/write", name="write")
     * @Method({"GET", "POST", "OPTIONS"})
     */
    public function appshedAction(Request $request)
    {
        if (Remote::isOptionsRequest()) {
            return Remote::getCORSSymfonyResponse();
        }

        $view = $this->getView($request);
        if (!$view->getId()) {
            $screen = new Screen("Error");
            $screen->addChild(new Text("You must setup the view first"));
            return (new Remote($screen))->getSymfonyResponse();
        }

        $store = $view->getStore();

        $data = Remote::getRequestVariables();
        $cols = array_keys($data);
        if (!count($cols)) {
            $screen = new Screen("Error");
            $screen->addChild(new Text("You must send some data"));
            return (new Remote($screen))->getSymfonyResponse();
        }

        //Add any new columns to the store
        $newColumns = array_diff($cols, $store->getColumns());
        if (count($newColumns)) {
            $store->setColumns(array_merge($store->getColumns(), $newColumns));
        }

        //Create new data obj
        $dataO = new Data();
        $dataO->setColumns($cols);
        $dataO->setData($data);
        $dataO->setStore($store);

        $em = $this->getDoctrine()->getManager();
        $em->persist($dataO);
        $em->flush();

        $screen = new Screen($view->getTitle());
        $screen->addChild(new Text($view->getMessage()));
        return (new Remote($screen))->getSymfonyResponse();
    }
}
