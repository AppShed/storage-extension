<?php
/**
 * Created by mcfedr on 05/05/2014 21:31
 */

namespace AppShed\Extensions\StorageBundle\Controller;

use AppShed\Extensions\StorageBundle\Entity\Api;
use AppShed\Extensions\StorageBundle\Entity\Data;
use AppShed\Extensions\StorageBundle\Entity\Field;
use AppShed\Extensions\StorageBundle\Entity\Filter;
use AppShed\Extensions\StorageBundle\Form\ApiEditType;
use AppShed\Extensions\StorageBundle\Form\ApiType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Intl\Exception\NotImplementedException;
use Symfony\Component\Routing\Exception\MethodNotAllowedException;

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
        return [
            'apis' => $this->getDoctrine()->getRepository('AppShedExtensionsStorageBundle:Api')->findBy(['app' => $app]),
            'appParams' => $this->getExtensionAppParameters($request),
            'apiVisualizerUrl' => $this->container->getParameter('api_visualizer_url')
        ];
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

        if (!$app->getId()) {
            $this->getDoctrine()->getManager()->persist($app);
            $this->getDoctrine()->getManager()->flush();
        }

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
            if ($api->getAction() == Api::ACTION_INSERT) {
                return $this->redirect($this->generateUrl('api_list', $appParams));
            }
            return $this->redirect($this->generateUrl('api_edit', array_merge($appParams, ['uuid' => $api->getUuid()])));
        }

        return [
            'api' => $api,
            'form'   => $form->createView(),
            'appParams' => $appParams
        ];
    }

    /**
     * @Route("/api/{uuid}/edit", name="api_edit")
     * @Method({"GET", "POST"})
     * @ParamConverter("api", class="AppShed\Extensions\StorageBundle\Entity\Api", options={"uuid"="uuid"})
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

        $form->handleRequest($request);
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'success',
                'Your changes were saved'
            );
            return $this->redirect($this->generateUrl('api_list', $appParams));
        }

        return [
            'api' => $api,
            'form'   => $form->createView(),
            'appParams' => $appParams,
            'const' => [
                'orderAggregateFunction' => Api::ORDER_AGGREGATE_FUNCTION,
                'orderAggregateFunctionText' => Api::ORDER_AGGREGATE_FUNCTION_TEXT
            ]
        ];
    }

    /**
     * @Route("/api/{uuid}/delete", name="api_delete")
     * @ParamConverter("api", class="AppShed\Extensions\StorageBundle\Entity\Api", options={"uuid"="uuid"})
     * @Method({"POST"})
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

    protected function getPostEditStoreRoute() {
        return 'show_api';
    }
}
