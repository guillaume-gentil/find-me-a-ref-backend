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
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

/**
 * @route("/api/v1", name="api_v1")
 */
class ArenaController extends AbstractController
{
    /**
     * Get a list of all the Arenas
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
     * Get arena by id
     * @Route("/arenas/{id}", name="arenas_by_id", methods={"GET"}, requirements={"id"="\d+"})
     * 
     * @return JsonResponse
     */
    public function getArneaById(Arena $arena = null): JsonResponse
    {
        if(is_null($arena)) {
            return $this->json(['error' => 'Arena\'s ID not found !'], Response::HTTP_NOT_FOUND);
        }

        return $this->json($arena, Response::HTTP_OK, [], [
            'groups' => 'games_collection'
        ]);
    }

    /** 
     * Add new Arena
     * @Route("/arenas", name="add_arena", methods={"POST"})
     */
    public function add(
        Request $request,
        SerializerInterface $serializer,
        ManagerRegistry $doctrine,
        ValidatorInterface $validator,
        GeolocationManager $geolocationManager
    ): JsonResponse
    {
        // get the new data from the request (JSON)
        $json = $request->getContent();
        $arena = $serializer->deserialize($json, Arena::class, 'json');

        // for setting longitude and latitude use custom service from GeolocationManager
        $arena->setLatitude($geolocationManager->useGeocoder($arena->getAddress(), 'lat'));
        $arena->setLongitude($geolocationManager->useGeocoder($arena->getAddress(), 'lng'));

        // initialize the property createdAt
        $arena->setCreatedAt(new \DateTimeImmutable('now'));

        // check the Assert (Entity's constraints)
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
        
        // if all the data are OK => save item in DB
        $manager = $doctrine->getManager();
        $manager->persist($arena);
        $manager->flush();

        // response : return the new Arena object 
        return $this->json($arena, Response::HTTP_CREATED, [], [
            'groups' => 'games_collection'
        ]);
    }

    /**
     * Edit an Arena
     *
     * @Route("/arenas/{id}/edit", name="arenas_edit", methods={"GET","PUT"}, requirements={"id"="\d+"})
     */
    public function edit(
        Arena $arena =null,
        Request $request,
        SerializerInterface $serializer,
        ManagerRegistry $doctrine,
        ValidatorInterface $validator,
        GeolocationManager $geolocationManager
    ): JsonResponse
    {
        // validate the Arena ID sent in URL
        if(is_null($arena)) {
            return $this->json(['error' => 'Arena\'s ID not found !'], Response::HTTP_NOT_FOUND);
        }
        
        // stock the old address from DB
        $previousAddress = $arena->getAddress();

        if($request->isMethod('put')) {
            
            // get the new data from the request (JSON)
            $json = $request->getContent();
            $arena= $serializer->deserialize($json, Arena::class, 'json', [AbstractNormalizer::OBJECT_TO_POPULATE => $arena]);

            // retreive from API the Geocode values only if the address change
            if($arena->getAddress() != $previousAddress) {
                $arena->setLongitude($geolocationManager->useGeocoder($arena->getAddress(), 'lng'));
                $arena->setLatitude($geolocationManager->useGeocoder($arena->getAddress(), 'lat'));
            }
            
            // update the property updatedAt
            $arena->setUpdatedAt(new \DateTimeImmutable('now'));
            
            // check the Assert (Entity's constraints)
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

            // if all data are OK => save changes in DB
            $doctrine
                ->getManager()
                ->flush()
                ;
        }

        // response : return the new Arena object
        return $this->json($arena, Response::HTTP_OK, [], [
            'groups' => 'games_collection'
        ]);
    }

    /**
     * Delete an Arena
     * 
     * @Route("/arenas/{id}", name="arenas_delete", methods={"DELETE"}, requirements={"id"="\d+"})
     *
     * @return JsonResponse
     */
    public function delete(Arena $arena =null, ArenaRepository $arenaRepository): JsonResponse
    {
        // validate the Arena ID sent in URL
        if(is_null($arena)) {
            return $this->json(['error' => 'Arena\'s ID not found !'], Response::HTTP_NOT_FOUND);
        }

        // delete the Arena
        $arenaRepository->remove($arena, true);

        // response : return OK code without content
        return $this->json(null, Response::HTTP_NO_CONTENT);
    }
}
