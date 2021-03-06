SET NAMES utf8;
SET foreign_key_checks = 0;
SET time_zone = 'SYSTEM';
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

DROP TABLE IF EXISTS `block`;
CREATE TABLE `block` (
	`block_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
	`block_type_id` bigint(20) unsigned NOT NULL,
	`page_id` bigint(20) unsigned NOT NULL,
  `size` ENUM ('1x1','2x1'),
  `classes` tinytext NULL,
  `style` tinytext NULL,
  `link` tinytext NULL,
  `param1` text NULL,
  `param2` text NULL,
  `param3` text NULL,
	`weight` int(11) NOT NULL,
	PRIMARY KEY (`block_id`),
	KEY `page_id` (`page_id`),
	KEY `block_type_id` (`block_type_id`),
	CONSTRAINT `block_ibfk_1` FOREIGN KEY (`page_id`) REFERENCES `page` (`page_id`),
	CONSTRAINT `block_ibfk_2` FOREIGN KEY (`block_type_id`) REFERENCES `list_block_type` (`block_type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;


DROP TABLE IF EXISTS `event`;
CREATE TABLE `event` (
	`event_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
	`name` tinytext COLLATE utf8_czech_ci NOT NULL,
	`url` varchar(255) COLLATE utf8_czech_ci NOT NULL,
	`description` tinytext COLLATE utf8_czech_ci NOT NULL,
	`extra_styles` text COLLATE utf8_czech_ci NOT NULL,
	`date` datetime NOT NULL,
	`registration_opened` datetime NOT NULL,
	`registration_closed` datetime NOT NULL,
	`hidden` tinyint(1) NOT NULL,
	UNIQUE KEY `url` (`url`),
	PRIMARY KEY (`event_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;


DROP TABLE IF EXISTS `list_block_type`;
CREATE TABLE `list_block_type` (
	`block_type_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
	`name` tinytext COLLATE utf8_czech_ci NOT NULL,
	PRIMARY KEY (`block_type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

INSERT INTO `list_block_type` (`block_type_id`, `name`) VALUES
(1,	'vtext'),
(2,	'text'),
(3,	'image'),
(4,	'registration');

DROP TABLE IF EXISTS `page`;
CREATE TABLE `page` (
	`page_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
	`event_id` bigint(20) unsigned NULL,
  `is_block_page` tinyint(1) unsigned NOT NULL,
  `name` tinytext NOT NULL,
  `content` text NULL,
  `gallery_path` text NULL,
  `url` varchar(255) NOT NULL,
	`is_default` tinyint(1) NOT NULL,
	`inserted` datetime NOT NULL,
	`updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	PRIMARY KEY (`page_id`),
	KEY `event_id` (`event_id`),
	CONSTRAINT `page_ibfk_2` FOREIGN KEY (`event_id`) REFERENCES `event` (`event_id`) ON DELETE CASCADE,
  CONSTRAINT `page_ibfk_3` UNIQUE (`event_id`, `url`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
	`user_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
	`role` enum('ADMIN','MANAGER','GUEST') NOT NULL,
	`nickname` varchar(255) COLLATE utf8_czech_ci NOT NULL,
	`name` tinytext COLLATE utf8_czech_ci NOT NULL,
	`password` varchar(64) COLLATE utf8_czech_ci NOT NULL,
	`inserted` datetime NOT NULL,
	`updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	`last_login` datetime NOT NULL,
	PRIMARY KEY (`user_id`),
	UNIQUE KEY `nickname` (`nickname`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

INSERT INTO `user` (`user_id`, `role`, `nickname`, `name`, `password`, `inserted`, `updated`, `last_login`) VALUES
(1,	'ADMIN',	'repli2dev',	'Jan Drábek',	'53d887d55459501cd34d7a18fbb94e882a165926970bb00cce1fe5ca3af93612',	NOW(),	NOW(),	'0000-00-00 00:00:00');