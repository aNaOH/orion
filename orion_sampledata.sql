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

-- Volcando datos para la tabla orion.builds: ~0 rows (aproximadamente)
INSERT INTO `builds` (`game_id`, `file`, `version`, `release_date`) VALUES
	(1, 'UjNPM3lXQ1RoMkR6YUUxMENQOXZ2dHpZR1dOc1BoVHRHSzFpUFJUcEVvUT0=', '3.4.50-2.1.3', '2024-12-03 22:02:13');

-- Volcando datos para la tabla orion.comments: ~0 rows (aproximadamente)

-- Volcando datos para la tabla orion.developers: ~1 rows (aproximadamente)
INSERT INTO `developers` (`id`, `name`, `profile_pic`, `motd`, `owner_id`) VALUES
	(1, 'Orion', NULL, 'Experience it united', 1);

-- Volcando datos para la tabla orion.entries: ~0 rows (aproximadamente)

-- Volcando datos para la tabla orion.gallery_entries: ~0 rows (aproximadamente)

-- Volcando datos para la tabla orion.game: ~20 rows (aproximadamente)
INSERT INTO `game` (`id`, `title`, `short_description`, `description`, `launch_date`, `original_launch_date`, `base_price`, `discount`, `as_editor`, `is_public`, `developer_name`, `developer_id`) VALUES
	(1, 'Minecraft', 'Crea. Explora. Sobrevive.', '                                                                                        Experimenta las distintas formas de explorar, sobrevivir y crear en Minecraft con Minecraft: Deluxe Collection para PC con Java y Bedrock y el iniciador de Minecraft incluidos. Juega Bedrock Edition en un PC para examinar contenido ilimitado creado por la comunidad en el Mercado de Minecraft, descubre nuevos estilos de juego a través de diferentes mapas y exprésate con emoticonos y elementos del Creador de personajes.\r\n\r\n### Sobre Orion Classic Collection\r\n\r\nPara conmemorar el lanzamiento de Orion, hemos colaborado con referentes en la industria para lanzar una colección de 20 juegos icónicos y puedas empezar a crear tu biblioteca en Orion                                                                                                                        ', '2024-12-02 23:00:00', '2011-11-18', 29.99, 0, 1, 1, 'Mojang', 1),
	(2, 'The Elder Scrolls V: Skyrim', 'Un RPG épico de mundo abierto', '**The Elder Scrolls V: Skyrim** ofrece un vasto mundo abierto lleno de misiones y aventuras.\r\n\r\n### Sobre Orion Classic Collection\r\n\r\nPara conmemorar el lanzamiento de Orion, hemos colaborado con referentes en la industria para lanzar una colección de 20 juegos icónicos y puedas empezar a crear tu biblioteca en Orion', '2024-12-02 23:00:00', '2011-11-11', 29.99, 20, 1, 1, 'Bethesda', 1),
	(3, 'Resident Evil', 'Terror y supervivencia', '**Resident Evil** introdujo el género de terror de supervivencia con un enfoque en la narrativa y los recursos limitados.\r\n\r\n### Sobre Orion Classic Collection\r\n\r\nPara conmemorar el lanzamiento de Orion, hemos colaborado con referentes en la industria para lanzar una colección de 20 juegos icónicos y puedas empezar a crear tu biblioteca en Orion', '2024-12-02 23:00:00', '1997-12-10', 9.99, 0, 1, 1, 'Capcom', 1),
	(4, 'Super Mario Bros.', 'Un clásico de plataformas', '**Super Mario Bros.** es un videojuego de plataformas desarrollado por Nintendo. Ayuda a Mario a rescatar a la princesa Peach en el Reino Champiñón.\r\n\r\n### Sobre Orion Classic Collection\r\n\r\nPara conmemorar el lanzamiento de Orion, hemos colaborado con referentes en la industria para lanzar una colección de 20 juegos icónicos y puedas empezar a crear tu biblioteca en Orion                                                                                                                        ', '2024-12-02 23:00:00', '1985-09-13', 9.99, 0, 1, 0, 'Nintendo', 1),
	(5, 'The Legend of Zelda', 'Aventura épica en Hyrule', '**The Legend of Zelda** es un juego de aventura y acción donde Link debe rescatar a la princesa Zelda y derrotar a Ganon.\r\n\r\n### Sobre Orion Classic Collection\r\n\r\nPara conmemorar el lanzamiento de Orion, hemos colaborado con referentes en la industria para lanzar una colección de 20 juegos icónicos y puedas empezar a crear tu biblioteca en Orion', '2024-12-02 23:00:00', '1986-02-21', 14.99, 0.1, 1, 1, 'Nintendo', 1),
	(6, 'Pac-Man', 'El clásico arcade de laberintos', '**Pac-Man** es un juego de arcade donde debes comer todos los puntos en el laberinto mientras evitas a los fantasmas.\r\n\r\n### Sobre Orion Classic Collection\r\n\r\nPara conmemorar el lanzamiento de Orion, hemos colaborado con referentes en la industria para lanzar una colección de 20 juegos icónicos y puedas empezar a crear tu biblioteca en Orion', '2024-12-02 23:00:00', '1980-05-22', 4.99, 0, 1, 1, 'Namco', 1),
	(7, 'Tetris', 'Un puzzle adictivo', '**Tetris** es un juego de rompecabezas donde debes encajar las piezas que caen para completar líneas y ganar puntos.\r\n\r\n### Sobre Orion Classic Collection\r\n\r\nPara conmemorar el lanzamiento de Orion, hemos colaborado con referentes en la industria para lanzar una colección de 20 juegos icónicos y puedas empezar a crear tu biblioteca en Orion', '2024-12-02 23:00:00', '1984-06-06', 6.99, 0, 1, 1, 'Alexey Pajitnov', 1),
	(8, 'DOOM', 'El pionero de los shooters en primera persona', '**DOOM** es un juego de disparos en primera persona donde enfrentas hordas de demonios en Marte.\r\n\r\n### Sobre Orion Classic Collection\r\n\r\nPara conmemorar el lanzamiento de Orion, hemos colaborado con referentes en la industria para lanzar una colección de 20 juegos icónicos y puedas empezar a crear tu biblioteca en Orion', '2024-12-02 23:00:00', '1993-12-10', 19.99, 0.2, 1, 1, 'id Software', 1),
	(9, 'Street Fighter II', 'El clásico de peleas uno contra uno', '**Street Fighter II** introdujo mecánicas revolucionarias en los juegos de lucha, con personajes icónicos como Ryu y Chun-Li.\r\n\r\n### Sobre Orion Classic Collection\r\n\r\nPara conmemorar el lanzamiento de Orion, hemos colaborado con referentes en la industria para lanzar una colección de 20 juegos icónicos y puedas empezar a crear tu biblioteca en Orion', '2024-12-02 23:00:00', '1991-03-14', 14.99, 0.1, 1, 1, 'Capcom', 1),
	(10, 'Final Fantasy VII', 'Una épica historia de rol', '**Final Fantasy VII** es un RPG que sigue a Cloud Strife y sus aliados en su lucha contra Shinra y Sephiroth.\r\n\r\n### Sobre Orion Classic Collection\r\n\r\nPara conmemorar el lanzamiento de Orion, hemos colaborado con referentes en la industria para lanzar una colección de 20 juegos icónicos y puedas empezar a crear tu biblioteca en Orion', '2024-12-02 23:00:00', '1997-01-31', 29.99, 0.15, 1, 1, 'Square Enix', 1),
	(11, 'Half-Life', 'Innovación en narrativa y jugabilidad', '**Half-Life** revolucionó los shooters con una narrativa inmersiva y física avanzada.\r\n\r\n### Sobre Orion Classic Collection\r\n\r\nPara conmemorar el lanzamiento de Orion, hemos colaborado con referentes en la industria para lanzar una colección de 20 juegos icónicos y puedas empezar a crear tu biblioteca en Orion', '2024-12-02 23:00:00', '1998-11-19', 14.99, 0.1, 1, 1, 'Valve', 1),
	(12, 'Metal Gear Solid', 'Sigilo y acción en su máxima expresión', '**Metal Gear Solid** combina sigilo, acción y una narrativa cinematográfica en un solo paquete.\r\n\r\n### Sobre Orion Classic Collection\r\n\r\nPara conmemorar el lanzamiento de Orion, hemos colaborado con referentes en la industria para lanzar una colección de 20 juegos icónicos y puedas empezar a crear tu biblioteca en Orion', '2024-12-02 23:00:00', '1998-09-03', 19.99, 0.1, 1, 1, 'Konami', 1),
	(13, 'Sonic the Hedgehog', 'Velocidad y acción', '**Sonic the Hedgehog** es un juego de plataformas protagonizado por un erizo azul rápido como el rayo.\r\n\r\n### Sobre Orion Classic Collection\r\n\r\nPara conmemorar el lanzamiento de Orion, hemos colaborado con referentes en la industria para lanzar una colección de 20 juegos icónicos y puedas empezar a crear tu biblioteca en Orion', '2024-12-02 23:00:00', '1991-06-23', 9.99, 0, 1, 1, 'Sega', 1),
	(14, 'The Sims', 'Crea y controla vidas', '**The Sims** es un simulador de vida donde los jugadores pueden diseñar casas y controlar personajes virtuales.\r\n\r\n### Sobre Orion Classic Collection\r\n\r\nPara conmemorar el lanzamiento de Orion, hemos colaborado con referentes en la industria para lanzar una colección de 20 juegos icónicos y puedas empezar a crear tu biblioteca en Orion', '2024-12-02 23:00:00', '2000-02-04', 19.99, 0.15, 1, 1, 'Maxis', 1),
	(15, 'Pokémon Red', 'Atrapa y entrena Pokémon', '**Pokémon Red** marcó el inicio de una franquicia legendaria de captura y entrenamiento de criaturas.\r\n\r\n### Sobre Orion Classic Collection\r\n\r\nPara conmemorar el lanzamiento de Orion, hemos colaborado con referentes en la industria para lanzar una colección de 20 juegos icónicos y puedas empezar a crear tu biblioteca en Orion', '2024-12-02 23:00:00', '1996-02-27', 14.99, 0.1, 1, 1, 'Game Freak', 1),
	(16, 'Chrono Trigger', 'Una obra maestra del RPG', '**Chrono Trigger** es un RPG aclamado por su narrativa no lineal y su diseño de combate innovador.\r\n\r\n### Sobre Orion Classic Collection\r\n\r\nPara conmemorar el lanzamiento de Orion, hemos colaborado con referentes en la industria para lanzar una colección de 20 juegos icónicos y puedas empezar a crear tu biblioteca en Orion', '2024-12-02 23:00:00', '1995-03-11', 19.99, 0.15, 1, 1, 'Square Enix', 1),
	(17, 'Castlevania: Symphony of the Night', 'Exploración y acción gótica', '**Castlevania: Symphony of the Night** es un juego de exploración con elementos de acción y RPG.\r\n\r\n### Sobre Orion Classic Collection\r\n\r\nPara conmemorar el lanzamiento de Orion, hemos colaborado con referentes en la industria para lanzar una colección de 20 juegos icónicos y puedas empezar a crear tu biblioteca en Orion', '2024-12-02 23:00:00', '1997-03-20', 14.99, 0.1, 1, 1, 'Konami', 1),
	(18, 'Mega Man 2', 'Acción de plataformas desafiante', '**Mega Man 2** es un juego de plataformas con acción intensa y batallas memorables contra jefes.\r\n\r\n### Sobre Orion Classic Collection\r\n\r\nPara conmemorar el lanzamiento de Orion, hemos colaborado con referentes en la industria para lanzar una colección de 20 juegos icónicos y puedas empezar a crear tu biblioteca en Orion', '2024-12-02 23:00:00', '1988-12-24', 9.99, 0, 1, 1, 'Capcom', 1),
	(19, 'Portal', 'Innovación en puzzles y física', '**Portal** es un juego de rompecabezas en primera persona que introduce mecánicas únicas con portales.\r\n\r\n### Sobre Orion Classic Collection\r\n\r\nPara conmemorar el lanzamiento de Orion, hemos colaborado con referentes en la industria para lanzar una colección de 20 juegos icónicos y puedas empezar a crear tu biblioteca en Orion', '2024-12-02 23:00:00', '2007-10-10', 19.99, 0.1, 1, 1, 'Valve', 1),
	(20, 'Grand Theft Auto: San Andreas', 'Mundo abierto revolucionario', '**Grand Theft Auto: San Andreas** redefine el género de mundo abierto con una historia rica y libertad de exploración.\r\n\r\n### Sobre Orion Classic Collection\r\n\r\nPara conmemorar el lanzamiento de Orion, hemos colaborado con referentes en la industria para lanzar una colección de 20 juegos icónicos y puedas empezar a crear tu biblioteca en Orion', '2024-12-02 23:00:00', '2004-10-26', 19.99, 0.15, 1, 1, 'Rockstar Games', 1);

