# participant changes - namibia viewing location and budget code for participtans
INSERT INTO  `translation` VALUES (NULL , NULL ,  'Viewing Location',  'Viewing Location', NULL , NULL ,  '0', CURRENT_TIMESTAMP ,  '2012-10-22 00:00:00');
INSERT INTO  `translation` VALUES (NULL , NULL ,  'Budget Code',  'Budget Code', NULL , NULL ,  '0', CURRENT_TIMESTAMP ,  '2012-10-22 00:00:00');

ALTER TABLE  `_system`
  ADD  `display_budget_code` TINYINT( 1 ) NOT NULL DEFAULT  '0',
  ADD  `display_viewing_location` TINYINT( 1 ) NOT NULL DEFAULT '0';

ALTER TABLE `person_to_training`
  ADD `viewing_location_option_id` INT( 11 ) NOT NULL DEFAULT '0',
  ADD `budget_code_option_id`      INT( 11 ) NOT NULL DEFAULT '0';

CREATE TABLE IF NOT EXISTS `person_to_training_viewing_loc_option` (
  `id` int(11) NOT NULL auto_increment,
  `uuid` char(36) default NULL,
  `location_phrase` varchar(128) NOT NULL,
  `modified_by` int(11) default NULL,
  `created_by` int(11) default NULL,
  `is_deleted` tinyint(1) NOT NULL default '0',
  `timestamp_updated` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `timestamp_created` timestamp NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `name_unique` (`location_phrase`),
  UNIQUE KEY `uuid_idx` (`uuid`),
  KEY `modified_by` (`modified_by`),
  KEY `created_by` (`created_by`)
) DEFAULT CHARSET=utf8 ;
DROP TRIGGER IF EXISTS `person_to_training_viewing_loc_insert`;
DELIMITER $$
CREATE TRIGGER `person_to_training_viewing_loc_insert` BEFORE INSERT ON `person_to_training_viewing_loc_option` FOR EACH ROW BEGIN
  SET NEW.`uuid` = IFNULL(NEW.`uuid`,UUID());
END$$
DELIMITER ;

CREATE TABLE IF NOT EXISTS `person_to_training_budget_option` (
  `id` int(11) NOT NULL auto_increment,
  `uuid` char(36) default NULL,
  `budget_code_phrase` varchar(128) NOT NULL,
  `modified_by` int(11) default NULL,
  `created_by` int(11) default NULL,
  `is_deleted` tinyint(1) NOT NULL default '0',
  `timestamp_updated` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `timestamp_created` timestamp NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `name_unique` (`budget_code_phrase`),
  UNIQUE KEY `uuid_idx` (`uuid`),
  KEY `modified_by` (`modified_by`),
  KEY `created_by` (`created_by`)
) DEFAULT CHARSET=utf8 ;

DROP TRIGGER IF EXISTS `person_to_training_budget_insert`;
DELIMITER $$
CREATE TRIGGER `person_to_training_budget_insert` BEFORE INSERT ON `person_to_training_budget_option` FOR EACH ROW BEGIN
  SET NEW.`uuid` = IFNULL(NEW.`uuid`,UUID());
END$$
DELIMITER ;



