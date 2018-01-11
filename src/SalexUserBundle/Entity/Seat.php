<?php

namespace SalexUserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Seat
 *
 * @ORM\Table(name="seat")
 * @ORM\Entity(repositoryClass="SalexUserBundle\Repository\SeatRepository")
 */
class Seat
{

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Reservation", inversedBy="seats")
     * @ORM\JoinColumn(name="reservation_id", referencedColumnName="id")
     */
    private $reservation;

    /**
     * @var string
     *
     * @ORM\Column(name="seat", type="string", length=255)
     */
    private $seat;

    /**
     * @var int
     *
     * @ORM\Column(name="type", type="integer", options={"default": 0}, nullable=true)
     */
    private $type;

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
     * Set seat
     *
     * @param string $seat
     *
     * @return Seat
     */
    public function setSeat($seat)
    {
        $this->seat = $seat;

        return $this;
    }

    /**
     * Get seat
     *
     * @return string
     */
    public function getSeat()
    {
        return $this->seat;
    }

    /**
     * Get seatNumber
     *
     * @return string
     */
    public function getSeatNumber()
    {
        $item = explode('_', $this->seat);
        return $item[1];
    }

    /**
     * Get seatRow
     *
     * @return string
     */
    public function getSeatRow()
    {
        $item = explode('_', $this->seat);
        return $item[0];
    }

    /**
     * Set reservation
     *
     * @param \SalexUserBundle\Entity\Reservation $reservation
     *
     * @return Seat
     */
    public function setReservation(\SalexUserBundle\Entity\Reservation $reservation = null)
    {
        $this->reservation = $reservation;

        return $this;
    }

    /**
     * Get reservation
     *
     * @return \SalexUserBundle\Entity\Reservation
     */
    public function getReservation()
    {
        return $this->reservation;
    }

    /**
     * Set type
     *
     * @param integer $type
     *
     * @return Seat
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
}
