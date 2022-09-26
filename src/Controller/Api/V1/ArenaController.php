<?php

namespace App\Controller\Api\V1;

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
            'groups' => 'games_get_collection'
        ]);
    }
}