#skillsmart
INSERT INTO  `translation` VALUES (NULL , NULL ,  'Occupational category', 'Occupational category', NULL, NULL, '0', CURRENT_TIMESTAMP, '2012-10-22 00:00:00');
INSERT INTO  `translation` VALUES (NULL , NULL ,  'Government employee', 'Government employee', NULL, NULL, '0', CURRENT_TIMESTAMP, '2012-10-22 00:00:00');
INSERT INTO  `translation` VALUES (NULL , NULL ,  'Professional bodies', 'Professional bodies', NULL, NULL, '0', CURRENT_TIMESTAMP, '2012-10-22 00:00:00');
INSERT INTO  `translation` VALUES (NULL , NULL ,  'Race', 'Race', NULL, NULL, '0', CURRENT_TIMESTAMP, '2012-10-22 00:00:00');
INSERT INTO  `translation` VALUES (NULL , NULL ,  'Disability', 'Disability', NULL, NULL, '0', CURRENT_TIMESTAMP, '2012-10-22 00:00:00');
INSERT INTO  `translation` VALUES (NULL , NULL ,  'Nurse trainer type', 'Nurse trainer type', NULL, NULL, '0', CURRENT_TIMESTAMP, '2012-10-22 00:00:00');
INSERT INTO  `translation` VALUES (NULL , NULL ,  'Year you started providing care', 'Year you started providing care', NULL, NULL, '0', CURRENT_TIMESTAMP, '2012-10-22 00:00:00');
INSERT INTO  `translation` VALUES (NULL , NULL ,  'Rank patient groups based on time', 'Rank patient groups based on time', NULL, NULL, '0', CURRENT_TIMESTAMP, '2012-10-22 00:00:00');
INSERT INTO  `translation` VALUES (NULL , NULL ,  'Supervised', 'Supervised', NULL, NULL, '0', CURRENT_TIMESTAMP, '2012-10-22 00:00:00');
INSERT INTO  `translation` VALUES (NULL , NULL ,  'Indicate the training you received', 'Indicate the training you received', NULL, NULL, '0', CURRENT_TIMESTAMP, '2012-10-22 00:00:00');
INSERT INTO  `translation` VALUES (NULL , NULL ,  'Facility department', 'Facility department', NULL, NULL, '0', CURRENT_TIMESTAMP, '2012-10-22 00:00:00');

#training completion ("award") lookup table
CREATE TABLE IF NOT EXISTS `person_to_training_award_option` (
  `id` int(11) NOT NULL auto_increment,
  `uuid` char(36) default NULL,
  `award_phrase` varchar(128) NOT NULL,
  `modified_by` int(11) default NULL,
  `created_by` int(11) default NULL,
  `is_deleted` tinyint(1) NOT NULL default '0',
  `timestamp_updated` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `timestamp_created` timestamp NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `name_unique` (`award_phrase`),
  UNIQUE KEY `uuid_idx` (`uuid`),
  KEY `modified_by` (`modified_by`),
  KEY `created_by` (`created_by`)
) DEFAULT CHARSET=utf8 ;

DROP TRIGGER IF EXISTS `person_to_training_award_insert`;
DELIMITER $$
CREATE TRIGGER `person_to_training_award_insert` BEFORE INSERT ON `person_to_training_award_option` FOR EACH ROW BEGIN
  SET NEW.`uuid` = IFNULL(NEW.`uuid`,UUID());
END$$
DELIMITER ;

INSERT INTO  `translation` VALUES (NULL , NULL ,  'Award', 'Complete', NULL, NULL, '0', CURRENT_TIMESTAMP, '2012-10-22 00:00:00');
INSERT INTO  `translation` VALUES (NULL , NULL ,  'Disability', 'Disability', NULL, NULL, '0', CURRENT_TIMESTAMP, '2012-10-22 00:00:00');
INSERT INTO  `translation` VALUES (NULL , NULL ,  'Disability Comments', 'Disability Comments', NULL, NULL, '0', CURRENT_TIMESTAMP, '2012-10-22 00:00:00');
INSERT INTO  `translation` VALUES (NULL , NULL ,  'Race', 'Race', NULL, NULL, '0', CURRENT_TIMESTAMP, '2012-10-22 00:00:00');
INSERT INTO  `person_to_training_award_option` VALUES (NULL , NULL ,  'Complete', NULL, '1', '0', CURRENT_TIMESTAMP, '2012-10-22 00:00:00');
INSERT INTO  `person_to_training_award_option` VALUES (NULL , NULL ,  'Certification', NULL, '1', '0', CURRENT_TIMESTAMP, '2012-10-22 00:00:00');


#requested changes to employee module: dob, race option, other id label

