/**
 *  Table for keeping track of groups.
 */
DROP TABLE IF EXISTS `groups`;
CREATE TABLE IF NOT EXISTS `groups` (
    `group_id` INT (4) NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(64) DEFAULT '' NOT NULL,
    PRIMARY KEY (`group_id`),
    KEY `group_idx` (`group_id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=0;

