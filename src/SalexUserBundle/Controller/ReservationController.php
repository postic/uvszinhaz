<?php

namespace SalexUserBundle\Controller;

use JMS\Serializer\Serializer;
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
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Routing\Annotation;

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
     * @Security("has_role('ROLE_ADMIN')")
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
            $filterBuilder->orderBy('r.createdAt', 'desc');
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
            $filterBuilder->andWhere('r.user='.$id);
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
            return $this->render("SalexUserBundle:Reservation:list-user-reservations.html.twig", array(
                'pagination' => $pagination,
                'form' => $form->createView(),
            ));
        }
    }

    /**
     * @Route("/list/my/reservations", name="list_my_reservations")
     * @Security("has_role('ROLE_USER')")
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
            10
        );
        $pagination->setTemplate('KnpPaginatorBundle:Pagination:foundation_v5_pagination.html.twig');
        return $this->render("SalexUserBundle:Reservation:list-my-reservations.html.twig", array(
            'pagination' => $pagination,
            'form' => $form->createView(),
        ));
    }


    /**
     * @Route("/add/reservation", name="add_reservation")
     * @Security("has_role('ROLE_ADMIN') or has_role('ROLE_USER')")
     * @return RedirectResponse
     */
    public function addAction(Request $request, \Swift_Mailer $mailer)
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

            // Slanje mail-a
            $email = $current_user->getEmail();
            $message = (new \Swift_Message('Uspešna rezervacija'))
                ->setFrom('no-reply@uvszinhaz.com')
                ->setTo($email)
                ->setBody(
                    $this->renderView(
                        '@SalexUser/Emails/request.html.twig',
                        array(
                            'p_ime' => $reservation->getFirstName(),
                            'p_prezime' => $reservation->getLastName(),
                            'p_title' => $reservation->getPerformance()['title'],
                            'p_datum_predstave' => $reservation->getPerformance()['datum'],
                            'p_datum_rezervacije' => $reservation->getCreatedAt()->format('d.m.Y. H:i'),
                            'p_brojPojedinacne' => $reservation->getBrojPojedinacne(),
                            'p_brojGrupne' => $reservation->getBrojGrupne(),
                            'p_brojPenzionerske' => $reservation->getBrojPenzionerske(),
                            'p_brojStudentske' => $reservation->getBrojStudentske(),
                            'p_sum' => number_format($reservation->getSum(),2,',','.').' RSD',
                            'p_scena' => $reservation->getScena()?'Velika scena':'Mala scena',
                        )

                    ),
                    'text/html'
                );

            $mailer->send($message);

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
     * @Security("has_role('ROLE_ADMIN')")
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
     * @Route("/get/reservation/{id}", name="get_reservation", options={"expose"=true}, requirements={"id": "\d+"})
     * @return RedirectResponse
     */
    public function getAction($id)
    {
        // get reservation
        $em = $this->getDoctrine()->getManager();
        $item = $em->getRepository(Reservation::class)->findOneBy(array('id' => $id));

        $p_status = '';
        switch($item->getStatusId()){
            case 1:
                $p_status = 'Zahtev';
                break;
            case 2:
                $p_status = 'Rezervisano';
                break;
            case 3:
                $p_status = 'Otkazano';
                break;
        }

        $p_seats = null;
        $a_seats = $item->getSeats()->toArray();
        foreach($a_seats as $key=>$seat){
            $p_seats[] = str_replace('_', '/', $seat->getSeat());
        }

        $serializer = $this->get('serializer');
        $data = $serializer->serialize(
            array(
                'p_ime' => $item->getFirstName(),
                'p_prezime' => $item->getLastName(),
                'p_title' => $item->getPerformance()['title'],
                'p_datum_predstave' => $item->getPerformance()['datum'],
                'p_datum_rezervacije' => $item->getCreatedAt()->format('d.m.Y. H:i'),
                'p_brojPojedinacne' => $item->getBrojPojedinacne(),
                'p_brojGrupne' => $item->getBrojGrupne(),
                'p_brojPenzionerske' => $item->getBrojPenzionerske(),
                'p_brojStudentske' => $item->getBrojStudentske(),
                'p_sum' => number_format($item->getSum(),2,',','.').' RSD',
                'p_status_id' => $item->getStatusId(),
                'p_status' => $p_status,
                'p_scena' => $item->getScena()?'Velika scena':'Mala scena',
                'p_sedista' => $p_seats,
            ),
            'json'
        );

        return new Response($data);
    }

    /**
     * @Route("/delete/reservation/{id}", name="delete_reservation", options={"expose"=true}, requirements={"id": "\d+"})
     * @return RedirectResponse
     */
    public function deleteAction($id)
    {
        // brisanje zahteva (rezervacije)
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
     * @Route("/close/reservation/{id}", name="close_reservation", options={"expose"=true}, requirements={"id": "\d+"})
     * @return RedirectResponse
     */
    public function closeAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $item = $em->getRepository(Reservation::class)->findOneBy(array('id' => $id));
        $item->setStatusId(2);
        $em->persist($item);
        $em->flush();
        return new RedirectResponse($this->generateUrl('list_reservations'));
    }

    /**
     * @Route("/print/reservation/{id}", name="print_reservation", options={"expose"=true}, requirements={"id": "\d+"})
     * @Security("has_role('ROLE_USER')")
     * @return RedirectResponse
     */
    public function printAction($id)
    {
        $snappy = $this->get('knp_snappy.pdf');

        $options = array(
            'images' => true,
            'orientation' => 'landscape',
            'encoding' => 'utf-8',
            'footer-right' => 'Jovana Subotića 3-5, Novi Sad 21000, tel: 021/66 22 592',
        );

        $filename = 'Rezervacija';
        $em = $this->getDoctrine()->getManager();
        $item = $em->getRepository(Reservation::class)->findOneBy(array('id' => $id));
        $html = $this->renderView('SalexUserBundle:Reservation:print-reservation.html.twig', array(
            'item'  => $item
        ));

        return new Response(
            $snappy->getOutputFromHtml($html, $options),
            200,
            array(
                'Content-Type'          => 'application/pdf',
                'Content-Disposition'   => 'inline; filename="'.$filename.'.pdf"'
            )
        );
    }
}
