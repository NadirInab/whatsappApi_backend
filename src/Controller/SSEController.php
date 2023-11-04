<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SSEController extends AbstractController
{
    /**
     * @Route("/sse/whatsapp", name="sse_whatsapp")
     */

    public function whatsapp(Request $request)
    {
        $response = new Response('', Response::HTTP_OK, ['Content-Type' => 'text/event-stream']);
        $response->headers->set('Cache-Control', 'no-cache');

        $response->sendHeaders();

        while ($request) {
            $messages = [];

            foreach ($messages as $message) {
                $response->setContent('event: message' . PHP_EOL . 'data: ' . json_encode($message) . PHP_EOL . PHP_EOL);
                $response->send();
            }

            flush();
        }

        return $response;
    }
}
