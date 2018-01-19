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

        $id = $a_resp->id;
        $tip_karte = $a_resp->tip_karte;

        // get reservation
        $em = $this->getDoctrine()->getManager();
        $reservation = $em->getRepository(Reservation::class)->findOneBy(array('id' => $id));
        $seats = $a_resp->seats;
        $num = sizeof($seats);



        // lorem
        $retval = $this->getDoctrine()
            ->getRepository(Seat::class)
            ->findBy(array('reservation'=>$reservation->getId(),'type'=>$tip_karte));



        switch ($tip_karte) {
            case 1:
                $broj = $reservation->getBrojPojedinacne();
                break;
            case 2:
                $broj = $reservation->getBrojGrupne();
                break;
            case 3:
                $broj = $reservation->getBrojStudentske();
                break;
            case 4:
                $broj = $reservation->getBrojPenzionerske();
                break;
        }

        /**
         * Ispisivanje poruke ako je broj izabranih sedista veci od dozvoljenog
         */
        if($num + sizeof($retval) > $broj) {
            // Add message
            $this->addFlash(
                'error',
                'Nije moguće rezervisati birani broj sedišta'
            );
        }
        else {

            foreach ($seats as $item) {
                $reservation->setStatusId(2);
                $seat = new Seat();
                $seat->setReservation($reservation);
                $seat->setSeat($item);
                $seat->setType($tip_karte);
                $em = $this->getDoctrine()->getManager();
                $em->persist($seat);
                $em->flush();
            }
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
