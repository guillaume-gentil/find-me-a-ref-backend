<?php

namespace App\Controller\Api\V1;

use App\Entity\Team;
use App\Repository\TeamRepository;
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
     * Get team by id
     *
     * @Route("/teams/{id}", name="teams_by_id", methods={"GET"} ,requirements={"id"="\d+"})
     * @return JsonResponse
     */
    public function getTeamById(Team $team =null): JsonResponse
    {
        if(is_null($team)) {
            return $this->json(['error' => 'Team\'s ID not found !'], Response::HTTP_NOT_FOUND);
        }

        return $this->json($team, Response::HTTP_OK, [], [
            'groups' => 'games_collection'
        ]);
    }

    /**
     * Add new team
     * @Route("/teams", name="teams_add", methods={"POST"})
     * 
     *@return JsonResponse
     */
    public function add(
        Request $request,
        SerializerInterface $serializer,
        TeamRepository $teamRepository,
        ValidatorInterface $validator
    ): JsonResponse
    {
        // get the new data from the request (JSON)
        $json = $request->getContent();
        $team = $serializer->deserialize($json, Team::class, 'json');

        // initialize the property createdAt
        $team->setCreatedAt(new \DateTimeImmutable('now'));

        // check the Assert (Entity's constraints)
        $errors = $validator->validate($team);
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
        $teamRepository->add($team, true);

        // response : return the new Team object 
        return $this->json($team, Response::HTTP_OK, [], [
            'groups' => 'games_collection'
        ]);
    }

    /**
     * Edit a team
     * 
     * @Route("/teams/{id}/edit", name="teams_edit", methods={"GET","PUT"}, requirements={"id"="\d+"})
     */
    public function edit(
        Team $team =null,
        Request $request,
        SerializerInterface $serializer,
        ManagerRegistry $doctrine,
        ValidatorInterface $validator
    ): JsonResponse
    {
        // validate the Team ID sent in URL
        if(is_null($team)) {
            return $this->json(['error' => 'Team\'s ID not found !'], Response::HTTP_NOT_FOUND);
        }

        // do action only if the HTTP method of the request is PUT (= the user request an update)
        if($request->isMethod('put')) {
            
            // get the new data from the request (JSON)
            $json = $request->getContent();
            $team = $serializer->deserialize($json, Team::class, 'json', [AbstractNormalizer::OBJECT_TO_POPULATE => $team]);

            // update the property updatedAt
            $team->setUpdatedAt(new \DateTimeImmutable('now'));

            // check the Assert (Entity's constraints)
            $errors = $validator->validate($team);
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
            
            // if all the data are OK => save changes in DB
            $doctrine
                ->getManager()
                ->flush()
                ;
        }

        // response : return the new Team object 
        return $this->json($team, Response::HTTP_OK, [], [
            'groups' => 'games_collection'
        ]);
    }

    /**
     * Delete a team
     *
     * @Route("/teams/{id}", name="teams_delete", methods={"DELETE"}, requirements={"id"="\d+"})
     * @return JsonResponse
     */
    public function delete(Team $team =null, TeamRepository $teamRepository): JsonResponse
    {
        if(is_null($team)) {
            return $this->json(['error' => 'Team\'s ID not found !'], Response::HTTP_NOT_FOUND);
        }

        $teamRepository->remove($team, true);
        return $this->json(null, Response::HTTP_NO_CONTENT);
    }
    
}
