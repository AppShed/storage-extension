<?php
/**
 * Created by mcfedr on 05/05/2014 20:05
 */

namespace AppShed\Extensions\StorageBundle\Controller;

use AppShed\Extensions\StorageBundle\Entity\App;
use AppShed\Extensions\StorageBundle\Entity\Store;
use AppShed\Extensions\StorageBundle\Entity\View;
use AppShed\Extensions\StorageBundle\Exception\MissingAppParametersException;
use AppShed\Extensions\StorageBundle\Form\StoreType;
use AppShed\Extensions\StorageBundle\Form\ViewType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

abstract class StorageController extends Controller
{
    /**
     * @param Request $request
     * @return App
     * @throws \AppShed\Extensions\StorageBundle\Exception\MissingAppParametersException
     */
    protected function getApp(Request $request)
    {
        $params = $this->getExtensionAppParameters($request);

        $app = $this->getDoctrine()->getRepository('AppShedExtensionsStorageBundle:App')->findOneBy(
            [
                'appId' => $params['appId'],
                'appSecret' => $params['appSecret']
            ]
        );

        if (!$app) {
            $app = new App();
            $app->setAppId($params['appId']);
            $app->setAppSecret($params['appSecret']);
            $this->getDoctrine()->getManager()->persist($app);
        }

        return $app;
    }

    /**
     * @param Request $request
     * @return View
     * @throws \AppShed\Extensions\StorageBundle\Exception\MissingAppParametersException
     */
    protected function getView(Request $request)
    {
        $params = $this->getExtensionParameters($request);

        $view = $this->getDoctrine()->getRepository('AppShedExtensionsStorageBundle:View')->findOneBy(
            [
                'itemId' => $params['itemid'],
                'identifier' => $params['identifier']
            ]
        );

        if (!$view) {
            $view = new View();
            $view->setItemId($params['itemid']);
            $view->setIdentifier($params['identifier']);
            $this->getDoctrine()->getManager()->persist($view);
        }

        return $view;
    }

    /**
     * @param Request $request
     * @return array
     * @throws \AppShed\Extensions\StorageBundle\Exception\MissingAppParametersException
     */
    protected function getExtensionParameters(Request $request)
    {
        if ($request->query->has('itemid') && $request->query->has('identifier')) {
            return array_merge($this->getExtensionAppParameters($request), [
                //This is converted to an int because of a bug in appshed where only the id is in the url in some places
                'itemid' => str_replace(["item", "tab"], "",  $request->query->get('itemid')),
                'identifier' => $request->query->get('identifier')
            ]);
        }
        throw new MissingAppParametersException('The extension parameters are missing');
    }

    /**
     * @param Request $request
     * @return array
     * @throws \AppShed\Extensions\StorageBundle\Exception\MissingAppParametersException
     */
    protected function getExtensionAppParameters(Request $request)
    {
        if ($request->query->has('appId') && $request->query->has('appSecret')) {
            return [
                'appId' => $request->query->get('appId'),
                'appSecret' => $request->query->get('appSecret')
            ];
        }
        throw new MissingAppParametersException('The extension parameters are missing');
    }








    protected function settingsAction(Request $request)
    {
        $app = $this->getApp($request);
        if (!$app->getStores()->count()) {
            $defaultStore = new Store();
            $defaultStore->setName('Default');
            $defaultStore->setApp($app);
            $app->addStore($defaultStore);

            $em = $this->getDoctrine()->getManager();
            $em->persist($defaultStore);
            $em->flush($defaultStore);
        }

        $form = $this->createForm(new ViewType($app), $this->getView($request));

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

    protected function editStoreAction($id, Request $request)
    {
        if ($id != 'new') {
            $store = $this->getDoctrine()->getRepository("AppShedExtensionsStorageBundle:Store")->findOneBy(
                [
                    'id' => $id,
                    'app' => $this->getApp($request)
                ]
            );

            if (!$store) {
                throw new NotFoundHttpException("Store with id $id not found");
            }
        } else {
            $store = new Store();
        }

        $form = $this->createForm(new StoreType(), $store);

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);

            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();

                if ($id == 'new') {
                    $app = $this->getApp($request);
                    $store->setApp($app);
                    $app->addStore($store);
                    $em->persist($store);
                }

                $em->flush();
                $this->get('session')->getFlashBag()->add(
                    'success',
                    'Your changes were saved'
                );
                return $this->redirect(
                    $this->generateUrl($this->getPostEditStoreRoute(), $this->getExtensionParameters($request))
                );
            }
        }

        return [
            'form' => $form->createView(),
            'appParams' => $this->getExtensionParameters($request)
        ];
    }

    /**
     * @return string
     */
    protected abstract function getPostEditStoreRoute();
} 