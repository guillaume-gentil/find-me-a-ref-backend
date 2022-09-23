<?php

namespace App\Controller\Api\V1;

use App\Entity\Arena;
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
     * get game's list
     * @Route("/games", name="games_get_collection", methods={"GET"})
     */
    public function getGamesCollection(GameRepository $gameRepository): JsonResponse
    {
        $games = $gameRepository->findAll();
        //for data in array to avoid JSON hijacking we send data response under this form ['games' => $games]
        return $this->json(['games' => $games], Response::HTTP_OK, [], [
            'groups' => 'games_get_collection'
        ]);
    }

    /**
     * list of games order by date
     * @Route("/games-by-dates", name="games_by_dates", methods={"GET"})
     */
    public function gamesByDates(GameRepository $gameRepository): JsonResponse
    {
        $games = $gameRepository->findByGameOrderedByDate();
        return $this->json(['games' => $games], Response::HTTP_OK, [], [
            'groups' => 'games_get_collection'
        ]);

    }

    /**
     * Get on game by Id
     * @Route("/games/{id}", name="games_get_item", methods={"GET"}, requirements={"id"="\d+"})
     */
    public function getGameItem(Game $game = null): JsonResponse
    {
        // gestion 404
        if(is_null($game)) {
            return $this->json(['error' => 'Game not found !'], Response::HTTP_NOT_FOUND);
        }

        return $this->json($game, Response::HTTP_OK, [], [
            'groups' => 'games_get_item'
        ]);
    }

    /**
     * Add new game
     * @Route("/games", name="games_add", methods={"POST"})
     */
    public function addGame(Request $request, SerializerInterface $serializer, ManagerRegistry $doctrine, ArenaRepository $arenaRepository, TypeRepository $typeRepository/*, ValidatorInterface $validator*/)
    {
        $json = $request->getContent();
        //dd($json);
        $game = $serializer->deserialize($json, Game::class, 'json');

        //Permet de récupérer le contenu sous forme de tableau affin d'extraire l'id de l'arena et du type pour les setter après.
        //impossible sans, juste avec le serializer on récupère qu'une entité vide à chaque fois pour Arena et Type. dans l'objet game
        $content = $request->toArray();
        $arenaId = $content['arenaId'] ?? -1;
        $typeId = $content['typeId'] ?? -1;

        $game->setArena($arenaRepository->find($arenaId));
        $game->setType($typeRepository->find($typeId));

        //dd($game);

        // à décommenté dès qu'on auras mis des @assets dans les entity pour contraindre les champs de validation
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
            'groups' => 'games_get_item'
        ]);
        
    }


}
