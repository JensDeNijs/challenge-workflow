<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\StatusRepository;
use App\Repository\TicketRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class ManagerController extends AbstractController
{
    /**
     * @Route("/manager", name="manager")
     */
    public function index(TicketRepository $ticketRepository, StatusRepository $statusRepository): Response
    {
        $openTickets = count($ticketRepository->findBy(array('status' => $statusRepository->find(1))));
        $inProgressTickets = count($ticketRepository->findBy(array('status' => $statusRepository->find(2))));
        $feedbackTickets = count($ticketRepository->findBy(array('status' => $statusRepository->find(3))));
        $closedTickets = count($ticketRepository->findBy(array('status' => $statusRepository->find(4))));


        return $this->render('manager/index.html.twig', [
            'controller_name' => 'ManagerController',
            'openTickets'=> $openTickets,
            'inProgressTickets' => $inProgressTickets,
            'feedbackTickets'=> $feedbackTickets,
            'closedTickets'=> $closedTickets,
        ]);
    }

    /**
     * @Route("/deassign", name="ticket_deassign", methods={"GET", "POST"})
     */
    public function deassigne(TicketRepository $ticketRepository, Request $request,StatusRepository $statusRepository): Response
    {

        $inprogress= $ticketRepository->findBy(array('status' => $statusRepository->find(2)));
        foreach ($inprogress as $item){
            $item->setStatus($statusRepository->find(1));
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($item);
            $entityManager->flush();
        }



        return $this->redirectToRoute('manager', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * @Route("/manageradminindex", name="manager_admin_index", methods={"GET"})
     */
    public function test(UserRepository $userRepository): Response
    {
        return $this->render('manager_admin/index.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }

    /**
     * @Route("/{id}/edit", name="manager_admin_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, User $user): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('manager_admin_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('manager_admin/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

}
