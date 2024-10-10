##Présentation du Projet Green Guardians

Dans le cadre de ma formation de **développeur web et web mobile**, j'ai réalisé un projet personnel intitulé **Green Guardians**, lancé en 2024. Ce projet a pour objectif de créer une carte interactive qui répertorie et promeut les actions écologiques tant au niveau local que national.

L'idée principale de la plateforme est de faciliter la connexion entre citoyens, associations, municipalités et entreprises engagées dans la protection de l'environnement. En réunissant ces acteurs, Green Guardians permet de participer à des projets environnementaux concrets, et ce, partout en France.

La carte interactive est au cœur de cette initiative. Elle permet aux utilisateurs de découvrir facilement des initiatives écologiques à proximité ou dans d'autres régions. Par exemple, les utilisateurs peuvent trouver des événements tels que des nettoyages de plages, des ateliers d’observation de la faune et de la flore, des actions de reforestation, ainsi que des événements de sensibilisation à l'écologie.

En outre, la plateforme offre la possibilité à chaque utilisateur de proposer ses propres projets, incitant ainsi la communauté à s'engager et à participer activement. Cela crée un véritable élan collaboratif pour le développement d'initiatives en faveur de l'environnement.

Ce projet m’a permis d’appliquer les compétences acquises durant ma formation, notamment en matière de développement web, de gestion de base de données et d’interaction avec des APIs, tout en contribuant à une cause qui me tient à cœur.

Visitez le site : [Green Guardians](https://green-guardians.fr/).

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
Installation de npm

```bash
npm install
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
