<?php

namespace App\Repository;

use App\Entity\Game;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Validator\Constraints\Count;

/**
 * @extends ServiceEntityRepository<Game>
 *
 * @method Game|null find($id, $lockMode = null, $lockVersion = null)
 * @method Game|null findOneBy(array $criteria, array $orderBy = null)
 * @method Game[]    findAll()
 * @method Game[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GameRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Game::class);
    }

    public function add(Game $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Game $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    
    #################################################################################################
    ### Home view with emergency filter
    #################################################################################################
    
    public function findGamesOrderByNumberOfUser()
    {
        /*
            SELECT game.date, game.id, count(gu.user_id) as ref
            FROM game
            LEFT JOIN game_user as gu ON game.id = gu.game_id
            GROUP BY game.id
            ORDER BY ref, game.date
        */

        $em = $this->getEntityManager();

        // use LEFT JOIN to include values : `count(u.id) = 0`
        $query = $em->createQuery(
            "SELECT g
            FROM App\Entity\Game g
            LEFT JOIN g.users u
            GROUP BY g.id
            ORDER BY count(u.id), g.date"
        );
        
        return $query->getResult();
    }
    
    #################################################################################################
    ### Home view with filters
    #################################################################################################
    
    public function findGamesOrderByDate()
    {
        return $this->createQueryBuilder('g')
            ->orderBy('g.date')
            ->getQuery()
            ->getResult();
    }

    public function findGamesByType(int $typeId)
    {
        /*
            SELECT *
            FROM game
            JOIN type ON type.id = game.type_id
            WHERE type.id = 31
            ORDER BY game.date
        */

        $em = $this->getEntityManager();

        $query = $em->createQuery(
            "SELECT g
            FROM App\Entity\Game g
            WHERE g.type = :id
            ORDER BY g.date"
        )->setParameter('id', $typeId);
        
        return $query->getResult();
    }

    public function findGamesByArena(int $arenaId)
    {
        /*
            SELECT *
            FROM game
            JOIN arena ON arena.id = game.arena_id
            WHERE arena.id = :id
            ORDER BY game.date
        */

        $em = $this->getEntityManager();

        $query = $em->createQuery(
            "SELECT g
            FROM App\Entity\Game g
            WHERE g.arena = :id
            ORDER BY g.date"
        )->setParameter('id', $arenaId);
        
        return $query->getResult();
    }

    public function findGamesByTeam(int $teamId)
    {
        /*
            SELECT *
            FROM game
            JOIN game_team ON game_id = game.id
            JOIN team ON team_id = team.id
            WHERE team.id = 76
            ORDER BY game.date
        */

        $em = $this->getEntityManager();

        // Notice : JOIN == INNER JOIN
        $query = $em->createQuery(
            "SELECT g
            FROM App\Entity\Game g
            JOIN g.teams t
            WITH t.id = :id
            ORDER BY g.date"
        )->setParameter('id', $teamId);
        
        return $query->getResult();
    }

    public function findGamesByCategory(int $categoryId)
    {
        /*
            SELECT *
            FROM game
            JOIN game_team ON game_id = game.id
            JOIN team ON team_id = team.id
            JOIN category ON team.category_id = category.id
            WHERE category.id = :id
            ORDER BY game.date
        */

        $em = $this->getEntityManager();

        $query = $em->createQuery(
            "SELECT g
            FROM App\Entity\Game g
            JOIN g.teams t
            WITH t.category = :id
            ORDER BY g.date"
        )->setParameter('id', $categoryId);
        
        return $query->getResult();
    }

    public function findGamesByClub(int $clubId)
    {
        /*
            SELECT *
            FROM game
            JOIN game_team ON game_id = game.id
            JOIN team ON team_id = team.id
            JOIN club ON team.club_id = club.id
            WHERE club.id = :id
            ORDER BY game.date
        */

        $em = $this->getEntityManager();

        $query = $em->createQuery(
            "SELECT g
            FROM App\Entity\Game g
            JOIN g.teams t
            WITH t.club = :id
            ORDER BY g.date"
        )->setParameter('id', $clubId);
        
        return $query->getResult();
    }

    #################################################################################################
    ### Referee Engagement/disengagement (detail view)
    #################################################################################################

    public function findAllRefByGame(int $game_id)
    {
        /* 
            SELECT user_id
            FROM game_user
            WHERE game_id = 43
        */

        $em = $this->getEntityManager();

        $query = $em->createQuery(
            "SELECT u.id
            FROM App\Entity\Game g
            JOIN g.users u
            WITH g.id = :id"
        )->setParameter('id', $game_id);
        
        return $query->getResult();
    }

}
