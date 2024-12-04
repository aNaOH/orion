-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Versión del servidor:         8.0.30 - MySQL Community Server - GPL
-- SO del servidor:              Win64
-- HeidiSQL Versión:             12.1.0.6537
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

-- Volcando estructura para tabla orion.achievements
CREATE TABLE IF NOT EXISTS `achievements` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `description` varchar(255) NOT NULL,
  `icon` varchar(255) NOT NULL,
  `locked_icon` varchar(255) DEFAULT NULL,
  `secret` tinyint(1) DEFAULT '0',
  `game_id` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `game_id` (`game_id`),
  CONSTRAINT `achievements_ibfk_1` FOREIGN KEY (`game_id`) REFERENCES `game` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla orion.badges
CREATE TABLE IF NOT EXISTS `badges` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `description` varchar(255) NOT NULL,
  `icon` varchar(255) NOT NULL,
  `game_id` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_game_id` (`game_id`),
  CONSTRAINT `fk_game_id` FOREIGN KEY (`game_id`) REFERENCES `game` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla orion.badge_unlocked
CREATE TABLE IF NOT EXISTS `badge_unlocked` (
  `badge_id` int NOT NULL,
  `user_id` int NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  KEY `badge_id` (`badge_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `badge_unlocked_ibfk_1` FOREIGN KEY (`badge_id`) REFERENCES `badges` (`id`),
  CONSTRAINT `badge_unlocked_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla orion.builds
CREATE TABLE IF NOT EXISTS `builds` (
  `game_id` int NOT NULL,
  `file` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `version` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `release_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  KEY `FK_builds_game` (`game_id`),
  CONSTRAINT `FK_builds_game` FOREIGN KEY (`game_id`) REFERENCES `game` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla orion.comments
CREATE TABLE IF NOT EXISTS `comments` (
  `id` int NOT NULL AUTO_INCREMENT,
  `author_id` int NOT NULL,
  `post_id` int NOT NULL,
  `body` text NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `author_id` (`author_id`),
  KEY `post_id` (`post_id`),
  CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`author_id`) REFERENCES `users` (`id`),
  CONSTRAINT `comments_ibfk_2` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla orion.developers
