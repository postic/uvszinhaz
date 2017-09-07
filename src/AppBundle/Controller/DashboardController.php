<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;

class DashboardController extends Controller
{
    /**
     * @Route("/", name="homepage")
     * @return RedirectResponse
     */
    public function indexAction()
    {
        return new RedirectResponse($this->generateUrl('users'));
    }

    /**
     * @Route("/dashboard", name="dashboard")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function dashboardAction()
    {
        return $this->render('default/dashboard.html.twig');
    }
}
