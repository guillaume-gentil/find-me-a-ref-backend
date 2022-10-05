<?php

namespace App\Controller\Api\V1;

use App\Entity\Arena;
use App\Repository\ArenaRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Service\GeolocationManager;

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
     * Add new Arena
     * @Route("/arenas", name="add_arena", methods={"POST"})
     */
    public function addArena(
        Request $request,
        SerializerInterface $serializer,
        ManagerRegistry $doctrine,
        ValidatorInterface $validator,
        GeolocationManager $geolocationManager
    ): JsonResponse
    {
        $json = $request->getContent();

        // Be careful to receive all the fields of the entity in the JSON file
        /* 
            {
                "name": "my-arena",
                "address": "147 rue de la chamberlière, 26000, valence, france",
                "zip_code": "26000",
            }
        */

        // TODO create SERVICE/event for automatically add createdAt field.
        $arena = $serializer->deserialize($json, Arena::class, 'json');

        // for setting longitude and latitude use custom service from GeolocationManager
        $arena->setLatitude($geolocationManager->useGeocoder($arena->getAddress(), 'lat'));
        $arena->setLongitude($geolocationManager->useGeocoder($arena->getAddress(), 'lng'));
        $arena->setCreatedAt(new \DateTimeImmutable('now'));

        $errors = $validator->validate($arena);

        if (count($errors) > 0) {
            $cleanErrors = [];
            /**
             * @var ConstraintViolation $error
             */
            foreach($errors as $error) {
                $property = $error->getPropertyPath();
                $message = $error->getMessage();
                $cleanErrors[$property][] = $message;
            }
            return $this->json($cleanErrors , Response::HTTP_UNPROCESSABLE_ENTITY );
        }

        $manager = $doctrine->getManager();
        $manager->persist($arena);
        $manager->flush();

        return $this->json($arena, Response::HTTP_CREATED, [], [
            'groups' => 'games_collection'
        ]);
    }
}