CREATE TABLE IF NOT EXISTS `developers` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `profile_pic` varchar(255) DEFAULT NULL,
  `motd` varchar(255) DEFAULT NULL,
  `owner_id` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `owner_id` (`owner_id`),
  CONSTRAINT `developers_ibfk_1` FOREIGN KEY (`owner_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla orion.entries
CREATE TABLE IF NOT EXISTS `entries` (
  `leaderboard_id` int NOT NULL,
  `user_id` int NOT NULL,
  `value` int NOT NULL,
  KEY `leaderboard_id` (`leaderboard_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `entries_ibfk_1` FOREIGN KEY (`leaderboard_id`) REFERENCES `leaderboards` (`id`),
  CONSTRAINT `entries_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla orion.gallery_entries
CREATE TABLE IF NOT EXISTS `gallery_entries` (
  `post_id` int NOT NULL,
  `media` varchar(255) NOT NULL,
  PRIMARY KEY (`post_id`),
  CONSTRAINT `gallery_entries_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla orion.game
CREATE TABLE IF NOT EXISTS `game` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `short_description` varchar(255) DEFAULT NULL,
  `description` text,
  `launch_date` timestamp NULL DEFAULT NULL,
  `original_launch_date` date DEFAULT NULL,
  `base_price` float DEFAULT NULL,
  `discount` float DEFAULT NULL,
  `as_editor` tinyint(1) NOT NULL DEFAULT '0',
  `is_public` tinyint(1) NOT NULL DEFAULT '0',
  `developer_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `developer_id` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_developer_id` (`developer_id`),
  CONSTRAINT `fk_developer_id` FOREIGN KEY (`developer_id`) REFERENCES `developers` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla orion.guides
CREATE TABLE IF NOT EXISTS `guides` (
  `post_id` int NOT NULL,
  `type_id` int NOT NULL,
  PRIMARY KEY (`post_id`),
  KEY `type_id` (`type_id`),
  CONSTRAINT `guides_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`),
  CONSTRAINT `guides_ibfk_2` FOREIGN KEY (`type_id`) REFERENCES `guide_types` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla orion.guide_types
CREATE TABLE IF NOT EXISTS `guide_types` (
  `id` int NOT NULL AUTO_INCREMENT,
  `icon` varchar(255) NOT NULL,
  `type` varchar(100) NOT NULL,
  `tint` varchar(7) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla orion.leaderboards
CREATE TABLE IF NOT EXISTS `leaderboards` (
  `id` int NOT NULL AUTO_INCREMENT,
  `concept` int NOT NULL,
  `type` int NOT NULL,
  `game_id` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `game_id` (`game_id`),
  CONSTRAINT `leaderboards_ibfk_1` FOREIGN KEY (`game_id`) REFERENCES `game` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla orion.owns
CREATE TABLE IF NOT EXISTS `owns` (
  `user_id` int NOT NULL,
  `game_id` int NOT NULL,
  `checkout_id` text NOT NULL,
  KEY `user_id` (`user_id`),
  KEY `game_id` (`game_id`),
  CONSTRAINT `owns_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  CONSTRAINT `owns_ibfk_2` FOREIGN KEY (`game_id`) REFERENCES `game` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla orion.posts
CREATE TABLE IF NOT EXISTS `posts` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `body` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `last_updated_at` timestamp NULL DEFAULT NULL,
  `is_public` tinyint(1) DEFAULT '0',
  `type` int DEFAULT NULL,
  `game_id` int DEFAULT NULL,
  `author_id` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `game_id` (`game_id`),
  KEY `author_id` (`author_id`),
  CONSTRAINT `posts_ibfk_1` FOREIGN KEY (`game_id`) REFERENCES `game` (`id`),
  CONSTRAINT `posts_ibfk_2` FOREIGN KEY (`author_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla orion.unlocks
CREATE TABLE IF NOT EXISTS `unlocks` (
  `achievement_id` int NOT NULL,
  `user_id` int NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  KEY `achievement_id` (`achievement_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `unlocks_ibfk_1` FOREIGN KEY (`achievement_id`) REFERENCES `achievements` (`id`),
  CONSTRAINT `unlocks_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla orion.users
CREATE TABLE IF NOT EXISTS `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `birthdate` date NOT NULL,
  `role` int NOT NULL,
  `profile_pic` varchar(255) DEFAULT NULL,
  `motd` varchar(255) DEFAULT NULL,
  `badge_id` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  KEY `fk_badge_id` (`badge_id`),
  CONSTRAINT `fk_badge_id` FOREIGN KEY (`badge_id`) REFERENCES `badges` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla orion.votes
CREATE TABLE IF NOT EXISTS `votes` (
  `user_id` int NOT NULL,
  `post_id` int NOT NULL,
  `modifier` tinyint(1) DEFAULT '0',
  KEY `user_id` (`user_id`),
  KEY `post_id` (`post_id`),
  CONSTRAINT `votes_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  CONSTRAINT `votes_ibfk_2` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `users` (`id`, `email`, `username`, `password`, `birthdate`, `role`, `profile_pic`, `motd`, `badge_id`, `created_at`) VALUES
	(1, 'admin@togetheronorion.com', 'Orion', '$2y$10$rHY66BW.lSKkU0P8485kreEg4114dyiXPiFD4NePMkSS1polkNV6C', '2004-11-28', 1, 'czNVL0pobW53cWxmSTU4Y1RtOWhyUT09', 'Orion\'s official account', NULL, '2024-10-30 15:24:12'),

-- La exportación de datos fue deseleccionada.

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