ALTER TABLE  `_system`
    ADD  `display_employee_registration_number` TINYINT( 1 ) NOT NULL DEFAULT  '1',
    ADD  `display_employee_dob`                 TINYINT( 1 ) NOT NULL DEFAULT  '1',
    ADD  `display_employee_race`                TINYINT( 1 ) NOT NULL DEFAULT  '1',
    ADD  `display_employee_other_id`            TINYINT( 1 ) NOT NULL DEFAULT  '1',
    ADD  `display_employee_disability`          TINYINT( 1 ) NOT NULL DEFAULT  '1',
    ADD  `display_employee_salary`              TINYINT( 1 ) NOT NULL DEFAULT  '1',
    ADD  `display_employee_benefits`            TINYINT( 1 ) NOT NULL DEFAULT  '1',
    ADD  `display_employee_additional_expenses` TINYINT( 1 ) NOT NULL DEFAULT  '1',
    ADD  `display_employee_stipend`             TINYINT( 1 ) NOT NULL DEFAULT  '1';
INSERT INTO  `translation` VALUES (NULL , NULL ,  'Other ID', 'I.D./Passport Number', NULL, NULL, '0', CURRENT_TIMESTAMP, '2012-10-22 00:00:00');
INSERT INTO  `translation` VALUES (NULL , NULL ,  'Employee Date of Birth', 'Date of Birth', NULL, NULL, '0', CURRENT_TIMESTAMP, '2012-10-22 00:00:00');
INSERT INTO  `translation` VALUES (NULL , NULL ,  'Disability', 'Disability', NULL, NULL, '0', CURRENT_TIMESTAMP, '2012-10-22 00:00:00');
INSERT INTO  `translation` VALUES (NULL , NULL ,  'Disability Comments', 'Disability Comments', NULL, NULL, '0', CURRENT_TIMESTAMP, '2012-10-22 00:00:00');
INSERT INTO  `translation` VALUES (NULL , NULL ,  'Race', 'Race', NULL, NULL, '0', CURRENT_TIMESTAMP, '2012-10-22 00:00:00');
INSERT INTO  `translation` VALUES (NULL , NULL ,  'Registration Number', 'Registration Number', NULL, NULL, '0', CURRENT_TIMESTAMP, '2012-10-22 00:00:00');
INSERT INTO  `translation` VALUES (NULL , NULL ,  'Salary', 'Salary', NULL, NULL, '0', CURRENT_TIMESTAMP, '2012-10-22 00:00:00');
INSERT INTO  `translation` VALUES (NULL , NULL ,  'Benefits', 'Benefits', NULL, NULL, '0', CURRENT_TIMESTAMP, '2012-10-22 00:00:00');
INSERT INTO  `translation` VALUES (NULL , NULL ,  'Additional Expenses', 'Additional Expenses', NULL, NULL, '0', CURRENT_TIMESTAMP, '2012-10-22 00:00:00');
INSERT INTO  `translation` VALUES (NULL , NULL ,  'Stipend', 'Stipend', NULL, NULL, '0', CURRENT_TIMESTAMP, '2012-10-22 00:00:00');
INSERT INTO  `translation` VALUES (NULL , NULL ,  'Staff Cadre', 'Staff Cadre', NULL, NULL, '0', CURRENT_TIMESTAMP, '2012-10-22 00:00:00');

ALTER TABLE `employee`
    CHANGE COLUMN `person_qualification_option_id` `employee_qualification_option_id` INT(11) NULL DEFAULT NULL,
    ADD COLUMN `disability_option_id` INT(11) AFTER `gender`,
    ADD COLUMN `disability_comments`  VARCHAR(128) AFTER `gender`,
    ADD COLUMN `dob`                  DATETIME AFTER `gender`,
    ADD COLUMN `race_option_id`       INT(11)  AFTER `gender`,
    ADD COLUMN `registration_number`  VARCHAR(40) DEFAULT null,
    ADD COLUMN `salary`               VARCHAR(11) DEFAULT null,
    ADD COLUMN `benefits`             VARCHAR(11) DEFAULT null,
    ADD COLUMN `additional_expenses`  VARCHAR(11) DEFAULT null,
    ADD COLUMN `stipend`              VARCHAR(11) DEFAULT null;

