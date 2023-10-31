<?php

namespace App\Controller;

use App\Websocket\MessageHandler;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MessagesController extends AbstractController
{

    protected $messageHandler;

    public function __construct(MessageHandler $messageHandler)
    {
        $this->messageHandler = $messageHandler;
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
        $apiEndpoint =  "https://graph.facebook.com/v17.0/100206783144220/messages";
        $accessToken = "EAACP9wBzdvEBOZB1iwDL7HmjOAkD1gb6ZAxjemglalOe1q0Rh7UHXtNXnbgzaAI1E0H2no1OuFWBUVqzbvG6B6zj9gdwO67AWC1g7HM9f07EwFTwuQNyRhnWXzZA5O4puOqtVlowgRP53MD4RLunzbKzUuyuMwXW0udhn2k2jfUXuv364cBTjNfVD8GugDW7FnyjBOb3Nfz5kJVoPS6AMMtVZBsZD";

        try {
            $client = HttpClient::create();

            $response = $client->request('POST', $apiEndpoint, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $accessToken,
                    'Content-Type' => 'application/json',
                ],
                'json' => $payload,
            ]);
            $whatsappResponseContent = json_decode($response->getContent(), true);

            return new JsonResponse($whatsappResponseContent, 200, [
                'Content-Type' => 'application/json',
            ]);
        } catch (\Exception $e) {
            return $this->json(['error' => 'Error sending message: ' . $e->getMessage()], 500);
        }
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
    public function getMessageResponse(Request $request, HubInterface $hub): JsonResponse
    {

        $jsonContent = $request->getContent();
        $data = json_decode($jsonContent, true);
        error_log("Received WhatsApp Data Here !!!! :====>  " . print_r($data, true));

        // if (!isset($data['messages'])) {
        //     error_log("Received WhatsApp Data Here !!!! :====>  " . print_r($data, true));    

        //     error_log("===========================> !!");

        //     $object = $data['object'];
        //     $entry = $data['entry'][0];
        //     $id = $entry['id'];

        //     if (isset($entry['changes'][0])) {
        //         $changes = $entry['changes'][0];
        //         $value = $changes['value'];

        //         if (isset($value['messaging_product'])) {
        //             $messagingProduct = $value['messaging_product'];

        //             if (isset($value['metadata'])) {
        //                 $metadata = $value['metadata'];

        //                 if (isset($metadata['display_phone_number'])) {
        //                     $displayPhoneNumber = $metadata['display_phone_number'];
        //                 }
        //                 if (isset($metadata['phone_number_id'])) {
        //                     $phoneNumberId = $metadata['phone_number_id'];
        //                 }
        //             }

        //             if (isset($value['contacts'][0])) {
        //                 $contacts = $value['contacts'][0];

        //                 if (isset($contacts['profile'])) {
        //                     $profile = $contacts['profile'];

        //                     if (isset($profile['name'])) {
        //                         $name = "Nadir";
        //                     }
        //                 }

        //                 if (isset($contacts['wa_id'])) {
        //                     $waId = $contacts['wa_id'];
        //                 }
        //             }

        //             if (isset($value['messages'][0])) {
        //                 $messages = $value['messages'][0];

        //                 if (isset($messages['from'])) {
        //                     $from = $messages['from'];
        //                 }
        //                 if (isset($messages['id'])) {
        //                     $id = $messages['id'];
        //                 }
        //                 if (isset($messages['timestamp'])) {
        //                     $timestamp = date('m/d/Y H:i:s', $messages['timestamp']);
        //                 }
        //                 if (isset($messages['text']) && isset($messages['text']['body'])) {
        //                     $text = $messages['text'];
        //                     $body = $text['body'];
        //                 }
        //                 if (isset($messages['type'])) {
        //                     $type = $messages['type'];
        //                 }
        //             }
        //         }
        //     }}

        // $responseData = [
        //     'object' => $object,
        //     'id' => $id,
        //     'messagingProduct' => $messagingProduct,
        //     'displayPhoneNumber' => $displayPhoneNumber,
        //     'phoneNumberId' => $phoneNumberId,
        //     'name' => $name,
        //     'waId' => $waId,
        //     'from' => $from,
        //     'messageId' => $id,
        //     'timestamp' => $timestamp,
        //     'messageText' => $body,
        //     'messageType' => $type,
        // ];

        $responseData = [
            'object' => "nadir",
            'id' => 1234,
            'messagingProduct' => "whatsapp",
            'displayPhoneNumber' => "212636740837",
            'phoneNumberId' => 1234,
            'name' => "nadir",
            'waId' => 1233,
            'from' => "test",
            'messageId' => 1234,
            'timestamp' => 123456787,
            'messageText' => "message seconde!!",
            'messageType' => "type",
        ];

        $update = new Update(
            'http://127.0.0.1:5173/',
            json_encode(['status' => 'Here we are !!!'])
        );

        $hub->publish($update);

        return new JsonResponse('published!');
    }
}
