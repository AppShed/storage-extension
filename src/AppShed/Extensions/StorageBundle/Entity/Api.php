<?php

namespace AppShed\Extensions\StorageBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Api
 *
 * @ORM\Table(indexes={
 *      @ORM\Index(name="api_uuid_idx", columns={"uuid"})
 * })
 * @ORM\Entity(repositoryClass="AppShed\Extensions\StorageBundle\Entity\Repository\ApiRepository")
 */
class Api
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(name="uuid", type="guid")
//     * @ ORM\Id
     * @ORM\GeneratedValue(strategy="UUID")
     *
     */
    private $uuid;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="action", type="string", length=50)
     */
    private $action;


    /**
     * @var string
     *
     * @ORM\Column(name="select", type="string", length=255, nullable=true)
     */
    private $select;

    /**
     * @var string
     *
     * @ORM\Column(name="groupBy", type="string", length=255, nullable=true)
     */
    private $groupBy;

    /**
     * @var string
     *
     * @ORM\Column(name="orderBy", type="string", length=255, nullable=true)
     */
    private $orderBy;

    /**
     * @var string
     *
     * @ORM\Column(name="limit", type="string", length=255, nullable=true)
     */
    private $limit;






    /**
     * @var string
     *
     * @ORM\Column(name="query", type="text", nullable=true)
     */
    private $query;

    /**
     * @var Store
     * @ORM\ManyToOne(targetEntity="AppShed\Extensions\StorageBundle\Entity\Store", inversedBy="apis")
     * @ORM\JoinColumn(onDelete="CASCADE")
     * @Assert\NotNull()
     */
    private $store;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Filter", mappedBy="api", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private $filters;

    /**
     * @var App
     * @ORM\ManyToOne(targetEntity="AppShed\Extensions\StorageBundle\Entity\App", inversedBy="apis")
     * @ORM\JoinColumn(onDelete="CASCADE")
     * @Assert\NotNull()
     */
    private $app;


    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Api
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set action
     *
     * @param string $action
     * @return Api
     */
    public function setAction($action)
    {
        $this->action = $action;

        return $this;
    }

    /**
     * Get action
     *
     * @return string
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * Set uuid
     *
     * @param guid $uuid
     * @return Api
     */
    public function setUuid($uuid)
    {
        $this->uuid = $uuid;

        return $this;
    }

    /**
     * Get uuid
     *
     * @return guid 
     */
    public function getUuid()
    {
        return $this->uuid;
    }

    /**
     * Set query
     *
     * @param string $query
     * @return Api
     */
    public function setQuery($query)
    {
        $this->query = $query;

        return $this;
    }

    /**
     * Get query
     *
     * @return string 
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * Set store
     *
     * @param \AppShed\Extensions\StorageBundle\Entity\Store $store
     * @return Api
     */
    public function setStore(\AppShed\Extensions\StorageBundle\Entity\Store $store = null)
    {
        $this->store = $store;

        return $this;
    }

    /**
     * Get store
     *
     * @return \AppShed\Extensions\StorageBundle\Entity\Store 
     */
    public function getStore()
    {
        return $this->store;
    }

    /**
     * Set app
     *
     * @param \AppShed\Extensions\StorageBundle\Entity\App $app
     * @return Api
     */
    public function setApp(\AppShed\Extensions\StorageBundle\Entity\App $app = null)
    {
        $this->app = $app;

        return $this;
    }

    /**
     * Get app
     *
     * @return \AppShed\Extensions\StorageBundle\Entity\App 
     */
    public function getApp()
    {
        return $this->app;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->filters = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add filters
     *
     * @param \AppShed\Extensions\StorageBundle\Entity\Filter $filters
     * @return Api
     */
    public function addFilter(\AppShed\Extensions\StorageBundle\Entity\Filter $filters)
    {
        $this->filters[] = $filters;

        return $this;
    }

    /**
     * Remove filters
     *
     * @param \AppShed\Extensions\StorageBundle\Entity\Filter $filters
     */
    public function removeFilter(\AppShed\Extensions\StorageBundle\Entity\Filter $filters)
    {
        $this->filters->removeElement($filters);
    }

    /**
     * Get filters
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getFilters()
    {
        return $this->filters;
    }

    /**
     * Set select
     *
     * @param string $select
     * @return Api
     */
    public function setSelect($select)
    {
        $this->select = $select;

        return $this;
    }

    /**
     * Get select
     *
     * @return string 
     */
    public function getSelect()
    {
        return $this->select;
    }

    /**
     * Set groupBy
     *
     * @param string $groupBy
     * @return Api
     */
    public function setGroupBy($groupBy)
    {
        $this->groupBy = $groupBy;

        return $this;
    }

    /**
     * Get groupBy
     *
     * @return string 
     */
    public function getGroupBy()
    {
        return $this->groupBy;
    }

    /**
     * Set orderBy
     *
     * @param string $orderBy
     * @return Api
     */
    public function setOrderBy($orderBy)
    {
        $this->orderBy = $orderBy;

        return $this;
    }

    /**
     * Get orderBy
     *
     * @return string 
     */
    public function getOrderBy()
    {
        return $this->orderBy;
    }

    /**
     * Set limit
     *
     * @param string $limit
     * @return Api
     */
    public function setLimit($limit)
    {
        $this->limit = $limit;

        return $this;
    }

    /**
     * Get limit
     *
     * @return string 
     */
    public function getLimit()
    {
        return $this->limit;
    }
}
