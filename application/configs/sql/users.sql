/**
 *  Table for maintaining user information.
 */
DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
    `user_id` INT (4) NOT NULL AUTO_INCREMENT,
    `username` VARCHAR(64) DEFAULT '' NOT NULL,
    `password` VARCHAR(128) DEFAULT '' NOT NULL,
    `salt` VARCHAR(16) DEFAULT '' NOT NULL,
    `email` VARCHAR(255) DEFAULT '' NOT NULL,
    PRIMARY KEY (`user_id`),
    KEY `user_idx` (`user_id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=0;

