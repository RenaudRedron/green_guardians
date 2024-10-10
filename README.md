# GREEN GUARDIANS

**Notre mission**
=======

Chez Green Guardians, notre mission est de promouvoir la préservation et la conservation de l'environnement en rassemblant les forces des associations, des communes et des particuliers. Nous croyons que chaque geste compte et que, ensemble, nous pouvons créer un impact positif et durable sur notre planète.

**Pourquoi ce projet ?**
=======

La protection de notre faune et de notre flore est plus urgente que jamais. Les initiatives locales jouent un rôle crucial dans la lutte contre le changement climatique et la dégradation environnementale. Green Guardians est né de la volonté de faciliter ces actions locales et de les rendre visibles et accessibles à tous. En centralisant les projets environnementaux sur une plateforme unique, nous espérons encourager la participation et la collaboration de chacun.

**Pour qui ?**
 =======

Green Guardians s'adresse à toutes les personnes et organisations passionnées par la préservation de l'environnement. Les associations y trouvent un outil puissant pour mobiliser des bénévoles et promouvoir leurs initiatives. Les communes peuvent impliquer activement leurs habitants dans des projets locaux de conservation, renforçant ainsi le lien communautaire. Quant aux particuliers, ils disposent d'une plateforme pour s'engager concrètement et localement dans des actions écologiques, contribuant ainsi à la protection de leur environnement immédiat.

**Comment ça marche ?**
 =======

Green Guardians simplifie la mise en relation et la participation à des projets environnementaux. Les utilisateurs peuvent publier leurs initiatives, qu'il s'agisse de reboisement, de nettoyage, de campagnes de sensibilisation, etc. Ces projets sont ensuite affichés sur une carte interactive, facilitant ainsi la découverte et l'accès aux actions locales. Les utilisateurs peuvent consulter cette carte pour trouver des projets proches de chez eux, y participer activement et échanger leurs idées et expériences via les commentaires. En unissant nos forces sur cette plateforme, nous rendons la préservation de la nature plus accessible et collaborative.

**Rejoignez-nous**
 =======

Green Guardians est plus qu'une simple plateforme, c'est une communauté dédiée à la protection de notre planète. Ensemble, nous pouvons faire la différence. Rejoignez-nous dès aujourd'hui et devenez un gardien de la nature pour un avenir plus vert et plus durable.

## Prérequis

- PHP
- Composer
- Symfony
- Symfony CLI
- MySql

## Installation

Cloner le dépôt

```bash
git clone https://github.com/RenaudRedron/green_guardians.git
```

Se rendre dans le répertoire du script

```bash
cd green_guardians
```

Installation des dépendances

```bash
composer install
```

Création et configuration du fichier **.env.local** pour ajouter les informations de connexion à la base de données et au système de messagerie

```bash
# In all environments, the following files are loaded if they exist,
# the latter taking precedence over the former:
#
#  * .env                contains default values for the environment variables needed by the app
#  * .env.local          uncommitted file with local overrides
#  * .env.$APP_ENV       committed environment-specific defaults
#  * .env.$APP_ENV.local uncommitted environment-specific overrides
#
# Real environment variables win over .env files.
#
# DO NOT DEFINE PRODUCTION SECRETS IN THIS FILE NOR IN ANY OTHER COMMITTED FILES.
# https://symfony.com/doc/current/configuration/secrets.html
#
# Run "composer dump-env prod" to compile .env files for production use (requires symfony/flex >=1.2).
# https://symfony.com/doc/current/best_practices.html#use-environment-variables-for-infrastructure-configuration

###> symfony/framework-bundle ###
APP_ENV=dev
APP_SECRET=bca21a82b8e379cf715f1600000c66591
###< symfony/framework-bundle ###

###> doctrine/doctrine-bundle ###
# Format described at https://www.doctrine-project.org/projects/doctrine-dbal/en/latest/reference/configuration.html#connecting-using-a-url
# IMPORTANT: You MUST configure your server version, either here or in config/packages/doctrine.yaml
#
# DATABASE_URL="sqlite:///%kernel.project_dir%/var/data.db"
DATABASE_URL="mysql://root@127.0.0.1:3306/green_guardians?serverVersion=10&charset=utf8mb4"
# DATABASE_URL="postgresql://app:!ChangeMe!@127.0.0.1:5432/app?serverVersion=16&charset=utf8"
###< doctrine/doctrine-bundle ###

###> symfony/messenger ###
# Choose one of the transports below
# MESSENGER_TRANSPORT_DSN=amqp://guest:guest@localhost:5672/%2f/messages
# MESSENGER_TRANSPORT_DSN=redis://localhost:6379/messages
MESSENGER_TRANSPORT_DSN=doctrine://default?auto_setup=0
###< symfony/messenger ###

###> symfony/mailer ###
MAILER_DSN=********************
###< symfony/mailer ###
```

Création de la base de données

```bash
symfony console doctrine:database:create
```

Génération des migrations

```bash
symfony console make:migration
```

Execution des migrations

```bash
symfony console doctrine:migrations:migrate
```

Execution des fixtures

```bash
symfony console doctrine:fixtures:load
```

Lancer Webpack

```bash
npm run build
```

## Démarrage du serveur

```bash
symfony server:start
```

## Utilisateur par défaut

- **Nom :** Administrateur
- **Email :** contact@green-guardians.fr
- **Mot de passe :** azerty12345.A

**Ne pas oublier de changer le mot de passe du compte administrateur une fois votre projet en production**