-- Volcando datos para la tabla orion.guides: ~0 rows (aproximadamente)

-- Volcando datos para la tabla orion.guide_types: ~1 rows (aproximadamente)
INSERT INTO `guide_types` (`id`, `icon`, `type`, `tint`) VALUES
	(1, 'MTY0bjlsK2pRL0NYYWxDOVF4OTdUUFZvT21SVjdxbml4eHBlRHJqaVNFbz0=', 'Logro', '#DEAB18'),
	(2, 'cm5zQzFwSWE4eE1ya3JYSFdEZzJQLzU1WjkvWXVuQXdSY044ZU84OHlHZz0=', 'Nivel/Mapa', '#978af4');

-- Volcando datos para la tabla orion.leaderboards: ~0 rows (aproximadamente)

-- Volcando datos para la tabla orion.owns: ~0 rows (aproximadamente)
INSERT INTO `owns` (`user_id`, `game_id`, `checkout_id`) VALUES
	(5, 9, 'cs_test_a1zIeQidrbi11bX4T1AfN0LrJr2k9ElvQEXutRggPl04eQ35H45aIWMAIR');

-- Volcando datos para la tabla orion.posts: ~0 rows (aproximadamente)

-- Volcando datos para la tabla orion.unlocks: ~0 rows (aproximadamente)

-- Volcando datos para la tabla orion.users: ~3 rows (aproximadamente)
INSERT INTO `users` (`id`, `email`, `username`, `password`, `birthdate`, `role`, `profile_pic`, `motd`, `badge_id`, `created_at`) VALUES
	(2, 'abehsosa2004@gmail.com', 'Abel', '$2y$10$rHY66BW.lSKkU0P8485kreEg4114dyiXPiFD4NePMkSS1polkNV6C', '2004-05-25', 0, NULL, '', NULL, '2024-12-01 18:04:42');

-- Volcando datos para la tabla orion.votes: ~0 rows (aproximadamente)

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
