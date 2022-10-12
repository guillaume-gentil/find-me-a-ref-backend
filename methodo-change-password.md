# Méthodo : mot de passe perdu

1. sur la page d'acceuil un *utilisateur clique sur mot de passe perdu* (il faut qu'il remplisse son email dans l'input)

2. On récupère son email via l'input que le front envoie l'email à l'api

3. depuis l'api : on envoie un mail à l'utilisateur, celui-ci contient une URL lui permettant de màj son password (cette URL est contrôlé par un token ?)

4. avec le lien l'utilisateur arrive sur une page où renseigner un nouveau mot de passe

5. On set le nouveau mot de passe dans la bdd

6. on redirige l'utilisateur sur la page d'acceuil
