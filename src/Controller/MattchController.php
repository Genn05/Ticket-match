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
use Symfony\Component\String\Slugger\SluggerInterface;

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
    public function new(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $mattch = new Mattch();
        $form = $this->createForm(MattchForm::class, $mattch);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Handle imageEquipeDomicile upload
            $imageDomicileFile = $form->get('imageEquipeDomicile')->getData();
            if ($imageDomicileFile) {
                $newFilename = uniqid().'.'.$imageDomicileFile->guessExtension();
                $imageDomicileFile->move($this->getParameter('upload_directory'), $newFilename);
                $mattch->setImageEquipeDomicile($newFilename);
            }

            // Handle imageEquipeExterieur upload
            $imageExterieurFile = $form->get('imageEquipeExterieur')->getData();
            if ($imageExterieurFile) {
                $newFilename = uniqid().'.'.$imageExterieurFile->guessExtension();
                $imageExterieurFile->move($this->getParameter('upload_directory'), $newFilename);
                $mattch->setImageEquipeExterieur($newFilename);
            }

            $entityManager->persist($mattch);
            $entityManager->flush();

            return $this->redirectToRoute('app_mattch_index');
        }

        return $this->render('mattch/new.html.twig', [
            'form' => $form,
        ]);
    }

#[Route('/{id}/edit', name: 'app_mattch_edit', methods: ['GET', 'POST'])]
public function edit(Request $request, Mattch $mattch, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
{
    $form = $this->createForm(MattchForm::class, $mattch); // Create the form based on the existing Mattch entity
    $form->handleRequest($request); // Handle the request (fetch form data)

    if ($form->isSubmitted() && $form->isValid()) {
        // Handle imageEquipeDomicile upload if a new file is provided
        $imageDomicileFile = $form->get('imageEquipeDomicile')->getData();
        if ($imageDomicileFile) {
            // Remove the old image if it exists
            if ($mattch->getImageEquipeDomicile()) {
                $oldImagePath = $this->getParameter('upload_directory') . '/' . $mattch->getImageEquipeDomicile();
                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath); // Delete the old image
                }
            }

            // Generate a new filename for the uploaded image
            $newFilename = uniqid().'.'.$imageDomicileFile->guessExtension();
            // Move the uploaded image to the specified directory
            $imageDomicileFile->move($this->getParameter('upload_directory'), $newFilename);
            // Update the Mattch entity with the new image filename
            $mattch->setImageEquipeDomicile($newFilename);
        }

        // Handle imageEquipeExterieur upload if a new file is provided
        $imageExterieurFile = $form->get('imageEquipeExterieur')->getData();
        if ($imageExterieurFile) {
            // Remove the old image if it exists
            if ($mattch->getImageEquipeExterieur()) {
                $oldImagePath = $this->getParameter('upload_directory') . '/' . $mattch->getImageEquipeExterieur();
                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath); // Delete the old image
                }
            }

            // Generate a new filename for the uploaded image
            $newFilename = uniqid().'.'.$imageExterieurFile->guessExtension();
            // Move the uploaded image to the specified directory
            $imageExterieurFile->move($this->getParameter('upload_directory'), $newFilename);
            // Update the Mattch entity with the new image filename
            $mattch->setImageEquipeExterieur($newFilename);
        }

        // Persist the updated Mattch entity and flush changes to the database
        $entityManager->flush();
        // Redirect to the match list page
        return $this->redirectToRoute('app_mattch_index');
    }

    // Render the form for editing the match
    return $this->render('mattch/edit.html.twig', [
        'form' => $form->createView(),
        'mattch' => $mattch,
    ]);
}
#[Route('/{id}', name: 'app_mattch_show', methods: ['GET'])]
public function show(Mattch $mattch): Response
{
    return $this->render('mattch/show.html.twig', [
        'mattch' => $mattch,
    ]);
}




    #[Route('/{id}', name: 'app_mattch_delete', methods: ['POST'])]
    public function delete(Request $request, Mattch $mattch, EntityManagerInterface $entityManager): Response
{
    if ($this->isCsrfTokenValid('delete' . $mattch->getId(), $request->request->get('_token'))) {
        // Remove images if they exist
        if ($mattch->getImageEquipeDomicile()) {
            $imagePath = $this->getParameter('upload_directory') . '/' . $mattch->getImageEquipeDomicile();
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
        }

        if ($mattch->getImageEquipeExterieur()) {
            $imagePath = $this->getParameter('upload_directory') . '/' . $mattch->getImageEquipeExterieur();
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
        }

        $entityManager->remove($mattch);
        $entityManager->flush();
    }

    return $this->redirectToRoute('app_mattch_index');
}

}
