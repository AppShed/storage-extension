<?php

namespace AppShed\Extensions\StorageBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Field
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Field
{
    const AGGREGATE_COUNT = 'count';
    const AGGREGATE_SUM = 'sum';
    const AGGREGATE_AVG = 'avg';
    const AGGREGATE_MIN = 'min';
    const AGGREGATE_MAX = 'max';

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
     * @ORM\Column(name="field", type="string", length=50)
     */
    private $field;

    /**
     * @var string
     *
     * @ORM\Column(name="aggregate", type="string", length=50, nullable=true)
     */
    private $aggregate;

    /**
     * @var string
     *
     * @ORM\Column(name="arg", type="string", length=50, nullable=true)
     */
    private $arg;


    /**
     * @var Api
     *
     * @ORM\ManyToOne(targetEntity="AppShed\Extensions\StorageBundle\Entity\Api", inversedBy="fields", cascade={"persist"})
     */
    private $api;

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
     * Set field
     *
     * @param string $field
     * @return Field
     */
    public function setField($field)
    {
        $this->field = $field;
        if ($this->aggregate) {
            $this->arg = $this->field;
            $this->field = $this->aggregate . '(' . $this->arg . ')';
        }
        return $this;
    }

    /**
     * Get field
     *
     * @return string 
     */
    public function getField()
    {
        return $this->field;
    }

    /**
     * Set aggregate
     *
     * @param string $aggregate
     * @return Field
     */
    public function setAggregate($aggregate)
    {
        $this->aggregate = $aggregate;
        $this->arg = $this->field;
        $this->field = $this->aggregate . '(' . $this->arg . ')';
        return $this;
    }

    /**
     * Get aggregate
     *
     * @return string 
     */
    public function getAggregate()
    {
        return $this->aggregate;
    }

    /**
     * Set api
     *
     * @param \AppShed\Extensions\StorageBundle\Entity\Api $api
     * @return Field
     */
    public function setApi(\AppShed\Extensions\StorageBundle\Entity\Api $api = null)
    {
        $this->api = $api;

        return $this;
    }

    /**
     * Get api
     *
     * @return \AppShed\Extensions\StorageBundle\Entity\Api 
     */
    public function getApi()
    {
        return $this->api;
    }

    /**
     * Set arg
     *
     * @param string $arg
     * @return Field
     */
    public function setArg($arg)
    {
        $this->arg = $arg;

        return $this;
    }

    /**
     * Get arg
     *
     * @return string 
     */
    public function getArg()
    {
        return $this->arg;
    }
}
