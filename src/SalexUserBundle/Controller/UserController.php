<?php
/**
 * Created by PhpStorm.
 * User: stevan
 * Date: 5.9.17.
 * Time: 09.09
 */

namespace SalexUserBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

class UserController extends Controller
{
    /**
     * @Route("/list/users", name="list_users")
     * @return RedirectResponse
     */
    public function listAction(Request $request)
    {
        $em    = $this->get('doctrine.orm.entity_manager');
        $sql   = "SELECT u FROM SalexUserBundle:User u";
        $query = $em->createQuery($sql);

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            2
        );
        $pagination->setTemplate('KnpPaginatorBundle:Pagination:foundation_v5_pagination.html.twig');

        return $this->render("SalexUserBundle:Profile:list-users.html.twig", array(
            'pagination' => $pagination,
        ));
    }
}