<?php

namespace App\Controller\Api\V1;

use App\Entity\Game;
use App\Repository\ArenaRepository;
use App\Repository\GameRepository;
use App\Repository\TypeRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @route("/api/v1", name="api_v1")
 */
class GameController extends AbstractController
{
    /**
     * Get game's list
     * @Route("/games", name="games_collection", methods={"GET"})
     */
    public function getGamesCollection(GameRepository $gameRepository): JsonResponse
    {
        $games = $gameRepository->findAll();

        // for data in array to avoid JSON hijacking we send data response under this form ['games' => $games]
        return $this->json(['games' => $games], Response::HTTP_OK, [], [
            'groups' => 'games_collection'
        ]);
    }

    /**
     * List of games order by date
     * @Route("/games-by-dates", name="games_by_dates", methods={"GET"})
     */
    public function getGamesByDates(GameRepository $gameRepository): JsonResponse
    {
        $games = $gameRepository->findGamesOrderByDate();

        return $this->json(['games' => $games], Response::HTTP_OK, [], [
            'groups' => 'games_collection'
        ]);

    }

    /**
     * Get games order by number of users (referee)
     * @Route("/games-by-users", name="games_by_users", methods={"GET"})
     */
    public function getGamesByUsers(GameRepository $gameRepository): JsonResponse
    {
        $games = $gameRepository->findGamesOrderByNumberOfUser();

        return $this->json(['games' => $games], Response::HTTP_OK, [], [
            'groups' => 'games_collection'
        ]);

    }

    /**
     * Get one game by Id
     * @Route("/games/{id}", name="game_by_id", methods={"GET"}, requirements={"id"="\d+"})
     */
    public function getGameById(Game $game = null): JsonResponse
    {
        // manage 404 error
        if(is_null($game)) {
            return $this->json(['error' => 'Game\'s ID not found !'], Response::HTTP_NOT_FOUND);
        }

        return $this->json($game, Response::HTTP_OK, [], [
            'groups' => 'game_item'
        ]);
    }

    /**
     * Add new game
     * @Route("/games", name="games_add", methods={"POST"})
     */
    public function addGame(Request $request, SerializerInterface $serializer, ManagerRegistry $doctrine, ArenaRepository $arenaRepository, TypeRepository $typeRepository/*, ValidatorInterface $validator*/)
    {
        $json = $request->getContent();
        
        $game = $serializer->deserialize($json, Game::class, 'json');

        //TODO: why Arena and Type are empty ? Check how to improve loading of arena and type entities (without arenaId and typeId)
        // Retrieve the content as an array to extract the arena and type id for setter after.
        // Transform object into array
        $content = $request->toArray();

        $arenaId = $content['arenaId'] ?? -1;
        $game->setArena($arenaRepository->find($arenaId));
        
        $typeId = $content['typeId'] ?? -1;
        $game->setType($typeRepository->find($typeId));

        // à décommenté dès qu'on auras mis des @asserts dans les entity pour contraindre les champs de validation
        //$errors = $validator->validate($game);
        //dd($errors);
        /*if (count($errors) > 0) {
            $cleanErrors = [];
            /**
             * @var ConstraintViolation $error
             */
            /* foreach($errors as $error) {
                $property = $error->getPropertyPath();
                $message = $error->getMessage();
                $cleanErrors[$property][] = $message;
            }
            return $this->json($cleanErrors , Response::HTTP_UNPROCESSABLE_ENTITY );
        }  */

        $manager = $doctrine->getManager();
        $manager->persist($game);
        
        $manager->flush();

        return $this->json($game, Response::HTTP_CREATED, [], [
            'groups' => 'game_item'
        ]);
    }
}
