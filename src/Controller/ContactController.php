<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Form\ContactForm;
use App\Repository\ContactRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Bundle\SecurityBundle\Security; // Correct namespace for Security class

#[Route('/contact')]
final class ContactController extends AbstractController
{
    #[Route(name: 'app_contact_index', methods: ['GET'])]
    public function index(ContactRepository $contactRepository, Security $security): Response
    {
        $user = $security->getUser();
        $contacts = $contactRepository->findBy(['user' => $user]);
        return $this->render('contact/index.html.twig', [
            'contacts' => $contacts,
        ]);
    }

    #[Route('/new', name: 'app_contact_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, Security $security, MailerInterface $mailer): Response
    {
        $contact = new Contact();
        $form = $this->createForm(ContactForm::class, $contact);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Set the active user as the contact's user
            $contact->setUser($security->getUser());
            // Set the creation date and time automatically (+1 heure)
            $date = new \DateTime();
            $date->modify('+1 hour');
            $contact->setDate($date);

            $entityManager->persist($contact);
            $entityManager->flush();

            // Send email notification
            $email = (new Email())
                ->from($contact->getUser() ? $contact->getUser()->getEmail() : 'no-reply@yourproject.com')
                ->to('serviceresponsableclient@gmail.com')
                ->subject('New Contact Submission')
                ->text(sprintf(
                    "A new contact has been submitted:\n\nSubject: %s\nMessage: %s\nDate: %s\nUser Email: %s",
                    $contact->getSujet(),
                    $contact->getMessage(),
                    $contact->getDate() ? $contact->getDate()->format('Y-m-d H:i:s') : 'N/A',
                    $contact->getUser() ? $contact->getUser()->getEmail() : 'N/A'
                ));

            $mailer->send($email);

            return $this->redirectToRoute('app_contact_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('contact/new.html.twig', [
            'contact' => $contact,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_contact_show', methods: ['GET'])]
    public function show(Contact $contact): Response
    {
        throw $this->createAccessDeniedException('La consultation des contacts est désactivée.');
    }

    #[Route('/{id}/edit', name: 'app_contact_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Contact $contact, EntityManagerInterface $entityManager): Response
    {
        throw $this->createAccessDeniedException('La modification des contacts est désactivée.');
    }

    #[Route('/{id}', name: 'app_contact_delete', methods: ['POST'])]
    public function delete(Request $request, Contact $contact, EntityManagerInterface $entityManager, Security $security): Response
    {
        // Only allow delete if the contact belongs to the current user or user is admin
        $user = $security->getUser();
        if ($contact->getUser() !== $user && !$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException('Vous ne pouvez supprimer que vos propres contacts.');
        }
        if ($this->isCsrfTokenValid('delete'.$contact->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($contact);
            $entityManager->flush();
        }
        return $this->redirectToRoute('app_contact_index', [], Response::HTTP_SEE_OTHER);
    }
}
