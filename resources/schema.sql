SET NAMES utf8;
SET foreign_key_checks = 0;
SET time_zone = 'SYSTEM';
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

DROP TABLE IF EXISTS `block`;
CREATE TABLE `block` (
	`block_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
	`block_type_id` bigint(20) unsigned NOT NULL,
	`page_id` bigint(20) unsigned NOT NULL,
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
	`description` tinytext COLLATE utf8_czech_ci NOT NULL,
	`date` date NOT NULL,
	`time` time NOT NULL,
	`hidden` tinyint(1) NOT NULL,
	PRIMARY KEY (`event_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;


DROP TABLE IF EXISTS `layout_parameter`;
CREATE TABLE `layout_parameter` (
	`layout_id` bigint(20) unsigned NOT NULL,
	`parameter_type_id` bigint(20) unsigned NOT NULL,
	`weight` int(11) NOT NULL,
	UNIQUE KEY `layout_id_parameter_type_id` (`layout_id`,`parameter_type_id`),
	KEY `parameter_type_id` (`parameter_type_id`),
	CONSTRAINT `layout_parameter_ibfk_1` FOREIGN KEY (`layout_id`) REFERENCES `list_layout` (`layout_id`),
	CONSTRAINT `layout_parameter_ibfk_2` FOREIGN KEY (`parameter_type_id`) REFERENCES `list_parameter_type` (`parameter_type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

INSERT INTO `layout_parameter` (`layout_id`, `parameter_type_id`, `weight`) VALUES
(1,	1,	1),
(2,	1,	1),
(2,	2,	2),
(3,	1,	1),
(3,	3,	2);

DROP TABLE IF EXISTS `list_block_type`;
CREATE TABLE `list_block_type` (
	`block_type_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
	`name` tinytext COLLATE utf8_czech_ci NOT NULL,
	PRIMARY KEY (`block_type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

INSERT INTO `list_block_type` (`block_type_id`, `name`) VALUES
(1,	'vtext'),
(2,	'text'),
(3,	'image');

DROP TABLE IF EXISTS `list_layout`;
CREATE TABLE `list_layout` (
	`layout_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
	`name` tinytext COLLATE utf8_czech_ci NOT NULL,
	`file` tinytext COLLATE utf8_czech_ci NOT NULL,
	PRIMARY KEY (`layout_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

INSERT INTO `list_layout` (`layout_id`, `name`, `file`) VALUES
(1,	'blocks',	'blocks.latte'),
(2,	'plain',	'plain.latte'),
(3,	'gallery',	'gallery.latte');

DROP TABLE IF EXISTS `list_parameter_type`;
CREATE TABLE `list_parameter_type` (
	`parameter_type_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
	`name` tinytext COLLATE utf8_czech_ci NOT NULL,
	PRIMARY KEY (`parameter_type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

INSERT INTO `list_parameter_type` (`parameter_type_id`, `name`) VALUES
(1,	'name'),
(2,	'content'),
(3,	'gallery_path');

DROP TABLE IF EXISTS `page`;
CREATE TABLE `page` (
	`page_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
	`event_id` bigint(20) unsigned NOT NULL,
	`layout_id` bigint(20) unsigned NOT NULL,
	`is_default` tinyint(1) NOT NULL,
	`inserted` datetime NOT NULL,
	`updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	PRIMARY KEY (`page_id`),
	KEY `layout_id` (`layout_id`),
	KEY `event_id` (`event_id`),
	CONSTRAINT `page_ibfk_1` FOREIGN KEY (`layout_id`) REFERENCES `list_layout` (`layout_id`),
	CONSTRAINT `page_ibfk_2` FOREIGN KEY (`event_id`) REFERENCES `event` (`event_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;


DROP TABLE IF EXISTS `page_parameter`;
CREATE TABLE `page_parameter` (
	`parameter_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
	`page_id` bigint(20) unsigned NOT NULL,
	`parameter_type_id` bigint(20) unsigned NOT NULL,
	`value` text COLLATE utf8_czech_ci NOT NULL,
	PRIMARY KEY (`parameter_id`),
	KEY `page_id` (`page_id`),
	KEY `parameter_type_id` (`parameter_type_id`),
	CONSTRAINT `page_parameter_ibfk_1` FOREIGN KEY (`page_id`) REFERENCES `page` (`page_id`) ON DELETE CASCADE,
	CONSTRAINT `page_parameter_ibfk_2` FOREIGN KEY (`parameter_type_id`) REFERENCES `list_parameter_type` (`parameter_type_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;


DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
	`user_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
	`nickname` varchar(255) COLLATE utf8_czech_ci NOT NULL,
	`password` varchar(64) COLLATE utf8_czech_ci NOT NULL,
	`salt` varchar(255) COLLATE utf8_czech_ci NOT NULL,
	`inserted` datetime NOT NULL,
	`updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	`last_login` datetime NOT NULL,
	PRIMARY KEY (`user_id`),
	UNIQUE KEY `nickname` (`nickname`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;
