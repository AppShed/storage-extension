<?php

namespace AppShed\Extensions\StorageBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Filter
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Filter
{
    const FILTER_EQUALS = '=';
    const FILTER_GREATER_THAN = '>';
    const FILTER_GREATER_THAN_OR_EQUALS = '>=';
    const FILTER_LESS_THAN = '<';
    const FILTER_LESS_THAN_OR_EQUALS = '<=';

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
     * @ORM\Column(name="col", type="string", length=255)
     */
    private $col;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=255)
     */
    private $type;

    /**
     * @var string
     *
     * @ORM\Column(name="value", type="string", length=255)
     */
    private $value;

    /**
     * @var View
     *
     * @ORM\ManyToOne(targetEntity="View", inversedBy="filters", cascade={"persist"})
     */
    private $view;

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
     * Set col
     *
     * @param string $col
     * @return Filter
     */
    public function setCol($col)
    {
        $this->col = $col;

        return $this;
    }

    /**
     * Get col
     *
     * @return string 
     */
    public function getCol()
    {
        return $this->col;
    }

    /**
     * Set type
     *
     * @param string $type
     * @return Filter
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string 
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set value
     *
     * @param string $value
     * @return Filter
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Get value
     *
     * @return string 
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set view
     *
     * @param View $view
     * @return Filter
     */
    public function setView(View $view = null)
    {
        $this->view = $view;

        return $this;
    }

    /**
     * Get view
     *
     * @return View
     */
    public function getView()
    {
        return $this->view;
    }
}
