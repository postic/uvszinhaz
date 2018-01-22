<?php

namespace SalexUserBundle\Controller;

use Knp\Bundle\SnappyBundle\Snappy\Response\PdfResponse;
use SalexUserBundle\Entity\Reservation;
use SalexUserBundle\Entity\Seat;
use SalexUserBundle\Filter\ItemFilterType;
use SalexUserBundle\Form\ReservationFormType;
use SalexUserBundle\Utility\PageUtility;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ReservationController extends Controller
{

    /**
     * @Route("/get/performance/{id}", name="get_performance", options={"expose"=true}, requirements={"id": "\d+"})
     * @return RedirectResponse
     */
    public function getPerformanceAction(Request $request, $id = 0)
    {
        $performance = $this->get('salex_user.uvszinhaz_listener')->getPerformance($id);
        $resp = json_encode($performance);
        return new Response($resp);
    }


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
                10
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
            $filterBuilder->andWhere('r.user='.$id);
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
        $filterBuilder->andWhere('r.user='.$user_id);
        $filterBuilder->orderBy('r.createdAt', 'desc');

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
        $reservation->setUser($current_user);
        $reservation->setStatusId(1);
        $reservation->setByPhone((int)$request->get('_phone'));

        /*
         * Ukoliko zahtev stize preko sajta, popuniti polja first_name i last_name
         */
        if(!$request->get('_phone')) {
            $reservation->setFirstName($current_user->getFirstName());
            $reservation->setLastName($current_user->getLastName());
        }

        $form = $this->createForm(ReservationFormType::class, $reservation);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $reservation = $form->getData();
            $em = $this->getDoctrine()->getManager();
            $em->persist($reservation);
            $em->flush();

            // Add message
            $this->addFlash(
                'success',
                'Vaš zahtev je uspešno snimljen.'
            );

            if($reservation->getByPhone()) {
                return $this->redirectToRoute('list_reservations');
            } else {
                return $this->redirectToRoute('list_my_reservations');
            }
        }

        return $this->render('SalexUserBundle:Reservation:add-reservation.html.twig', array(
            'form' => $form->createView()
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

        return $this->render('SalexUserBundle:Reservation:show-reservation.html.twig', array(
            'item' => $item,
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

    /**
     * @Route("/cancel/reservation/{id}", name="cancel_reservation", options={"expose"=true}, requirements={"id": "\d+"})
     * @return RedirectResponse
     */
    public function cancelAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $item = $em->getRepository(Reservation::class)->findOneBy(array('id' => $id));
        $item->setStatusId(3);
        $em->persist($item);
        $em->flush();
        return new RedirectResponse($this->generateUrl('list_reservations'));
    }

    /**
     * @Route("/print/reservation/{id}", name="print_reservation", options={"expose"=true}, requirements={"id": "\d+"})
     * @return RedirectResponse
     */
    public function printAction($id)
    {
        $options = array(
            'page-width' => '210mm',
            'page-height' => '297mm',
            'dpi' => 72,
            'images' => true,
            'orientation' => 'portrait',
            'encoding' => 'utf-8',
            #'header-right'=> 'Novosadsko pozorište, 2017',
            'header-left'=> 'Novosadsko pozorište, 2017',
            #'footer-right'=> 'Novosadsko pozorište, 2017',
            'footer-left'=> 'Novosadsko pozorište, 2017'
        );

        $em = $this->getDoctrine()->getManager();
        $item = $em->getRepository(Reservation::class)->findOneBy(array('id' => $id));

        $html = $this->renderView('SalexUserBundle:Reservation:print-reservation.html.twig', array(
            'item'  => $item
        ));

        return new Response(
            $this->get('knp_snappy.pdf')->getOutputFromHtml($html, $options),
            200,
            array(
                'Content-Type'          => 'application/pdf',
                'Content-Disposition'   => 'inline; filename="reservation.pdf"'
            )
        );
    }
}
