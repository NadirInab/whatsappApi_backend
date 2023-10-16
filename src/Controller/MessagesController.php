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
        $accessToken = 'EAACP9wBzdvEBOxk9zdpKnOtqBnWUYXZBnJYZAfoX1V6O0ZAZB6vK1SETCCeyhgETfTEbN0hb9XZCsEt4HNn1wd5Yg9FJhrNctkIigZBNUli9uMBNuVEZBcZA8Y9eg9E9qRN76xSgtvg5zHssb9cjh77KMlkFykFLATFDS05IivRyUIZA8TRNJsKafUud0YNGZB7Lp98RUG3wzn79uex1uFbTFq1pQ7MvsZD';  // Replace with your Meta API access token

        try {
            $client = HttpClient::create();

            $response = $client->request('POST', $apiEndpoint, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $accessToken,
                    'Content-Type' => 'application/json',
                ],
                'json' => $payload,
            ]);

            $statusCode = $response->getStatusCode();

            if ($statusCode === 200) {
                return $this->json(['message' => 'Message sent to WhatsApp!', 'recipient' => $payload['to']]);
            } else {
                return $this->json(['error' => 'Error sending message: ' . $response->getContent()], $statusCode);
            }
        } catch (\Exception $e) {
            return $this->json(['error' => 'Error sending message: ' . $e->getMessage()], 500);
        }
    }

}
