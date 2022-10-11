# Install Find Me A Ref (PROD)

- cloner le projet sur le serveur
`git clone git@github.com:O-clock-Einstein/01-find-me-a-ref-back.git`

- se placer dans le dossier de l'app
`cd 01-find-me-a-ref-back/`

- paramétrer le .env et le .env.local
`nano .env`
  - et remplacer `APP_ENV=dev` par `APP_ENV=prod`
  - et créer un fichier `.env.local` contenant :

    ```ini
        # données à compléter 

        ###> Connexion à la DB
        DATABASE_URL=
        ###< Connexion à la DB

        ###> lexik/jwt-authentication-bundle
        JWT_PASSPHRASE=
        ###< lexik/jwt-authentication-bundle

        ###> OpenCage/Geocode API
        OPENCAGE_API_KEY=
        ###< OpenCage/Geocode API
    ```

- installer les dépendances via composer

```php
composer install --no-dev --optimize-autoloader
```

- optimiser le chargement des variables d'environnement

```php
composer dump-env prod
```

- vider le cache

```php
APP_ENV=prod APP_DEBUG=0 php bin/console cache:clear
```

- création de la BDD

```php
bin/console doctrine:database:create
bin/console doctrine:migrations:migrate
> yes
```

- créer les l'utilisateur admin dans la BDD + les catégories et les types de base

```ini
http://guillaume-gentil-server.eddi.cloud/adminer/?username=XXXXXX&db=findmearef
# remplacer XXXXXX par le nom d'utilisateur
```

- exécuter la requête SQL du fichier `data_for_prod.sql` qui se trouve à la racine du projet

```bash
cat data_for_prod.sql

# copier le contenu du fichier et l'exécuter dans adminer
```

- create your key pair
```php
bin/console lexik:jwt:generate-keypair
```

🎉 Bravo ! l'application est installée avec succès. Il ne reste plus qu'à configurer le virtualhost et apache (fichiers de log)

- aller dans le dossier

```bash
cd /etc/apache2/sites-available
```

- créer un fichier de config pour l'app sur le modèle du fichier de default

```bash
sudo cp 000-default.conf 01-find-me-a-ref-back.conf
```

- modifier le fichier de config de l'app

```bash
sudo nano 01-find-me-a-ref-back.conf
```

- copier la config suivante

```conf
<VirtualHost *:80>
	# The ServerName directive sets the request scheme, hostname and port that
	# the server uses to identify itself. This is used when creating
	# redirection URLs. In the context of virtual hosts, the ServerName
	# specifies what hostname must appear in the request's Host: header to
	# match this virtual host. For the default virtual host (this file) this
	# value is not decisive as it is used as a last resort host regardless.
	# However, you must set it for any further virtual host explicitly.
	ServerName guillaume-gentil-server.eddi.cloud
	
	ServerAdmin findmearef@gmail.com
	DocumentRoot /var/www/html/01-find-me-a-ref-back/public

	# autorise, pour ce dossier, l'utilisation des .htaccess 
        # sans cette ligne, apache ne lira pas les .htaccess et donc pas de redirection
        <Directory "/var/www/html/01-find-me-a-ref-back/public">
                AllowOverride all
        </Directory>

	# Available loglevels: trace8, ..., trace1, debug, info, notice, warn,
	# error, crit, alert, emerg.
	# It is also possible to configure the loglevel for particular
	# modules, e.g.
	#LogLevel info ssl:warn

	ErrorLog ${APACHE_LOG_DIR}/error_fmar.log
	CustomLog ${APACHE_LOG_DIR}/access_fmar.log combined

	# For most configuration files from conf-available/, which are
	# enabled or disabled at a global level, it is possible to
	# include a line for only one particular virtual host. For example the
	# following line enables the CGI configuration for this host only
	# after it has been globally disabled with "a2disconf".
	#Include conf-available/serve-cgi-bin.conf
</VirtualHost>
```

- configurer le virtual host

```bash
sudo a2dissite 000-default
sudo a2ensite 01-find-me-a-ref-back
sudo systemctl reload apache2
```

- configurer l'affichage de l'URL (retirer le index.php)

```bash
sudo a2enmod rewrite
sudo systemctl restart apache2
```

- Passer le serveur en HTTPS !

https://certbot.eff.org/

- choisir `Apache` & `Ubuntu 20`

```bash
  # Ces étapes proviennent de la doc CertBot :

  # connexion en ssh au serveur 
  ssh student@mon-adresse-server.eddi.cloud

  ## les actions suivantes s'effectuent sur le serveur !
  # installe / met à jour Snap
  sudo snap install core; sudo snap refresh core

  # désinstalle Snap si il a été installé avec le gestionnaire de paquet `apt`
  sudo apt-get remove certbot

  # installer CertBot via Snap
  sudo snap install --classic certbot

  # "activer" la commande `certbot` dans le terminal
  sudo ln -s /snap/bin/certbot /usr/bin/certbot

  # installer les certificats (modifie automatiquement les fichiers apache)
  sudo certbot --apache
  > adresse.mail@gmail.com  # entrer sa véritable adresse mail (c'est mieux !)
  > Y  # accepter les termes du contrat)
  > N  # ou Y, inscription à la newsletter
  > 1  # choix parmi les propositions
  > # retour attendu : "Congratulations! You have successfully enabled HTTPS on https://guillaume-gentil-server.eddi.cloud"
  ```
  