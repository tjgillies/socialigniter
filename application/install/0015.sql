ALTER TABLE  `connections` ADD  `site_id` INT( 6 ) NOT NULL AFTER  `connection_id`;
ALTER TABLE  `sites` DROP  `tagline` , DROP `description` , DROP `keywords`;
ALTER TABLE  `sites` ADD  `module` CHAR( 16 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL AFTER  `url`;
ALTER TABLE  `relationships` ADD  `site_id` INT( 6 ) NOT NULL AFTER  `relationship_id`;
ALTER TABLE  `relationships` ADD  `module` CHAR( 16 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL AFTER  `user_id`;
ALTER TABLE  `relationships` ADD  `updated_at` DATETIME NOT NULL AFTER  `created_at`;