CREATE TABLE IF NOT EXISTS `person_race_option` (
  `id` int(11) NOT NULL auto_increment,
  `uuid` char(36) default NULL,
  `race_phrase` varchar(128) NOT NULL,
  `modified_by` int(11) default NULL,
  `created_by` int(11) default NULL,
  `is_deleted` tinyint(1) NOT NULL default '0',
  `timestamp_updated` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `timestamp_created` timestamp NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `name_unique` (`race_phrase`),
  UNIQUE KEY `uuid_idx` (`uuid`),
  KEY `modified_by` (`modified_by`),
  KEY `created_by` (`created_by`)
) DEFAULT CHARSET=utf8 ;
DROP TRIGGER IF EXISTS `person_race_insert`;
DELIMITER $$
CREATE TRIGGER `person_race_insert` BEFORE INSERT ON `person_race_option` FOR EACH ROW BEGIN
  SET NEW.`uuid` = IFNULL(NEW.`uuid`,UUID());
END$$
DELIMITER ;

CREATE TABLE IF NOT EXISTS `employee_qualification_option` (
  `id` int(11) NOT NULL auto_increment,
  `uuid` char(36) default NULL,
  `qualification_phrase` varchar(128) NOT NULL,
  `qualification_code`   varchar(64)  default NULL,
  `modified_by`          int(11) default NULL,
  `created_by`           int(11) default NULL,
  `is_deleted`           tinyint(1) NOT NULL default '0',
  `timestamp_updated`    timestamp NOT NULL default CURRENT_TIMESTAMP,
  `timestamp_created`    timestamp NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `name_unique` (`qualification_phrase`),
  UNIQUE KEY `uuid_idx` (`uuid`),
  KEY `modified_by` (`modified_by`),
  KEY `created_by` (`created_by`)
) DEFAULT CHARSET=utf8 ;
DROP TRIGGER IF EXISTS `employee_qualification_insert`;
DELIMITER $$
CREATE TRIGGER `employee_qualification_insert` BEFORE INSERT ON `employee_qualification_option` FOR EACH ROW BEGIN
  SET NEW.`uuid` = IFNULL(NEW.`uuid`,UUID());
END$$
DELIMITER ;

#make fields nullable for desktop application
ALTER TABLE  `person_to_training`
  CHANGE  `award_id`  `award_id` TINYINT( 1 ) NULL DEFAULT  '0',
  CHANGE  `viewing_location_option_id`  `viewing_location_option_id` INT( 11 ) NULL DEFAULT  '0',
  CHANGE  `budget_code_option_id`  `budget_code_option_id` INT( 11 ) NULL DEFAULT  '0';

# migrate some acls so ppl can edit evaluations and employees if they are admins..
INSERT INTO user_to_acl SELECT DISTINCT null, 'edit_evaluations', id, 1, NOW() from user_to_acl where acl_id = 'edit_country_options';
INSERT INTO user_to_acl SELECT DISTINCT null, 'edit_employee', id, 1, NOW() from user_to_acl where acl_id = 'edit_country_options';

#south africa requested changes to employee

CREATE TABLE IF NOT EXISTS `employee_fulltime_option` (
  `id` int(11) NOT NULL auto_increment,
  `uuid` char(36) default NULL,
  `fulltime_phrase`      varchar(128) NOT NULL,
  `modified_by`          int(11) default NULL,
  `created_by`           int(11) default NULL,
  `is_deleted`           tinyint(1) NOT NULL default '0',
  `timestamp_updated`    timestamp NOT NULL default CURRENT_TIMESTAMP,
  `timestamp_created`    timestamp NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `name_unique` (`fulltime_phrase`),
  UNIQUE KEY `uuid_idx` (`uuid`),
  KEY `modified_by` (`modified_by`),
  KEY `created_by` (`created_by`)
) DEFAULT CHARSET=utf8 ;
DROP TRIGGER IF EXISTS `employee_fulltime_insert`;
DELIMITER $$
CREATE TRIGGER `employee_fulltime_insert` BEFORE INSERT ON `employee_fulltime_option` FOR EACH ROW BEGIN
  SET NEW.`uuid` = IFNULL(NEW.`uuid`,UUID());
