<?php 

namespace App\Services ;

use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;


class MessagesService {
    protected $test ; 

    public function __construct()
    {
        $this->test = "test" ;
    }

    public function handleMessagesSending ($data){
        $payload = [
            'messaging_product' => 'whatsapp',
            'to' => $data['to'],
            'type' => 'text',
            'text' => [
                'body' => $data['text']['body']
            ]
        ];
        $apiEndpoint =  "https://graph.facebook.com/v17.0/123/messages"; //
        $accessToken = "token"; 

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
            // return $this->json(['error' => 'Error sending message: ' . $e->getMessage()], 500);
        }
    }

    public function handleWhatAppResponse($data){
           
        error_log("Received WhatsApp Data Here !!!! :====>  " . print_r($data, true));
        error_log("==================================> start ");
         if (array_key_exists('messages', $data)) {
            $messagesData = $data['messages'][0];
            $responseData = [
                'object' => $data['object'],
                'id' => $data['entry'][0]['id'],
                'messagingProduct' => $messagesData['value']['messaging_product'],
                'displayPhoneNumber' => $messagesData['value']['metadata']['display_phone_number'],
                'phoneNumberId' => $messagesData['value']['metadata']['phone_number_id'],
                'name' => $messagesData['value']['contacts'][0]['profile']['name'],
                'waId' => $messagesData['value']['contacts'][0]['wa_id'],
                'from' => $messagesData['value']['messages'][0]['from'],
                'messageId' => $messagesData['value']['messages'][0]['id'],
                'timestamp' => date('m/d/Y H:i:s', $messagesData['value']['messages'][0]['timestamp']),
                'messageText' => $messagesData['value']['messages'][0]['text']['body'],
                'messageType' => $messagesData['value']['messages'][0]['type'],
            ];
    
            // Extract data from the second array structure
            $object2 = $data['entry'][0]['changes'][0]['value']['messaging_product'];
            $displayPhoneNumber2 = $data['entry'][0]['changes'][0]['value']['metadata']['display_phone_number'];
            $phoneNumberId2 = $data['entry'][0]['changes'][0]['value']['metadata']['phone_number_id'];
            $status2 = $data['entry'][0]['changes'][0]['value']['statuses'][0]['status'];
            $timestamp2 = date('m/d/Y H:i:s', $data['entry'][0]['changes'][0]['value']['statuses'][0]['timestamp']);
    
            // Combine data from both structures into one response
            $responseData['object2'] = $data['object'];
            $responseData['messagingProduct2'] = $object2;
            $responseData['displayPhoneNumber2'] = $displayPhoneNumber2;
            $responseData['phoneNumberId2'] = $phoneNumberId2;
            $responseData['status2'] = $status2;
            $responseData['timestamp2'] = $timestamp2;
    
            return $this->sse($responseData);
        } else {
            $message = "This is a streamed message.";

            $callback = function () use ($message) {
                echo $message;
            };
        
            $response = new StreamedResponse($callback);
            $response->headers->set('Content-Type', 'text/plain');
            $response->headers->set('Content-Disposition', 'inline; filename="message.txt"');
        
            return $response;
        }
    }

    public function sse($data): StreamedResponse
    {
        error_log("here data is in see ".$data) ;
        $response = new StreamedResponse() ;
        $response->headers->set('Content-Type', 'text/event-stream');
        $response->headers->set('Cache-Control', 'no-cache');
        $response->headers->set('Connection', 'keep-alive');
        $data = $data ;
        $callback = function() use ($data)  {
            while (true) {
                $responseData = [
                    'message' => $data,
                    'timestamp' => time(),
                ];
                echo "data: " . json_encode($responseData) . "\n\n";
                ob_flush();
                flush();
                sleep(1); 
            }
        };

        $response->setCallback($callback);
        return $response;
    }

}