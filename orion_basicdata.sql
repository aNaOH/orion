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

INSERT INTO `game_features` (`id`, `icon`, `name`, `tint`) VALUES
    (1, 'RnF1OS9BaFBnZFVEay9hUXBMZzZwMVc3amtSZ2Y4TDlBYTJDWjY0SkVCbz0=', 'Un jugador', '#AE8FB3'),
    (2, 'NzlUQ3dlVkx6WW1Ldk9LUXVXbllGa2plRWJlOG1SRlQ5RzBsNCtqN0lCMD0=', 'Multijugador Local', '#B71EE6'),
    (3, 'ZmxpbkFqdUk0N2lQai9ZNTlNMjFJWlBwditaUTJHMmtxU1J1WGpTYTNJMD0=', 'Multijugador Online', '#DA8AE0'),
    (4, 'cGFWbmc5SzdwajJQNTJYRkpleTZxL3J0ZVQ4WlorN0o4d09QYTRSZDBrND0=', 'Cooperativo', '#E39A1C'),
    (5, 'SXh6d2prQ1M3Y1orOFMyS3FlZktqSURGSHRBbVNrNVplTGZHY2NoSEVnZz0=', 'Competitivo', '#855A2A'),
    (6, 'cGFWbmc5SzdwajJQNTJYRkpleTZxODV5TFplbFc2cEt5aDY4bWhkVDFHUT0=', 'Juego Cruzado (PC)', '#FF5733'),
    (7, 'cGFWbmc5SzdwajJQNTJYRkpleTZxOUhBZG9Id014ZklreWRQWW5BNTE1UT0=', 'Juego Cruzado (Multiplataforma)', '#9F403C'),
    (8, 'aDZSdXFrb0JNLy9xbFJLdVl1WTNValVlUmhuVmlUUkNWWmRRY3hoL3FDTT0=', 'Soporte de mods', '#143778'),
    (9, 'aDZSdXFrb0JNLy9xbFJLdVl1WTNVaEVEQUhXSC9mZGZ3dTR1OEZ0Z2p0UT0=', 'Soporte de mods (mod.io)', '#07C1D8'),
    (10, 'cGFWbmc5SzdwajJQNTJYRkpleTZxK3JnSlhQY3NOc0NhcitOdmlZSGV3Zz0=', 'Guardado en la nube', '#9F403C'),
    (11, 'cGFWbmc5SzdwajJQNTJYRkpleTZxd1JNNXdOTFpsSFNpYmN3STAyNmN6WT0=', 'Guardado Multiplataforma', '#38912A'),
    (12, 'Z1RMVnBnaVBWUXcwMkpQandVMDdVU3ZaWU1GRy94TUQyNXFlTlRKcURHZz0=', 'Soporte de Mando', '#5A8A71');
