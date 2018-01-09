<?php
/**
 * Created by PhpStorm.
 * User: steva
 * Date: 8.1.18.
 * Time: 09.12
 */

namespace SalexUserBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

class ChangeLocaleController extends Controller
{

    /**
     * @Route("/change-locale", name="change_locale")
     * @return RedirectResponse
     */
    public function changeLocaleAction(Request $request)
    {
        var_dump($request->getLocale());
        var_dump($request->get('_locale'));
        return $this->render('SalexUserBundle:Reservation:add-reservation.html.twig');
    }

}