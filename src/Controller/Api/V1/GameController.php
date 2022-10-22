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
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;

/**
 * @Route("/api/v1", name="api_v1")
 */
class GameController extends AbstractController
{
    #################################################################################################
    ### Home view standard (List)
    #################################################################################################

     /**
     * List of games order by date
     * 
     * @Route("/games-by-dates", name="games_by_dates", methods={"GET"})
     */
    public function getGamesByDates(GameRepository $gameRepository): JsonResponse
    {
        // looklike findAll method but order by date and limit to up or egal than today
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
     * 
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
     * 
     * @Route("/types/{id}/games", name="games_by_type", methods={"GET"}, requirements={"id"="\d+"})
     */
    public function getGamesByType(Type $type = null, GameRepository $gameRepository): JsonResponse
    {
        // validate the Type ID sent in URL
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
     * 
     * @Route("/arenas/{id}/games", name="games_by_arena", methods={"GET"}, requirements={"id"="\d+"})
     */
    public function getGamesByArena(Arena $arena = null, GameRepository $gameRepository): JsonResponse
    {
        // validate the Arena ID sent in URL
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
     * 
     * @Route ("/teams/{id}/games", name="games_by_team", methods={"GET"}, requirements={"id"="\d+"})
     */
    public function getGamesByTeam(Team $team = null, GameRepository $gameRepository): JsonResponse
    {
        // validate the Team ID sent in URL
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
     * 
     * @Route("/categories/{id}/games", name="games_by_category", methods={"GET"}, requirements={"id"="\d+"})
     */
    public function getGamesByCategory(Category $category = null, GameRepository $gameRepository): JsonResponse
    {
        // validate the Category ID sent in URL
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
        // validate the Club ID sent in URL
        if(is_null($club)) {
            return $this->json(['error' => 'Category\'s ID not found !'], Response::HTTP_NOT_FOUND);
        }

        $games = $gameRepository->findGamesByClub($club->getId());

        return $this->json(['games' => $games], Response::HTTP_OK, [], [
            'groups' => 'games_collection'
        ]); 
    }

    /**
     * Get games by distance from user address (the user need to be connected)
     *
     * @Route("/games/distance", name="games_by_distance_from_user", methods={"GET"})
     */
    public function getGamesByDistanceFromUser(
        Request $request,
        GameRepository $gameRepository,
        GeolocationManager $geolocationManager
        )
    {
        //* params
        // get user geoloc (lng, lat) from the JWT
        /** @var User */
        $user = $this->getUser();

        // get the radius in km
        $radius = json_decode($request->getContent(), true)["radius"];

        //* algorithm
        // get all the games sort by date
        $games = $gameRepository->findBy([], ['date' => 'ASC']);

        // init array : games in range
        $games_in_range = [];

        // compare each games (arenas) address with user address
        foreach ($games as $game) {
            // calculate distance between user and game (arena)
            $distance = $geolocationManager->crowFliesDistance(
                $user->getLongitude(),
                $user->getLatitude(),
                $game->getArena()->getLongitude(),
                $game->getArena()->getLatitude()
            );

            // store the game if it's in range
            if ($distance <= $radius) {
                $games_in_range[] = $game;
                // $games_in_radius[] = $distance;
            }
        }

        //* response
        // TODO: add the distance in return and display it in game card (front app react)
        // return the games in range sort by date
        return $this->json(['games_in_range' => $games_in_range], Response::HTTP_OK, [], [
            'groups' => 'games_collection'
        ]); 
    }


    #################################################################################################
    ### Referee Engagement/disengagement (detail view)
    #################################################################################################

    /**
     * Get one game by Id
     * 
     * @Route("/games/{id}", name="game_by_id", methods={"GET"}, requirements={"id"="\d+"})
     */
    public function getGameById(Game $game = null): JsonResponse
    {
        // validate the Game ID sent in URL
        if(is_null($game)) {
            return $this->json(['error' => 'Game\'s ID not found !'], Response::HTTP_NOT_FOUND);
        }

        // response : return the Game
        return $this->json($game, Response::HTTP_OK, [], [
            'groups' => 'games_collection'
        ]);
    }

    #################################################################################################
    ### Managing's methods
    #################################################################################################


    /**
     * Add new game
     * 
     * @Route("/games", name="games_add", methods={"POST"})
     */
    public function addGame(
        Request $request,
        SerializerInterface $serializer,
        GameRepository $gameRepository,
        ValidatorInterface $validator
        ): JsonResponse
    {
        // get the new data from the request (JSON)
        $json = $request->getContent();
        $game = $serializer->deserialize($json, Game::class, 'json');

        // initialize the property createdAt
        $game->setCreatedAt(new \DateTimeImmutable('now'));

        // check the Assert (Entity's constraints)
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
        
        // if all the data are OK => save item in DB
        $gameRepository->add($game, true);

        // response : return the new Game object 
        return $this->json($game, Response::HTTP_CREATED, [], [
            'groups' => 'games_collection'
        ]);
    }

    /**
     * Toggle user (referee) on a game
     * 
     * @Route("/games/{id}", name="toggle_user_on_game", methods={"PATCH"}, requirements={"id"="\d+"})
     */
    public function toggleUserOnGame(
        Game $game = null, 
        GameRepository $gameRepository,
        ManagerRegistry $doctrine
        ): JsonResponse
    {
        // manage 404 error if the Game ID doesn't exist in DB
        if(is_null($game)) {
            return $this->json(['error' => 'Game\'s ID not found !'], Response::HTTP_NOT_FOUND);
        }
        
        // get all the Game's Users previous changes
        $previousUsers = $gameRepository->findAllRefByGame($game->getId());
        
        // get Users' ID in one level array
        $previousUsersID = [];
        for ($i=0; $i < count($previousUsers); $i++) { 
            $previousUsersID[] = $previousUsers[$i]['id'];
        }
        
        // get user from token
        $currentUser = $this->getUser();
        /**
         * @var User $currentUser
         */
        $currentUserID = $currentUser->getId();

        // toggle the engagement of a referee
        // Max users in each game = 2
        if (in_array($currentUserID, $previousUsersID)) {
            // the current User is already engage => he want to disengage
            $game->removeUser($currentUser);
        } elseif (count($previousUsersID) >= 2) {
            // there is already two referee (User) for this game, it's full
            return $this->json('You already have 2 referee for this match !', Response::HTTP_UNPROCESSABLE_ENTITY);
        } else {
            // the game need more referee AND the current User isn't already engage on it
            $game->addUser($currentUser);
        }
        
        // update the property updatedAt
        $game->setUpdatedAt(new \DateTimeImmutable('now'));
        
        // update DB
        $doctrine
                ->getManager()
                ->flush()
                ;

        // return the new Game object
        return $this->json($game, Response::HTTP_OK, [], [
            'groups' => 'games_collection'
        ]);
    }

    /**
     * Edit a game
     * 
     * @Route("/games/{id}/edit", name="games_edit", methods={"GET", "PUT"}, requirements={"id"="\d+"})
     */
    public function edit(
        Game $game = null,
        Request $request,
        SerializerInterface $serializer,
        ManagerRegistry $doctrine,
        ValidatorInterface $validator
        ): JsonResponse
    {
        // validate the Game ID sent in URL
        if(is_null($game)) {
            return $this->json(['error' => 'Game\'s ID not found !'], Response::HTTP_NOT_FOUND);
        }

        if($request->isMethod('put')) {

            // get the new data from the request (JSON)
            $json = $request->getContent();
            $game = $serializer->deserialize($json, Game::class, 'json', [AbstractNormalizer::OBJECT_TO_POPULATE => $game]);
            
            // update the property updatedAt
            $game->setUpdatedAt(new \DateTimeImmutable('now'));

            // check the Assert (Entity's constraints)
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

            // update DB
            $doctrine
                ->getManager()
                ->flush()
                ;
        }
        // response : return the actual object ("GET") or the new object ("PUT")
        return $this->json($game, Response::HTTP_OK, [], [
            'groups' => 'games_collection'
        ]);
    }

    /**
     * Delete a game
     * 
     * @Route("/games/{id}", name="games_delete", methods={"DELETE"}, requirements={"id"="\d+"})
     */
    public function delete(Game $game = null, GameRepository $gameRepository): JsonResponse
    {
        // validate the Game ID sent in URL
        if(is_null($game)) {
            return $this->json(['error' => 'Game\'s ID not found !'], Response::HTTP_NOT_FOUND);
        }
        
        // delete the Game
        $gameRepository->remove($game, true);

        // response : return OK code without content
        return $this->json(null, Response::HTTP_NO_CONTENT); 
       
    }

}
