-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Hôte : mysql
-- Généré le : ven. 29 août 2025 à 17:52
-- Version du serveur : 9.4.0
-- Version de PHP : 8.2.29

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `blog`
--
CREATE DATABASE IF NOT EXISTS blog CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE blog;


-- --------------------------------------------------------

--
-- Structure de la table `article`
--

CREATE TABLE `article` (
  `id` int UNSIGNED NOT NULL,
  `user_id` int UNSIGNED DEFAULT NULL,
  `title` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `chapo` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `content` text COLLATE utf8mb4_general_ci NOT NULL,
  `date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `article`
--

INSERT INTO `article` (`id`, `user_id`, `title`, `chapo`, `content`, `date`) VALUES
(6, 6, 'Créer une petite API avec Symfony rapidement', 'Objectif: exposer une ressource simple en lecture/écriture sans complexité.', 'Symfony permet de créer une API basique en peu d’étapes. On démarre un nouveau projet, on ajoute la couche API (API Platform si on veut aller vite), on crée une entité simple comme Book avec un titre et un auteur, puis on lance le serveur. On obtient des routes pour lister, créer, lire et supprimer. L’intérêt est d’aller au plus simple: définir le modèle, activer la ressource, tester avec un outil comme curl ou un client HTTP. Ensuite, on ajoute petit à petit la validation (champs obligatoires), la pagination et l’authentification si besoin. Le but n’est pas la perfection, mais un résultat fonctionnel rapide, clair, et facile à faire évoluer.\r\n', '2025-08-29 14:43:39'),
(7, 6, 'Mon environnement de dev PHP/Symfony avec Docker', 'Pourquoi j’utilise Docker pour isoler le projet et gagner du temps.', 'Docker me permet d’avoir PHP, une base de données et un serveur web prêts à l’emploi, sans “ça marche chez moi mais pas chez toi”. Je lance la stack, je monte le code source et je peux développer tout de suite. Pour Symfony, j’utilise souvent un conteneur PHP-FPM, un Nginx simple et MySQL ou PostgreSQL. Je garde les variables (base de données, mots de passe) dans un fichier d’environnement. L’avantage principal est la reproductibilité: un nouvel environnement se met en place en quelques minutes. Quand le projet grandit, je peux ajouter des services comme Redis, un mailer de test ou un moteur de file d’attente, toujours de manière maîtrisée.', '2025-08-29 14:44:32'),
(8, 6, 'Envoyer des e-mails en local sans risquer le spam', 'Capturer les e-mails de développement dans une fausse boîte plutôt que d’envoyer en vrai.', 'En développement, je n’envoie jamais de vrais e-mails. J’utilise un outil comme MailHog ou Mailpit. Le principe est simple: l’application envoie ses mails vers un serveur SMTP local, et ces mails apparaissent dans une interface web de test. Je peux vérifier l’objet, le contenu et les pièces jointes sans déranger de vrais utilisateurs. C’est pratique pour tester des confirmations de compte, des réinitialisations de mot de passe ou des notifications. Le jour où je passe en préproduction ou production, je change seulement la configuration SMTP pour utiliser un vrai fournisseur, sans toucher au code.', '2025-08-29 14:44:50'),
(9, 6, 'Formulaires Symfony: aller à l’essentiel', 'Créer un formulaire propre, valider les données et afficher des messages simples.', 'Les formulaires Symfony rendent la collecte d’informations fiable. Je définis un type de formulaire, j’ajoute mes champs, puis je branche la validation avec des règles courtes: obligatoire, longueur, format e-mail. Côté vue, j’affiche le formulaire et les messages d’erreur au même endroit. L’idée est d’éviter la complexité: des libellés clairs, des erreurs compréhensibles, et un traitement propre côté contrôleur. Si je dois enregistrer en base, je mappe simplement les champs sur mon entité. Résultat: un flux lisible pour l’utilisateur et un code facile à maintenir.', '2025-08-29 14:45:18'),
(10, 6, 'Petits gestes qui rendent une app plus rapide', 'Quelques idées simples pour gagner en performance sans tout réécrire.', 'Je commence par mesurer: temps de réponse, requêtes lentes et pages lourdes. Ensuite, j’applique des actions simples. Côté base, je vérifie les index et j’évite les requêtes inutiles. Côté serveur, j’active le cache des réponses quand c’est possible. Côté PHP, j’utilise l’opcache et je supprime le code mort. Côté application, je réduis ce que j’envoie: moins de données, moins de templates complexes. L’objectif n’est pas de tout optimiser, mais d’attaquer les points visibles: la page la plus consultée, la requête la plus coûteuse, l’action la plus lente. Quelques ajustements bien ciblés apportent souvent un vrai gain.', '2025-08-29 14:45:34');

-- --------------------------------------------------------

--
-- Structure de la table `comments`
--

CREATE TABLE `comments` (
  `id` int UNSIGNED NOT NULL,
  `article_id` int UNSIGNED NOT NULL,
  `user_id` int UNSIGNED NOT NULL,
  `content` text NOT NULL,
  `is_approved` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `comments`
--

INSERT INTO `comments` (`id`, `article_id`, `user_id`, `content`, `is_approved`, `created_at`) VALUES
(29, 10, 7, 'Super merci pour l\'astuce', 1, '2025-08-29 16:29:03');

-- --------------------------------------------------------

--
-- Structure de la table `user`
--

CREATE TABLE `user` (
  `id` int UNSIGNED NOT NULL,
  `username` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `role` enum('admin','user') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'user',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `user`
--

INSERT INTO `user` (`id`, `username`, `email`, `password`, `role`, `created_at`, `updated_at`) VALUES
(6, 'Admin', 'admin@admin.fr', '$2y$12$11Whq84xYDE.SikJlbc8fupZ3Oy3xpSMSMU.vzHlsrHb8Q2V4TaA.', 'admin', '2025-08-29 14:36:46', '2025-08-29 14:36:46'),
(7, 'user', 'user@user.fr', '$2y$12$hYv0yQx.L6JJVUh4dHQJXO/jQLb/YIid9jjYsnGSXfqQMrmH8VGou', 'user', '2025-08-29 16:04:06', '2025-08-29 16:04:06'),
(8, 'bob', 'bob@bob.fr', '$2y$12$o1Odk8QtYSLEkpD4I/QoUOVoWWVD.hMso5bKItWqhRoqiBkCB6vAS', 'user', '2025-08-29 17:09:36', '2025-08-29 17:09:36');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `article`
--
ALTER TABLE `article`
  ADD PRIMARY KEY (`id`),
  ADD KEY `date` (`date`),
  ADD KEY `article_ibfk_1` (`user_id`);

--
-- Index pour la table `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `article_id` (`article_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Index pour la table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `article`
--
ALTER TABLE `article`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT pour la table `comments`
--
ALTER TABLE `comments`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT pour la table `user`
--
ALTER TABLE `user`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `article`
--
ALTER TABLE `article`
  ADD CONSTRAINT `article_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE SET NULL ON UPDATE RESTRICT;

--
-- Contraintes pour la table `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`article_id`) REFERENCES `article` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `comments_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
