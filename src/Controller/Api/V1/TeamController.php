<?php

namespace App\Controller\Api\V1;

use App\Entity\Team;
use App\Repository\TeamRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @route("/api/v1", name="api_v1")
 */
class TeamController extends AbstractController
{
    /**
     * @Route("/teams", name="teams", methods={"GET"})
     */
    public function getTeams(TeamRepository $teamRepository): JsonResponse
    {
        $teams = $teamRepository->findAll();

        return $this->json(['teams' => $teams], Response::HTTP_OK, [], [
            'groups' => 'games_collection'
        ]);
    }

    /**
     * Get team by Id
     * @Route ("/teams/{id}/games", name="games_by_team", methods={"GET"}, requirements={"id"="\d+"})
     */
    public function getGamesByTeam(Team $team = null)
    {
        if(is_null($team)) {
            return $this->json(['error' => 'Team\'s ID not found !'], Response::HTTP_NOT_FOUND);
        }

        return $this->json($team, Response::HTTP_OK, [], [
            'groups' => 'games_by_team'
        ]);
    }
}
