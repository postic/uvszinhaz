<?php

namespace SalexUserBundle\Controller;

use SalexUserBundle\Entity\Reservation;
use SalexUserBundle\Form\ReservationType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

class ReservationController extends Controller
{

    /**
     * @Route("/list/reservations/{id}", name="list_reservations"), requirements={"id": "\d+"}
     * @return RedirectResponse
     */
    public function listAction($id = 0)
    {
        $repository = $this->getDoctrine()->getRepository(Reservation::class);
        if($id === 0) {
            $items = $repository->findAll();
            return $this->render("SalexUserBundle:Reservation:list-all-reservations.html.twig", ['items'=>$items]);
        }
        else {
            $items = $repository->findBy([ "userId"=>$id ]);
            return $this->render("SalexUserBundle:Reservation:list-user-reservations.html.twig", ['items'=>$items]);
        }

    }

    /**
     * @Route("/list/my/reservations", name="list_my_reservations")
     * @return RedirectResponse
     */
    public function listMyReservationsAction()
    {
        $user = $this->getUser();
        $user_id = $user->getId();
        $repository = $this->getDoctrine()->getRepository(Reservation::class);
        $items = $repository->findBy([ "userId"=>$user_id ]);
        return $this->render("SalexUserBundle:Reservation:list-my-reservations.html.twig", ['items'=>$items]);

    }


    /**
     * @Route("/add/reservation", name="add_reservation")
     * @return RedirectResponse
     */
    public function addAction(Request $request)
    {
        $current_user = $this->get('security.token_storage')->getToken()->getUser();

        $reservation = new Reservation();
        $reservation->setCreatedAt(new \DateTime());
        $reservation->setUserId($current_user->getId());
        $reservation->setStatusId(1);

        $form = $this->createForm(ReservationType::class, $reservation);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $reservation = $form->getData();
            $em = $this->getDoctrine()->getManager();
            $em->persist($reservation);
            $em->flush();
            return $this->redirectToRoute('list_reservations');
        }

        return $this->render('SalexUserBundle:Reservation:add-reservation.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/show/reservation/{id}", name="show_reservation", requirements={"id": "\d+"})
     * @return RedirectResponse
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $item = $em->getRepository(Reservation::class)->findOneBy(array('id' => $id));
        return $this->render('SalexUserBundle:Reservation:show-reservation.html.twig', array(
            'item' => $item,
        ));
    }

    /**
     * @Route("/delete/reservation/{id}", name="delete_reservation", requirements={"id": "\d+"})
     * @return RedirectResponse
     */
    public function deleteAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $item = $em->getRepository(Reservation::class)->findOneBy(array('id' => $id));
        $em->remove($item);
        $em->flush();
        return new RedirectResponse($this->generateUrl('list_reservations'));
    }

}
