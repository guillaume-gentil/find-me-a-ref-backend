<?php

namespace App\Controller\Api\V1;

use App\Entity\Arena;
use App\Repository\ArenaRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @route("/api/v1", name="api_v1")
 */
class ArenaController extends AbstractController
{
    /**
     * @Route("/arenas", name="arenas", methods={"GET"})
     */
    public function getArenas(ArenaRepository $arenaRepository): JsonResponse
    {
        $arenas = $arenaRepository->findAll();

        return $this->json(['arenas' => $arenas], Response::HTTP_OK, [], [
            'groups' => 'games_collection'
        ]);
    }

    /** 
     * Get arena by Id
     * @Route("/arenas/{id}/games", name="games_by_arena", methods={"GET"}, requirements={"id"="\d+"})
     */
    public function getGamesByArena(Arena $arena = null)
    {
        if(is_null($arena)) {
            return $this->json(['error' => 'Arena\'s ID not found !'], Response::HTTP_NOT_FOUND);
        }

        return $this->json($arena, Response::HTTP_OK, [], [
            'groups' => 'games_by_arena'
        ]); 
    }
}
