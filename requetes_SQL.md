# Liste des requêtes SQL du projet

## Find all games by type : `/api/v1/types/{id}/games`

```sql
SELECT *
FROM game
JOIN type ON type.id = game.type_id
WHERE type.id = 31
```
