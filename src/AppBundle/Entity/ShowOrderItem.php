<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ShowOrderItem
 *
 * @ORM\Table(name="show_order_item")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ShowOrderItemRepository")
 */
class ShowOrderItem {

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
     * @ORM\Column(name="sku", type="string", length=255)
     */
    private $sku;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var int
     *
     * @ORM\Column(name="quantity", type="integer")
     */
    private $quantity;

    /**
     * @var string
     *
     * @ORM\Column(name="vendor", type="string", length=255)
     */
    private $vendor;

    /**
     *
     * @var ShowOrder
     * 
     * @ORM\ManyToOne(targetEntity="ShowOrder", inversedBy="items")
     */
    private $showOrder;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set sku
     *
     * @param string $sku
     * @return ShowOrderItem
     */
    public function setSku($sku) {
        $this->sku = $sku;

        return $this;
    }

    /**
     * Get sku
     *
     * @return string 
     */
    public function getSku() {
        return $this->sku;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return ShowOrderItem
     */
    public function setName($name) {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName() {
        return $this->name;
    }

    /**
     * Set quantity
     *
     * @param integer $quantity
     * @return ShowOrderItem
     */
    public function setQuantity($quantity) {
        $this->quantity = $quantity;

        return $this;
    }

    /**
     * Get quantity
     *
     * @return integer 
     */
    public function getQuantity() {
        return $this->quantity;
    }

    /**
     * Set vendor
     *
     * @param string $vendor
     * @return ShowOrderItem
     */
    public function setVendor($vendor) {
        $this->vendor = $vendor;

        return $this;
    }

    /**
     * Get vendor
     *
     * @return string 
     */
    public function getVendor() {
        return $this->vendor;
    }

    public function getShowOrder() {
        return $this->showOrder;
    }

    public function setShowOrder(ShowOrder $showOrder) {
        $this->showOrder = $showOrder;
        return $this;
    }

}
