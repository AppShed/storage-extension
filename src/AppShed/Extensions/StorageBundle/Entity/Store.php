<?php

namespace AppShed\Extensions\StorageBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

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
     * @Assert\NotBlank()
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
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="View", mappedBy="store", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private $views;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Data", mappedBy="store", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private $data;


    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="AppShed\Extensions\StorageBundle\Entity\Api", mappedBy="store", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private $apis;


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
        $this->views = new ArrayCollection();
        $this->data = new ArrayCollection();
        $this->apis = new ArrayCollection();
    }

    /**
     * Set app
     *
     * @param App $app
     * @return Store
     */
    public function setApp(App $app = null)
    {
        $this->app = $app;

        return $this;
    }

    /**
     * Get app
     *
     * @return App
     */
    public function getApp()
    {
        return $this->app;
    }

    /**
     * Add views
     *
     * @param View $views
     * @return Store
     */
    public function addView(View $views)
    {
        $this->views[] = $views;

        return $this;
    }

    /**
     * Remove views
     *
     * @param View $views
     */
    public function removeView(View $views)
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
     * @param Data $data
     * @return Store
     */
    public function addDatum(Data $data)
    {
        $this->data[] = $data;

        return $this;
    }

    /**
     * Remove data
     *
     * @param Data $data
     */
    public function removeDatum(Data $data)
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

    /**
     * Add apis
     *
     * @param \AppShed\Extensions\StorageBundle\Entity\Api $apis
     * @return Store
     */
    public function addApi(\AppShed\Extensions\StorageBundle\Entity\Api $apis)
    {
        $this->apis[] = $apis;

        return $this;
    }

    /**
     * Remove apis
     *
     * @param \AppShed\Extensions\StorageBundle\Entity\Api $apis
     */
    public function removeApi(\AppShed\Extensions\StorageBundle\Entity\Api $apis)
    {
        $this->apis->removeElement($apis);
    }

    /**
     * Get apis
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getApis()
    {
        return $this->apis;
    }

    public function __toString() {
        return $this->getName();
    }
}
