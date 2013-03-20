# and the table for new training partner module
DROP TABLE IF EXISTS `facility_partners`;
DROP TABLE IF EXISTS `organizer_partners`;
CREATE TABLE IF NOT EXISTS `organizer_partners` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uuid` char(36) DEFAULT NULL,
  `organizer_id` int(11) NOT NULL,
  `partner1_name` varchar(64) NULL DEFAULT '',
  `subpartner` varchar(64) DEFAULT '',
  `mechanism_id` varchar(32) DEFAULT '',
  `funder_name` varchar(64) DEFAULT '',
  `funder_id` varchar(32) DEFAULT '',
  `timestamp_created` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified_by` int(11) DEFAULT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) DEFAULT CHARSET=utf8;
ALTER TABLE  `organizer_partners` ADD INDEX (`mechanism_id`);

DROP TRIGGER IF EXISTS `organizer_partners_insert`;
DELIMITER $$
CREATE TRIGGER `organizer_partners_insert` BEFORE INSERT ON `organizer_partners` FOR EACH ROW BEGIN
  SET NEW.`uuid` = IFNULL(NEW.`uuid`,UUID());
END$$
DELIMITER ;
########

#add 3 translations for new feature
INSERT INTO  `translation` VALUES (NULL , NULL ,  'Facility Comments',  'Facility Comments', NULL , NULL ,  '0',  CURRENT_TIMESTAMP ,  '0000-00-00 00:00:00');
INSERT INTO  `translation` VALUES (NULL , NULL ,  'Qualification Comments',  'Qualification Comments', NULL , NULL ,  '0',  CURRENT_TIMESTAMP ,  '0000-00-00 00:00:00');
INSERT INTO  `translation` VALUES (NULL , NULL ,  'Training Comments',  'Training Comments', NULL , NULL ,  '0',  CURRENT_TIMESTAMP ,  '0000-00-00 00:00:00');

#remove unique requirement of facility_name column
ALTER TABLE `facility` DROP INDEX `facility_name`, ADD INDEX `facility_name`(facility_name, location_id);
#  add system settings for new training partner module
ALTER TABLE `_system` ADD COLUMN `display_training_partner` tinyint(1) NOT NULL DEFAULT 0;

ALTER TABLE `facility` DROP INDEX  `facility_name` , ADD UNIQUE  `facility_name` (  `facility_name` ,  `location_id` );

/* change refresher course to multi select value */
ALTER TABLE  `_system`
  ADD  `multi_opt_refresher_course` TINYINT( 1 ) NOT NULL DEFAULT '0';
ALTER TABLE `training`
  ADD `training_refresher_option_id` INT(11) NULL DEFAULT NULL;
