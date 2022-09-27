# How tu use our repository

## 1.Only the first time

in your folder open a terminal and clone this repo with ssh key and `git clone` command.

## 1.Other time

replace `git clone` command by `git pull`.

## 2.Intstall all requires

`composer install` warning don't do `composer update` for be sure to have the same version of requires in all your local storage.

## 3.Configure .env.local

Add to your local .env all datas you don't want to see in git repository. (password to database, JWT secret key ...).

## 4.Create database and add fixtures for dev version

You have to do :
    - `bin/consle doctrine:database:create`
    - `doctrine:migrations:migrate` Warning don't use make:migration.
    - `bin/console doctrine:fixture:load`

## 5.Initialise keys for JWT

- TODO(GG) : Check JWT_PASSPHRASE into `.env`
- create your key pair : `bin/console lexik:jwt:generate-keypair`

Your local repo is up to work!