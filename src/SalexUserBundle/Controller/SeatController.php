<?php

namespace SalexUserBundle\Controller;

use SalexUserBundle\Entity\Reservation;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use SalexUserBundle\Entity\Seat;

class SeatController extends Controller
{
    /**
     * @Route("/add/seat", name="add_seat", options={"expose"=true})
     * @return RedirectResponse
     */
    public function addAction(Request $request)
    {
        $resp = $request->getContent();
        $a_resp = json_decode($resp);
        foreach($a_resp->seats as $item){

            // get reservation
            $em = $this->getDoctrine()->getManager();
            $reservation = $em->getRepository(Reservation::class)->findOneBy(array('id' => $a_resp->id));
            $reservation->setStatusId(2);

            $seat = new Seat();
            $seat->setReservation($reservation);
            $seat->setSeat($item);
            $em = $this->getDoctrine()->getManager();
            $em->persist($seat);
            $em->flush();

        }

        return new Response($a_resp->id);
    }

    /**
     * @Route("/list/seats/{id}", name="list_seats", options={"expose"=true}, requirements={"id": "\d+"})
     * @return RedirectResponse
     */
    public function listAction(Request $request, $id = 0)
    {
       $seats = $this->getDoctrine()
            ->getEntityManager()
            ->createQueryBuilder()
            ->select('s')
            ->from('SalexUserBundle:Seat', 's')
            ->innerJoin('s.reservation', 'r')
            ->where('r.performanceId = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getResult();

        $a_resp = array();
        foreach($seats as $seat){
            $a_resp[] = $seat->getSeat();
        }
        $resp = json_encode($a_resp);

        return new Response($resp);
    }

    /**
     * @Route("/delete/seat/{id}", name="delete_seat", options={"expose"=true}, requirements={"id": "\d+"})
     * @return RedirectResponse
     */
    public function deleteAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $item = $em->getRepository(Seat::class)->findOneBy(array('id' => $id));
        $reservation_id = $item->getReservation()->getId();
        $em->remove($item);
        $em->flush();
        return new RedirectResponse($this->generateUrl('show_reservation', array('id' => $reservation_id)));
    }

}
