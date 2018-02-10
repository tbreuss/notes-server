
CREATE TABLE `article_likes` (
  `article_id` int(11) unsigned NOT NULL,
  `user_id` int(11) unsigned NOT NULL,
  `created` datetime DEFAULT NULL,
  PRIMARY KEY (`article_id`,`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `article_views` (
  `article_id` int(11) unsigned NOT NULL,
  `user_id` int(11) unsigned NOT NULL DEFAULT '0',
  `created` date NOT NULL DEFAULT '0000-00-00',
  PRIMARY KEY (`article_id`,`user_id`,`created`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `articles` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `tags` varchar(255) NOT NULL,
  `views` int(10) unsigned NOT NULL DEFAULT '0',
  `likes` int(10) unsigned NOT NULL DEFAULT '0',
  `created` datetime DEFAULT NULL,
  `created_by` int(10) unsigned DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `modified_by` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `tags` (`tags`),
  KEY `created` (`created`),
  KEY `modified` (`modified`),
  KEY `title` (`title`),
  KEY `views` (`views`),
  KEY `likes` (`likes`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `history` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned DEFAULT NULL,
  `article_id` int(11) unsigned DEFAULT NULL,
  `content` text,
  `created` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `tags` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(128) DEFAULT NULL,
  `frequency` int(11) unsigned NOT NULL DEFAULT '0',
  `created` datetime DEFAULT NULL,
  `created_by` int(10) unsigned DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `modified_by` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `frequency` (`frequency`),
  KEY `name` (`name`),
  KEY `created` (`created`),
  KEY `modified` (`modified`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

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

INSERT INTO `users` (`username`, `password`, `salt`, `name`, `email`, `role`, `scopes`, `article_likes`, `article_views`, `lastlogin`, `deleted`, `created`, `modified`)
VALUES
  ('user', '710b51dff55228b5668b3a2795ec4f10', '5a68d4c4256007.12366978', 'User', 'user@example.com', 'user', '["article.add","article.delete","article.edit"]', 0, 0, NULL, 0, NOW(), NULL);
