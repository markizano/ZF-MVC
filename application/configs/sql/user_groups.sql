/**
 *  Table for maintaining the relation between users and groups.
 */
DROP TABLE IF EXISTS `user_groups`;
CREATE TABLE IF NOT EXISTS `user_groups` (
    `user_id` INT (4) NOT NULL,
    `group_id` INT (4) NOT NULL,
    PRIMARY KEY (`user_id`, `group_id`),
    KEY `user_idx` (`user_id`),
    KEY `group_idx` (`group_id`),
    CONSTRAINT `user_groups_user_user_id_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
    CONSTRAINT `user_groups_groups_group_id_fk` FOREIGN KEY (`group_id`) REFERENCES `groups` (`group_id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=0;

