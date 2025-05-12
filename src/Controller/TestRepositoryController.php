<?php
namespace App\Controller;

use App\Entity\ResetPasswordRequest;
use App\Entity\User;
use App\Repository\ResetPasswordRequestRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TestRepositoryController extends AbstractController
{
    #[Route('/test-repository', name: 'app_test_repository')]
    public function testRepository(
        EntityManagerInterface $entityManager,
        ResetPasswordRequestRepository $repository
    ): Response {
        // Fetch a user from the database (replace with a valid user ID or email)
        $user = $entityManager->getRepository(User::class)->find(1); // Replace '1' with a valid user ID

        if (!$user) {
            return new Response('❌ No user found with the given ID.');
        }

        // Create a new ResetPasswordRequest entity
        $resetPasswordRequest = new ResetPasswordRequest();
        $resetPasswordRequest->setUser($user);
        $resetPasswordRequest->setToken('test-token');
        $resetPasswordRequest->setRequestedAt(new \DateTimeImmutable());
        $resetPasswordRequest->setExpiresAt((new \DateTimeImmutable())->modify('+1 hour'));

        // Persist and save to the database
        $entityManager->persist($resetPasswordRequest);
        $entityManager->flush();

        // Fetch all ResetPasswordRequest entries
        $requests = $repository->findAll();

        return new Response('✅ Repository test successful! Found ' . count($requests) . ' entries.');
    }
}