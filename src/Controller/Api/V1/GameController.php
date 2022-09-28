<?php

namespace App\Controller\Api\V1;

use App\Entity\Game;
use App\Repository\GameRepository;
use App\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;

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
    public function addGame(
        Request $request,
        SerializerInterface $serializer,
        ManagerRegistry $doctrine,
        ValidatorInterface $validator
        )
    {
        $json = $request->getContent();

        // Be careful to receive all the fields of the entity in the JSON file
        /* 
            {
                "date":"2022-12-11 10:30:00",
                "createdAt":"2022-10-28 00:00:00",
                "teams":[187,186],
                "users":[],
                "arena":122,
                "type":122
            }
        */
        // TODO create event for automatically add createdAt field.

        $game = $serializer->deserialize($json, Game::class, 'json');

        $errors = $validator->validate($game);

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
        $manager->persist($game);
        $manager->flush();

        return $this->json($game, Response::HTTP_CREATED, [], [
            'groups' => 'game_item'
        ]);
    }

    /**
     * Add user (referee) on a game
     * @Route("/games/{id}", name="add_user_on_game", methods={"PATCH"}, requirements={"id"="\d+"})
     */
    public function addUserOnGame(
        Game $game = null, 
        Request $request, 
        ManagerRegistry $doctrine, 
        GameRepository $gameRepository, 
        UserRepository $userRepository
        )
    {
        // TODO traduire les commentaires en anglais
        // manage 404 error
        if(is_null($game)) {
            return $this->json(['error' => 'Game\'s ID not found !'], Response::HTTP_NOT_FOUND);
        }
        
        // je transforme le contenu de la requete en tableau
        $content = $request->toArray();

        $userId = $content['user_id'];        

        // récupère dans un array les arbitres (User) présents sur le match
        $users_brut = $gameRepository->findAllRefByGame($game->getId());
        $users = [];

        // "met à plat" le tableau des arbitres
        for ($i=0; $i < count($users_brut); $i++) { 
            $users[] = $users_brut[$i]['id'];
        }

        // fait un toggle de l'engagement d'un arbitre : ajoute ou enlève un arbitre sur un match
        if (count($users) >= 2) {
            if (in_array($userId, $users)) {
                $game->removeUser($userRepository->find($userId));
            } else {
                return $this->json('You already have 2 referee for this match !', Response::HTTP_UNPROCESSABLE_ENTITY);
            }
        } elseif (count($users) < 2) {
            if (in_array($userId, $users)) {
                $game->removeUser($userRepository->find($userId));
            } else {
                $game->addUser($userRepository->find($userId));
            }
        }

        $game->setUpdatedAt(new \DateTimeImmutable('now'));

        $manager = $doctrine->getManager();
        $manager->flush();
        
        return $this->json($game, Response::HTTP_OK, [], [
            'groups' => 'game_item'
        ]);

    }
}
