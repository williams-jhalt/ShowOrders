<?php

namespace AppBundle\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Show
 *
 * @ORM\Table(name="`show`")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ShowRepository")
 */
class Show {

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
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

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
     * @var ArrayCollection
     * 
     * @ORM\OneToMany(targetEntity="ShowOrder", mappedBy="show", cascade={"remove"})
     */
    private $orders;

    public function __construct() {
        $this->orders = new ArrayCollection();
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId() {
        return $this->id;
    }

    public function getName() {
        return $this->name;
    }

    public function getCreatedOn() {
        return $this->createdOn;
    }

    public function getUpdatedOn() {
        return $this->updatedOn;
    }

    public function getOrders() {
        return $this->orders;
    }

    public function setName($name) {
        $this->name = $name;
        return $this;
    }

    public function setCreatedOn(DateTime $createdOn) {
        $this->createdOn = $createdOn;
        return $this;
    }

    public function setUpdatedOn(DateTime $updatedOn) {
        $this->updatedOn = $updatedOn;
        return $this;
    }

    public function setOrders(ArrayCollection $orders) {
        $this->orders = $orders;
        return $this;
    }

}
