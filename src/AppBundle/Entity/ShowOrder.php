<?php

namespace AppBundle\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * ShowOrder
 *
 * @ORM\Table(name="show_order")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ShowOrderRepository")
 */
class ShowOrder {

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="customerNumber", type="string", length=255)
     */
    private $customerNumber;

    /**
     * @var DateTime
     *
     * @ORM\Column(name="createdOn", type="datetime")
     */
    private $createdOn;

    /**
     * @var DateTime
     *
     * @ORM\Column(name="updatedOn", type="datetime")
     */
    private $updatedOn;

    /**
     *
     * @var ShowOrder
     * 
     * @ORM\ManyToOne(targetEntity="Show", inversedBy="orders")
     */
    private $show;

    /**
     *
     * @var ArrayCollection
     * 
     * @ORM\OneToMany(targetEntity="ShowOrderItem", mappedBy="showOrder", cascade={"remove"})
     */
    private $items;

    public function __construct() {
        $this->items = new ArrayCollection();
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set customerNumber
     *
     * @param string $customerNumber
     * @return ShowOrder
     */
    public function setCustomerNumber($customerNumber) {
        $this->customerNumber = $customerNumber;

        return $this;
    }

    /**
     * Get customerNumber
     *
     * @return string 
     */
    public function getCustomerNumber() {
        return $this->customerNumber;
    }

    /**
     * Set createdOn
     *
     * @param DateTime $createdOn
     * @return ShowOrder
     */
    public function setCreatedOn($createdOn) {
        $this->createdOn = $createdOn;

        return $this;
    }

    /**
     * Get createdOn
     *
     * @return DateTime 
     */
    public function getCreatedOn() {
        return $this->createdOn;
    }

    /**
     * Set updatedOn
     *
     * @param DateTime $updatedOn
     * @return ShowOrder
     */
    public function setUpdatedOn($updatedOn) {
        $this->updatedOn = $updatedOn;

        return $this;
    }

    /**
     * Get updatedOn
     *
     * @return DateTime 
     */
    public function getUpdatedOn() {
        return $this->updatedOn;
    }

    public function getItems() {
        return $this->items;
    }

    public function setItems(ArrayCollection $items) {
        $this->items = $items;
        return $this;
    }
    
    public function getShow() {
        return $this->show;
    }

    public function setShow(Show $show) {
        $this->show = $show;
        return $this;
    }



}
