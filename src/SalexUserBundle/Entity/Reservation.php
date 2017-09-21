<?php

namespace SalexUserBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use SalexUserBundle\Utility\Services;

/**
 * Reservation
 *
 * @ORM\Table(name="reservation")
 * @ORM\Entity(repositoryClass="SalexUserBundle\Repository\ReservationRepository")
 */
class Reservation
{

    private $service;

    public function setService(Services $service) {
        $this->service = $service;
    }
    public function getService() {
        return $this->service;
    }

    /**
     * @ORM\OneToMany(targetEntity="Seat", mappedBy="reservation")
     */
    private $seats;

    public function __construct()
    {
        $this->seats = new ArrayCollection();
    }

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="reservations")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var int
     *
     * @ORM\Column(name="performance_id", type="integer")
     */
    private $performanceId;

    /**
     * @var int
     *
     * @ORM\Column(name="seats_number", type="integer")
     */
    private $seats_number;

    /**
     * @var int
     *
     * @ORM\Column(name="type", type="integer")
     */
    private $type;

    /**
     * @var DateTime $created
     *
     * @ORM\Column(name="created_at", type="datetime", options={"default": 0}, nullable=false)
     */
    private $createdAt;

    /**
     * @var int
     *
     * @ORM\Column(name="status_id", type="integer")
     */
    private $statusId;

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set performanceId
     *
     * @param integer $performanceId
     *
     * @return Reservation
     */
    public function setPerformanceId($performanceId)
    {
        $this->performanceId = $performanceId;

        return $this;
    }

    /**
     * Get performanceId
     *
     * @return int
     */
    public function getPerformanceId()
    {
        return $this->performanceId;
    }

    /**
     * Set seatsNumber
     *
     * @param integer $seatsNumber
     *
     * @return Reservation
     */
    public function setSeatsNumber($seatsNumber)
    {
        $this->seats_number = $seatsNumber;

        return $this;
    }

    /**
     * Get seatsNumber
     *
     * @return integer
     */
    public function getSeatsNumber()
    {
        return $this->seats_number;
    }

    /**
     * Set type
     *
     * @param integer $type
     *
     * @return Reservation
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return integer
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return Reservation
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set statusId
     *
     * @param integer $statusId
     *
     * @return Reservation
     */
    public function setStatusId($statusId)
    {
        $this->statusId = $statusId;

        return $this;
    }

    /**
     * Get statusId
     *
     * @return integer
     */
    public function getStatusId()
    {
        return $this->statusId;
    }

    /**
     * Get performance title
     *
     * @return string
     */
    public function getTitle(){
        $performance_id = $this->getPerformanceId();
        $title = $this->getService()->getPerformance($performance_id);
        return $title;
    }

    /**
     * Add seat
     *
     * @param \SalexUserBundle\Entity\Seat $seat
     *
     * @return Reservation
     */
    public function addSeat(\SalexUserBundle\Entity\Seat $seat)
    {
        $this->seats[] = $seat;

        return $this;
    }

    /**
     * Remove seat
     *
     * @param \SalexUserBundle\Entity\Seat $seat
     */
    public function removeSeat(\SalexUserBundle\Entity\Seat $seat)
    {
        $this->seats->removeElement($seat);
    }

    /**
     * Get seats
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSeats()
    {
        return $this->seats;
    }

    /**
     * Set user
     *
     * @param \SalexUserBundle\Entity\User $user
     *
     * @return Reservation
     */
    public function setUser(\SalexUserBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \SalexUserBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }
}
