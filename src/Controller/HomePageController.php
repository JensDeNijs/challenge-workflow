<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomePageController extends AbstractController
{
    /**
     * @Route("/home/page", name="home_page")
     */
    public function index(): Response
    {

        $auth_checker = $this->get('security.authorization_checker');
        $isRoleUser = $auth_checker->isGranted('ROLE_USER');
        $isRoleAgent = $auth_checker->isGranted('ROLE_AGENT');
        $isRoleAdmin = $auth_checker->isGranted('ROLE_ADMIN');
        var_dump($isRoleUser);
        var_dump($isRoleAgent);
        var_dump($isRoleAdmin);

        if ($isRoleAdmin){
            return $this->redirectToRoute('manager');
        }

        if ($isRoleAgent){
            return $this->redirectToRoute('agent');
        }

        if ($isRoleUser){
            return $this->redirectToRoute('customer');
        }

        return $this->redirectToRoute('app_login');

    }
}
