<?php

namespace SalexUserBundle\Controller;

use SalexUserBundle\Entity\Reservation;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use SalexUserBundle\Entity\Seat;

class SaleController extends Controller
{

    /**
     * @Route("/upcoming-performances", name="list_upcoming_performances")
     * @return RedirectResponse
     */
    public function listAction()
    {
        $items = $this->get('salex_user.uvszinhaz_listener')->getPerformances();
        return $this->render("SalexUserBundle:Sale:upcoming-performances.html.twig", array(
            'items' => $items,
        ));
    }

}
