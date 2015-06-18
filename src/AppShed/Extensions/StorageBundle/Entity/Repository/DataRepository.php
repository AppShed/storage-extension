<?php


namespace AppShed\Extensions\StorageBundle\Entity\Repository;

use AppShed\Extensions\StorageBundle\Entity\Api;
use AppShed\Extensions\StorageBundle\Entity\Data;
use AppShed\Extensions\StorageBundle\Entity\Filter;
use AppShed\Extensions\StorageBundle\Entity\Store;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityRepository;
use AppShed\Extensions\StorageBundle\Entity\View;

use Doctrine\ORM\Query\Expr;

class DataRepository extends EntityRepository
{
    /**
     * @param View $view
     * @return Data[]
     */
    public function getDataForView(View $view)
    {
        return $this->getFilteredData($view->getStore(), new ArrayCollection($view->getFilters()->toArray()));
    }

    /**
     * @param Api $api
     * @param array $additionalFilters;
     * @return Data[]
     */
    public function getDataForApi(Api $api, $additionalFilters = [])
    {
        return $this->getFilteredData($api->getStore(), new ArrayCollection(array_merge($api->getFilters()->toArray(), $additionalFilters)));
    }

    private function getFilteredData(Store $store, Collection $filters) {
        $qb = $this->createQueryBuilder('d')
            ->andWhere('d.store = :store')->setParameter('store', $store);

        if ($filters->count()) {
            $filteredData = [];

            $em = $this->getEntityManager();
            $batchSize = 20;
            $i = 0;
            $iterator = $qb->getQuery()->iterate();
            foreach ($iterator as $row) {
                /** @var Data $dataO */
                $dataO = $row[0];

                $data = $dataO->getData();
                $ok = true;
                /** @var Filter $filter */
                foreach ($filters as $filter) {
                    if (!isset($data[$filter->getCol()])) {
                        $ok = false;
                        break;
                    }
                    switch ($filter->getType()) {
                        case Filter::FILTER_EQUALS:
                            if (!($data[$filter->getCol()] == $filter->getValue())) {
                                $ok = false;
                                break 2;
                            }
                            break;
                        case Filter::FILTER_GREATER_THAN:
                            if (!($data[$filter->getCol()] > $filter->getValue())) {
                                $ok = false;
                                break 2;
                            }
                            break;
                        case Filter::FILTER_GREATER_THAN_OR_EQUALS:
                            if (!($data[$filter->getCol()] >= $filter->getValue())) {
                                $ok = false;
                                break 2;
                            }
                            break;
                        case Filter::FILTER_LESS_THAN:
                            if (!($data[$filter->getCol()] < $filter->getValue())) {
                                $ok = false;
                                break 2;
                            }
                            break;
                        case Filter::FILTER_LESS_THAN_OR_EQUALS:
                            if (!($data[$filter->getCol()] <= $filter->getValue())) {
                                $ok = false;
                                break 2;
                            }
                            break;
                    }
                }
                if ($ok) {
                    $filteredData[] = $dataO;
                }

                if (($i++ % $batchSize) == 0) {
                    $em->clear(); // Detaches all objects from Doctrine!
                }
            }

            return $filteredData;
        }
        else {
            return $qb->getQuery()->getResult();
        }
    }
}