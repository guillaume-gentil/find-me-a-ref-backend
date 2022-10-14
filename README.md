# Find Me A Ref

- [Genesis](#genesis)
- [Issues](#issues)
- [Local installation for linux (Ubuntu 20) (for DEV)](#local-installation-for-linux-ubuntu-20-for-dev)
- [Install the app for production (on Ubuntu 20 server) (for PROD)](#install-the-app-for-production-on-ubuntu-20-server-for-prod)
- [Versioning](#versioning)
- [Authors](#authors)
- [License](#license)

## Genesis

[The origin](https://github.com/O-clock-Einstein/Projects/issues/1).

This project is a REST API built with PHP/Symfony.

You can use this API with this [React Project](https://github.com/O-clock-Einstein/01-find-me-a-ref-front).

## Issues

[For memory](https://github.com/O-clock-Einstein/Projects/issues?q=is%3Aopen+is%3Aissue).

## Local installation for linux (Ubuntu 20) (for DEV)

### DEV | Prerequisites

- IDE : [VSCODE](https://code.visualstudio.com/)
- Git
- MariaDB 10.3.25
- PHP 7.4
- [Composer](https://getcomposer.org/download/)

### DEV | Clone the project

```bash
git clone git@github.com:O-clock-Einstein/01-find-me-a-ref-back.git
```

### DEV | Install the components

```bash
cd 01-find-me-a-ref-back/
composer install
```

### DEV | Create keys for JWT (JSON Web Token)

```bash
bin/console lexik:jwt:generate-keypair
```

### DEV | Configure the App

```ini
# file : .env.local

###> DB connection ###
DB_USER=<COMPLETE_WITH_YOUR_DATA>
DB_PASS=<COMPLETE_WITH_YOUR_DATA>
DB_NAME=<COMPLETE_WITH_YOUR_DATA>
MARIADB_VERSION=<COMPLETE_WITH_YOUR_DATA>

DATABASE_URL=mysql://${DB_USER}:${DB_PASS}@127.0.0.1:3306/${DB_NAME}?serverVersion=mariadb-${MARIADB_VERSION}
###< Connect to DB ###

###> lexik/jwt-authentication-bundle ###
JWT_PASSPHRASE=<COMPLETE_WITH_YOUR_DATA>  # cut/paste it from .env file
###< lexik/jwt-authentication-bundle ###

###> OpenCage/Geocode API ###
OPENCAGE_API_KEY=<COMPLETE_WITH_YOUR_DATA>  # https://opencagedata.com/users/sign_up
###< OpenCage/Geocode API ###
```

### DEV | Create DB and fixtures

```bash
bin/console d:d:c  # doctrine:database:create
bin/console d:m:m  # doctrine:migrations:migrate
 > yes  # to continue
bin/console d:f:l  # doctrine:fixture:load`, 
 > yes  # to continue
```

### DEV | Run PHP server

- `php -S 0.0.0.0:8000 -t public`

### DEV | Use MailHog for tests

- install MailHog

[MailHog project](https://github.com/mailhog/MailHog)

```bash
sudo apt-get -y install golang-go
go get github.com/mailhog/MailHog
```

- configure `.env` file

```ini
// file : .env

MAILER_DSN=smtp://localhost:1025
```

- run MailHog :

```bash
~/go/bin/MailHog
```

- see smtp traffic in your web browser : `localhost:8025`

🎉 Bravo! You are ready to dev!

---

## Install the app for production (on Ubuntu 20 server) (for PROD)

### PROD | Connection with server

Connect to the server with ssh

```bash
ssh student@guillaume-gentil-server.eddi.cloud
```

### PROD | Install prerequisites

- Git
- MariaDB 10.3.25
- PHP 7.4
- [Composer](https://getcomposer.org/download/)

Get some help :

- [installation-serveur (readme)](https://github.com/O-clock-Einstein/S08-installation-serveur-SebLOclock)
- [installation-serveur (script)](https://github.com/O-clock-Einstein/S08-installation-serveur)
- [installation-serveur-ubutnu](https://kourou.oclock.io/ressources/fiche-recap/installation-serveur-ubutnu/)
- [deploiement-projet-sur-serveur-aws](https://kourou.oclock.io/ressources/fiche-recap/deploiement-projet-sur-serveur-aws-ubuntu-procedure/)

### PROD | Clone the project

```bash
cd /var/www/html/
git clone git@github.com:O-clock-Einstein/01-find-me-a-ref-back.git
```

### PROD | Install the components

```bash
cd 01-find-me-a-ref-back/
composer install --no-dev --optimize-autoloader
composer dump-env prod
APP_ENV=prod APP_DEBUG=0 php bin/console cache:clear
```

### PROD | Create keys for JWT (JSON Web Token)

```bash
bin/console lexik:jwt:generate-keypair
```

### PROD | Configure the App

- update the `.env` file : replace `APP_ENV=dev` by `APP_ENV=prod`

-create the `.env.local` file

```ini
# file : .env.local

###> DB connection ###
DB_USER=<COMPLETE_WITH_YOUR_DATA>
DB_PASS=<COMPLETE_WITH_YOUR_DATA>
DB_NAME=<COMPLETE_WITH_YOUR_DATA>
MARIADB_VERSION=<COMPLETE_WITH_YOUR_DATA>

DATABASE_URL=mysql://${DB_USER}:${DB_PASS}@127.0.0.1:3306/${DB_NAME}?serverVersion=mariadb-${MARIADB_VERSION}
###< Connect to DB ###

###> lexik/jwt-authentication-bundle ###
JWT_PASSPHRASE=<COMPLETE_WITH_YOUR_DATA>  # cut/paste it from .env file
###< lexik/jwt-authentication-bundle ###

###> OpenCage/Geocode API ###
OPENCAGE_API_KEY=<COMPLETE_WITH_YOUR_DATA>  # https://opencagedata.com/users/sign_up
###< OpenCage/Geocode API ###
```

### PROD | Create DB and initialize the datas

```bash
bin/console d:d:c  # doctrine:database:create
bin/console d:m:m  # doctrine:migrations:migrate
 > yes  # to continue
```

Then, execute the [SQL script](https://github.com/O-clock-Einstein/01-find-me-a-ref-back/blob/main/data_for_prod.sql) with Adminer (or another way)

### Configure Apache Virtual Host

```bash
cd /etc/apache2/sites-available
sudo nano 01-find-me-a-ref-back.conf
```

```conf
# file : /etc/apache2/sites-available/01-find-me-a-ref-back.conf

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

```bash
sudo a2dissite 000-default
sudo a2ensite 01-find-me-a-ref-back
sudo systemctl reload apache2

sudo a2enmod rewrite
sudo systemctl restart apache2
```

### Configure HTTPS

Go to [certbot](https://certbot.eff.org/) and follow the documentation.

```bash
# install/update snap
sudo snap install core; sudo snap refresh core

# remove Certbot from apt if necessary
sudo apt-get remove certbot

# install certbot with snap
sudo snap install --classic certbot

# activate certbot CLI
sudo ln -s /snap/bin/certbot /usr/bin/certbot

# install certificates
sudo certbot --apache
> adresse.mail@gmail.com  # website/admin email 
> Y  # licence
> N  # newsletter
> 1  # select server
> # "Congratulations! You have successfully enabled HTTPS on https://guillaume-gentil-server.eddi.cloud"
```

🎉 Bravo! Now you have to check the [Front-end App](https://github.com/O-clock-Einstein/01-find-me-a-ref-front)!

## Versioning

v1.0

## Authors

[Arnaud Joguet](https://github.com/Arnaud-Joguet), [Guillaume Gentil](https://github.com/guillaume-gentil), [Tomas Conan](https://github.com/TomasConan), [Loïc Guégan](https://github.com/Runebearer)

## License

https://www.gnu.org/licenses/gpl-howto.fr.html
