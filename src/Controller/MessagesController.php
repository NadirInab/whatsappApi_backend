<?php

namespace App\Controller;

use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

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

    // Send a message to WhatsApp
    #[Route('/api/sendMessage', methods: ['POST'])]
    public function sendMessage(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        // =============================>

        // $payload = [
        //     "messaging_product" => "whatsapp",
        //     "recipient_type" => "individual",
        //     "to" => "212636740837",
        //     "type" => "template",
        //     "template" => [
        //         "name" => "imane",
        //         "language" => [
        //             "code" => "fr"
        //         ],
        //         "components" => [
        //             [
        //                 "type" => "header",
        //                 "parameters" => [
        //                     [
        //                         "type" => "image",
        //                         "image" => [
        //                             "link" => "https://play-lh.googleusercontent.com/lC50PAc7_DmaGZrE01f7jVPMTjoYqEzuC59D3zj77ZOk5HlKGybX1dOMyWrGvXMHf1Yr"
        //                         ]
        //                     ]
        //                 ]
        //             ]
        //         ]
        //     ]
        // ];

        $payload = [
            'messaging_product' => 'whatsapp',
            'to' => $data['to'],
            'type' => 'text',
            'text' => [
                'body' => $data['text']['body']
            ]
        ];


        $apiEndpoint =  "https://graph.facebook.com/v17.0/100206783144220/messages";
        $accessToken = "EAACP9wBzdvEBOyc8Rep52y19BZABdhYtckTMIOZBbwLQErjTvM227xWoiEZBmqazQM5pDzx1WOabhWxpyQxKRQcC7z9ldBHt8vZCm5R00j4X91Ux8vIuxxuO8GLTsRSIB0nvPZA9CfmKuVm389lsRlYdmiFHLHYcGnG70UswgJQ4QTeBxIulp2UliuHw7q4jWicdLALZBc7CefSsWlyj1MyRv3HygZD";

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

    // Receive messages from WhatsApp webhook
    // #[Route('/api/receiveMessage', methods: ['POST'])]
    // public function receiveMessage(Request $request): JsonResponse
    // {
    //     $data = json_decode($request->getContent(), true);
    //     return new JsonResponse($data, 200);
    // }

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

    //     return new JsonResponse('Invalid request method');
    // }


    #[Route('/api/webhooks', methods: ['GET', 'POST'])]
    public function getMessageResponse(Request $request): JsonResponse
    {

        $jsonContent = $request->getContent();
        $data = json_decode($jsonContent, true);
        error_log("Received WhatsApp Data Here !!!! :====>  " . print_r($data, true));    

        error_log("===========================> !!") ;
        $object = $data['object']; 
        $entry = $data['entry'][0]; 

        $id = $entry['id']; 

        error_log("here we are ". $id) ;

        $changes = $entry['changes'][0]; 
        $value = $changes['value']; 
        $messagingProduct = $value['messaging_product']; 

        $metadata = $value['metadata']; 
        $displayPhoneNumber = $metadata['display_phone_number'];
        $phoneNumberId = $metadata['phone_number_id'];

        $contacts = $value['contacts'][0]; 

        $profile = $contacts['profile']; 
        $name = $profile['name']; 
        error_log("here we are ". $name) ;

        $waId = $contacts['wa_id']; 

        $messages = $value['messages'][0];

        $from = $messages['from'];
        $id = $messages['id']; 
        $timestamp = date('m/d/Y H:i:s',$messages['timestamp']) ; 

        $text = $messages['text']; 
        $body = $text['body'];
        $type = $messages['type'];

        $responseData = [
            'object' => $object,
            'id' => $id,
            'messagingProduct' => $messagingProduct,
            'displayPhoneNumber' => $displayPhoneNumber,
            'phoneNumberId' => $phoneNumberId,
            'name' => $name,
            'waId' => $waId,
            'from' => $from,
            'messageId' => $id,
            'timestamp' => $timestamp,
            'messageText' => $body,
            'messageType' => $type,
        ];

        return new JsonResponse(['data' =>$responseData ], 200);

    }
}
