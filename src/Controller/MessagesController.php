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

        $apiEndpoint = 'https://graph.facebook.com/v17.0/100206783144220/messages';
        $accessToken = 'EAACP9wBzdvEBO12VIoWcDBtwsYtSKIdhH9ZAisGZCKg1KQzLCWv3gfTZB2ZAUXi63A5BKSUKmBOd6S12IiC3A6kbUGajZCNAz2s4GxBz3srCZCCSDLIjSoitP9r417GqtTVdZBCHxWZCm3c38FC7yqTOjaassSuc96owWM9uyu0RbUsMZCA0R85iq4lAWelF0Mf7dty6nsY1NkYi2hbZAeJNoCeZAVcBEG9PAZDZD';  // Replace with your Meta API access token

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

            $response->headers->set('Access-Control-Allow-Origin', 'http://127.0.0.1:8000'); // http://127.0.0.1:8000

            return $response;
        } catch (\Exception $e) {
            return $this->json(['error' => 'Error sending message: ' . $e->getMessage()], 500);
        }
    }
}
