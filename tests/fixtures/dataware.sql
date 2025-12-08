CREATE TABLE `pages` (
  `page_wikia_id` int unsigned NOT NULL,
  `page_id` int unsigned NOT NULL,
  `page_namespace` int unsigned NOT NULL DEFAULT '0',
  `page_title` varchar(255) NOT NULL,
  `page_is_content` tinyint unsigned NOT NULL DEFAULT '0',
  `page_is_redirect` tinyint unsigned NOT NULL DEFAULT '0',
  `page_latest` int unsigned NOT NULL DEFAULT '0',
  `page_last_edited` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `page_created_at` timestamp NULL,
  PRIMARY KEY (`page_wikia_id`,`page_id`)
);

CREATE INDEX `page_title_namespace_latest_idx` ON `pages` (`page_title`,`page_namespace`,`page_latest`);
-- Dump completed on 2024-06-15 12:00:00

CREATE TABLE `users` (
  `user_id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_name` varchar(255) NOT NULL,
  `user_email` varchar(255) DEFAULT NULL,
  `user_created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`user_id`)
);

CREATE INDEX `user_name_idx` ON `users` (`user_name`);
-- Dump completed on 2024-06-15 12:00:00

CREATE TABLE `revisions` (
  `rev_id` int unsigned NOT NULL AUTO_INCREMENT,
  `rev_page` int unsigned NOT NULL,
  `rev_text_id` int unsigned NOT NULL,
  `rev_user` int unsigned DEFAULT NULL,
  `rev_timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`rev_id`)
);