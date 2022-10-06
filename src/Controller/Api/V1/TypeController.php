<?php

namespace App\Controller\Api\V1;

use App\Entity\Type;
use App\Repository\GameRepository;
use App\Repository\TypeRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @route("/api/v1", name="api_v1")
 */
class TypeController extends AbstractController
{
    /**
     * @Route("/types", name="types", methods={"GET"})
     */
    public function getTypes(TypeRepository $typeRepository): JsonResponse
    {
        $types = $typeRepository->findAll();

        return $this->json(['types' => $types], Response::HTTP_OK, [], [
            'groups' => 'games_collection'
        ]);
    }

    /**
     * get type of game by id
     * @Route("/types/{id}", name="types_by_id", methods={"GET"} ,requirements={"id"="\d+"})
     *
     */
    public function getTypeById(Type $type = null ): JsonResponse
    {
        if(is_null($type)) {
            return $this->json(['error' => 'Type\'s ID not found !'], Response::HTTP_NOT_FOUND);
        }

        return $this->json($type, Response::HTTP_OK, [], [
            'groups' => 'games_collection'
        ]);
    }

}
