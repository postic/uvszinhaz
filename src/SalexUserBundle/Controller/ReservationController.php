<?php

namespace SalexUserBundle\Controller;

use SalexUserBundle\Entity\Reservation;
use SalexUserBundle\Entity\Seat;
use SalexUserBundle\Filter\ItemFilterType;
use SalexUserBundle\Form\ReservationType;
use SalexUserBundle\Utility\PageUtility;
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
    public function listAction(Request $request, $id = 0)
    {
        if($id === 0) {
            $filterBuilder = $this->get('doctrine.orm.entity_manager')
                ->getRepository(Reservation::class)
                ->createQueryBuilder('r');
            $form = $this->get('form.factory')->create('SalexUserBundle\Filter\ItemFilterType');
            if ($request->query->has($form->getName())) {
                $form->submit($request->query->get($form->getName()));
                $this->get('lexik_form_filter.query_builder_updater')->addFilterConditions($form, $filterBuilder);
            }
            $query = $filterBuilder->getQuery();
            $paginator  = $this->get('knp_paginator');
            $pagination = $paginator->paginate(
                $query,
                $request->query->getInt('page', 1),
                5
            );
            $pagination->setTemplate('KnpPaginatorBundle:Pagination:foundation_v5_pagination.html.twig');
            return $this->render("SalexUserBundle:Reservation:list-all-reservations.html.twig", array(
                'pagination' => $pagination,
                'form' => $form->createView(),
            ));
        }
        else {
            $filterBuilder = $this->get('doctrine.orm.entity_manager')
                ->getRepository(Reservation::class)
                ->createQueryBuilder('r');
            $filterBuilder->andWhere('r.userId='.$id);
            $form = $this->get('form.factory')->create('SalexUserBundle\Filter\ItemFilterType');
            if ($request->query->has($form->getName())) {
                $form->submit($request->query->get($form->getName()));
                $this->get('lexik_form_filter.query_builder_updater')->addFilterConditions($form, $filterBuilder);
            }
            $query = $filterBuilder->getQuery();
            $paginator  = $this->get('knp_paginator');
            $pagination = $paginator->paginate(
                $query,
                $request->query->getInt('page', 1),
                5
            );
            $pagination->setTemplate('KnpPaginatorBundle:Pagination:foundation_v5_pagination.html.twig');
            return $this->render("SalexUserBundle:Reservation:list-user-reservations.html.twig", array(
                'pagination' => $pagination,
                'form' => $form->createView(),
            ));

        }

    }

    /**
     * @Route("/list/my/reservations", name="list_my_reservations")
     * @return RedirectResponse
     */
    public function listMyReservationsAction(Request $request)
    {
        $user = $this->getUser();
        $user_id = $user->getId();
        $filterBuilder = $this->get('doctrine.orm.entity_manager')
            ->getRepository(Reservation::class)
            ->createQueryBuilder('r');
        $filterBuilder->andWhere('r.userId='.$user_id);
        $form = $this->get('form.factory')->create('SalexUserBundle\Filter\ItemFilterType');
        if ($request->query->has($form->getName())) {
            $form->submit($request->query->get($form->getName()));
            $this->get('lexik_form_filter.query_builder_updater')->addFilterConditions($form, $filterBuilder);
        }
        $query = $filterBuilder->getQuery();
        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            5
        );
        $pagination->setTemplate('KnpPaginatorBundle:Pagination:foundation_v5_pagination.html.twig');
        return $this->render("SalexUserBundle:Reservation:list-my-reservations.html.twig", array(
            'pagination' => $pagination,
            'form' => $form->createView(),
        ));
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
            return $this->redirectToRoute('list_my_reservations');
        }

        return $this->render('SalexUserBundle:Reservation:add-reservation.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/show/reservation/{id}", name="show_reservation", options={"expose"=true}, requirements={"id": "\d+"})
     * @return RedirectResponse
     */
    public function showAction($id)
    {
        // get reservation
        $em = $this->getDoctrine()->getManager();
        $item = $em->getRepository(Reservation::class)->findOneBy(array('id' => $id));

        // get seats
        $seats = $em->getRepository(Seat::class)->findBy(array('reservationId' => $id));

        return $this->render('SalexUserBundle:Reservation:show-reservation.html.twig', array(
            'item' => $item,
            'seats' => $seats,
        ));
    }

    /**
     * @Route("/delete/reservation/{id}", name="delete_reservation", options={"expose"=true}, requirements={"id": "\d+"})
     * @return RedirectResponse
     */
    public function deleteAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $item = $em->getRepository(Reservation::class)->findOneBy(array('id' => $id));
        $em->remove($item);
        $em->flush();
        return new RedirectResponse($this->generateUrl('list_my_reservations'));
    }

}
