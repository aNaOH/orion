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

-- Volcando datos para la tabla orion.achievements: ~0 rows (aproximadamente)

-- Volcando datos para la tabla orion.badges: ~0 rows (aproximadamente)

-- Volcando datos para la tabla orion.badge_unlocked: ~0 rows (aproximadamente)

-- Volcando datos para la tabla orion.comments: ~8 rows (aproximadamente)
INSERT INTO `comments` (`id`, `author_id`, `post_id`, `body`, `date`) VALUES
	(2, 1, 1, 'Probando comentario', '2024-11-29 15:51:11'),
	(3, 1, 1, 'Probando comentario', '2024-11-29 15:53:19'),
	(4, 1, 1, 'Probando comentario', '2024-11-29 15:54:46'),
	(5, 1, 1, 'Probando comentario 2', '2024-11-29 15:55:01'),
	(6, 1, 1, 'Probando comentario 3', '2024-11-29 15:56:19'),
	(7, 1, 1, 'Probando comentario 3', '2024-11-29 15:56:39'),
	(8, 1, 1, 'Probando comentario dawdawd', '2024-11-29 15:57:08'),
	(9, 1, 1, 'Probando comentario dawdawd', '2024-11-29 15:58:21'),
	(10, 1, 4, 'Probando dhsajidhnsajdsa', '2024-11-29 16:04:55');

-- Volcando datos para la tabla orion.developers: ~2 rows (aproximadamente)
INSERT INTO `developers` (`id`, `name`, `profile_pic`, `motd`, `owner_id`) VALUES
	(1, 'Orion', NULL, 'Experience it united', 1),
	(4, 'Abel', NULL, NULL, 3),
	(5, '', NULL, NULL, 5);

-- Volcando datos para la tabla orion.entries: ~0 rows (aproximadamente)

-- Volcando datos para la tabla orion.gallery_entries: ~2 rows (aproximadamente)
INSERT INTO `gallery_entries` (`post_id`, `media`) VALUES
	(5, 'ZjI1empBS0VjREZhWk92Qlp2aVpIUT09'),
	(6, 'dkNNQlBGNUFZSGpHVVV2bFUrWDA1QT09');

-- Volcando datos para la tabla orion.game: ~0 rows (aproximadamente)
INSERT INTO `game` (`id`, `title`, `short_description`, `description`, `launch_date`, `base_price`, `discount`, `file`, `version`, `developer_id`) VALUES
	(1, 'Dashes & Squares', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1);

-- Volcando datos para la tabla orion.guides: ~0 rows (aproximadamente)
INSERT INTO `guides` (`post_id`, `type_id`) VALUES
	(4, 1);

-- Volcando datos para la tabla orion.guide_types: ~1 rows (aproximadamente)
INSERT INTO `guide_types` (`id`, `icon`, `type`, `tint`) VALUES
	(1, 'MTY0bjlsK2pRL0NYYWxDOVF4OTdUUFZvT21SVjdxbml4eHBlRHJqaVNFbz0=', 'Logro', '#DEAB18'),
	(2, 'cm5zQzFwSWE4eE1ya3JYSFdEZzJQLzU1WjkvWXVuQXdSY044ZU84OHlHZz0=', 'Nivel/Mapa', '#978af4');

-- Volcando datos para la tabla orion.leaderboards: ~0 rows (aproximadamente)

-- Volcando datos para la tabla orion.owns: ~0 rows (aproximadamente)

-- Volcando datos para la tabla orion.posts: ~5 rows (aproximadamente)
INSERT INTO `posts` (`id`, `title`, `body`, `created_at`, `last_updated_at`, `is_public`, `type`, `game_id`, `author_id`) VALUES
	(1, 'Prueba', 'Probando lol', '2024-11-08 15:11:13', '2024-11-08 15:11:13', 1, 0, 1, 1),
	(2, 'Prueba MD', '**Probando lol**\n\n# aló\nadios', '2024-11-08 16:05:40', '2024-11-08 16:05:40', 1, 0, 1, 1),
	(4, 'Conseguir logro secreto', 'Pasos para conseguirlo\n* A\n* B\n* C\n* D', '2024-11-27 14:17:51', '2024-11-27 14:17:51', 1, 2, 1, 1),
	(5, 'Prueba', '', '2024-11-30 14:53:55', '2024-11-30 14:53:55', 1, 1, 1, 1),
	(6, 'Clip', '', '2024-11-30 15:06:51', '2024-11-30 15:06:51', 1, 1, 1, 1);

-- Volcando datos para la tabla orion.unlocks: ~0 rows (aproximadamente)

-- Volcando datos para la tabla orion.users: ~1 rows (aproximadamente)
INSERT INTO `users` (`id`, `email`, `username`, `password`, `birthdate`, `role`, `profile_pic`, `motd`, `badge_id`, `created_at`) VALUES
	(1, 'admin@togetheronorion.com', 'Orion', '$2y$10$rHY66BW.lSKkU0P8485kreEg4114dyiXPiFD4NePMkSS1polkNV6C', '2004-11-28', 1, 'czNVL0pobW53cWxmSTU4Y1RtOWhyUT09', 'Orion\'s official account', NULL, '2024-10-30 15:24:12'),
	(3, 'abel@moonnastd.com', 'Abel', '$2y$10$bowr.44pmAkshMkeyzcHzeW088SfVuiSBxOG7FsNPBN1VZhsSXR9e', '2004-05-25', 0, NULL, 'moonna studios developer', NULL, '2024-12-01 16:31:18'),
	(5, 'abehsosa2004@gmail.com', 'abehsosa2004', '$2y$10$3PNrzFbd7nvSwAEH.oxRVezNBej6kOOWorAgHW257OC4GtOunO/Sm', '2004-05-25', 0, NULL, NULL, NULL, '2024-12-01 18:04:42');

-- Volcando datos para la tabla orion.votes: ~1 rows (aproximadamente)
INSERT INTO `votes` (`user_id`, `post_id`, `modifier`) VALUES
	(1, 6, 2),
	(1, 5, 0);

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
