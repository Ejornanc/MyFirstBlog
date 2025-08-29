# SnowTricks

MyFirstBlog est un site de blog web PHP de présentation personnelle et de partage d'articles, d'astuces, de tutoriels ou d'autres informations en rapport avec le développement web.

## Prérequis

- PHP 8.2 ou supérieur
- Composer
- MySQL 9.2 ou supérieur
- Docker

## Installation

### 1. Cloner le dépôt

```bash
git clone https://github.com/Ejornanc/MyFirstBlog/).git
cd MyFirstBlog
```

### 2. Installer les dépendances PHP

```bash
composer install
```

### 3. Créer les containers Docker (Mailhog, MySQL, phpMyAdmin)

Le chemin par défaut de la base de données sera le dossier parent du projet. Changez la valeur dans le docker-compose si vous souhaitez le modifier.

```bash
docker compose -f docker-compose.yaml -p myfirstblog up -d
```

### 4. Configurer l'environnement

Modifiez ou vérifiez les paramètres de connexion PDO (host, dbname, user, password) dans la classe Database pour qu’ils correspondent à votre configuration MySQL :

```
'mysql:host=mysql;dbname=blog;charset=utf8',
'root',
'123user',
```
Importer la base de données qui se trouve dans Data-Bdd.


## Fonctionnalités

- Gestion du blog : création, modification et suppression d’articles
- Système d’authentification utilisateur (inscription, connexion, déconnexion)
- Commentaires sur les articles (ajout, affichage)
- Gestion des commentaires : validation/modération et suppression

## Utilisateur Enregistré

### Admin
- email : admin@admin.fr
- password : admin123

### User
- email : user@user.fr
- password : user123
