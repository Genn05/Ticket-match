<?php

namespace App\Controller;

use App\Entity\Mattch;
use App\Form\MattchForm;
use App\Repository\MattchRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/mattch')]
final class MattchController extends AbstractController
{
    #[Route(name: 'app_mattch_index', methods: ['GET'])]
    public function index(MattchRepository $mattchRepository): Response
    {
        return $this->render('mattch/index.html.twig', [
            'mattches' => $mattchRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_mattch_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $mattch = new Mattch();
        $form = $this->createForm(MattchForm::class, $mattch);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($mattch);
            $entityManager->flush();

            return $this->redirectToRoute('app_mattch_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('mattch/new.html.twig', [
            'mattch' => $mattch,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_mattch_show', methods: ['GET'])]
    public function show(Mattch $mattch): Response
    {
        return $this->render('mattch/show.html.twig', [
            'mattch' => $mattch,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_mattch_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Mattch $mattch, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(MattchForm::class, $mattch);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_mattch_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('mattch/edit.html.twig', [
            'mattch' => $mattch,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_mattch_delete', methods: ['POST'])]
    public function delete(Request $request, Mattch $mattch, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$mattch->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($mattch);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_mattch_index', [], Response::HTTP_SEE_OTHER);
    }
}
