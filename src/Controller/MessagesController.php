<?php

namespace App\Controller;

use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;



class MessagesController extends AbstractController
{

    #[Route('/api', methods: ['GET'])]
    public function index(): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/MessagesController.php',
        ]);
    }

    #[Route('/api/sendMessage', methods: ['POST'])]
    public function sendMessage(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $payload = [
            'messaging_product' => 'whatsapp',
            'to' => $data['to'],
            'type' => 'text',
            'text' => [
                'body' => $data['text']['body']
            ]
        ];

        $apiEndpoint = $_ENV('META_END_POINT');
        $accessToken = $_ENV('META_API_TOKEN'); 

        try {
            $client = HttpClient::create();

            $response = $client->request('POST', $apiEndpoint, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $accessToken,
                    'Content-Type' => 'application/json',
                ],
                'json' => $payload,
            ]);

            $whatsappResponseContent = $response->getContent();
            $response = new JsonResponse($whatsappResponseContent, 200, [
                'Content-Type' => 'application/json',
            ]);

            $response->headers->set('Access-Control-Allow-Origin', 'http://127.0.0.1:8000');

            return $response;
        } catch (\Exception $e) {
            return $this->json(['error' => 'Error sending message: ' . $e->getMessage()], 500);
        }
    }
}
