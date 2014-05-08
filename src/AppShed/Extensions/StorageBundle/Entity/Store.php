<?php

namespace AppShed\Extensions\StorageBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Store
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Store
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
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var array
     *
     * @ORM\Column(name="columns", type="json_array", nullable=true)
     */
    private $columns;

    /**
     * @var App
     *
     * @ORM\ManyToOne(targetEntity="App", inversedBy="stores", cascade={"persist"})
     */
    private $app;

    /**
     * @var View[]
     *
     * @ORM\OneToMany(targetEntity="View", mappedBy="store", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private $views;

    /**
     * @var Data[]
     *
     * @ORM\OneToMany(targetEntity="Data", mappedBy="store", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private $data;

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
     * @return Store
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
     * Set columns
     *
     * @param array $columns
     * @return Store
     */
    public function setColumns($columns)
    {
        $this->columns = $columns;

        return $this;
    }

    /**
     * Get columns
     *
     * @return array 
     */
    public function getColumns()
    {
        return $this->columns;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->views = new \Doctrine\Common\Collections\ArrayCollection();
        $this->data = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Set app
     *
     * @param \AppShed\Extensions\StorageBundle\Entity\App $app
     * @return Store
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
     * Add views
     *
     * @param \AppShed\Extensions\StorageBundle\Entity\View $views
     * @return Store
     */
    public function addView(\AppShed\Extensions\StorageBundle\Entity\View $views)
    {
        $this->views[] = $views;

        return $this;
    }

    /**
     * Remove views
     *
     * @param \AppShed\Extensions\StorageBundle\Entity\View $views
     */
    public function removeView(\AppShed\Extensions\StorageBundle\Entity\View $views)
    {
        $this->views->removeElement($views);
    }

    /**
     * Get views
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getViews()
    {
        return $this->views;
    }

    /**
     * Add data
     *
     * @param \AppShed\Extensions\StorageBundle\Entity\Data $data
     * @return Store
     */
    public function addDatum(\AppShed\Extensions\StorageBundle\Entity\Data $data)
    {
        $this->data[] = $data;

        return $this;
    }

    /**
     * Remove data
     *
     * @param \AppShed\Extensions\StorageBundle\Entity\Data $data
     */
    public function removeDatum(\AppShed\Extensions\StorageBundle\Entity\Data $data)
    {
        $this->data->removeElement($data);
    }

    /**
     * Get data
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getData()
    {
        return $this->data;
    }
}
