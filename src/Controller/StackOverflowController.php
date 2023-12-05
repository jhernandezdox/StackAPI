<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\Request;

class StackOverflowController extends AbstractController
{
    /**
     * @Route("/stack-overflow-questions", name="stack_overflow_questions")
     */
    public function getStackOverflowQuestions(
        Request $request,
        SerializerInterface $serializer
    ): JsonResponse {
        $httpClient = HttpClient::create();
        $apiUrl = 'https://api.stackexchange.com/2.3/questions';

        // Get parameters from the request
        $tag = $request->query->get('tag');
        $fromDate = $request->query->get('fromdate');
        $toDate = $request->query->get('todate');

        // Define the default parameters
        $parameters = [
            'order' => 'desc',
            'sort' => 'activity',
            'tagged' => $tag,
            'site' => 'stackoverflow',
        ];

        // Add fromDate and toDate if present
        if ($fromDate) {
            $parameters['fromdate'] = strtotime($fromDate);
        }

        if ($toDate) {
            $parameters['todate'] = strtotime($toDate);
        }

        // Make the request and get the response
        $response = $httpClient->request('GET', $apiUrl, ['query' => $parameters]);
        $data = $response->toArray();

        // Convert the response data to JSON
        $jsonResponse = $serializer->serialize($data, 'json');

        // Create a JsonResponse and return it
        return new JsonResponse($jsonResponse, 200, [], true);
    }
}
