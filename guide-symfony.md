# Liste des commandes Symfony utiles au projet Find Me A Ref

## Installer un projet Symfo skeleton (seulement la base)

Ce mettre dans le dossier voulu et via composer taper:

`composer create-project symfony/skeleton mon-sous-dossier`

Il est possible de garder le projet dans le sous dossier ou de le transferer dans le dossier racine.

La commande à taper pour déplacer l'ensemble des fichiers du sous dossier dans le dossier racine :

`mv mon-sous-dossier/* mon-sous-dossier/.* ./`
Le terminal de cde met une erreur met tous se passe correctement.
`rmdir mon-sous-dossier` pour supprimer le sous dossier vide.

## mise en place apachepack

- installer apache-pack

```bash
composer require symfony/apache-pack
> y  # recipes
```

- lancer le serveur

```bash
php -S 0.0.0.0:8000 -t public
```

## installation des composants utiles pour une API

- activer les annotations (@Route)

```bash
composer require doctrine/annotations 
```

## Pour faire toutes les commande make:Controller, make:Model etc

```bash
composer require --dev symfony/maker-bundle

# --dev permet l'installation seulement sur l'environnement de dev et pas en prod.
```

## Installer la toolbar

```bash
composer require --dev symfony/profiler-pack

composer require symfony/debug-bundle
```

## Pour créer des fausses données de dev

```bash
composer require --dev orm-fixtures
> n

composer require fakerphp/faker  

# voir doc : fakerphp.github.io
```

## Permet d'avoir une communication MVC dans notre projet

Sans lui, le CRUD marche pas (en gros) permet de faire Browse, Read, Edit, Add, Delete avec la commande `php bin/console make:crud`

```bash
composer require sensio/framework-extra-bundle
```

## pour passer d'un objet à du JSON

```bash
composer require symfony/serializer
```

dans l'entitée rajouter un use `use Symfony\Component\Serializer\Annotation\Groups;`
dans les dockblocks de chaque propriété qu'on veux faire passer en API on rajoute une annotation
`@Groups({"movies_get_collection"})` pour la route movies_get_collection de notre controller api/movie

Dans la méthode du controller dans le return du content on rajoute groups => nom_du_group
ce qui permet de dire qu'on veux récupérer que ça de l'entitée movie pour cette méthode
le premier [ ] vide correspond aux paramètres du header

```php
// MovieController.php
// [...]

public function getMoviesCollection(MovieRepository $movieRepository): JsonResponse
{
    // [...]

    return $this->json($movies, Response::HTTP_OK, [], [
        'groups' => 'movies_get_collection'
    ]);
}
```

## bundle JWT pour sécuriser l'authentification

- installer le composant JWT

```bash
composer require lexik/jwt-authentication-bundle
```

- générer une paire de clés privée + publique

```bash
bin/console lexik:jwt:generate-keypair
```

config/jwt/private.pem et public.pem

dans `.env` on passe `JWT_PASSPHRASE` en `.env.local` pour être sur de pas le divulger

ensuite dans config/package/security.yaml
**JUSTE** sous firewalls:

```yaml
# Firewalls pour API avec JWT
        login:
            pattern: ^/api/v1/login
            stateless: true
            json_login:
                check_path: /api/v1/login_check
                success_handler: lexik_jwt_authentication.handler.authentication_success
                failure_handler: lexik_jwt_authentication.handler.authentication_failure
        api:
            pattern:   ^/api/v1
            stateless: true
            jwt: ~
```

puis dans
config/routes.yaml

```yaml
# API JWT Login
api_login_check:
    path: /api/v1/login_check
```

dans security.yaml
au niveau `access_control:`

```yaml
# ACL API
        # pour utiliser l'API, il faut être au minimum authentifié
        - { path: ^/api/v1, roles: IS_AUTHENTICATED_FULLY }
```

dans insommnia on utilise `auth/ bearer Token`
dans le champ `token` on c/c le token pour s'identifier et pouvoir utiliser l'API qui est maintenant restreinte qu'aux seuls user identifiés.

## gérer les CORS Cross Origin Ressource Sharing

pour que le navigateur autorise une requête fetch externe (sans ça, seules les requêtes provenant de la machine locale seront acceptées = "Same Origin")

```bash
composer require nelmio/cors-bundle
```

<https://github.com/nelmio/NelmioCorsBundle>
permet d'accepter les requêtes d'un autre ordinateur sur notre API.

## sécurité

A ne pas installer toute de suite, voir si réellement besoin
```bash
composer require symfony/security-bundle
```

config/package/security.yaml
dans le firewall en environnnement de dev on peux rajouter assets dans le pattern
firewall/main pour l'environnement de prod

access_control permet de réserver certaines routes aux users ayant l'autorisation (admin).
firewall/mainlazy/true ne démmarre pas de session si l'utilisateur n'est pas connecter.

Pour enregistrer les différents rôles

```yaml
    providers:
        users_in_memory:
            memory:
                users:
                    # username: {password: xxxx, roles: ['ROLE_A', 'ROLE_B', ...]}
                    admin: {password: 'admin', roles: ['ROLE_ADMIN']}  
```

pour activer les moyens d'authentifications
formulaire de login, login via JSON

Il faut configurer le passwordhasher `Symfony\Component\Security\Core\User\InMemoryUser: 'plaintext'`

pour se connecter sans mdp haché
pour hacher le mdp
`Symfony\Component\Security\Core\User\InMemoryUser: 'auto'`
puis

`bin/console security:hash-password`
récupérer le mdp haché depuis la console ds le terminal et venir la coller dans la ligne
`admin: {password: '$2y$13$UdYdZghuLYPNu.R1julTK.5NNhy2OKzCOxDtZ.OOaGiTN6sPFiPfi ', roles: ['ROLE_ADMIN']}`

## mettre en place la déconnexion

faire un SecurtyController sans template

```php
class SecurityController extends AbstractController
{
    /**
     * @Route("/logout", name="logout")
     */
    public function logout()
    {
        // le code ici ne sera jamais exécuté
    }
}

```

dans le fichier security.yaml venir modifier

```yaml
    firewalls:
        dev:
            pattern: ^/(_(profiler|wdt)|css|images|js)/
            security: false
        main:
            lazy: true
            provider: app_user_provider

            form_login:
                login_path: login
                check_path: login
```

## installation de Doctrine

## A la racine du projet

`composer require symfony/orm-pack` demande si on veux utilisier docker => `n`

## config et création BDD

créer un fichier `.env.local` à la racine du projet
Dans ce fichier
configurer `DATABASE_URL="mysql://explorateur:Ereul9Aeng@127.0.0.1:3306/oflix?serverVersion=mariadb-10.3.25"`
`DATABASE_URL="mysql://` le nom d'utilisateur `:` le mot de passe `@` le port de connexion `/` le nom de la BDD `?serverVersion=mariadb` la version de la BDD

Dans le terminale de cde pour créer ma BDD

`bin/console doctrine:database:create`

## valider les données reçues en JSON

installer le bundle

```bash
composer require symfony/validator doctrine/annotations
```
