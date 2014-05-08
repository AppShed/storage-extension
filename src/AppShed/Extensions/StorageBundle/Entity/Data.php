<?php

namespace AppShed\Extensions\StorageBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Data
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Data
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
     * @var array
     *
     * @ORM\Column(name="data", type="json_array")
     */
    private $data;

    /**
     * @var Store
     *
     * @ORM\ManyToOne(targetEntity="Store", inversedBy="data", cascade={"persist"})
     */
    private $store;


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
     * Set data
     *
     * @param array $data
     * @return Data
     */
    public function setData($data)
    {
        $this->data = $data;

        return $this;
    }

    /**
     * Get data
     *
     * @return array 
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Set store
     *
     * @param \AppShed\Extensions\StorageBundle\Entity\Store $store
     * @return Data
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
}
