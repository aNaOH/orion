-- ===========================
-- SISTEMA DE TICKETS
-- ===========================

CREATE TABLE IF NOT EXISTS `tickets` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `user_id` INT NOT NULL,
  `type` VARCHAR(50) NOT NULL,
  `status` TINYINT NOT NULL DEFAULT 0, -- 0: Pendiente, 1: Aceptado, 2: Rechazado
  `admin_comment` TEXT DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_tickets_user_id` (`user_id`),
  CONSTRAINT `fk_tickets_user_id`
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `ticket_report_user` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `ticket_id` INT NOT NULL,
  `reported_user_id` INT NOT NULL,
  `reason` VARCHAR(50) NOT NULL,
  `description` TEXT NOT NULL,
  `snapshot` JSON NOT NULL, -- { "username": "...", "motd": "...", "profile_pic": "key" }
  PRIMARY KEY (`id`),
  KEY `fk_report_user_ticket_id` (`ticket_id`),
  KEY `fk_report_user_reported_id` (`reported_user_id`),
  CONSTRAINT `fk_report_user_ticket_id`
    FOREIGN KEY (`ticket_id`) REFERENCES `tickets` (`id`)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_report_user_reported_id`
    FOREIGN KEY (`reported_user_id`) REFERENCES `users` (`id`)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `user_suspensions` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `user_id` INT NOT NULL,
  `ticket_id` INT DEFAULT NULL,
  `reason` VARCHAR(255) NOT NULL,
  `admin_comment` TEXT DEFAULT NULL,
  `starts_at` DATETIME NOT NULL,
  `ends_at` DATETIME DEFAULT NULL,
  `is_active` TINYINT(1) NOT NULL DEFAULT '1',
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_user_suspensions_user_id` (`user_id`),
  KEY `fk_user_suspensions_ticket_id` (`ticket_id`),
  CONSTRAINT `fk_user_suspensions_user_id`
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_user_suspensions_ticket_id`
    FOREIGN KEY (`ticket_id`) REFERENCES `tickets` (`id`)
    ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB;
