<?php

namespace App\Controller;

use App\Service\OpenAIService;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AIController extends AbstractController
{
    private OpenAIService $openAIService;
    private LoggerInterface $logger;

    public function __construct(OpenAIService $openAIService, LoggerInterface $logger)
    {
        $this->openAIService = $openAIService;
        $this->logger = $logger;
    }

    /**
     * @Route("/ai", name="ai_prompt", methods={"GET", "POST"})
     */
    public function prompt(Request $request): Response
    {
        $response = null;

        if ($request->isMethod('POST')) {
            $prompt = $request->request->get('prompt');
            $response = $this->openAIService->ask($prompt);

            // Transmettre la réponse brute au template, même en cas d'erreur
            return $this->render('ai/prompt.html.twig', [
                'response' => $response,
            ]);
        }

        return $this->render('ai/prompt.html.twig', [
            'response' => $response,
        ]);
    }
}
