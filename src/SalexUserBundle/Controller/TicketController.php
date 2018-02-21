<?php

namespace SalexUserBundle\Controller;

use SalexUserBundle\Entity\Reservation;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use SalexUserBundle\Entity\Seat;

class TicketController extends Controller
{

    /**
     * @Route("/upcoming-performances", name="list_upcoming_performances")
     * @return RedirectResponse
     */
    public function listAction()
    {
        $items = $this->get('salex_user.uvszinhaz_listener')->getPerformances();
        return $this->render("SalexUserBundle:Ticket:upcoming-performances.html.twig", array(
            'items' => $items,
        ));
    }

    /**
     * @Route("/show/ticket/{id}", name="show_ticket", options={"expose"=true}, requirements={"id": "\d+"})
     * @return RedirectResponse
     */
    public function showAction($id)
    {
        $performance = $this->get('salex_user.uvszinhaz_listener')->getPerformance($id);
        $types = $this->getTicketTypes($id);

        // get reservation
        $em = $this->getDoctrine()->getManager();
//        $reservations = $em->getRepository(Reservation::class)->findBy(array('performanceId' => $id, 'statusId' => 2));
        $reservations = $em->getRepository(Seat::class)->findAll();

        return $this->render('SalexUserBundle:Ticket:show-ticket.html.twig', array(
            'reservations' => $reservations,
            'performance' => $performance[0],
            'types' => $types,
        ));
    }

    public function getTicketTypes($id)
    {
        $a_types = array();
        $a_cena = $this->get('salex_user.uvszinhaz_listener')->getPerformance($id)[0]['cena'];
        foreach ($a_cena as $key=>$value) {
            if ($key == 5) {
                $a_types[5] = 'Besplatne';
            }
            if ($key == 1) {
                $a_types[1] = 'Pojedinačne';
            }
            if ($key == 2) {
                $a_types[2] = 'Grupne';
            }
            if ($key == 3) {
                $a_types[3] = 'Studentske';
            }
            if ($key == 4) {
                $a_types[4] = 'Penzionerske';
            }
        }
        return $a_types;
    }

}
