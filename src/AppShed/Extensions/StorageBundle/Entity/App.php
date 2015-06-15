<?php

namespace AppShed\Extensions\StorageBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * App
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class App
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
     * @ORM\Column(name="appId", type="string", length=255)
     */
    private $appId;

    /**
     * @var string
     *
     * @ORM\Column(name="appSecret", type="string", length=255)
     */
    private $appSecret;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="Store", mappedBy="app", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private $stores;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="AppShed\Extensions\StorageBundle\Entity\Api", mappedBy="app", cascade={"persist", "remove"}, orphanRemoval=true)
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
     * Set appId
     *
     * @param string $appId
     * @return App
     */
    public function setAppId($appId)
    {
        $this->appId = $appId;

        return $this;
    }

    /**
     * Get appId
     *
     * @return string 
     */
    public function getAppId()
    {
        return $this->appId;
    }

    /**
     * Set appSecret
     *
     * @param string $appSecret
     * @return App
     */
    public function setAppSecret($appSecret)
    {
        $this->appSecret = $appSecret;

        return $this;
    }

    /**
     * Get appSecret
     *
     * @return string 
     */
    public function getAppSecret()
    {
        return $this->appSecret;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->stores = new ArrayCollection();
        $this->apis = new ArrayCollection();
    }

    /**
     * Add stores
     *
     * @param Store $stores
     * @return App
     */
    public function addStore(Store $stores)
    {
        $this->stores[] = $stores;

        return $this;
    }

    /**
     * Remove stores
     *
     * @param Store $stores
     */
    public function removeStore(Store $stores)
    {
        $this->stores->removeElement($stores);
    }

    /**
     * Get stores
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getStores()
    {
        return $this->stores;
    }

    /**
     * Add apis
     *
     * @param \AppShed\Extensions\StorageBundle\Entity\Api $apis
     * @return App
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
}
