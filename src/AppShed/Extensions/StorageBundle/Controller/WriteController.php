<?php

namespace AppShed\Extensions\StorageBundle\Controller;

use AppShed\Extensions\StorageBundle\Entity\Store;
use AppShed\Extensions\StorageBundle\Form\StoreType;
use AppShed\Extensions\StorageBundle\Form\ViewType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
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
}
