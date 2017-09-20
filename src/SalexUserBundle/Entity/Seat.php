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
     * @var int
     *
     * @ORM\Column(name="reservation_id", type="integer")
     */
    private $reservationId;

    /**
     * @var string
     *
     * @ORM\Column(name="seat", type="string", length=255)
     */
    private $seat;


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
     * Set reservationId
     *
     * @param integer $reservationId
     *
     * @return Seat
     */
    public function setReservationId($reservationId)
    {
        $this->reservationId = $reservationId;

        return $this;
    }

    /**
     * Get reservationId
     *
     * @return int
     */
    public function getReservationId()
    {
        return $this->reservationId;
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
}

