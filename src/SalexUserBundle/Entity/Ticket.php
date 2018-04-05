<?php

namespace SalexUserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use SalexUserBundle\Utility\Services;

/**
 * Ticket
 *
 * @ORM\Table(name="ticket")
 * @ORM\Entity(repositoryClass="SalexUserBundle\Repository\TicketRepository")
 */
class Ticket
{

    private $service;

    public function setService(Services $service) {
        $this->service = $service;
    }
    public function getService() {
        return $this->service;
    }

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
     * @ORM\Column(name="cena", type="integer")
     */
    private $cena;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\ManyToOne(targetEntity="SalexUserBundle\Entity\Performance", inversedBy="tickets")
     * @ORM\JoinColumn(name="performance_id", referencedColumnName="id", nullable=false)
     */
    private $performance;

    /**
     * @ORM\OneToOne(targetEntity="SalexUserBundle\Entity\Seat", cascade={"remove"})
     * @ORM\JoinColumn(name="seat_id", referencedColumnName="id")
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
     * Set cena
     *
     * @param integer $cena
     *
     * @return Ticket
     */
    public function setCena($cena)
    {
        $this->cena = $cena;

        return $this;
    }

    /**
     * Get cena
     *
     * @return int
     */
    public function getCena()
    {
        return $this->cena;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return Ticket
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
     * Set seat
     *
     * @param \SalexUserBundle\Entity\Seat $seat
     *
     * @return Ticket
     */
    public function setSeat(\SalexUserBundle\Entity\Seat $seat = null)
    {
        $this->seat = $seat;

        return $this;
    }

    /**
     * Get seat
     *
     * @return \SalexUserBundle\Entity\Seat
     */
    public function getSeat()
    {
        return $this->seat;
    }



    /**
     * Set performance
     *
     * @param \SalexUserBundle\Entity\Performance $performance
     *
     * @return Ticket
     */
    public function setPerformance(\SalexUserBundle\Entity\Performance $performance)
    {
        $this->performance = $performance;

        return $this;
    }

    /**
     * Get performance
     *
     * @return \SalexUserBundle\Entity\Performance
     */
    public function getPerformance()
    {
        return $this->performance;
    }
}
