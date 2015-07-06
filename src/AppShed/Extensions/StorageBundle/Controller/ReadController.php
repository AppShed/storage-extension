<?php
/**
 * Created by mcfedr on 05/05/2014 21:31
 */

namespace AppShed\Extensions\StorageBundle\Controller;

use AppShed\Extensions\StorageBundle\Entity\Data;
use AppShed\Extensions\StorageBundle\Form\FiltersViewType;
use AppShed\Remote\Element\Item\Link;
use AppShed\Remote\Element\Item\Text;
use AppShed\Remote\Element\Item\Thumb;
use AppShed\Remote\Element\Screen\Screen;
use AppShed\Remote\HTML\Remote;
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
        $view = $this->getView($request);
        $form = $this->createForm(new FiltersViewType($view->getStore()), $view);

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);

            if ($form->isValid()) {
                $this->get('session')->getFlashBag()->add(
                    'success',
                    'Your changes were saved'
                );
                $this->getDoctrine()->getManager()->flush();
            }
        }

        return [
            'form' => $form->createView(),
            'appParams' => $this->getExtensionParameters($request)
        ];
    }

    /**
     * @Route("/read", name="read")
     * @Method({"GET", "POST", "OPTIONS"})
     */
    public function appshedAction(Request $request)
    {
        $view = $this->getView($request);
        if (!$view->getId()) {
            $screen = new Screen("Error");
            $screen->addChild(new Text("You must setup the view first"));
            return (new Remote($screen))->getSymfonyResponse();
        }

        $rootScreen = new Screen($view->getTitle() ?: "Results");
        if ($view->getMessage()) {
            $rootScreen->addChild(new Text($view->getMessage()));
        }

        $datas = $this->getDoctrine()->getRepository('AppShedExtensionsStorageBundle:Data')->getDataForView($view);
        /** @var Data $dataO */
        foreach ($datas as $dataO) {
            $data = $dataO->getData();
            if (count($data)) {
                if (array_key_exists('title', $data)) {
                    $title = $data['title'];
                    unset($data['title']);
                } else {
                    $title = current($data);
                    if (is_array($title)) {
                        $title = array_keys($data)[0];
                    }
                }

                $dataScreen = new Screen($title);
                foreach ($data as $key => $value) {
                    if (is_array($value)) {
                        $value = '[' . implode(', ', $value) . ']';
                    }
                    $dataScreen->addChild(new Thumb($key, $value));
                }

                $link = new Link($title);
                $link->setScreenLink($dataScreen);
                $rootScreen->addChild($link);
            }
        }

        return (new Remote($rootScreen))->getSymfonyResponse();
    }
} 