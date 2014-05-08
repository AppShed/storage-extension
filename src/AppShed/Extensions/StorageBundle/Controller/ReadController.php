<?php
/**
 * Created by mcfedr on 05/05/2014 21:31
 */

namespace AppShed\Extensions\StorageBundle\Controller;

use AppShed\Extensions\StorageBundle\Entity\Store;
use AppShed\Extensions\StorageBundle\Form\FiltersViewType;
use AppShed\Extensions\StorageBundle\Form\ViewType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;

class ReadController extends StorageController
{
    /**
     * @Route("/read/settings", name="read_settings")
     * @Template()
     * @Method({"GET", "POST"})
     */
    public function settingsAction(Request $request)
    {
        return parent::settingsAction($request);
    }

    /**
     * @Route("/read/settings/store/{id}", name="read_store_edit", requirements={"id": "new|\d+"}, defaults={"id" = "new"})
     * @Template()
     * @Method({"GET", "POST"})
     */
    public function editStoreAction($id, Request $request) {
        return parent::editStoreAction($id, $request);
    }

    protected function getPostEditStoreRoute() {
        return 'read_settings';
    }

    /**
     * @Route("/read/settings/filters", name="read_filters")
     * @Template()
     * @Method({"GET", "POST"})
     */
    public function filtersAction(Request $request)
    {
        $form = $this->createForm(new FiltersViewType(), $this->getView($request));

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);

            if ($form->isValid()) {
                $this->getDoctrine()->getManager()->flush();
            }
        }

        return [
            'form' => $form->createView(),
            'appParams' => $this->getExtensionParameters($request)
        ];
    }
} 