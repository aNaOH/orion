CREATE TABLE `users` (
  `id` integer PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` integer NOT NULL,
  `profile_pic` varchar(255),
  `motd` varchar(255),
  `badge` integer,
  `created_at` timestamp
);

CREATE TABLE `owns` (
  `user` integer NOT NULL,
  `game` integer NOT NULL,
  `base_price` float NOT NULL,
  `discount` float NOT NULL
);

CREATE TABLE `developers` (
  `id` integer PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `profile_pic` varchar(255),
  `motd` varchar(255),
  `owner` integer NOT NULL
);

CREATE TABLE `game` (
  `id` integer PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `title` varchar(255),
  `short_description` varchar(255),
  `description` text COMMENT 'Content of the post',
  `launch_date` timestamp,
  `base_price` float,
  `discount` float,
  `file` varchar(255),
  `version` varchar(255),
  `developer` integer NOT NULL
);

CREATE TABLE `posts` (
  `id` integer PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `body` text NOT NULL,
  `created_at` timestamp,
  `last_updated_at` timestamp,
  `is_public` bool DEFAULT false,
  `type` integer,
  `game` integer,
  `author` integer
);

CREATE TABLE `gallery_entries` (
  `post_id` integer PRIMARY KEY NOT NULL,
  `media` varchar(255) NOT NULL
);

CREATE TABLE `guides` (
  `post_id` integer PRIMARY KEY NOT NULL,
  `type` integer NOT NULL
);

CREATE TABLE `guide_types` (
  `id` integer PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `icon` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL
);

CREATE TABLE `comments` (
  `author` integer NOT NULL,
  `post_id` integer NOT NULL
);

CREATE TABLE `votes` (
  `user` integer NOT NULL,
  `is_downvote` bool NOT NULL DEFAULT false
);

CREATE TABLE `badges` (
  `id` integer PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `icon` varchar(255) NOT NULL,
  `game` integer NOT NULL
);

CREATE TABLE `badge_unlocked` (
  `badge` integer NOT NULL,
  `user` integer NOT NULL,
  `date` timestamp NOT NULL
);

CREATE TABLE `achievements` (
  `id` integer PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `icon` varchar(255) NOT NULL,
  `locked_icon` varchar(255),
  `secret` bool NOT NULL DEFAULT false,
  `game` integer NOT NULL
);

CREATE TABLE `leaderboards` (
  `id` integer PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `concept` integer NOT NULL,
  `type` integer NOT NULL,
  `game` integer NOT NULL
);

CREATE TABLE `entries` (
  `leaderboard` integer NOT NULL,
  `user` integer NOT NULL,
  `value` integer NOT NULL
);

CREATE TABLE `unlocks` (
  `achievement` integer NOT NULL,
  `user` integer NOT NULL,
  `date` timestamp NOT NULL
);

ALTER TABLE `users` ADD FOREIGN KEY (`id`) REFERENCES `posts` (`author`);

ALTER TABLE `game` ADD FOREIGN KEY (`id`) REFERENCES `posts` (`game`);

ALTER TABLE `posts` ADD FOREIGN KEY (`id`) REFERENCES `gallery_entries` (`post_id`);

ALTER TABLE `posts` ADD FOREIGN KEY (`id`) REFERENCES `guides` (`post_id`);

ALTER TABLE `guides` ADD FOREIGN KEY (`type`) REFERENCES `guide_types` (`id`);

ALTER TABLE `users` ADD FOREIGN KEY (`id`) REFERENCES `comments` (`author`);

ALTER TABLE `comments` ADD FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`);

ALTER TABLE `users` ADD FOREIGN KEY (`id`) REFERENCES `votes` (`user`);

ALTER TABLE `votes` ADD FOREIGN KEY (`is_downvote`) REFERENCES `gallery_entries` (`post_id`);

ALTER TABLE `game` ADD FOREIGN KEY (`id`) REFERENCES `leaderboards` (`game`);

ALTER TABLE `leaderboards` ADD FOREIGN KEY (`id`) REFERENCES `entries` (`leaderboard`);

ALTER TABLE `users` ADD FOREIGN KEY (`id`) REFERENCES `entries` (`user`);

ALTER TABLE `game` ADD FOREIGN KEY (`id`) REFERENCES `achievements` (`game`);

ALTER TABLE `achievements` ADD FOREIGN KEY (`id`) REFERENCES `unlocks` (`achievement`);

ALTER TABLE `users` ADD FOREIGN KEY (`id`) REFERENCES `unlocks` (`user`);

ALTER TABLE `users` ADD FOREIGN KEY (`badge`) REFERENCES `badges` (`id`);

ALTER TABLE `game` ADD FOREIGN KEY (`id`) REFERENCES `badges` (`game`);

ALTER TABLE `badges` ADD FOREIGN KEY (`id`) REFERENCES `badge_unlocked` (`badge`);

ALTER TABLE `users` ADD FOREIGN KEY (`id`) REFERENCES `badge_unlocked` (`user`);

ALTER TABLE `users` ADD FOREIGN KEY (`id`) REFERENCES `developers` (`owner`);

ALTER TABLE `developers` ADD FOREIGN KEY (`id`) REFERENCES `game` (`developer`);

ALTER TABLE `owns` ADD FOREIGN KEY (`user`) REFERENCES `users` (`id`);

ALTER TABLE `owns` ADD FOREIGN KEY (`game`) REFERENCES `game` (`id`);
