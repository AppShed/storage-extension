<?php

namespace AppShed\Extensions\StorageBundle\Entity;

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
     * @var  Store[]
     *
     * @ORM\OneToMany(targetEntity="Store", mappedBy="app", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private $stores;

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
        $this->stores = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add stores
     *
     * @param \AppShed\Extensions\StorageBundle\Entity\Store $stores
     * @return App
     */
    public function addStore(\AppShed\Extensions\StorageBundle\Entity\Store $stores)
    {
        $this->stores[] = $stores;

        return $this;
    }

    /**
     * Remove stores
     *
     * @param \AppShed\Extensions\StorageBundle\Entity\Store $stores
     */
    public function removeStore(\AppShed\Extensions\StorageBundle\Entity\Store $stores)
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
}