END$$
DELIMITER ;

# default values
INSERT INTO `employee_fulltime_option` (`id`, `uuid`, `fulltime_phrase`, `modified_by`, `created_by`, `is_deleted`, `timestamp_updated`, `timestamp_created`) VALUES (NULL, NULL, 'Full Time', NULL, '1', '0', CURRENT_TIMESTAMP, '0000-00-00 00:00:00'), (NULL, NULL, 'Part Time', NULL, '1', '0', CURRENT_TIMESTAMP, '0000-00-00 00:00:00');

ALTER TABLE `employee`
    CHANGE `full_time` `employee_fulltime_option_id` INT(11) NULL DEFAULT NULL,
    ADD  `employee_transition_complete_option_id` INT NOT NULL AFTER  `employee_transition_option_id`,
    ADD  `option_nationality_id` INT( 11 ) NULL DEFAULT  '0' AFTER  `race_option_id`;

ALTER TABLE `_system`
    ADD COLUMN `display_employee_complete_transition`  tinyint(1) NOT NULL DEFAULT 1,
    ADD COLUMN `display_facility_postal_code`          tinyint(1) NOT NULL DEFAULT 1,
    ADD COLUMN `display_facility_lat_long`             tinyint(1) NOT NULL DEFAULT 1,
    ADD COLUMN `display_employee_nationality`          tinyint(1) NOT NULL DEFAULT 1,
    ADD COLUMN `display_facility_sponsor`              tinyint(1) NOT NULL DEFAULT 1;

# engender health updates
INSERT INTO  `translation` VALUES (NULL , NULL ,  'Sponsor Date',  'Sponsor Date', NULL , NULL ,  '0',  CURRENT_TIMESTAMP ,  '0000-00-00 00:00:00');
INSERT INTO  `translation` VALUES (NULL , NULL ,  'Employee Nationality', 'Nationality', NULL , NULL ,  '0',  CURRENT_TIMESTAMP ,  '0000-00-00 00:00:00');

# requested changes to evaluations 5/1
ALTER TABLE  `evaluation_response` ADD     `person_id` INT( 11 ) NULL DEFAULT NULL AFTER  `trainer_person_id`;

DROP TABLE IF EXISTS `evaluation_custom_answers`;
SET character_set_client = utf8;
CREATE TABLE `evaluation_custom_answers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uuid` CHAR(36) NULL DEFAULT NULL,
  `evaluation_id` int(11) NOT NULL,
  `question_id` int(11) NOT NULL,
  `answer_phrase` varchar(128) NOT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
  `timestamp_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `timestamp_created` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `modified_by` (`modified_by`),
  KEY `created_by` (`created_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
DROP TRIGGER IF EXISTS `evaluation_custom_answers_insert`;
DELIMITER $$
CREATE TRIGGER `evaluation_custom_answers_insert` BEFORE INSERT ON `evaluation_custom_answers` FOR EACH ROW BEGIN
  SET NEW.`uuid` = IFNULL(NEW.`uuid`,UUID());
END$$
DELIMITER ;

# requested changes to employees 5/1
INSERT INTO  `translation` VALUES (NULL , NULL ,  'Type of Partner',  'Type of Partner', NULL , NULL ,  '0',  CURRENT_TIMESTAMP ,  '0000-00-00 00:00:00');
INSERT INTO  `translation` VALUES (NULL , NULL ,  'Employee Based at','Based at', NULL , NULL ,  '0',  CURRENT_TIMESTAMP ,  '0000-00-00 00:00:00');

ALTER TABLE `_system`
    ADD COLUMN `display_partner_type`  tinyint(1) NOT NULL DEFAULT '1',
    ADD COLUMN `display_employee_base` tinyint(1) NOT NULL DEFAULT '1';

