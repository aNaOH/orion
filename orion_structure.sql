-- -----------------------------------------------------
-- Estructura de la base de datos ORION
-- -----------------------------------------------------

-- ===========================
-- TABLAS BASE
-- ===========================

CREATE TABLE IF NOT EXISTS `users` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `email` VARCHAR(255) NOT NULL,
  `username` VARCHAR(100) NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  `birthdate` DATE NOT NULL,
  `role` INT NOT NULL,
  `profile_pic` VARCHAR(255) DEFAULT NULL,
  `motd` VARCHAR(255) DEFAULT NULL,
  `badge_id` INT DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `is_archived` TINYINT(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_users_email` (`email`),
  KEY `fk_users_badge_id` (`badge_id`)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `game_genres` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `tint` VARCHAR(7) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `guide_types` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `icon` VARCHAR(255) NOT NULL,
  `type` VARCHAR(100) NOT NULL,
  `tint` VARCHAR(7) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `game_features` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `icon` VARCHAR(255) NOT NULL,
  `name` VARCHAR(100) NOT NULL,
  `tint` VARCHAR(7) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `game_news_categories` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `tint` VARCHAR(7) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB;

-- ===========================
-- TABLAS DEPENDIENTES DE USERS
-- ===========================

CREATE TABLE IF NOT EXISTS `developers` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `profile_pic` VARCHAR(255) DEFAULT NULL,
  `motd` VARCHAR(255) DEFAULT NULL,
  `owner_id` INT NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_developers_owner_id` (`owner_id`),
  CONSTRAINT `fk_developers_owner_id`
    FOREIGN KEY (`owner_id`) REFERENCES `users` (`id`)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

-- ===========================
-- TABLAS DE JUEGOS
-- ===========================

CREATE TABLE IF NOT EXISTS `game` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(255) NOT NULL,
  `short_description` VARCHAR(255) DEFAULT NULL,
  `description` TEXT,
  `launch_date` TIMESTAMP NULL DEFAULT NULL,
  `original_launch_date` DATE DEFAULT NULL,
  `base_price` FLOAT DEFAULT NULL,
  `discount` FLOAT DEFAULT NULL,
  `as_editor` TINYINT(1) NOT NULL DEFAULT '0',
  `is_public` TINYINT(1) NOT NULL DEFAULT '0',
  `developer_name` VARCHAR(255) DEFAULT NULL,
  `developer_id` INT DEFAULT NULL,
  `genre_id` INT DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_game_developer_id` (`developer_id`),
  KEY `fk_game_genre_id` (`genre_id`),
  CONSTRAINT `fk_game_developer_id`
    FOREIGN KEY (`developer_id`) REFERENCES `developers` (`id`)
    ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_game_genre_id`
    FOREIGN KEY (`genre_id`) REFERENCES `game_genres` (`id`)
    ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB;

-- ===========================
-- TABLAS RELACIONADAS CON GAME
-- ===========================

CREATE TABLE IF NOT EXISTS `badges` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(100) NOT NULL,
  `description` VARCHAR(255) NOT NULL,
  `icon` VARCHAR(255) NOT NULL,
  `game_id` INT DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_badges_game_id` (`game_id`),
  CONSTRAINT `fk_badges_game_id`
    FOREIGN KEY (`game_id`) REFERENCES `game` (`id`)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

ALTER TABLE `users`
  ADD CONSTRAINT `fk_users_badge_id`
  FOREIGN KEY (`badge_id`) REFERENCES `badges` (`id`)
  ON DELETE SET NULL ON UPDATE CASCADE;

CREATE TABLE IF NOT EXISTS `stat` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `game_id` INT NOT NULL,
  `name` VARCHAR(255) NOT NULL,
  `number` INT NOT NULL,
  `type` INT NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_stat_game_id` (`game_id`),
  CONSTRAINT `fk_stat_game_id`
    FOREIGN KEY (`game_id`) REFERENCES `game` (`id`)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `achievements` (
  `game_id` INT DEFAULT NULL,
  `id` INT NOT NULL,
  `name` VARCHAR(100) NOT NULL,
  `description` VARCHAR(255) NOT NULL,
  `icon` VARCHAR(255) NOT NULL,
  `locked_icon` VARCHAR(255) DEFAULT NULL,
  `secret` TINYINT(1) DEFAULT '0',
  `type` INT NOT NULL,
  `stat_id` INT DEFAULT NULL,
  `stat_value` INT DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_achievements_game_id` (`game_id`),
  KEY `fk_achievements_stat_id` (`stat_id`),
  CONSTRAINT `fk_achievements_game_id`
    FOREIGN KEY (`game_id`) REFERENCES `game` (`id`)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_achievements_stat_id`
    FOREIGN KEY (`stat_id`) REFERENCES `stat` (`id`)
    ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `leaderboards` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `concept` INT NOT NULL,
  `game_id` INT DEFAULT NULL,
  `stat_id` INT DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_leaderboards_game_id` (`game_id`),
  KEY `fk_leaderboards_stat_id` (`stat_id`),
  CONSTRAINT `fk_leaderboards_game_id`
    FOREIGN KEY (`game_id`) REFERENCES `game` (`id`)
    ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_leaderboards_stat_id`
    FOREIGN KEY (`stat_id`) REFERENCES `stat` (`id`)
    ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `builds` (
  `game_id` INT NOT NULL,
  `file` VARCHAR(255) NOT NULL,
  `version` VARCHAR(50) NOT NULL,
  `patch_notes` LONGTEXT,
  `release_date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  KEY `fk_builds_game_id` (`game_id`),
  CONSTRAINT `fk_builds_game_id`
    FOREIGN KEY (`game_id`) REFERENCES `game` (`id`)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `owns` (
  `user_id` INT NOT NULL,
  `game_id` INT NOT NULL,
  `checkout_id` TEXT NOT NULL,
  KEY `fk_owns_user_id` (`user_id`),
  KEY `fk_owns_game_id` (`game_id`),
  CONSTRAINT `fk_owns_user_id`
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_owns_game_id`
    FOREIGN KEY (`game_id`) REFERENCES `game` (`id`)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `game_has_feature` (
  `game_id` INT NOT NULL,
  `feature_id` INT NOT NULL,
  KEY `fk_game_has_feature_game_id` (`game_id`),
  KEY `fk_game_has_feature_feature_id` (`feature_id`),
  CONSTRAINT `fk_game_has_feature_game_id`
    FOREIGN KEY (`game_id`) REFERENCES `game` (`id`)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_game_has_feature_feature_id`
    FOREIGN KEY (`feature_id`) REFERENCES `game_features` (`id`)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

-- ===========================
-- PUBLICACIONES Y CONTENIDO
-- ===========================

CREATE TABLE IF NOT EXISTS `posts` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(255) NOT NULL,
  `body` LONGTEXT NOT NULL,
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `last_updated_at` TIMESTAMP NULL DEFAULT NULL,
  `is_public` TINYINT(1) DEFAULT '0',
  `type` INT DEFAULT NULL,
  `game_id` INT DEFAULT NULL,
  `author_id` INT DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_posts_game_id` (`game_id`),
  KEY `fk_posts_author_id` (`author_id`),
  CONSTRAINT `fk_posts_game_id`
    FOREIGN KEY (`game_id`) REFERENCES `game` (`id`)
    ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_posts_author_id`
    FOREIGN KEY (`author_id`) REFERENCES `users` (`id`)
    ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `comments` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `author_id` INT NOT NULL,
  `post_id` INT NOT NULL,
  `body` TEXT NOT NULL,
  `date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_comments_author_id` (`author_id`),
  KEY `fk_comments_post_id` (`post_id`),
  CONSTRAINT `fk_comments_author_id`
    FOREIGN KEY (`author_id`) REFERENCES `users` (`id`)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_comments_post_id`
    FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `votes` (
  `user_id` INT NOT NULL,
  `post_id` INT NOT NULL,
  `modifier` TINYINT(1) DEFAULT '0',
  KEY `fk_votes_user_id` (`user_id`),
  KEY `fk_votes_post_id` (`post_id`),
  CONSTRAINT `fk_votes_user_id`
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_votes_post_id`
    FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `gallery_entries` (
  `post_id` INT NOT NULL,
  `media` VARCHAR(255) NOT NULL,
  PRIMARY KEY (`post_id`),
  CONSTRAINT `fk_gallery_entries_post_id`
    FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `guides` (
  `post_id` INT NOT NULL,
  `type_id` INT NOT NULL,
  PRIMARY KEY (`post_id`),
  KEY `fk_guides_type_id` (`type_id`),
  CONSTRAINT `fk_guides_post_id`
    FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_guides_type_id`
    FOREIGN KEY (`type_id`) REFERENCES `guide_types` (`id`)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `game_news` (
  `post_id` INT NOT NULL,
  `category_id` INT NOT NULL,
  PRIMARY KEY (`post_id`),
  CONSTRAINT `fk_game_news_post_id`
    FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_game_news_category_id`
    FOREIGN KEY (`category_id`) REFERENCES `game_news_categories` (`id`)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

-- ===========================
-- ESTADÍSTICAS Y LOGROS
-- ===========================

CREATE TABLE IF NOT EXISTS `has_stat` (
  `user_id` INT NOT NULL,
  `stat_id` INT NOT NULL,
  `value` INT NOT NULL,
  KEY `fk_has_stat_user_id` (`user_id`),
  KEY `fk_has_stat_stat_id` (`stat_id`),
  CONSTRAINT `fk_has_stat_user_id`
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_has_stat_stat_id`
    FOREIGN KEY (`stat_id`) REFERENCES `stat` (`id`)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `unlocks` (
  `game_id` INT NOT NULL,
  `achievement_id` INT NOT NULL,
  `user_id` INT NOT NULL,
  `date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  KEY `fk_unlocks_achievement_id` (`achievement_id`),
  KEY `fk_unlocks_user_id` (`user_id`),
  CONSTRAINT `fk_unlocks_game_id`
    FOREIGN KEY (`game_id`) REFERENCES `games` (`id`)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_unlocks_achievement_id`
    FOREIGN KEY (`achievement_id`) REFERENCES `achievements` (`id`)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_unlocks_user_id`
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `badge_unlocked` (
  `badge_id` INT NOT NULL,
  `user_id` INT NOT NULL,
  `date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  KEY `fk_badge_unlocked_badge_id` (`badge_id`),
  KEY `fk_badge_unlocked_user_id` (`user_id`),
  CONSTRAINT `fk_badge_unlocked_badge_id`
    FOREIGN KEY (`badge_id`) REFERENCES `badges` (`id`)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_badge_unlocked_user_id`
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;
