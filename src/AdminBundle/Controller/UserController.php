<?php
/**
 * Created by PhpStorm.
 * User: stevan
 * Date: 5.9.17.
 * Time: 09.09
 */

namespace AdminBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;

class UserController extends Controller
{
    /**
     * @Route("/users", name="users")
     * @return RedirectResponse
     */
    public function usersAction()
    {
        $users2 = $this->get('fos_user.user_manager')->findUsers();
        // $users = $this->getDoctrine()->getRepository("AppBundle:User")->findAll();
        return $this->render("AdminBundle:Default:users.html.twig", ['users'=>$users2]);
    }
}