ALTER TABLE `partner` ADD COLUMN `hr_contact_name`  varchar(64) NULL DEFAULT NULL AFTER `fax`;
ALTER TABLE `partner` ADD COLUMN `hr_contact_phone` varchar(40) NULL DEFAULT NULL AFTER `hr_contact_name`;
ALTER TABLE `partner` ADD COLUMN `hr_contact_fax`   varchar(40) NULL DEFAULT NULL AFTER `hr_contact_phone`;
ALTER TABLE `partner` ADD COLUMN `hr_contact_email` varchar(40) NULL DEFAULT NULL AFTER `hr_contact_fax`;
ALTER TABLE `partner` ADD COLUMN `partner_type_option_id` int(11) NULL DEFAULT NULL AFTER `partner`;

ALTER TABLE `employee`
    ADD COLUMN `facility_type_option_id`  int(11) NULL DEFAULT NULL AFTER `site_id`,
    ADD COLUMN `employee_base_option_id`  int(11) NULL DEFAULT NULL AFTER `subpartner_id`,
    ADD COLUMN `transition_date`          DATETIME NULL DEFAULT NULL AFTER `transition_confirmed`,
    CHANGE  `primary_role`  `employee_role_option_id` INT( 11 ) NULL DEFAULT NULL;


DROP TABLE IF EXISTS `partner_type_option`;
SET character_set_client = utf8;
CREATE TABLE `partner_type_option` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uuid` CHAR(36) NULL DEFAULT NULL,
  `type_phrase` varchar(128) NOT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
  `timestamp_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `timestamp_created` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name_unique` (`type_phrase`),
  KEY `modified_by` (`modified_by`),
  KEY `created_by` (`created_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TRIGGER IF EXISTS `partner_type_insert`;
DELIMITER $$
CREATE TRIGGER `partner_type_insert` BEFORE INSERT ON `partner_type_option` FOR EACH ROW BEGIN
  SET NEW.`uuid` = IFNULL(NEW.`uuid`,UUID());
END$$
DELIMITER ;

DROP TABLE IF EXISTS `employee_base_option`;
SET character_set_client = utf8;
CREATE TABLE `employee_base_option` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uuid` CHAR(36) NULL DEFAULT NULL,
  `base_phrase` varchar(128) NOT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
  `timestamp_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `timestamp_created` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name_unique` (`base_phrase`),
  KEY `modified_by` (`modified_by`),
  KEY `created_by` (`created_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TRIGGER IF EXISTS `employee_base_insert`;
DELIMITER $$
CREATE TRIGGER `employee_base_insert` BEFORE INSERT ON `employee_base_option` FOR EACH ROW BEGIN
  SET NEW.`uuid` = IFNULL(NEW.`uuid`,UUID());
END$$
DELIMITER ;

#employee changes requested - 5/31
ALTER TABLE `employee`
    ADD COLUMN `transition_complete_date` DATETIME NULL DEFAULT NULL AFTER `employee_transition_complete_option_id`;
ALTER TABLE `_system`
    ADD COLUMN `display_facility_comments`  tinyint(1) NOT NULL DEFAULT '1',
    ADD COLUMN `display_employee_actual_transition_date`  tinyint(1) NOT NULL DEFAULT '1',
    ADD COLUMN `display_employee_site_type`  tinyint(1) NOT NULL DEFAULT '1';


DROP TABLE IF EXISTS `employee_site_type_option`;
SET character_set_client = utf8;
CREATE TABLE `employee_site_type_option` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uuid` CHAR(36) NULL DEFAULT NULL,
  `site_type_phrase` varchar(128) NOT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
  `timestamp_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `timestamp_created` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `modified_by` (`modified_by`),
  KEY `created_by` (`created_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
DROP TRIGGER IF EXISTS `employee_site_type_insert`;
DELIMITER $$
CREATE TRIGGER `employee_site_type_insert` BEFORE INSERT ON `employee_site_type_option` FOR EACH ROW BEGIN
  SET NEW.`uuid` = IFNULL(NEW.`uuid`,UUID());
END$$
DELIMITER ;