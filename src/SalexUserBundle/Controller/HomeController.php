<?php

namespace SalexUserBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;

class HomeController extends Controller
{
    /**
     * @Route("/", name="homepage")
     * @return RedirectResponse
     */
    public function indexAction()
    {
        $is_admin = $this->container->get('security.authorization_checker')->isGranted('ROLE_SUPER_ADMIN');
        if($is_admin) {
            return new RedirectResponse($this->generateUrl('list_users'));
        }
        else {
            return new RedirectResponse($this->generateUrl('add_reservation'));
        }
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
