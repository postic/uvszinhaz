<?php

namespace SalexUserBundle\Controller;

use SalexUserBundle\Entity\Performance;
use SalexUserBundle\Entity\Reservation;
use SalexUserBundle\Entity\Ticket;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use SalexUserBundle\Entity\Seat;

class TicketController extends Controller
{


    /**
     * @Route("/list/tickets", name="list_tickets")
     * @Security("has_role('ROLE_SALE')")
     * @return RedirectResponse
     */
    public function listTicketsAction(Request $request)
    {
        $user = $this->getUser();
        $user_id = $user->getId();
        $filterBuilder = $this->get('doctrine.orm.entity_manager')
            ->getRepository(Ticket::class)
            ->createQueryBuilder('r');
        $filterBuilder->orderBy('r.createdAt', 'desc');

        $form = $this->get('form.factory')->create('SalexUserBundle\Filter\TicketFilterType');
        if ($request->query->has($form->getName())) {
            $form->submit($request->query->get($form->getName()));
            $this->get('lexik_form_filter.query_builder_updater')->addFilterConditions($form, $filterBuilder);
        }
        $query = $filterBuilder->getQuery();
        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            10
        );
        $pagination->setTemplate('KnpPaginatorBundle:Pagination:foundation_v5_pagination.html.twig');
        return $this->render("SalexUserBundle:Ticket:list-tickets.html.twig", array(
            'pagination' => $pagination,
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/upcoming-performances", name="list_upcoming_performances")
     * @Security("has_role('ROLE_SALE')")
     * @return RedirectResponse
     */
    public function listAction()
    {
        $now = new \DateTime();
        $current_date = $now->format('Y-m-d');

        $qb = $this->get('doctrine.orm.entity_manager')->createQueryBuilder();
        $qb->select(array('p'))
            ->from('SalexUserBundle:Performance', 'p')
            ->where($qb->expr()->gte('p.date', ':current_date'))
            ->orderBy('p.date', 'asc')
            ->setParameter('current_date', $current_date);

        $items = $qb->getQuery()->getResult();
        return $this->render("SalexUserBundle:Ticket:upcoming-performances.html.twig", array(
            'items' => $items,
        ));
    }

    /**
     * @Route("/show/ticket/{id}", name="show_ticket", options={"expose"=true}, requirements={"id": "\d+"})
     * @Security("has_role('ROLE_SALE')")
     * @return RedirectResponse
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $performance = $em->getRepository(Performance::class)->find($id);

        $prices = array();
        $a_prices = $performance->getPrices()->toArray();
        foreach ($a_prices as $price) {
            $prices[$price->getType()] = $price->getPrice();
        }

        $types = $this->getTicketTypes($id);

        // get reservation
        $em = $this->getDoctrine()->getManager();
        $seats = $em->getRepository(Seat::class)->findBy(array('performanceId' => $id, 'statusId' => 0));

        return $this->render('SalexUserBundle:Ticket:show-ticket.html.twig', array(
            'reservations' => $seats,
            'performance' => $performance,
            'types' => $types,
            'prices' => $prices,
        ));
    }

    public function getTicketTypes($id)
    {
        $a_types = array();

        // get performance
        $em = $this->getDoctrine()->getManager();
        $performance = $em->getRepository(Performance::class)->find($id);

        $a_cena = $performance->getPrices();
        foreach ($a_cena as $key=>$value) {
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
            if ($key == 5) {
                $a_types[5] = 'Besplatne';
            }
            if ($key == 6) {
                $a_types[6] = 'Stručne';
            }
        }
        return $a_types;
    }

    /**
     * @Route("/remove/seat/{id}", name="remove_seat", options={"expose"=true}, requirements={"id": "\d+"})
     * @Security("has_role('ROLE_SALE')")
     * @return RedirectResponse
     */
    public function deleteAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $item = $em->getRepository(Seat::class)->findOneBy(array('id' => $id));
        $reservation = $item->getReservation();
        $performance_id = $reservation->getPerformanceId();
        $em->remove($item);
        $em->flush();
        return new RedirectResponse($this->generateUrl('show_ticket', array('id' => $performance_id)));
    }

    /**
     * @Route("/add/ticket", name="add_ticket", options={"expose"=true})
     * @Security("has_role('ROLE_SALE')")
     * @return RedirectResponse
     */
    public function addAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $resp = $request->getContent();
        $a_resp = json_decode($resp);
        $performance_id = $a_resp->performance_id;
        $seats = $a_resp->seats;

        foreach ($seats as $item) {

            if ($item->seat_id == null) { // Kreiranje sedista u tabeli Seat ukoliko ne postoji

                $new_seat = new Seat();
                $new_seat->setPerformanceId(intval($item->performance_id));
                $new_seat->setType(intval($item->type));
                $new_seat->setSeat($item->seat_number); // TODO !!!!!
                $new_seat->setStatusId(1);
                $new_seat->setReservation(null);
                $em->persist($new_seat);
                $em->flush();

                $ticket = new Ticket();
                $ticket->setCena(intval($item->price));
                $ticket->setCreatedAt(new \DateTime());
                $ticket->setPerformanceId(intval($item->performance_id));
                $ticket->setSeat($new_seat);
                $em->persist($ticket);
                $em->flush();

            } else { // Update record

                $seat = $em->getRepository(Seat::class)->findOneBy(array('id' => $item->seat_id));
                $seat->setStatusId(1);
                $em->flush();

                $ticket = new Ticket();
                $ticket->setCena(intval($item->price));
                $ticket->setCreatedAt(new \DateTime());
                $ticket->setPerformanceId(intval($item->performance_id));
                $ticket->setSeat($seat);
                $em->persist($ticket);
                $em->flush();
            }

        }

        return new Response($performance_id);
    }

    /**
     * @Route("/delete/ticket/{id}", name="delete_ticket", options={"expose"=true}, requirements={"id": "\d+"})
     * @Security("has_role('ROLE_SALE')")
     * @return RedirectResponse
     */
    public function deleteTicketAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $item = $em->getRepository(Ticket::class)->findOneBy(array('id' => $id));
        $em->remove($item);
        $em->flush();
        return new RedirectResponse($this->generateUrl('list_tickets' ));
    }

}
