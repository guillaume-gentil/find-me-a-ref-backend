<?php

namespace App\Controller\Api\V1;

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
}
