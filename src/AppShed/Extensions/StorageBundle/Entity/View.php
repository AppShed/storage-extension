<?php

namespace AppShed\Extensions\StorageBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * View
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class View
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
     *
     * @ORM\Column(name="itemId", type="string", length=255)
     */
    private $itemId;

    /**
     * @var string
     *
     * @ORM\Column(name="identifier", type="string", length=255)
     */
    private $identifier;

    /**
     * @var Store
     *
     * @ORM\ManyToOne(targetEntity="Store", inversedBy="views", cascade={"persist"})
     */
    private $store;

    /**
     * @var Filter[]
     *
     * @ORM\OneToMany(targetEntity="Filter", mappedBy="view", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private $filters;


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
     * Constructor
     */
    public function __construct()
    {
        $this->filters = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Set store
     *
     * @param \AppShed\Extensions\StorageBundle\Entity\Store $store
     * @return View
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
     * Add filters
     *
     * @param \AppShed\Extensions\StorageBundle\Entity\Filter $filters
     * @return View
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
     * Set itemId
     *
     * @param string $itemId
     * @return View
     */
    public function setItemId($itemId)
    {
        $this->itemId = $itemId;

        return $this;
    }

    /**
     * Get itemId
     *
     * @return string 
     */
    public function getItemId()
    {
        return $this->itemId;
    }

    /**
     * Set identifier
     *
     * @param string $identifier
     * @return View
     */
    public function setIdentifier($identifier)
    {
        $this->identifier = $identifier;

        return $this;
    }

    /**
     * Get identifier
     *
     * @return string 
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }
}
