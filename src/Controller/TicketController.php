<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Status;
use App\Entity\Ticket;
use App\Form\CommentType;
use App\Form\TicketType;
use App\Repository\StatusRepository;
use App\Repository\TicketRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


#[Route('/ticket')]
class TicketController extends AbstractController
{
    #[Route('/', name: 'ticket_index', methods: ['GET'])]
    public function index(TicketRepository $ticketRepository): Response
    {
        return $this->render('ticket/index.html.twig', [
            'tickets' => $ticketRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'ticket_new', methods: ['GET', 'POST'])]
    public function new(Request $request, StatusRepository $statusRepo): Response
    {
        $ticket = new Ticket();
        $status = new Status();
        $form = $this->createForm(TicketType::class, $ticket);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $ticket->setEscalated(false);
            $ticket->setStatus($statusRepo->find(1));

            $ticket->setCreatedBy($this->get('security.token_storage')->getToken()->getUser());


            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($ticket);
            $entityManager->flush();


            return $this->redirectToRoute('ticket_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('ticket/new.html.twig', [
            'ticket' => $ticket,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'ticket_show', methods: ['GET','POST'])]
    public function show(Ticket $ticket, Request $request, TicketRepository $ticketRepo): Response
    {
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        var_dump($request->get('id'));

        if ($form->isSubmitted() && $form->isValid()) {

            $comment->setPrivate(false);

            $dateNow = new \DateTime('now' , new \DateTimeZone('Europe/Brussels'));
            $comment->setDate( $dateNow);

            $comment->setTicketID($ticketRepo->find($request->get('id')));
            $comment->setUserID($this->get('security.token_storage')->getToken()->getUser());



            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($comment);
            $entityManager->flush();

            return $this->redirectToRoute('ticket_index', [], Response::HTTP_SEE_OTHER);
        }


        return $this->renderForm('ticket/show.html.twig', [
            'ticket' => $ticket,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/edit', name: 'ticket_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Ticket $ticket): Response
    {
        $form = $this->createForm(TicketType::class, $ticket);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('ticket_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('ticket/edit.html.twig', [
            'ticket' => $ticket,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'ticket_delete', methods: ['POST'])]
    public function delete(Request $request, Ticket $ticket): Response
    {
        if ($this->isCsrfTokenValid('delete' . $ticket->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($ticket);
            $entityManager->flush();
        }

        return $this->redirectToRoute('ticket_index', [], Response::HTTP_SEE_OTHER);
    }
}
