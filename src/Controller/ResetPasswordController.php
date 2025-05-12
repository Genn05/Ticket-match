<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\ResetPasswordRequest;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Psr\Log\LoggerInterface;

final class ResetPasswordController extends AbstractController
{
    #[Route('/reset-password', name: 'app_reset_password_request')]
    public function request(Request $request, EntityManagerInterface $entityManager, MailerInterface $mailer, LoggerInterface $logger): Response
    {
        // Handle password reset request form submission
        if ($request->isMethod('POST')) {
            $email = $request->request->get('email');

            // Validate email
            if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $this->addFlash('error', 'Please enter a valid email address.');
                return $this->redirectToRoute('app_reset_password_request');
            }

            // Find user by email
            $user = $entityManager->getRepository(User::class)->findOneBy(['email' => $email]);
            if ($user) {
                try {
                    // Generate a reset token and expiration time
                    $resetToken = bin2hex(random_bytes(32));
                    $requestedAt = new \DateTimeImmutable();
                    $expiresAt = $requestedAt->modify('+2 hours');

                    // Remove existing reset requests for the user
                    $existingRequest = $entityManager->getRepository(ResetPasswordRequest::class)->findOneBy(['user' => $user]);
                    if ($existingRequest) {
                        $entityManager->remove($existingRequest);
                    }

                    // Create a new reset request
                    $resetPasswordRequest = new ResetPasswordRequest();
                    $resetPasswordRequest->setUser($user);
                    $resetPasswordRequest->setToken($resetToken);
                    $resetPasswordRequest->setRequestedAt($requestedAt);
                    $resetPasswordRequest->setExpiresAt($expiresAt);

                    // Save the request
                    $entityManager->persist($resetPasswordRequest);
                    $entityManager->flush();

                    // Send reset email
                    $emailMessage = (new Email())
                        ->from('serviceresponsableclient@gmail.com')
                        ->to($user->getEmail())
                        ->subject('Password Reset Request')
                        ->html('<p>To reset your password, please click the link below:</p>
                                <a href="' . $this->generateUrl('app_reset_password_form', ['token' => $resetToken], \Symfony\Component\Routing\Generator\UrlGeneratorInterface::ABSOLUTE_URL) . '">Reset Password</a>');

                    $mailer->send($emailMessage);

                    $this->addFlash('success', 'A password reset link has been sent to your email.');
                } catch (\Exception $e) {
                    $this->addFlash('error', 'An error occurred while processing your request. Please try again later.');
                    $logger->error('Error during password reset request: ' . $e->getMessage());
                }
            } else {
                $this->addFlash('error', 'No user found with that email address.');
            }
        }

        // Render the reset request form
        return $this->render('reset_password/request.html.twig');
    }

    #[Route('/reset-password/{token}', name: 'app_reset_password_form')]
    public function reset(Request $request, string $token, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
    {
        // Find the reset request by token
        $resetPasswordRequest = $entityManager->getRepository(ResetPasswordRequest::class)->findOneBy(['token' => $token]);

        if (!$resetPasswordRequest) {
            $this->addFlash('error', 'This reset link is invalid.');
            return $this->redirectToRoute('app_reset_password_request');
        }

        // Check if the token has expired
        if ($resetPasswordRequest->getExpiresAt() < new \DateTimeImmutable()) {
            $this->addFlash('error', 'This reset link has expired.');
            return $this->redirectToRoute('app_reset_password_request');
        }

        // Handle password reset form submission
        if ($request->isMethod('POST')) {
            $newPassword = $request->request->get('password');

            // Validate password
            if (!$newPassword || strlen($newPassword) < 6) {
                $this->addFlash('error', 'The password must be at least 6 characters long.');
                return $this->render('reset_password/reset.html.twig', ['token' => $token]);
            }

            // Update the user's password
            $user = $resetPasswordRequest->getUser();
            $hashedPassword = $passwordHasher->hashPassword($user, $newPassword);
            $user->setPassword($hashedPassword);

            // Remove the reset request
            $entityManager->remove($resetPasswordRequest);
            $entityManager->flush();

            $this->addFlash('success', 'Your password has been successfully reset.');
            return $this->redirectToRoute('app_login');
        }

        // Render the reset password form
        return $this->render('reset_password/reset.html.twig', [
            'token' => $token,
        ]);
    }
}