<?php
/**
 * Created by mcfedr on 05/05/2014 21:31
 */

namespace AppShed\Extensions\StorageBundle\Controller;

use AppShed\Extensions\StorageBundle\Entity\Data;
use AppShed\Extensions\StorageBundle\Entity\Store;
use AppShed\Extensions\StorageBundle\Form\FiltersViewType;
use AppShed\Remote\Element\Item\Link;
use AppShed\Remote\Element\Item\Text;
use AppShed\Remote\Element\Item\Thumb;
use AppShed\Remote\Element\Screen\Screen;
use AppShed\Remote\HTML\Remote;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class TableController extends StorageController
{
    /**
     * @Route("/table/list", name="store_list")
     * @Method({"GET"})
     */
    public function listAction(Request $request)
    {
        $app = $this->getApp($request);
        $data['stores'] = $this->getDoctrine()->getRepository('AppShedExtensionsStorageBundle:Store')->findBy(['app' => $app]);
        $data['appParams'] = $this->getExtensionAppParameters($request);
        return $this->render('@AppShedExtensionsStorage/Table/list.html.twig', $data);
    }

    /**
     * @Route("/table/{id}", requirements={"id": "\d+"}, name="store_view")
     * @param $id
     * @Method({"GET"})
     * @return Response
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
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

        return $this->render('@AppShedExtensionsStorage/Table/data.html.twig', $data);
    }















    protected function getPostEditStoreRoute() {
        return 'show_data';
    }
}