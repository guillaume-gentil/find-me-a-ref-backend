<?php

namespace App\Controller\Api\V1;

use App\Repository\GameRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @route("/api/v1", name="api_v1")
 */
class GameController extends AbstractController
{
    /**
     * get game's list
     * @Route("/games", name="games_get_collection", methods={"GET"})
     */
    public function getGamesCollection(GameRepository $gameRepository): JsonResponse
    {
        $games = $gameRepository->findAll();
        //for data in array to avoid JSON hijacking we send data response under this form ['games' => $games]
        return $this->json(['games' => $games], Response::HTTP_OK, [], [
            'groups' => 'games_get_collection'
        ]);
    }
}
