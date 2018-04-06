# ************************************************************
# Sequel Pro SQL dump
# Version 4541
#
# http://www.sequelpro.com/
# https://github.com/sequelpro/sequelpro
#
# Host: 127.0.0.1 (MySQL 5.6.38)
# Datenbank: notes_server_dev
# Erstellt am: 2018-04-06 04:47:05 +0000
# ************************************************************


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


# Export von Tabelle article_likes
# ------------------------------------------------------------

DROP TABLE IF EXISTS `article_likes`;

CREATE TABLE `article_likes` (
  `article_id` int(11) unsigned NOT NULL,
  `user_id` int(11) unsigned NOT NULL,
  `created` datetime DEFAULT NULL,
  PRIMARY KEY (`article_id`,`user_id`),
  KEY `user2` (`user_id`),
  CONSTRAINT `article_likes_article_id` FOREIGN KEY (`article_id`) REFERENCES `articles` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `article_likes_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Export von Tabelle article_to_tag
# ------------------------------------------------------------

DROP TABLE IF EXISTS `article_to_tag`;

CREATE TABLE `article_to_tag` (
  `article_id` int(11) unsigned NOT NULL,
  `tag_id` int(11) unsigned NOT NULL DEFAULT '0',
  `created` datetime NOT NULL,
  PRIMARY KEY (`article_id`,`tag_id`),
  KEY `tag` (`tag_id`),
  CONSTRAINT `article_to_tag_article_id` FOREIGN KEY (`article_id`) REFERENCES `articles` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `article_to_tag_tag_id` FOREIGN KEY (`tag_id`) REFERENCES `tags` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


# Export von Tabelle article_views
# ------------------------------------------------------------

DROP TABLE IF EXISTS `article_views`;

CREATE TABLE `article_views` (
  `article_id` int(11) unsigned NOT NULL,
  `user_id` int(11) unsigned NOT NULL DEFAULT '0',
  `created` date NOT NULL DEFAULT '0000-00-00',
  PRIMARY KEY (`article_id`,`user_id`,`created`),
  KEY `user1` (`user_id`),
  CONSTRAINT `article_views_article_id` FOREIGN KEY (`article_id`) REFERENCES `articles` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `article_views_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


# Export von Tabelle articles
# ------------------------------------------------------------

DROP TABLE IF EXISTS `articles`;

CREATE TABLE `articles` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `tags_BACKUP` varchar(255) DEFAULT NULL,
  `tag_ids` varchar(255) DEFAULT NULL,
  `views` int(10) unsigned NOT NULL DEFAULT '0',
  `likes` int(10) unsigned NOT NULL DEFAULT '0',
  `created` datetime DEFAULT NULL,
  `created_by` int(10) unsigned DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `modified_by` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `tags` (`tags_BACKUP`),
  KEY `created` (`created`),
  KEY `modified` (`modified`),
  KEY `title` (`title`),
  KEY `views` (`views`),
  KEY `likes` (`likes`),
  KEY `articles_created_by` (`created_by`),
  KEY `articles_` (`modified_by`),
  CONSTRAINT `articles_modified_by` FOREIGN KEY (`modified_by`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `articles_created_by` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


# Export von Tabelle history
# ------------------------------------------------------------

DROP TABLE IF EXISTS `history`;

CREATE TABLE `history` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned DEFAULT NULL,
  `article_id` int(11) unsigned DEFAULT NULL,
  `content` text,
  `created` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `history` (`user_id`),
  KEY `history_article_id` (`article_id`),
  CONSTRAINT `history_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `history_article_id` FOREIGN KEY (`article_id`) REFERENCES `articles` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Export von Tabelle tags
# ------------------------------------------------------------

DROP TABLE IF EXISTS `tags`;

CREATE TABLE `tags` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(128) DEFAULT NULL,
  `frequency` int(11) unsigned NOT NULL DEFAULT '0',
  `created` datetime DEFAULT NULL,
  `created_by` int(10) unsigned DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `modified_by` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name_2` (`name`),
  KEY `frequency` (`frequency`),
  KEY `name` (`name`),
  KEY `created` (`created`),
  KEY `modified` (`modified`),
  KEY `tags_created_by` (`created_by`),
  KEY `tags_modified_by` (`modified_by`),
  CONSTRAINT `tags_created_by` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `tags_modified_by` FOREIGN KEY (`modified_by`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


# Export von Tabelle users
# ------------------------------------------------------------

DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(50) DEFAULT NULL,
  `password` varchar(32) DEFAULT NULL,
  `salt` varchar(23) DEFAULT NULL,
  `name` varchar(50) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `role` enum('guest','user','admin') NOT NULL DEFAULT 'guest',
  `scopes` varchar(1024) NOT NULL DEFAULT '[]',
  `article_likes` int(10) unsigned NOT NULL DEFAULT '0',
  `article_views` int(10) unsigned DEFAULT '0',
  `lastlogin` datetime DEFAULT NULL,
  `deleted` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;

INSERT INTO `users` (`id`, `username`, `password`, `salt`, `name`, `email`, `role`, `scopes`, `article_likes`, `article_views`, `lastlogin`, `deleted`, `created`, `modified`)
VALUES
	(1,'user','710b51dff55228b5668b3a2795ec4f10','5a68d4c4256007.12366978','User','user@example.com','user','[\"article.add\",\"article.delete\",\"article.edit\"]',0,0,'2018-04-06 06:28:19',0,'2018-01-31 19:01:49',NULL),
	(2,'guest','710b51dff55228b5668b3a2795ec4f10','5a68d4c4256007.12366978','Guest','user@example.com','guest','[\"article.add\",\"article.delete\",\"article.edit\"]',0,0,'2018-04-01 09:58:10',0,'2018-01-31 19:01:49',NULL);

/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;



/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
