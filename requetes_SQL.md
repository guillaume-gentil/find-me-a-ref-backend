# Liste des requêtes SQL du projet

## Find all games ordered by date: `findGamesOrderByDate`

```sql
SELECT *
FROM game
ORDER BY game.date
```

## Find all games ordered by number of users (=referee) : `findGamesOrderByNumberOfUser` => *EMERGENCY* filter

```sql
SELECT game.date, game.id, count(gu.user_id) as ref
FROM game
LEFT JOIN game_user as gu ON game.id = gu.game_id
GROUP BY game.id
ORDER BY ref, game.date
```

## Find all users (=referee) of the game `findAllRefByGame`

```sql
SELECT user_id
FROM game_user
WHERE game_id = :id
```

## Find all games by type : `/api/v1/types/{id}/games`

```sql
SELECT *
FROM game
JOIN type ON type.id = game.type_id
WHERE type.id = :id
```

## Find all games by team : `/api/v1/teams/{id}/games`

```sql
SELECT *
FROM game
JOIN game_team ON game_id = game.id
JOIN team ON team_id = team.id
WHERE team.id = :id
```

## Find all games by category : `/api/v1/category/{id}/games`

```sql
SELECT *
FROM game
JOIN game_team ON game_id = game.id
JOIN team ON team_id = team.id
JOIN category ON team.category_id = category.id
WHERE category.id = :id
```

## Find all games by arena : `/api/v1/arenas/{id}/games`

```sql
SELECT *
FROM game
JOIN arena ON arena.id = game.arena_id
WHERE arena.id = :id
```

## Find all games by club : `/api/v1/club/{id}/games`

```sql
SELECT *
FROM game
JOIN game_team ON game_id = game.id
JOIN team ON team_id = team.id
JOIN club ON team.club_id = club.id
WHERE club.id = :id
```