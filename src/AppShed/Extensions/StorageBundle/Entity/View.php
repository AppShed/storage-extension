<?php

namespace AppShed\Extensions\StorageBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints\NotBlank;

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
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Filter", mappedBy="view", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private $filters;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255, nullable=true)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="message", type="text", nullable=true)
     */
    private $message;

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
        $this->filters = new ArrayCollection();
    }

    /**
     * Set store
     *
     * @param Store $store
     * @return View
     */
    public function setStore(Store $store = null)
    {
        $this->store = $store;

        return $this;
    }

    /**
     * Get store
     *
     * @return Store
     */
    public function getStore()
    {
        return $this->store;
    }

    /**
     * Add filters
     *
     * @param Filter $filters
     * @return View
     */
    public function addFilter(Filter $filters)
    {
        $this->filters[] = $filters;

        $filters->setView($this);

        return $this;
    }

    /**
     * Remove filters
     *
     * @param Filter $filters
     */
    public function removeFilter(Filter $filters)
    {
        $this->filters->removeElement($filters);

        $filters->setView(null);
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

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     * @return View
     */
    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param string $message
     * @return View
     */
    public function setMessage($message)
    {
        $this->message = $message;
        return $this;
    }
}
