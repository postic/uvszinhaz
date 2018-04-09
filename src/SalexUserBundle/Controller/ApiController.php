<?php
/**
 * Created by PhpStorm.
 * User: stevan
 * Date: 7.4.18.
 * Time: 21.06
 */

namespace SalexUserBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\View\View;
use SalexUserBundle\Entity\Performance;
use Symfony\Component\Validator\Constraints\DateTime;


class ApiController extends FOSRestController
{
    /**
     * @Rest\Get("/api")
     */
    public function getAction()
    {
        $restresult = $this->getDoctrine()->getRepository('SalexUserBundle:User')->findAll();
        if ($restresult === null) {
            return new View("there are no users exist", Response::HTTP_NOT_FOUND);
        }
        return $restresult;
    }

    /**
     * @Rest\Get("/api/{id}")
     */
    public function idAction($id)
    {
        $singleresult = $this->getDoctrine()->getRepository('SalexUserBundle:Performance')->find($id);
        if ($singleresult === null) {
            return new View("user not found", Response::HTTP_NOT_FOUND);
        }
        return $singleresult;
    }

    /**
     * @Rest\Post("/api/")
     */
    public function postAction(Request $request)
    {
        $data = new Performance();
        $title = $request->get('title');
        $hall = $request->get('hall');
        $seats = $request->get('seats');
        if(empty($title) || empty($hall))
        {
            return new View("NULL VALUES ARE NOT ALLOWED", Response::HTTP_NOT_ACCEPTABLE);
        }
        $data->setTitle($title);
        $data->setHall($hall);
        $data->setSeats($seats);
        $em = $this->getDoctrine()->getManager();
        $em->persist($data);
        $em->flush();
        return new View("Performance Added Successfully", Response::HTTP_OK);
    }

    /**
     * @Rest\Put("/api/{id}")
     */
    public function updateAction($id, Request $request)
    {
        $data = new User;
        $name = $request->get('name');
        $role = $request->get('role');
        $sn = $this->getDoctrine()->getManager();
        $user = $this->getDoctrine()->getRepository('AppBundle:User')->find($id);
        if (empty($user)) {
            return new View("user not found", Response::HTTP_NOT_FOUND);
        }
        elseif(!empty($name) && !empty($role)){
            $user->setName($name);
            $user->setRole($role);
            $sn->flush();
            return new View("User Updated Successfully", Response::HTTP_OK);
        }
        elseif(empty($name) && !empty($role)){
            $user->setRole($role);
            $sn->flush();
            return new View("role Updated Successfully", Response::HTTP_OK);
        }
        elseif(!empty($name) && empty($role)){
            $user->setName($name);
            $sn->flush();
            return new View("User Name Updated Successfully", Response::HTTP_OK);
        }
        else return new View("User name or role cannot be empty", Response::HTTP_NOT_ACCEPTABLE);
    }

    /**
     * @Rest\Delete("/api/{id}")
     */
    public function deleteAction($id)
    {
        $data = new User;
        $sn = $this->getDoctrine()->getManager();
        $user = $this->getDoctrine()->getRepository('AppBundle:User')->find($id);
        if (empty($user)) {
            return new View("user not found", Response::HTTP_NOT_FOUND);
        }
        else {
            $sn->remove($user);
            $sn->flush();
        }
        return new View("deleted successfully", Response::HTTP_OK);
    }
}