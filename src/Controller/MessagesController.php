<?php

namespace App\Controller;

use App\Websocket\MessageHandler;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\StreamedResponse;

use App\Services\MessagesService;

class MessagesController extends AbstractController
{

    protected $messageHandler;
    protected $messaegesService ; 

    public function __construct(MessageHandler $messageHandler,  MessagesService $messaegesService)
    {
        $this->messageHandler = $messageHandler;
        $this->messaegesService = $messaegesService ;
    }

    #[Route('/api', methods: ['GET'])]
    public function index(): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/MessagesController.php',
        ]);
    }

    #[Route('/api/sendMessage', methods: ['POST'])]
    public function sendMessage(Request $request, ): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        return $this->messaegesService->handleMessagesSending($data) ;
    }

    // #[Route('/api/webhooks', methods: ['GET'])]
    // public function handleWebhook(Request $request): JsonResponse
    // {
    //     if ($request->isMethod('GET')) {
    //         $queryParameters = $request->query->all();
    //         $verifyToken = $queryParameters['hub_verify_token'];
    //         $challenge = $queryParameters['hub_challenge'];

    //         $queryParameters = $request->query->all();
    //         error_log("Query Parameters: " . print_r($queryParameters, true));

    //         if ($verifyToken != "qwqwqw12") {
    //             return new JsonResponse('Invalid verification token');
    //         }

    //         return new JsonResponse((int) $challenge);
    //     }

    //     return new JsonResponse('Invalid request method'); StreamedResponse
    // }


    #[Route('/api/webhooks', methods: ['GET', 'POST'])]
    public function getMessageResponse(Request $request): StreamedResponse
    {
        $jsonContent = $request->getContent();
        $data = json_decode($jsonContent, true);
        return $this->messaegesService->handleWhatAppResponse($data);
    }
}