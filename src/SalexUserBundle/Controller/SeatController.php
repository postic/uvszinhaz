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
        $em = $this->getDoctrine()->getManager();
        $response = $request->getContent();
        $resp = json_decode($response);

        $id = $resp->id;
        $seats = $resp->seats;
        $reservation = $em->getRepository(Reservation::class)->findOneBy(array('id' => $id));

        foreach ($seats as $item) {
            $seat = new Seat();
            $seat->setReservation($reservation);
            $seat->setSeat($item->seat_number);
            $seat->setType($item->type);
            $seat->setPerformanceId($item->performance_id);
            $seat->setStatusId(0);
            $em->persist($seat);
            $em->flush();
        }

        return new Response($id);
    }

    /**
     * @Route("/list/reservation/seats/{id}", name="list_reservation_seats", options={"expose"=true}, requirements={"id": "\d+"})
     * @return RedirectResponse
     */
    public function listReservationSeatsAction(Request $request, $id = 0)
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
            $a_resp[] = array('seat'=>$seat->getSeat(), 'type'=>$seat->getType(), 'status'=>$seat->getStatusId());
        }
        $resp = json_encode($a_resp);

        return new Response($resp);
    }

    /**
     * @Route("/list/seats/{id}", name="list_seats", options={"expose"=true}, requirements={"id": "\d+"})
     * @return RedirectResponse
     */
    public function listAction(Request $request, $id = 0)
    {
        // get reservation
        $em = $this->getDoctrine()->getManager();
        $seats = $em->getRepository(Seat::class)->findBy(array('performanceId' => $id));
        $a_resp = array();
        foreach($seats as $seat){
            $a_resp[] = array('seat'=>$seat->getSeat(),'type'=>$seat->getType(),'status'=>$seat->getStatusId());
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
