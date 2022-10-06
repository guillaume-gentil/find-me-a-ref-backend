<?php

namespace App\Controller\Api\V1;

use App\Entity\Club;
use App\Entity\Game;
use App\Entity\Team;
use App\Entity\Type;
use App\Entity\User;
use App\Entity\Arena;
use App\Entity\Category;
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
    #################################################################################################
    ### Home view standard (List)
    #################################################################################################

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

    #################################################################################################
    ### Home view with emergency filter
    #################################################################################################

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

    #################################################################################################
    ### Home view with filters
    #################################################################################################

    /**
     * Get games by Type
     * @Route("/types/{id}/games", name="games_by_type", methods={"GET"}, requirements={"id"="\d+"})
     */
    public function getGamesByType(Type $type = null, GameRepository $gameRepository): JsonResponse
    {
        if(is_null($type)) {
            return $this->json(['error' => 'Type\'s ID not found !'], Response::HTTP_NOT_FOUND);
        }

        $games = $gameRepository->findGamesByType($type->getId());

        return $this->json(['games' => $games], Response::HTTP_OK, [], [
            'groups' => 'games_collection'
        ]); 
    }

    /**
     * Get games by Arena
     * @Route("/arenas/{id}/games", name="games_by_arena", methods={"GET"}, requirements={"id"="\d+"})
     */
    public function getGamesByArena(Arena $arena = null, GameRepository $gameRepository): JsonResponse
    {
        if(is_null($arena)) {
            return $this->json(['error' => 'Arena\'s ID not found !'], Response::HTTP_NOT_FOUND);
        }

        $games = $gameRepository->findGamesByArena($arena->getId());

        return $this->json(['games' => $games], Response::HTTP_OK, [], [
            'groups' => 'games_collection'
        ]); 
    }

    /**
     * Get games by Team
     * @Route ("/teams/{id}/games", name="games_by_team", methods={"GET"}, requirements={"id"="\d+"})
     */
    public function getGamesByTeam(Team $team = null, GameRepository $gameRepository): JsonResponse
    {
        if(is_null($team)) {
            return $this->json(['error' => 'Team\'s ID not found !'], Response::HTTP_NOT_FOUND);
        }

        $games = $gameRepository->findGamesByTeam($team->getId());

        return $this->json(['games' => $games], Response::HTTP_OK, [], [
            'groups' => 'games_collection'
        ]);
    }

    /**
     * Get games by Category
     * @Route("/categories/{id}/games", name="games_by_category", methods={"GET"}, requirements={"id"="\d+"})
     */
    public function getGamesByCategory(Category $category = null, GameRepository $gameRepository): JsonResponse
    {
        if(is_null($category)) {
            return $this->json(['error' => 'Category\'s ID not found !'], Response::HTTP_NOT_FOUND);
        }

        $games = $gameRepository->findGamesByCategory($category->getId());

        return $this->json(['games' => $games], Response::HTTP_OK, [], [
            'groups' => 'games_collection'
        ]); 
    }
    
    /**
     * Get games by Club
     * @Route("/clubs/{id}/games", name="games_by_club", methods={"GET"}, requirements={"id"="\d+"})
     */
    public function getGamesByClub(Club $club = null, GameRepository $gameRepository): JsonResponse
    {
        if(is_null($club)) {
            return $this->json(['error' => 'Category\'s ID not found !'], Response::HTTP_NOT_FOUND);
        }

        $games = $gameRepository->findGamesByClub($club->getId());

        return $this->json(['games' => $games], Response::HTTP_OK, [], [
            'groups' => 'games_collection'
        ]); 
    }

    #################################################################################################
    ### Referee Engagement/disengagement (detail view)
    #################################################################################################

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
            'groups' => 'games_collection'
        ]);
    }

    #################################################################################################
    ### Managing's methods
    #################################################################################################


    /**
     * Add new game
     * @Route("/games", name="games_add", methods={"POST"})
     */
    public function addGame(
        Request $request,
        SerializerInterface $serializer,
        ManagerRegistry $doctrine,
        ValidatorInterface $validator
        ): JsonResponse
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
        $game->setCreatedAt(new \DateTimeImmutable('now'));
        $manager = $doctrine->getManager();
        $manager->persist($game);
        $manager->flush();

        return $this->json($game, Response::HTTP_CREATED, [], [
            'groups' => 'games_collection'
        ]);
    }

    /**
     * Toggle user (referee) on a game
     * @Route("/games/{id}", name="toggle_user_on_game", methods={"PATCH"}, requirements={"id"="\d+"})
     */
    public function toggleUserOnGame(
        Game $game = null, 
        Request $request, 
        ManagerRegistry $doctrine,
        GameRepository $gameRepository,
        UserRepository $userRepository
        ): JsonResponse
    {
        // manage 404 error
        if(is_null($game)) {
            return $this->json(['error' => 'Game\'s ID not found !'], Response::HTTP_NOT_FOUND);
        }

        // TODO: il est préférable de décoder le token JWT envoyé par le Front (lors de la request) directement dans le back et non dans le front (ou bien les deux !)
        // decode the request content (JSON -> array)
        $content = $request->toArray();
        $userEmailFromJSON = $content['user_email'];   
        
        // find user by email receive by json file 
        $userFromJSON = $userRepository->findOneBy(array('email' => $userEmailFromJSON));
        $userId = $userFromJSON->getId();
  
        // get user from JWT for check
        $userFromJWT = $this->getUser();
        $userEmailFromJWT = $userFromJWT->getUserIdentifier();

        if($userEmailFromJSON === $userEmailFromJWT) {

            
            // get all the Game's Users with too many dimensions
            $users_brut = $gameRepository->findAllRefByGame($game->getId());
            
            // remove one level from the Game's Users array
            $users = [];
            for ($i=0; $i < count($users_brut); $i++) { 
                $users[] = $users_brut[$i]['id'];
            }
            
            // toggle the engagement of a referee
            // Max users in each game = 2
            // TODO: simplifier l'algorythme ci-dessous
            //* utiliser les objets et non les ID
            //* si (utilisateur courant dans le game)
                //* "l'arbitre se désengage"
            //* sinon si (2 utilisateurs)
                //* "impossible d'ajouter, match complet
            //* sinon
                //* "l'arbitre s'engage"
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
            //TODO: Fin de l'algorythme
            
            // TODO: make this action with a service
            $game->setUpdatedAt(new \DateTimeImmutable('now'));
    
            $manager = $doctrine->getManager();
            $manager->flush();
        } else {
            // this error occur when a hacker try to send a different email between JSON request and JWT
            return $this->json(['error' => 'Please, send a valid email'], Response::HTTP_BAD_REQUEST);
        }
        
        return $this->json($game, Response::HTTP_OK, [], [
            'groups' => 'games_collection'
        ]);
    }

    /**
     * Edit a game
     * @Route("/games/{id}/edit", name="games_edit", methods={"PUT"}, requirements={"id"="\d+"})
     */
    public function edit(
        Game $game = null,
        Request $request,
        SerializerInterface $serializer,
        ManagerRegistry $doctrine,
        ValidatorInterface $validator
        ): JsonResponse
    {
        // manage 404 error
        if(is_null($game)) {
            return $this->json(['error' => 'Game\'s ID not found !'], Response::HTTP_NOT_FOUND);
        }

        $json = $request->getContent();

        // Be careful to receive all the fields of the entity in the JSON file
        /* 
            {
                "date":"2022-12-11 10:30:00",
                "teams":[260,263],
                "users":[],
                "arena":176,
                "type":152
            }
        */

        $game = $serializer->deserialize($json, Game::class, 'json', [AbstractNormalizer::OBJECT_TO_POPULATE => $game]);
        //dd($game);

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
        $game->setUpdatedAt(new \DateTimeImmutable('now'));

        $manager = $doctrine->getManager();
        $manager->flush();

        return $this->json($game, Response::HTTP_OK, [], [
            'groups' => 'games_collection'
        ]);
    }

    /**
     * Delete a game
     * @Route("/games/{id}", name="games_delete", methods={"DELETE"}, requirements={"id"="\d+"})
     */
    public function delete(Game $game = null, GameRepository $gameRepository)
    {
        // manage 404 error
        if(is_null($game)) {
            return $this->json(['error' => 'Game\'s ID not found !'], Response::HTTP_NOT_FOUND);
        }


        $user = $this->getUser();
        $userRole = $user->getRoles();
        if (in_array("ROLE_ADMIN", $userRole)) {

            $gameRepository->remove($game, true);
            return $this->json(null, Response::HTTP_NO_CONTENT); 
        } else {
            return $this->json(['you don\'t have the rights to do this action'], Response::HTTP_FORBIDDEN);
        }
    }


}
