<?php

namespace App\Controller\Api\V1;

use App\Entity\Club;
use App\Repository\ClubRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use App\Service\GeolocationManager;

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
     * get club by id
     * 
     * @Route("/clubs/{id}", name="clubs-by-id", methods={"GET"} ,requirements={"id"="\d+"})
     *
     * @return JsonResponse
     */
    public function getClubById(Club $club = null): JsonResponse
    {
        if(is_null($club)) {
            return $this->json(['error' => 'Club\'s ID not found !'], Response::HTTP_NOT_FOUND);
        }

        return $this->json($club, Response::HTTP_OK, [], [
            'groups' => 'games_collection'
        ]);
    }

    /**
     * Add new club
     *
     * @Route("/clubs", name="clubs_add", methods={"POST"})
     * @return JsonResponse
     */
    public function add(
        Request $request,
        SerializerInterface $serializer,
        ClubRepository $clubRepository,
        ValidatorInterface $validator,
        GeolocationManager $geolocationManager
    ): JsonResponse
    {
        $json = $request->getContent();
        $club = $serializer->deserialize($json, Club::class, 'json');
        
        $club->setLongitude($geolocationManager->useGeocoder($club->getAddress(), 'lng'));
        $club->setLatitude($geolocationManager->useGeocoder($club->getAddress(), 'lat'));
        $club->setCreatedAt(new \DateTimeImmutable('now'));

        $errors = $validator->validate($club);
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

        $clubRepository->add($club, true);

        return $this->json($club, Response::HTTP_OK, [], [
            'groups' => 'games_collection'
        ]);

    }

}
