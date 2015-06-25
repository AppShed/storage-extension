<?php

namespace AppShed\Extensions\StorageBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Rhumsaa\Uuid\Uuid;

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

    const ODRER_DIRECTION_ASC = 'asc';
    const ODRER_DIRECTION_DESC = 'desc';
    const ORDER_AGGREGATE_FUNCTION = 'aggregateFunction';
    const ORDER_AGGREGATE_FUNCTION_TEXT = '[SELECT AGGREGATE FUNCTION]';
    const ACTION_SELECT = 'Select';
    const ACTION_INSERT = 'Insert';
    const ACTION_UPDATE = 'Update';
    const ACTION_DELETE = 'Delete';

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
     * @ORM\Column(name="uuid", type="string", length=255)
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
     * @ORM\Column(name="selectPhrase", type="string", length=255, nullable=true)
     */
    private $select;

    /**
     * @var string
     *
     * @ORM\Column(name="group_field", type="string", length=255, nullable=true)
     */
    private $groupField;

    /**
     * @var string
     *
     * @ORM\Column(name="order_field", type="string", length=255, nullable=true)
     */
    private $orderField;

    /**
     * @var string
     *
     * @ORM\Column(name="order_direction", type="string", length=255, nullable=true)
     */
    private $orderDirection;

    /**
     * @var string
     *
     * @ORM\Column(name="limitPhrase", type="string", length=255, nullable=true)
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
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="AppShed\Extensions\StorageBundle\Entity\Field", mappedBy="api", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private $fields;

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
        $this->uuid = Uuid::uuid1()->toString();
    }

    /**
     * Add filters
     *
     * @param \AppShed\Extensions\StorageBundle\Entity\Filter $filter
     * @return Api
     */
    public function addFilter(\AppShed\Extensions\StorageBundle\Entity\Filter $filter)
    {
        $filter->setApi($this);
        $this->filters[] = $filter;

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
        return json_decode($this->select);
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

    /**
     * Set orderField
     *
     * @param string $orderField
     * @return Api
     */
    public function setOrderField($orderField)
    {
        $this->orderField = $orderField;

        return $this;
    }

    /**
     * Get orderField
     *
     * @return string 
     */
    public function getOrderField()
    {
        return $this->orderField;
    }

    /**
     * Set orderDirection
     *
     * @param string $orderDirection
     * @return Api
     */
    public function setOrderDirection($orderDirection)
    {
        $this->orderDirection = $orderDirection;

        return $this;
    }

    /**
     * Get orderDirection
     *
     * @return string 
     */
    public function getOrderDirection()
    {
        return $this->orderDirection;
    }

    /**
     * Set groupField
     *
     * @param string $groupField
     * @return Api
     */
    public function setGroupField($groupField)
    {
        $this->groupField = $groupField;

        return $this;
    }

    /**
     * Get groupField
     *
     * @return string 
     */
    public function getGroupField()
    {
        return $this->groupField;
    }

    /**
     * Add fields
     *
     * @param \AppShed\Extensions\StorageBundle\Entity\Field $fields
     * @return Api
     */
    public function addField(\AppShed\Extensions\StorageBundle\Entity\Field $fields)
    {
        $fields->setApi($this);
        $this->fields[] = $fields;

        return $this;
    }

    /**
     * Remove fields
     *
     * @param \AppShed\Extensions\StorageBundle\Entity\Field $fields
     */
    public function removeField(\AppShed\Extensions\StorageBundle\Entity\Field $fields)
    {
        $this->fields->removeElement($fields);
    }

    /**
     * Get fields
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getFields()
    {
        return $this->fields;
    }
}
