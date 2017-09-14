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
     * @Route("/list/users", name="list_users")
     * @return RedirectResponse
     */
    public function listAction()
    {
        $users2 = $this->get('fos_user.user_manager')->findUsers();
        return $this->render("AdminBundle:Default:users.html.twig", ['users'=>$users2]);
    }
}