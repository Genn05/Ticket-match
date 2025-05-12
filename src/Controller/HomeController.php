<?php

// src/Controller/HomeController.php
namespace App\Controller;

use App\Repository\TicketRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/home', name: 'app_home')]
    public function index(TicketRepository $ticketRepository): Response
    {
        $tickets = $ticketRepository->findAll();

        return $this->render('home/index.html.twig', [
            'tickets' => $tickets,
        ]);
    }
}
