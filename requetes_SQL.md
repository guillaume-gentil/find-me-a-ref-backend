# Liste des requêtes SQL du projet

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
