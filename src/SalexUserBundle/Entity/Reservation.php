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
     * @ORM\OneToMany(targetEntity="Seat", mappedBy="reservation", orphanRemoval=true)
     */
    private $seats;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="reservations")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="SalexUserBundle\Entity\Performance", inversedBy="reservations")
     * @ORM\JoinColumn(name="performance_id", referencedColumnName="id", nullable=false)
     */
    private $performance;

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
     * @ORM\Column(name="seats_number", type="integer", nullable=true)
     */
    private $seats_number;

    /**
     * @var int
     *
     * @ORM\Column(name="type", type="integer", nullable=true)
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
     * @var string
     *
     * @ORM\Column(name="first_name", type="string", length=255, nullable=true)
     */
    protected $firstName;

    /**
     * @var string
     *
     * @ORM\Column(name="last_name", type="string", length=255, nullable=true)
     */
    protected $lastName;

    /**
     * @var string
     *
     * @ORM\Column(name="phone_number", type="string", length=255, nullable=true)
     */
    protected $phoneNumber;

    /**
     * @var int
     *
     * @ORM\Column(name="by_phone", type="integer", options={"default": 0}, nullable=true)
     */
    protected $byPhone;

    /**
     * @var int
     *
     * @ORM\Column(name="broj_pojedinacne", type="integer", options={"default": 0}, nullable=true)
     */
    protected $brojPojedinacne;

    /**
     * @var int
     *
     * @ORM\Column(name="broj_grupne", type="integer", options={"default": 0}, nullable=true)
     */
    protected $brojGrupne;

    /**
     * @var int
     *
     * @ORM\Column(name="broj_studentske", type="integer", options={"default": 0}, nullable=true)
     */
    protected $brojStudentske;

    /**
     * @var int
     *
     * @ORM\Column(name="broj_penzionerske", type="integer", options={"default": 0}, nullable=true)
     */
    protected $brojPenzionerske;

    /**
     * @var int
     *
     * @ORM\Column(name="broj_besplatne", type="integer", options={"default": 0}, nullable=true)
     */
    protected $brojBesplatne;

    /**
     * @var int
     *
     * @ORM\Column(name="scena", type="integer", options={"default": 0}, nullable=true)
     */
    protected $scena;

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

    /**
     * Set firstName
     *
     * @param string $firstName
     *
     * @return Reservation
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * Get firstName
     *
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * Set lastName
     *
     * @param string $lastName
     *
     * @return Reservation
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * Get lastName
     *
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * Set phoneNumber
     *
     * @param string $phoneNumber
     *
     * @return Reservation
     */
    public function setPhoneNumber($phoneNumber)
    {
        $this->phoneNumber = $phoneNumber;

        return $this;
    }

    /**
     * Get phoneNumber
     *
     * @return string
     */
    public function getPhoneNumber()
    {
        return $this->phoneNumber;
    }

    /**
     * Set byPhone
     *
     * @param integer $byPhone
     *
     * @return Reservation
     */
    public function setByPhone($byPhone)
    {
        $this->byPhone = $byPhone;

        return $this;
    }

    /**
     * Get byPhone
     *
     * @return integer
     */
    public function getByPhone()
    {
        return $this->byPhone;
    }

    /**
     * Set brojPojedinacne
     *
     * @param integer $brojPojedinacne
     *
     * @return Reservation
     */
    public function setBrojPojedinacne($brojPojedinacne)
    {
        $this->brojPojedinacne = $brojPojedinacne;

        return $this;
    }

    /**
     * Get brojPojedinacne
     *
     * @return integer
     */
    public function getBrojPojedinacne()
    {
        return $this->brojPojedinacne;
    }

    /**
     * Set brojGrupne
     *
     * @param integer $brojGrupne
     *
     * @return Reservation
     */
    public function setBrojGrupne($brojGrupne)
    {
        $this->brojGrupne = $brojGrupne;

        return $this;
    }

    /**
     * Get brojGrupne
     *
     * @return integer
     */
    public function getBrojGrupne()
    {
        return $this->brojGrupne;
    }

    /**
     * Set brojStudentske
     *
     * @param integer $brojStudentske
     *
     * @return Reservation
     */
    public function setBrojStudentske($brojStudentske)
    {
        $this->brojStudentske = $brojStudentske;

        return $this;
    }

    /**
     * Get brojStudentske
     *
     * @return integer
     */
    public function getBrojStudentske()
    {
        return $this->brojStudentske;
    }

    /**
     * Set brojPenzionerske
     *
     * @param integer $brojPenzionerske
     *
     * @return Reservation
     */
    public function setBrojPenzionerske($brojPenzionerske)
    {
        $this->brojPenzionerske = $brojPenzionerske;

        return $this;
    }

    /**
     * Get brojPenzionerske
     *
     * @return integer
     */
    public function getBrojPenzionerske()
    {
        return $this->brojPenzionerske;
    }

    /**
     * Get types
     *
     * @return array
     */
    public function getReservationTypes()
    {
        $a_types = array();
        if( $this->getBrojBesplatne() ) {
            $a_types[0] = 'Besplatne';
        }
        if( $this->getBrojPojedinacne() ) {
            $a_types[1] = 'Pojedinačne';
        }
        if( $this->getBrojGrupne() ) {
            $a_types[2] = 'Grupne';
        }
        if( $this->getBrojStudentske() ) {
            $a_types[3] = 'Studentske';
        }
        if( $this->getBrojPenzionerske() ) {
            $a_types[4] = 'Penzionerske';
        }
        if( $this->getBrojPenzionerske() ) {
            $a_types[5] = 'Stručne';
        }
        return $a_types;
    }

    /**
     * Set scena
     *
     * @param integer $scena
     *
     * @return Reservation
     */
    public function setScena($scena)
    {
        $this->scena = $scena;

        return $this;
    }

    /**
     * Get scena
     *
     * @return integer
     */
    public function getScena()
    {
        return $this->scena;
    }

    /**
     * Get performance price
     *
     * @return string
     */
    public function getPrice(){
        /*
        $performance_id = $this->getPerformanceId();
        $price = $this->getService()->getPerformance($performance_id);
        return $price[0]['cena'];
        */
        return null;
    }

    /**
     * Get reservation sum
     *
     * @return string
     */
    public function getSum(){
        $sum = 0;
        $prices = $this->getPerformance()->getPrices()->toArray();
        foreach ($prices as $item) {
            if ($this->getBrojPojedinacne())
                $sum = $sum + $item->getPrice() * $this->getBrojPojedinacne();
            if ($this->getBrojGrupne())
                $sum = $sum + $item->getPrice() * $this->getBrojGrupne();
            if ($this->getBrojStudentske())
                $sum = $sum + $item->getPrice() * $this->getBrojStudentske();
            if ($this->getBrojPenzionerske())
                $sum = $sum + $item->getPrice() * $this->getBrojPenzionerske();
        }
        return $sum;
    }


    /**
     * Set brojBesplatne
     *
     * @param integer $brojBesplatne
     *
     * @return Reservation
     */
    public function setBrojBesplatne($brojBesplatne)
    {
        $this->brojBesplatne = $brojBesplatne;

        return $this;
    }

    /**
     * Get brojBesplatne
     *
     * @return integer
     */
    public function getBrojBesplatne()
    {
        return $this->brojBesplatne;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->seats = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Set performance
     *
     * @param \SalexUserBundle\Entity\Performance $performance
     *
     * @return Reservation
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
