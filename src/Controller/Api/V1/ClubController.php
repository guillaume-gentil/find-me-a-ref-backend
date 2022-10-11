<?php

namespace App\Controller\Api\V1;

use App\Entity\Club;
use App\Repository\ClubRepository;
use App\Service\GeolocationManager;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/api/v1", name="api_v1")
 */
class ClubController extends AbstractController
{
    /**
     * Get a list of all the Clubs
     * @Route("/clubs", name="clubs", methods={"GET"})
     */
    public function getClubs(ClubRepository $clubRepository): JsonResponse
    {
        $clubs = $clubRepository->findAll();

        // response : return all Clubs
        return $this->json(['clubs' => $clubs], Response::HTTP_OK, [], [
            'groups' => 'games_collection'
        ]);
    }

    /**
     * Get club by id
     * 
     * @Route("/clubs/{id}", name="clubs_by_id", methods={"GET"}, requirements={"id"="\d+"})
     */
    public function getClubById(Club $club = null): JsonResponse
    {
        // validate the Club ID sent in URL
        if(is_null($club)) {
            return $this->json(['error' => 'Club\'s ID not found !'], Response::HTTP_NOT_FOUND);
        }

        // response : return the Club
        return $this->json($club, Response::HTTP_OK, [], [
            'groups' => 'games_collection'
        ]);
    }

    /**
     * Add new club
     *
     * @Route("/clubs", name="clubs_add", methods={"POST"})
     */
    public function add(
        Request $request,
        SerializerInterface $serializer,
        ClubRepository $clubRepository,
        ValidatorInterface $validator,
        GeolocationManager $geolocationManager
    ): JsonResponse
    {
        // get the new data from the request (JSON)
        $json = $request->getContent();
        $club = $serializer->deserialize($json, Club::class, 'json');
        
        // for setting longitude and latitude use custom service from GeolocationManager
        $club->setLatitude($geolocationManager->useGeocoder($club->getAddress(), $club->getZipCode(), 'lat'));
        $club->setLongitude($geolocationManager->useGeocoder($club->getAddress(), $club->getZipCode(), 'lng'));

        // initialize the property createdAt
        $club->setCreatedAt(new \DateTimeImmutable('now'));

        // check the Assert (Entity's constraints)
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

        // if all the data are OK => save item in DB
        $clubRepository->add($club, true);

        // response : return the new Club object 
        return $this->json($club, Response::HTTP_OK, [], [
            'groups' => 'games_collection'
        ]);
    }

    /**
     * Edit a club
     *
     * @Route("/clubs/{id}/edit", name="clubs_edit", methods={"GET","PUT"}, requirements={"id"="\d+"})
     */
    public function edit(
        Club $club = null,
        Request $request,
        SerializerInterface $serializer,
        ManagerRegistry $doctrine,
        ValidatorInterface $validator,
        GeolocationManager $geolocationManager
    ): JsonResponse
    {
        // validate the Club ID sent in URL
        if(is_null($club)) {
            return $this->json(['error' => 'Type\'s ID not found !'], Response::HTTP_NOT_FOUND);
        }

        // get the current address from DB
        $previousAddress = $club->getAddress();

        if($request->isMethod('put')) {
            // get the new data from the request (JSON)
            $json = $request->getContent();
            $club = $serializer->deserialize($json, Club::class, 'json', [AbstractNormalizer::OBJECT_TO_POPULATE => $club]);

            // retreive from API the Geocode values only if the address change
            if($club->getAddress() != $previousAddress) {
                $club->setLatitude($geolocationManager->useGeocoder($club->getAddress(), $club->getZipCode(), 'lat'));
                $club->setLongitude($geolocationManager->useGeocoder($club->getAddress(), $club->getZipCode(), 'lng'));
            }

            // update the property updatedAt
            $club->setUpdatedAt(new \DateTimeImmutable('now'));

            // check the Assert (Entity's constraints)
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
            
            // if all data are OK => save changes in DB
            $doctrine
                ->getManager()
                ->flush()
                ;
        }

        // response : return the actual object ("GET") or the new object ("PUT")
        return $this->json($club, Response::HTTP_OK, [], [
            'groups' => 'games_collection'
        ]);
    }

    /**
     * Delete a club
     * @Route("/clubs/{id}", name="clubs_delete", methods={"DELETE"}, requirements={"id"="\d+"})
     */
    public function delete(Club $club =null, ClubRepository $clubRepository): JsonResponse
    {
        // validate the Club ID sent in URL
        if(is_null($club)) {
            return $this->json(['error' => 'Club\'s ID not found !'], Response::HTTP_NOT_FOUND);
        }

        // delete the Club
        $clubRepository->remove($club, true);

        // response : return OK code without content
        return $this->json(null, Response::HTTP_NO_CONTENT); 
    }

}
