<?php

namespace App\Controller\Api\V1;

use App\Entity\Club;
use App\Repository\ClubRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @route("/api/v1", name="api_v1")
 */
class ClubController extends AbstractController
{
    /**
     * @Route("/clubs", name="clubs", methods={"GET"})
     */
    public function getClubs(ClubRepository $clubRepository): JsonResponse
    {
        $clubs = $clubRepository->findAll();

        return $this->json(['clubs' => $clubs], Response::HTTP_OK, [], [
            'groups' => 'games_collection'
        ]);
    }

    /**
     * Get type by Id
     * @Route("/clubs/{id}/games", name="games_by_club", methods={"GET"}, requirements={"id"="\d+"})
     */
    public function getGamesByClub(Club $club = null): JsonResponse
    {
        if(is_null($club)) {
            return $this->json(['error' => 'Club\'s ID not found !'], Response::HTTP_NOT_FOUND);
        }

        return $this->json($club, Response::HTTP_OK, [], [
            'groups' => 'games_by_club'
        ]);   
    }
}
