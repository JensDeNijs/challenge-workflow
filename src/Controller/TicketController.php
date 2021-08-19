<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Status;
use App\Entity\Ticket;
use App\Form\CommentType;
use App\Form\TicketType;
use App\Repository\CommentRepository;
use App\Repository\StatusRepository;
use App\Repository\TicketRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/ticket")
 */
class TicketController extends AbstractController
{
    /**
     * @Route("/", name="ticket_index", methods="GET")
     */
    public function index(TicketRepository $ticketRepository): Response
    {
        $user = $this->getUser();
        $roles = $user->getRoles();

       if (in_array("ROLE_AGENT", $roles)){
            $tickets = $ticketRepository -> findBy(array('assignedTo' => $user));
            $title = 'All Tickets';
        }

        elseif (in_array("ROLE_USER", $roles)){
            $tickets = $ticketRepository -> findBy(array('createdBy' => $user));
            $title = 'My Tickets';
        }

        return $this->render('ticket/index.html.twig', [
            'tickets' => $tickets,
            'title' => $title,
        ]);
    }

    /**
     * @Route("/mytickets", name="mytickets", methods="GET")
     */
    public function mytickets(TicketRepository $ticketRepository): Response
    {
        $user = $this->getUser();
        $tickets = $ticketRepository -> findBy(array('createdBy' => $user));
        return $this->render('ticket/index.html.twig', [
            'tickets' => $tickets,
            'title' => 'My Tickets'
        ]);
    }

    /**
     * @Route("/alltickets", name="alltickets", methods="GET")
     */
    public function alltickets(TicketRepository $ticketRepository): Response
    {
        return $this->render('ticket/index.html.twig', [
            'tickets' => $ticketRepository->findAll(),
            'title' => 'All Tickets'
        ]);
    }
    /**
     * @Route("/assignedtickets", name="assignedtickets", methods="GET")
     */
    public function assignedtickets(TicketRepository $ticketRepository): Response
    {
        $user = $this->getUser();
        $tickets = $ticketRepository -> findBy(array('assignedTo' => $user));
        return $this->render('ticket/index.html.twig', [
            'tickets' => $tickets,
            'title' => 'My Assigned Tickets'
        ]);
    }

    /**
     * @Route("/new", name="ticket_new", methods={"GET", "POST"})
     */
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

    /**
     * @Route("/{id}", name="ticket_show", methods={"GET", "POST"})
     */
    public function show(Ticket $ticket, Request $request, TicketRepository $ticketRepo, CommentRepository $commentRepo): Response
    {

        $comments = $commentRepo->findBy(array('ticketID' => $ticket->getId()));

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
            'comments' => $comments,
            'ticket' => $ticket,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="ticket_edit", methods={"GET", "POST"})
     */
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

    /**
     * @Route("/{id}/edit", name="ticket_delete", methods="POST")
     */
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
