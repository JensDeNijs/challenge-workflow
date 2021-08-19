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
        var_dump($isRoleUser);

     if ($isRoleUser){
         return $this->render('home_page/index.html.twig', [
             'controller_name' => 'HomePageController',
         ]);
     }
        return $this->forward('App\Controller\LoginSecurityController::login', [
        ]);

    }
}
