INSERT INTO `users` (`id`, `email`, `username`, `password`, `birthdate`, `role`, `profile_pic`, `motd`, `badge_id`, `created_at`) VALUES
	(1, 'admin@togetheronorion.com', 'Orion', '$2y$10$rHY66BW.lSKkU0P8485kreEg4114dyiXPiFD4NePMkSS1polkNV6C', '2004-11-28', 1, 'czNVL0pobW53cWxmSTU4Y1RtOWhyUT09', 'Orion\'s official account', NULL, '2024-10-30 15:24:12');

INSERT INTO `game_genres` (`id`, `name`, `tint`) VALUES
	(1, 'Mundo Abierto', '#FF5733'),
	(2, 'RPG', '#33FF57'),
	(3, 'Terror', '#5733FF'),
	(4, 'Plataformas', '#FF33A1'),
	(5, 'Aventura', '#33FFF5'),
	(6, 'Arcade', '#F5FF33'),
	(7, 'Puzle', '#FF5733'),
	(8, 'Disparos', '#33FF57'),
	(9, 'Lucha', '#5733FF'),
	(10, 'Simulación', '#FF33A1');

INSERT INTO `game_news_categories` (`id`, `name`, `tint`) VALUES
	(1, 'Actualización', '#FF5733'),
	(2, 'Parche', '#33FF57'),
	(3, 'Novedad', '#5733FF');