/* tables */
DROP TABLE IF EXISTS `training_refresher_option`;
CREATE TABLE `training_refresher_option` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `refresher_phrase_option` varchar(128) NOT NULL DEFAULT '',
  `is_default` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `modified_by` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
  `timestamp_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `timestamp_created` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name_unique` (`refresher_phrase_option`),
  KEY `created_by` (`created_by`),
  KEY `modified_by` (`modified_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
DROP TABLE IF EXISTS `training_to_training_refresher_option`;
CREATE TABLE `training_to_training_refresher_option` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `training_id` int(11) NOT NULL,
  `training_refresher_option_id` int(11) NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  `timestamp_created` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_training_cat` (`training_refresher_option_id`,`training_id`),
  KEY `training_id` (`training_id`),
  KEY `created_by` (`created_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
ALTER TABLE `training_refresher_option` ADD COLUMN `uuid` char(36) AFTER `id`;
ALTER TABLE `training_refresher_option` ADD UNIQUE `uuid_idx`(uuid);
ALTER TABLE `training_refresher_option` CHANGE COLUMN `uuid` `uuid` char(36) DEFAULT NULL;
ALTER TABLE `training_to_training_refresher_option` ADD COLUMN `uuid` char(36) AFTER `id`;
ALTER TABLE `training_to_training_refresher_option` ADD UNIQUE `uuid_idx`(uuid);
ALTER TABLE `training_to_training_refresher_option` CHANGE COLUMN `uuid` `uuid` char(36) DEFAULT NULL;

DELIMITER ;;
CREATE TRIGGER `training_to_training_refresher_option_insert` BEFORE INSERT ON `training_to_training_refresher_option` FOR EACH ROW BEGIN
  SET NEW.`uuid` = IFNULL(NEW.`uuid`,UUID());
  END;;
CREATE TRIGGER `training_refresher_option_insert` BEFORE INSERT ON `training_refresher_option` FOR EACH ROW BEGIN
  SET NEW.`uuid` = IFNULL(NEW.`uuid`,UUID());
  END;;
DELIMITER ;

/*UPDATE training_to_training_refresher_option SET uuid = UUID(); */
/*UPDATE training_refresher_option SET uuid = UUID(); */
/* end live refresher update */


# migration of users
# logic - select users with acl 'edit_course' and give them matching acl's in the new system equal to power as edit_course used to be.
INSERT INTO user_to_acl (acl_id, user_id, created_by, timestamp_created) SELECT 'acl_editor_training_organizer', user_id, 1, NOW() from user_to_acl where acl_id = 'edit_course' ;
INSERT INTO user_to_acl (acl_id, user_id, created_by, timestamp_created) SELECT 'edit_training_location', user_id, 1, NOW() from user_to_acl where acl_id = 'edit_course' ;
INSERT INTO user_to_acl (acl_id, user_id, created_by, timestamp_created) SELECT 'edit_facility', user_id, 1, NOW() from user_to_acl where acl_id = 'edit_course' ;
INSERT INTO user_to_acl (acl_id, user_id, created_by, timestamp_created) SELECT 'acl_editor_training_level', user_id, 1, NOW() from user_to_acl where acl_id = 'edit_course' ;
INSERT INTO user_to_acl (acl_id, user_id, created_by, timestamp_created) SELECT 'acl_editor_method', user_id, 1, NOW() from user_to_acl where acl_id = 'edit_course' ;
INSERT INTO user_to_acl (acl_id, user_id, created_by, timestamp_created) SELECT 'acl_editor_training_topic', user_id, 1, NOW() from user_to_acl where acl_id = 'edit_course' ;
INSERT INTO user_to_acl (acl_id, user_id, created_by, timestamp_created) SELECT 'acl_editor_funding', user_id, 1, NOW() from user_to_acl where acl_id = 'edit_course' ;
INSERT INTO user_to_acl (acl_id, user_id, created_by, timestamp_created) SELECT 'acl_editor_nationalcurriculum', user_id, 1, NOW() from user_to_acl where acl_id = 'edit_course' ;
/* Employee tracking module - for South Africa */
/* requires engenderhealth-upgrade.sql or 'edit_employee' added to acl table and user_to_acl table's acl enumeration */
ALTER TABLE  `_system` ADD  `module_employee_enabled` TINYINT( 1 ) NOT NULL DEFAULT '0' AFTER  `module_unknown_participants_enabled`;
ALTER TABLE  `_system` ADD  `employee_header` TEXT NULL DEFAULT NULL;
UPDATE `_system` SET employee_header = 'Welcome to the TrainSMART employee tracking module, users can only view their own organizations and admins can add users and edit this text through the admin control panel.' WHERE 1;
ALTER TABLE  `_system` ADD  `display_employee_employee_header`       TINYINT( 1 ) NOT NULL DEFAULT '1';
ALTER TABLE  `_system` ADD  `display_employee_partner`               TINYINT( 1 ) NOT NULL DEFAULT '1';
ALTER TABLE  `_system` ADD  `display_employee_sub_partner`           TINYINT( 1 ) NOT NULL DEFAULT '1';
ALTER TABLE  `_system` ADD  `display_employee_site_name`             TINYINT( 1 ) NOT NULL DEFAULT '1';
ALTER TABLE  `_system` ADD  `display_employee_funder`                TINYINT( 1 ) NOT NULL DEFAULT '1';
ALTER TABLE  `_system` ADD  `display_employee_funding_end_date`      TINYINT( 1 ) NOT NULL DEFAULT '1';
ALTER TABLE  `_system` ADD  `display_employee_full_time`             TINYINT( 1 ) NOT NULL DEFAULT '1';
ALTER TABLE  `_system` ADD  `display_employee_funded_hours_per_week` TINYINT( 1 ) NOT NULL DEFAULT '1';
ALTER TABLE  `_system` ADD  `display_employee_staff_category`        TINYINT( 1 ) NOT NULL DEFAULT '1';
ALTER TABLE  `_system` ADD  `display_employee_annual_cost`           TINYINT( 1 ) NOT NULL DEFAULT '1';
ALTER TABLE  `_system` ADD  `display_employee_primary_role`          TINYINT( 1 ) NOT NULL DEFAULT '1';
ALTER TABLE  `_system` ADD  `display_employee_importance`            TINYINT( 1 ) NOT NULL DEFAULT '1';
ALTER TABLE  `_system` ADD  `display_employee_contract_end_date`     TINYINT( 1 ) NOT NULL DEFAULT '1';
ALTER TABLE  `_system` ADD  `display_employee_agreement_end_date`    TINYINT( 1 ) NOT NULL DEFAULT '1';
ALTER TABLE  `_system` ADD  `display_employee_intended_transition`   TINYINT( 1 ) NOT NULL DEFAULT '1';
ALTER TABLE  `_system` ADD  `display_employee_transition_confirmed`  TINYINT( 1 ) NOT NULL DEFAULT '1';
ALTER TABLE  `_system` ADD  `display_employee_incoming_partner`      TINYINT( 1 ) NOT NULL DEFAULT '1';
ALTER TABLE  `_system` ADD  `display_employee_relationship`          TINYINT( 1 ) NOT NULL DEFAULT '1';
ALTER TABLE  `_system` ADD  `display_employee_referral_mechanism`    TINYINT( 1 ) NOT NULL DEFAULT '0';
ALTER TABLE  `_system` ADD  `display_employee_chw_supervisor`        TINYINT( 1 ) NOT NULL DEFAULT '0';
ALTER TABLE  `_system` ADD  `display_employee_trainings_provided`    TINYINT( 1 ) NOT NULL DEFAULT '0';
ALTER TABLE  `_system` ADD  `display_employee_courses_completed`     TINYINT( 1 ) NOT NULL DEFAULT '0';


INSERT INTO  `translation` VALUES (NULL , NULL ,  'Partner', 'Partner', NULL , NULL ,  '0', CURRENT_TIMESTAMP ,  '2012-10-22 00:00:00');
INSERT INTO  `translation` VALUES (NULL , NULL ,  'Sub Partner', 'Sub Partner', NULL , NULL ,  '0', CURRENT_TIMESTAMP ,  '2012-10-22 00:00:00');
INSERT INTO  `translation` VALUES (NULL , NULL ,  'Funder', 'Funder', NULL , NULL ,  '0', CURRENT_TIMESTAMP ,  '2012-10-22 00:00:00');
INSERT INTO  `translation` VALUES (NULL , NULL ,  'Full Time', 'Full Time', NULL , NULL ,  '0', CURRENT_TIMESTAMP ,  '2012-10-22 00:00:00');
INSERT INTO  `translation` VALUES (NULL , NULL ,  'Funded hours per week', 'Funded hours per week', NULL , NULL ,  '0', CURRENT_TIMESTAMP ,  '2012-10-22 00:00:00');
INSERT INTO  `translation` VALUES (NULL , NULL ,  'Staff Category', 'Staff Category', NULL , NULL ,  '0', CURRENT_TIMESTAMP ,  '2012-10-22 00:00:00');
INSERT INTO  `translation` VALUES (NULL , NULL ,  'Annual Cost', 'Annual Cost', NULL , NULL ,  '0', CURRENT_TIMESTAMP ,  '2012-10-22 00:00:00');
INSERT INTO  `translation` VALUES (NULL , NULL ,  'Primary Role', 'Primary Role', NULL , NULL ,  '0', CURRENT_TIMESTAMP ,  '2012-10-22 00:00:00');
INSERT INTO  `translation` VALUES (NULL , NULL ,  'Importance', 'Importance', NULL , NULL ,  '0', CURRENT_TIMESTAMP ,  '2012-10-22 00:00:00');
INSERT INTO  `translation` VALUES (NULL , NULL ,  'Intended Transition', 'Intended Transition', NULL , NULL ,  '0', CURRENT_TIMESTAMP ,  '2012-10-22 00:00:00');
INSERT INTO  `translation` VALUES (NULL , NULL ,  'Incoming partner', 'Incoming partner', NULL , NULL ,  '0', CURRENT_TIMESTAMP ,  '2012-10-22 00:00:00');
INSERT INTO  `translation` VALUES (NULL , NULL ,  'Relationship', 'Relationship between CHW and formal health services', NULL , NULL ,  '0', CURRENT_TIMESTAMP ,  '2012-10-22 00:00:00');
INSERT INTO  `translation` VALUES (NULL , NULL ,  'Referral Mechanism', 'Referral Mechanism', NULL , NULL ,  '0', CURRENT_TIMESTAMP ,  '2012-10-22 00:00:00');
INSERT INTO  `translation` VALUES (NULL , NULL ,  'CHW Supervisor', 'CHW Supervisor', NULL , NULL ,  '0', CURRENT_TIMESTAMP ,  '2012-10-22 00:00:00');
INSERT INTO  `translation` VALUES (NULL , NULL ,  'Trainings provided', 'What training do you provide', NULL , NULL ,  '0', CURRENT_TIMESTAMP ,  '2012-10-22 00:00:00');
INSERT INTO  `translation` VALUES (NULL , NULL ,  'Courses Completed', 'Courses Completed', NULL , NULL ,  '0', CURRENT_TIMESTAMP ,  '2012-10-22 00:00:00');

/* create tables `partner` and `employee` */
DROP TABLE IF EXISTS `partner`;
CREATE TABLE `partner` (
	`id` INT(11) NULL DEFAULT NULL AUTO_INCREMENT,
	`uuid` CHAR(36) NULL DEFAULT NULL,
  `organizer_option_id` INT( 11 ) NULL DEFAULT NULL,
	`partner` VARCHAR(100) NULL DEFAULT NULL,
	`location_id` INT(11) NULL DEFAULT NULL,
	`address1` VARCHAR(128) NULL DEFAULT NULL,
	`address2` VARCHAR(128) NULL DEFAULT NULL,
	`phone` VARCHAR(64) NULL DEFAULT NULL,
	`fax` VARCHAR(64) NULL DEFAULT NULL,
	`employee_transition_option_id` INT(11) NULL DEFAULT NULL,
	`partner_importance_option_id`  INT(11) NULL DEFAULT NULL,
  `agreement_end_date` DATETIME NULL DEFAULT NULL,
	`transition_confirmed` TINYINT(1) NULL DEFAULT NULL,
	`comments` TEXT NULL DEFAULT NULL,
	`incoming_partner` INT(11) NULL DEFAULT NULL,
	`created_by` int(11) DEFAULT NULL,
	`timestamp_created` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
	PRIMARY KEY (`id`)
	)
 ENGINE = InnoDB DEFAULT CHARSET=utf8;
DROP TRIGGER IF EXISTS `partner_insert`;
DELIMITER $$
CREATE TRIGGER `partner_insert` BEFORE INSERT ON `partner` FOR EACH ROW BEGIN
  SET NEW.`uuid` = IFNULL(NEW.`uuid`,UUID());
END$$
DELIMITER ;


DROP TABLE IF EXISTS `partner_to_subpartner`;
CREATE TABLE `partner_to_subpartner` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`partner_id` int(11) NOT NULL,
	`subpartner_id` int(11) NOT NULL,
	`created_by` int(11) DEFAULT NULL,
	`timestamp_created` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
	PRIMARY KEY (`id`),
  KEY (`partner_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `employee`;
CREATE TABLE `employee` (
	`id` INT(11) NULL DEFAULT NULL AUTO_INCREMENT,
	`uuid` CHAR(36) NULL DEFAULT NULL,
	`partner_id` INT( 11 ) NULL DEFAULT NULL,
	`subpartner_id` INT( 11 ) NULL DEFAULT NULL,
	`location_id` INT(11) NULL DEFAULT NULL,
	`title_option_id` INT(11) NULL DEFAULT NULL,
	`first_name` VARCHAR(32) NULL DEFAULT NULL,
	`middle_name` VARCHAR(32) NULL DEFAULT NULL,
	`last_name`  VARCHAR(32) NULL DEFAULT NULL,
	`national_id` VARCHAR(64) NULL DEFAULT NULL,
	`other_id` VARCHAR(64) NULL DEFAULT NULL,
	`gender` enum('male','female','na') NULL DEFAULT NULL,
	`site_id` INT(11) NULL DEFAULT NULL,
	`address1` VARCHAR(128) NULL DEFAULT NULL,
	`address2` VARCHAR(128) NULL DEFAULT NULL,
	`primary_phone` VARCHAR(64) NULL DEFAULT NULL,
	`secondary_phone` VARCHAR(64) NULL DEFAULT NULL,
	`email` VARCHAR(128) NULL,
	`person_qualification_option_id` INT(11) NULL DEFAULT NULL,
	`employee_category_option_id` INT(11) NULL DEFAULT NULL,
	`primary_role` INT(11) NULL DEFAULT NULL,
	`full_time` TINYINT(1) NULL DEFAULT NULL,
	`funded_hours_per_week` INT(11) NULL DEFAULT NULL,
	`annual_cost` VARCHAR(11) NULL DEFAULT NULL,
	`agreement_end_date` DATETIME NULL DEFAULT NULL,
	`supervisor_id` INT(11) NULL DEFAULT NULL,
	`employee_transition_option_id` INT(11) NULL DEFAULT NULL,
	`transition_confirmed` INT(11) NULL DEFAULT NULL,
	`employee_training_provided_option_id` INT(11) NULL DEFAULT NULL,
  `comments` TEXT NULL DEFAULT NULL,
	`created_by` int(11) DEFAULT NULL,
	`timestamp_created` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
	PRIMARY KEY (`id`)
	)
 ENGINE = InnoDB DEFAULT CHARSET=utf8;
DROP TRIGGER IF EXISTS `employee_insert`;
DELIMITER $$
CREATE TRIGGER `employee_insert` BEFORE INSERT ON `employee` FOR EACH ROW BEGIN
  SET NEW.`uuid` = IFNULL(NEW.`uuid`,UUID());
END$$
DELIMITER ;

DROP TABLE IF EXISTS `employee_to_relationship`;
CREATE TABLE `employee_to_relationship` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`employee_id` int(11) NOT NULL,
	`relationship_option_id` int(11) NOT NULL,
	`created_by` int(11) DEFAULT NULL,
	`timestamp_created` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `employee_to_referral`;
CREATE TABLE `employee_to_referral` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`employee_id` int(11) NOT NULL,
	`referral_option_id` int(11) NOT NULL,
	`created_by` int(11) DEFAULT NULL,
	`timestamp_created` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `employee_to_role`;
CREATE TABLE `employee_to_role` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`employee_id` int(11) NOT NULL,
	`employee_role_option_id` int(11) NOT NULL,
	`created_by` int(11) DEFAULT NULL,
	`timestamp_created` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `employee_to_course`;
CREATE TABLE `employee_to_course` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `employee_id` varchar(64) NOT NULL,
  `course_name` varchar(64) NULL DEFAULT NULL,
  `provider`    varchar(64) NULL DEFAULT NULL,
  `content`     varchar(64) NULL DEFAULT NULL,
  `duration`    varchar(64) NULL DEFAULT NULL,
  `nqf_level`   varchar(64) NULL DEFAULT NULL,
  `certificate` varchar(64) NULL DEFAULT NULL,
  `accredited`  varchar(64) NULL DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `timestamp_created` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/** OPTION TABLES **/
DROP TABLE IF EXISTS `employee_category_option`;
SET character_set_client = utf8;
CREATE TABLE `employee_category_option` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uuid` CHAR(36) NULL DEFAULT NULL,
  `category_phrase` varchar(128) NOT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
  `timestamp_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `timestamp_created` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name_unique` (`category_phrase`),
  KEY `modified_by` (`modified_by`),
  KEY `created_by` (`created_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TRIGGER IF EXISTS `employee_category_insert`;
DELIMITER $$
CREATE TRIGGER `employee_category_insert` BEFORE INSERT ON `employee_category_option` FOR EACH ROW BEGIN
  SET NEW.`uuid` = IFNULL(NEW.`uuid`,UUID());
END$$
DELIMITER ;

DROP TABLE IF EXISTS `employee_role_option`;
SET character_set_client = utf8;
CREATE TABLE `employee_role_option` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uuid` CHAR(36) NULL DEFAULT NULL,
  `role_phrase` varchar(128) NOT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
  `timestamp_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `timestamp_created` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name_unique` (`role_phrase`),
  KEY `modified_by` (`modified_by`),
  KEY `created_by` (`created_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TRIGGER IF EXISTS `employee_role_insert`;
DELIMITER $$
CREATE TRIGGER `employee_role_insert` BEFORE INSERT ON `employee_role_option` FOR EACH ROW BEGIN
  SET NEW.`uuid` = IFNULL(NEW.`uuid`,UUID());
END$$
DELIMITER ;

DROP TABLE IF EXISTS `employee_transition_option`;
SET character_set_client = utf8;
CREATE TABLE `employee_transition_option` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uuid` CHAR(36) NULL DEFAULT NULL,
  `transition_phrase` varchar(128) NOT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
  `timestamp_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `timestamp_created` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name_unique` (`transition_phrase`),
  KEY `modified_by` (`modified_by`),
  KEY `created_by` (`created_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TRIGGER IF EXISTS `employee_transition_insert`;
DELIMITER $$
CREATE TRIGGER `employee_transition_insert` BEFORE INSERT ON `employee_transition_option` FOR EACH ROW BEGIN
  SET NEW.`uuid` = IFNULL(NEW.`uuid`,UUID());
END$$
DELIMITER ;

DROP TABLE IF EXISTS `employee_relationship_option`;
SET character_set_client = utf8;
CREATE TABLE `employee_relationship_option` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uuid` CHAR(36) NULL DEFAULT NULL,
  `relationship_phrase` varchar(128) NOT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
  `timestamp_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `timestamp_created` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name_unique` (`relationship_phrase`),
  KEY `modified_by` (`modified_by`),
  KEY `created_by` (`created_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TRIGGER IF EXISTS `employee_relationship_insert`;
DELIMITER $$
CREATE TRIGGER `employee_relationship_insert` BEFORE INSERT ON `employee_relationship_option` FOR EACH ROW BEGIN
  SET NEW.`uuid` = IFNULL(NEW.`uuid`,UUID());
END$$
DELIMITER ;

DROP TABLE IF EXISTS `employee_referral_option`;
SET character_set_client = utf8;
CREATE TABLE `employee_referral_option` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uuid` CHAR(36) NULL DEFAULT NULL,
  `referral_phrase` varchar(128) NOT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
  `timestamp_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `timestamp_created` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name_unique` (`referral_phrase`),
  KEY `modified_by` (`modified_by`),
  KEY `created_by` (`created_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TRIGGER IF EXISTS `employee_referral_insert`;
DELIMITER $$
CREATE TRIGGER `employee_referral_insert` BEFORE INSERT ON `employee_referral_option` FOR EACH ROW BEGIN
  SET NEW.`uuid` = IFNULL(NEW.`uuid`,UUID());
END$$
DELIMITER ;

DROP TABLE IF EXISTS `employee_training_provided_option`;
SET character_set_client = utf8;
CREATE TABLE `employee_training_provided_option` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uuid` CHAR(36) NULL DEFAULT NULL,
  `training_provided_phrase` varchar(128) NOT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
  `timestamp_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `timestamp_created` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name_unique` (`training_provided_phrase`),
  KEY `modified_by` (`modified_by`),
  KEY `created_by` (`created_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TRIGGER IF EXISTS `employee_training_provided_insert`;
DELIMITER $$
CREATE TRIGGER `employee_training_provided_insert` BEFORE INSERT ON `employee_training_provided_option` FOR EACH ROW BEGIN
  SET NEW.`uuid` = IFNULL(NEW.`uuid`,UUID());
END$$
DELIMITER ;
/* partner option tables */

DROP TABLE IF EXISTS `partner_importance_option`;
SET character_set_client = utf8;
CREATE TABLE `partner_importance_option` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uuid` CHAR(36) NULL DEFAULT NULL,
  `importance_phrase` varchar(128) NOT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
  `timestamp_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `timestamp_created` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name_unique` (`importance_phrase`),
  KEY `modified_by` (`modified_by`),
  KEY `created_by` (`created_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TRIGGER IF EXISTS `partner_importance_insert`;
DELIMITER $$
CREATE TRIGGER `partner_importance_insert` BEFORE INSERT ON `partner_importance_option` FOR EACH ROW BEGIN
  SET NEW.`uuid` = IFNULL(NEW.`uuid`,UUID());
END$$
DELIMITER ;

DROP TABLE IF EXISTS `partner_funder_option`;
SET character_set_client = utf8;
CREATE TABLE `partner_funder_option` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uuid` CHAR(36) NULL DEFAULT NULL,
  `funder_phrase` varchar(128) NOT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
  `timestamp_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `timestamp_created` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name_unique` (`funder_phrase`),
  KEY `modified_by` (`modified_by`),
  KEY `created_by` (`created_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TRIGGER IF EXISTS `partner_funder_insert`;
DELIMITER $$
CREATE TRIGGER `partner_funder_insert` BEFORE INSERT ON `partner_funder_option` FOR EACH ROW BEGIN
  SET NEW.`uuid` = IFNULL(NEW.`uuid`,UUID());
END$$
DELIMITER ;

DROP TABLE IF EXISTS `partner_to_funder`;
CREATE TABLE `partner_to_funder` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`partner_id` int(11) NOT NULL,
	`partner_funder_option_id` int(11) NOT NULL,
	`funder_end_date` DATETIME NULL DEFAULT NULL,
	`created_by` int(11) DEFAULT NULL,
	`timestamp_created` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
	PRIMARY KEY (`id`),
  KEY (`partner_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


/********************************/
/* engenderhealth - new updates */
/********************************/
/* site rollup feature */
ALTER TABLE  `_system`
  ADD  `module_datashare_enabled`  TINYINT( 1 ) NOT NULL DEFAULT '0',
  ADD  `datashare_password` TEXT NOT NULL DEFAULT '';

DROP TABLE IF EXISTS `datashare_sites`;
CREATE TABLE `datashare_sites` (
  `id`                int(11) NOT NULL AUTO_INCREMENT,
  `uuid`              char(36) DEFAULT NULL,
  `db_name`           VARCHAR(255) NOT NULL,
  `site_password`     TEXT DEFAULT NULL,
  `organizer_access`  TEXT DEFAULT NULL,
  `modified_by`       int(11) DEFAULT NULL,
  `created_by`        int(11) DEFAULT NULL,
  `timestamp_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `timestamp_created` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `created_by` (`created_by`),
  KEY `modified_by` (`modified_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
ALTER TABLE `datashare_sites` ADD UNIQUE `uuid_idx`(uuid);
DELIMITER ;;
CREATE TRIGGER `datashare_sites_insert` BEFORE INSERT ON `datashare_sites` FOR EACH ROW BEGIN
  SET NEW.`uuid` = IFNULL(NEW.`uuid`,UUID());
  END;;
DELIMITER ;


/* facility latitude and longitude */
ALTER TABLE  `facility` ADD  `lat` DECIMAL( 9, 6 ) NULL DEFAULT NULL AFTER  `facility_name` , ADD  `long` DECIMAL( 9, 6 ) NULL DEFAULT NULL AFTER  `lat`;

/* facility sponsors*/
DROP TABLE IF EXISTS `facility_sponsors`;
CREATE TABLE `facility_sponsors` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `facility_id` int(11) NOT NULL,
  `facility_sponsor_phrase_id` int(11) DEFAULT NULL,
  `start_date`  timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `end_date`    timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `is_default`  tinyint(1) unsigned NOT NULL DEFAULT '0',
  `modified_by` int(11) DEFAULT NULL,
  `created_by`  int(11) DEFAULT NULL,
  `is_deleted`  tinyint(1) NOT NULL DEFAULT '0',
  `timestamp_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `timestamp_created` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `created_by` (`created_by`),
  KEY `modified_by` (`modified_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
ALTER TABLE `facility_sponsors` ADD COLUMN `uuid` char(36) AFTER `id`;
ALTER TABLE `facility_sponsors` ADD UNIQUE `uuid_idx`(uuid);
ALTER TABLE `facility_sponsors` CHANGE COLUMN `uuid` `uuid` char(36) DEFAULT NULL;
DELIMITER ;;
CREATE TRIGGER `facility_sponsors_insert` BEFORE INSERT ON `facility_sponsors` FOR EACH ROW BEGIN
  SET NEW.`uuid` = IFNULL(NEW.`uuid`,UUID());
  END;;
DELIMITER ;

/********************************/
/* engenderhealth - new updates */
/********************************/

/*ALTER TABLE `_system` DROP `display_training_partner`; */
/*ALTER TABLE `_system` DROP 	ADD  `display_training_partner` TINYINT( 1 ) NOT NULL DEFAULT  '0', */

ALTER TABLE  `_system`
  ADD  `require_duplicate_acl` TINYINT( 1 ) NOT NULL DEFAULT  '0',
  ADD  `module_attendance_enabled` TINYINT( 1 ) NOT NULL DEFAULT '0',
  ADD  `display_sponsor_dates`     TINYINT( 1 ) NOT NULL DEFAULT '0',
	ADD  `require_sponsor_dates`     TINYINT( 1 ) NOT NULL DEFAULT '0',
  ADD  `allow_multi_sponsors`      TINYINT( 1 ) NOT NULL DEFAULT '0',
  ADD  `allow_multi_approvers`     TINYINT( 1 ) NOT NULL DEFAULT '0',
	ADD  `fiscal_year_start`         DATETIME NULL DEFAULT NULL ,
	ADD  `display_gender`            TINYINT( 1 ) NOT NULL DEFAULT  '1',
	ADD  `display_training_custom3`  TINYINT( 1 ) NOT NULL DEFAULT  '0',
	ADD  `display_training_custom4`  TINYINT( 1 ) NOT NULL DEFAULT  '0',
	ADD  `display_people_custom3`    TINYINT( 1 ) NOT NULL DEFAULT  '0',
	ADD  `display_people_custom4`    TINYINT( 1 ) NOT NULL DEFAULT  '0',
	ADD  `display_people_custom5`    TINYINT( 1 ) NOT NULL DEFAULT  '0',
	ADD  `display_training_pepfar`   TINYINT( 1 ) NOT NULL DEFAULT  '1',
	ADD  `require_trainer_skill`     TINYINT( 1 ) NOT NULL DEFAULT  '0';
ALTER TABLE  `_system`
	ADD `display_region_d` TINYINT(1) NOT NULL DEFAULT '0',
	ADD `display_region_e` TINYINT(1) NOT NULL DEFAULT '0',
	ADD `display_region_f` TINYINT(1) NOT NULL DEFAULT '0',
	ADD `display_region_g` TINYINT(1) NOT NULL DEFAULT '0',
	ADD `display_region_h` TINYINT(1) NOT NULL DEFAULT '0',
	ADD `display_region_i` TINYINT(1) NOT NULL DEFAULT '0';

/* some labels */
INSERT INTO  `translation` VALUES (NULL , NULL ,  'Training',  'Training', NULL , NULL ,  '0', CURRENT_TIMESTAMP ,  '2012-10-22 00:00:00');
INSERT INTO  `translation` VALUES (NULL , NULL ,  'Trainer',  'Trainer', NULL , NULL ,  '0',   CURRENT_TIMESTAMP ,  '2012-10-22 00:00:00');
INSERT INTO  `translation` VALUES (NULL , NULL ,  'Trainings',  'Trainings', NULL , NULL ,  '0', CURRENT_TIMESTAMP ,  '2012-10-22 00:00:00');
INSERT INTO  `translation` VALUES (NULL , NULL ,  'Trainers',  'Trainers', NULL , NULL ,  '0', CURRENT_TIMESTAMP ,  '2012-10-22 00:00:00');
INSERT INTO  `translation` VALUES (NULL , NULL ,  'Participant',  'Participant', NULL , NULL ,  '0', CURRENT_TIMESTAMP ,  '2012-10-22 00:00:00');
INSERT INTO  `translation` VALUES (NULL , NULL ,  'Participants',  'Participants', NULL , NULL ,  '0', CURRENT_TIMESTAMP ,  '2012-10-22 00:00:00');
INSERT INTO  `translation` VALUES (NULL , NULL ,  'Training Center',  'Training Center', NULL , NULL ,  '0', CURRENT_TIMESTAMP ,  '2012-10-22 00:00:00');
INSERT INTO  `translation` VALUES (NULL , NULL ,  'Region D',  'Local Region', NULL , NULL ,  '0', CURRENT_TIMESTAMP ,  '2012-10-22 00:00:00');
INSERT INTO  `translation` VALUES (NULL , NULL ,  'Region E',  'Local Region', NULL , NULL ,  '0', CURRENT_TIMESTAMP ,  '2012-10-22 00:00:00');
INSERT INTO  `translation` VALUES (NULL , NULL ,  'Region F',  'Local Region', NULL , NULL ,  '0', CURRENT_TIMESTAMP ,  '2012-10-22 00:00:00');
INSERT INTO  `translation` VALUES (NULL , NULL ,  'Region G',  'Local Region', NULL , NULL ,  '0', CURRENT_TIMESTAMP ,  '2012-10-22 00:00:00');
INSERT INTO  `translation` VALUES (NULL , NULL ,  'Region H',  'Local Region', NULL , NULL ,  '0', CURRENT_TIMESTAMP ,  '2012-10-22 00:00:00');
INSERT INTO  `translation` VALUES (NULL , NULL ,  'Region I',  'Local Region', NULL , NULL ,  '0', CURRENT_TIMESTAMP ,  '2012-10-22 00:00:00');

/* people labels */
INSERT INTO  `translation` VALUES (NULL , NULL ,  'People Custom 3',  'People Custom 3', NULL , NULL ,  '0', CURRENT_TIMESTAMP ,  '2012-10-22 00:00:00');
INSERT INTO  `translation` VALUES (NULL , NULL ,  'People Custom 4',  'People Custom 4', NULL , NULL ,  '0', CURRENT_TIMESTAMP ,  '2012-10-22 00:00:00');
INSERT INTO  `translation` VALUES (NULL , NULL ,  'People Custom 5',  'People Custom 5', NULL , NULL ,  '0', CURRENT_TIMESTAMP ,  '2012-10-22 00:00:00');
INSERT INTO  `translation` VALUES (NULL , NULL ,  'Gender',  'Gender', NULL , NULL ,  '0', CURRENT_TIMESTAMP ,  '2012-10-22 00:00:00');
INSERT INTO  `translation` VALUES (NULL , NULL ,  'Address 1',  'Address 1', NULL , NULL ,  '0', CURRENT_TIMESTAMP ,  '2012-10-22 00:00:00');
INSERT INTO  `translation` VALUES (NULL , NULL ,  'Address 2',  'Address 2', NULL , NULL ,  '0', CURRENT_TIMESTAMP ,  '2012-10-22 00:00:00');
INSERT INTO  `translation` VALUES (NULL , NULL ,  'Home phone',  'Home phone', NULL , NULL ,  '0', CURRENT_TIMESTAMP ,  '2012-10-22 00:00:00');

ALTER TABLE  `person`
  ADD  `custom_3` VARCHAR( 255 ) NULL DEFAULT  '',
  ADD  `custom_4` VARCHAR( 255 ) NULL DEFAULT  '',
  ADD  `custom_5` VARCHAR( 255 ) NULL DEFAULT  '';

/* track attendance */
ALTER TABLE `person_to_training`
  ADD  `award_id`   TINYINT( 1 ) NOT NULL DEFAULT '0',
  ADD  `duration_days` float NULL;

/* approval */
ALTER TABLE `person`   ADD `approved` TINYINT(1) NULL DEFAULT NULL;
ALTER TABLE `facility` ADD `approved` TINYINT(1) NULL DEFAULT NULL;
ALTER TABLE `_system`  ADD `module_person_approval` TINYINT(1) NOT NULL DEFAULT '0';
ALTER TABLE `_system`  ADD `module_facility_approval` TINYINT(1) NOT NULL DEFAULT '0';
UPDATE `person` SET `approved` = 1;
UPDATE `facility` SET `approved` = 1;

/* acls */
ALTER TABLE  `user_to_acl` CHANGE  `acl_id`  `acl_id` ENUM ('in_service', 'pre_service', 'edit_employee', 'edit_course', 'view_course', 'duplicate_training', 'approve_trainings', 'master_approver', 'edit_people', 'view_people', 'edit_training_location', 'edit_facility', 'view_facility', 'view_create_reports', 'training_organizer_option_all', 'training_title_option_all', 'use_offline_app', 'admin_files', 'facility_and_person_approver', 'edit_evaluations', 'edit_country_options', 'acl_editor_training_category', 'acl_editor_people_qualifications', 'acl_editor_people_responsibility', 'acl_editor_training_organizer', 'acl_editor_people_trainer', 'acl_editor_training_topic', 'acl_editor_people_titles', 'acl_editor_training_level', 'acl_editor_refresher_course', 'acl_editor_people_trainer_skills', 'acl_editor_pepfar_category', 'acl_editor_people_languages', 'acl_editor_funding', 'acl_editor_people_affiliations', 'acl_editor_recommended_topic', 'acl_editor_nationalcurriculum', 'acl_editor_people_suffix', 'acl_editor_method', 'acl_editor_people_active_trainer', 'acl_editor_facility_types', 'acl_editor_ps_classes', 'acl_editor_facility_sponsors', 'acl_editor_ps_cadres', 'acl_editor_ps_degrees', 'acl_editor_ps_funding', 'acl_editor_ps_institutions', 'acl_editor_ps_languages', 'acl_editor_ps_nationalities', 'acl_editor_ps_joindropreasons', 'acl_editor_ps_sponsors', 'acl_editor_ps_tutortypes', 'acl_editor_ps_coursetypes', 'acl_editor_ps_religions', 'add_edit_users', 'acl_admin_training', 'acl_admin_people', 'acl_admin_facilities', 'import_training', 'import_training_location', 'import_facility', 'import_person' )  NOT NULL DEFAULT 'view_course';
INSERT INTO `acl` (`id` ,`acl`) VALUES ('edit_employee',  'edit_employee');
INSERT INTO `acl` (`id` ,`acl`) VALUES ('duplicate_training', 'duplicate_training');
INSERT INTO `acl` (`id` ,`acl`) VALUES ('import_training',  'import_training');
INSERT INTO `acl` (`id` ,`acl`) VALUES ('import_training_location',  'import_training_location');
INSERT INTO `acl` (`id` ,`acl`) VALUES ('import_facility',  'import_facility');
INSERT INTO `acl` (`id` ,`acl`) VALUES ('import_person',  'import_person');
INSERT INTO `acl` (`id` ,`acl`) VALUES ('facility_and_person_approver',  'facility_and_person_approver');
INSERT INTO `acl` (`id`, `acl`) VALUES ('acl_editor_training_category', 'acl_editor_training_category');
INSERT INTO `acl` (`id`, `acl`) VALUES ('acl_editor_people_qualifications', 'acl_editor_people_qualifications');
INSERT INTO `acl` (`id`, `acl`) VALUES ('acl_editor_people_responsibility', 'acl_editor_people_responsibility');
INSERT INTO `acl` (`id`, `acl`) VALUES ('acl_editor_training_organizer', 'acl_editor_training_organizer');
INSERT INTO `acl` (`id`, `acl`) VALUES ('acl_editor_people_trainer', 'acl_editor_people_trainer');
INSERT INTO `acl` (`id`, `acl`) VALUES ('acl_editor_training_topic', 'acl_editor_training_topic');
INSERT INTO `acl` (`id`, `acl`) VALUES ('acl_editor_people_titles', 'acl_editor_people_titles');
INSERT INTO `acl` (`id`, `acl`) VALUES ('acl_editor_training_level', 'acl_editor_training_level');
INSERT INTO `acl` (`id`, `acl`) VALUES ('acl_editor_people_trainer_skills', 'acl_editor_people_trainer_skills');
INSERT INTO `acl` (`id`, `acl`) VALUES ('acl_editor_pepfar_category', 'acl_editor_pepfar_category');
INSERT INTO `acl` (`id`, `acl`) VALUES ('acl_editor_people_languages', 'acl_editor_people_languages');
INSERT INTO `acl` (`id`, `acl`) VALUES ('acl_editor_funding', 'acl_editor_funding');
INSERT INTO `acl` (`id`, `acl`) VALUES ('acl_editor_people_affiliations', 'acl_editor_people_affiliations');
INSERT INTO `acl` (`id`, `acl`) VALUES ('acl_editor_recommended_topic', 'acl_editor_recommended_topic');
INSERT INTO `acl` (`id`, `acl`) VALUES ('acl_editor_nationalcurriculum', 'acl_editor_nationalcurriculum');
INSERT INTO `acl` (`id`, `acl`) VALUES ('acl_editor_people_suffix', 'acl_editor_people_suffix');
INSERT INTO `acl` (`id`, `acl`) VALUES ('acl_editor_method', 'acl_editor_method');
INSERT INTO `acl` (`id`, `acl`) VALUES ('acl_editor_people_active_trainer', 'acl_editor_people_active_trainer');
INSERT INTO `acl` (`id`, `acl`) VALUES ('acl_editor_facility_types', 'acl_editor_facility_types');
INSERT INTO `acl` (`id`, `acl`) VALUES ('acl_editor_facility_sponsors', 'acl_editor_facility_sponsors');
INSERT INTO `acl` (`id`, `acl`) VALUES ('acl_editor_ps_classes', 'acl_editor_ps_classes');
INSERT INTO `acl` (`id`, `acl`) VALUES ('acl_editor_ps_cadres', 'acl_editor_ps_cadres');
INSERT INTO `acl` (`id`, `acl`) VALUES ('acl_editor_ps_degrees', 'acl_editor_ps_degrees');
INSERT INTO `acl` (`id`, `acl`) VALUES ('acl_editor_ps_funding', 'acl_editor_ps_funding');
INSERT INTO `acl` (`id`, `acl`) VALUES ('acl_editor_ps_institutions', 'acl_editor_ps_institutions');
INSERT INTO `acl` (`id`, `acl`) VALUES ('acl_editor_ps_languages', 'acl_editor_ps_languages');
INSERT INTO `acl` (`id`, `acl`) VALUES ('acl_editor_ps_nationalities', 'acl_editor_ps_nationalities');
INSERT INTO `acl` (`id`, `acl`) VALUES ('acl_editor_ps_joindropreasons', 'acl_editor_ps_joindropreasons');
INSERT INTO `acl` (`id`, `acl`) VALUES ('acl_editor_ps_sponsors', 'acl_editor_ps_sponsors');
INSERT INTO `acl` (`id`, `acl`) VALUES ('acl_editor_ps_tutortypes', 'acl_editor_ps_tutortypes');
INSERT INTO `acl` (`id`, `acl`) VALUES ('acl_editor_ps_coursetypes', 'acl_editor_ps_coursetypes');
INSERT INTO `acl` (`id`, `acl`) VALUES ('acl_editor_ps_religions', 'acl_editor_ps_religions');
INSERT INTO `acl` (`id`, `acl`) VALUES ('acl_admin_training', 'acl_admin_training');
INSERT INTO `acl` (`id`, `acl`) VALUES ('acl_admin_people', 'acl_admin_people');
INSERT INTO `acl` (`id`, `acl`) VALUES ('acl_admin_facilities', 'acl_admin_facilities');
INSERT INTO `acl` (`id`, `acl`) VALUES ('acl_editor_refresher_course', 'acl_editor_refresher_course');
INSERT INTO `acl` (`id`, `acl`) VALUES ('master_approver',  'master_approver');
INSERT INTO `acl` (`id`, `acl`) VALUES ('edit_evaluations',  'edit_evaluations');
INSERT INTO `acl` (`id`, `acl`) VALUES ('edit_facility',  'edit_facility');
INSERT INTO `acl` (`id`, `acl`) VALUES ('view_facility',  'view_facility');
INSERT INTO `acl` (`id`, `acl`) VALUES ('edit_training_location',  'edit_training_location');


/* evaluations */
ALTER TABLE  `evaluation_question` CHANGE  `question_type`  `question_type` ENUM(  'Likert',  'Text',  'Likert3',  'Likert3NA', 'LikertNA' ) NOT NULL DEFAULT  'Likert';
ALTER TABLE  `evaluation_response` ADD     `trainer_person_id` INT( 11 ) NULL DEFAULT NULL AFTER  `evaluation_to_training_id`;

/* score - improved indexing */
ALTER TABLE `score` ADD INDEX  `scorelabel` (  `score_label` );


/* custom fields 'training' */
ALTER TABLE  `training`
  ADD  `custom_3` VARCHAR( 255 ) NULL DEFAULT  '',
  ADD  `custom_4` VARCHAR( 255 ) NULL DEFAULT  '';
INSERT INTO  `translation` VALUES (NULL , NULL ,  'Training Custom 3',  'Custom field 3', NULL , NULL ,  '0',  CURRENT_TIMESTAMP ,  '2012-10-22 00:00:00');
INSERT INTO  `translation` VALUES (NULL , NULL ,  'Training Custom 4',  'Custom field 4', NULL , NULL ,  '0',  CURRENT_TIMESTAMP ,  '2012-10-22 00:00:00');

ALTER TABLE `person`
	CHANGE `multi_facility_ids` `multi_facility_ids` varchar(255) NULL DEFAULT NULL,
	CHANGE `home_city` `home_city` varchar(255) NULL DEFAULT '',
	CHANGE `highest_level_option_id` `highest_level_option_id` int(11) NULL DEFAULT NULL,
	CHANGE `govemp_option_id` `govemp_option_id` tinyint(4) NULL DEFAULT '0',
	CHANGE `occupational_category_id` `occupational_category_id` int(10) unsigned NULL DEFAULT NULL,
	CHANGE `persal_number` `persal_number` int(10) unsigned NULL DEFAULT NULL,
	CHANGE `bodies_id` `bodies_id` int(10) unsigned NULL DEFAULT NULL,
	CHANGE `race_option_id` `race_option_id` int(10) unsigned NULL DEFAULT NULL,
	CHANGE `disability_option_id` `disability_option_id` int(10) unsigned NULL DEFAULT NULL,
	CHANGE `professional_reg_number` `professional_reg_number` int(10) unsigned NULL DEFAULT NULL,
	CHANGE `nationality_id` `nationality_id` int(10) unsigned NULL DEFAULT NULL,
	CHANGE `nurse_training_id` `nurse_training_id` int(10) unsigned NULL DEFAULT NULL,
	CHANGE `care_start_year` `care_start_year` int(10) unsigned NULL DEFAULT NULL,
	CHANGE `timespent_rank_pregnant` `timespent_rank_pregnant` int(10) unsigned NULL DEFAULT NULL,
	CHANGE `timespent_rank_adults` `timespent_rank_adults` int(10) unsigned NULL DEFAULT NULL,
	CHANGE `timespent_rank_children` `timespent_rank_children` int(10) unsigned NULL DEFAULT NULL,
	CHANGE `timespent_rank_pregnant_pct` `timespent_rank_pregnant_pct` int(10) unsigned NULL DEFAULT NULL,
	CHANGE `timespent_rank_adults_pct` `timespent_rank_adults_pct` int(10) unsigned NULL DEFAULT NULL,
	CHANGE `timespent_rank_children_pct` `timespent_rank_children_pct` int(10) unsigned NULL DEFAULT NULL,
	CHANGE `supervised_id` `supervised_id` int(10) unsigned NULL DEFAULT NULL,
	CHANGE `supervision_frequency_id` `supervision_frequency_id` int(10) unsigned NULL DEFAULT NULL,
	CHANGE `supervisors_profession` `supervisors_profession` varchar(255) NULL DEFAULT NULL,
	CHANGE `training_recieved_data` `training_recieved_data` text NULL DEFAULT NULL,
	CHANGE `facilitydepartment_id` `facilitydepartment_id` int(10) unsigned NULL DEFAULT NULL;
