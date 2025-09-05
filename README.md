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

Modifiez ou vérifiez les paramètres de connexion PDO (host, dbname, user, password) dans le ficher .env pour qu’ils correspondent à votre configuration MySQL :

```
DB_DRIVER=mysql
DB_HOST=mysql
DB_PORT=3306
DB_NAME=blog
DB_USER=user
DB_PASS=mdp
DB_CHARSET=utf8mb4
```
Alternative (optionnelle) : DATABASE_URL

```
DATABASE_URL=mysql://user:mdp@mysql:3306/blog?charset=utf8mb4
```

Importer la base de données qui se trouve dans Data-Bdd (cela va créer la base de données blog).


## Mail (PHPMailer + MailHog)

- Un service MailHog est fourni via Docker pour intercepter les emails de développement.
- L’UI est accessible sur http://localhost:8025 et le SMTP écoute sur localhost:1025 depuis l’hôte.
- Le code utilise PHPMailer configuré en SMTP vers le service `mailhog` (réseau Docker) sans auth ni TLS.
- Le formulaire de contact envoie un email vers contact@mon-site.test qui sera visible dans MailHog.

Override via variables d’environnement (facultatif):
- SMTP_HOST (défaut: mailhog)
- SMTP_PORT (défaut: 1025)
- MAIL_FROM_EMAIL (défaut: no-reply@mon-site.test)
- MAIL_FROM_NAME (défaut: Mon Site)

Lancer les conteneurs:
```
docker compose up -d
```

Vérifier l’envoi:
1. Ouvrir http://localhost:8080
2. Soumettre le formulaire Contact
3. Ouvrir http://localhost:8025 pour voir le message

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
