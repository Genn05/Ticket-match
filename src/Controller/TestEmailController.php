<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class TestEmailController extends AbstractController
{
    #[Route('/test-email', name: 'app_test_email')]
    public function testEmail(MailerInterface $mailer): Response
    {
        $email = (new Email())
            ->from('serviceresponsableclient@gmail.com')
            ->to('ademjendoubi21@gmail.com') // <-- remplace par une vraie adresse
            ->subject('✅ Test d’envoi d’email via MAILER_DSN')
            ->text("Ceci est un email de test envoyé depuis Symfony avec MAILER_DSN.");

        $mailer->send($email);

        return new Response('✅ Email envoyé avec succès !');
    }
}
