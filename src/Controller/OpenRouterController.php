<?php
namespace App\Controller;

use App\Service\OpenRouterService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OpenRouterController extends AbstractController
{
    #[Route('/ask', name: 'ask_ai', methods: ['POST','GET'])]
    public function ask(Request $request, OpenRouterService $openRouter): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $question = $data['question'] ?? '';

        if (empty($question)) {
            return new JsonResponse(['error' => 'Question manquante'], 400);
        }

        $response = $openRouter->askAI($question);

        return new JsonResponse(['response' => $response]);
    }

    /**
     * @Route("/openrouter", name="openrouter_prompt", methods={"GET"})
     */
    public function prompt(): Response
    {
        return $this->render('ai/openrouter.html.twig');
    }
}
