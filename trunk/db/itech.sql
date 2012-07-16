-- MySQL dump 10.13  Distrib 5.1.26-rc, for apple-darwin8.11.1 (i686)
--
-- Host: localhost    Database: itech_clean
-- ------------------------------------------------------
-- Server version	5.1.37-log

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
--
-- Table structure for table `_system`
--

DROP TABLE IF EXISTS `_system`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `_system` (
  `country` varchar(64) NOT NULL DEFAULT '',
  `country_uuid` char(38) NOT NULL DEFAULT '' COMMENT 'legacy id',
  `locale` varchar(32) NOT NULL DEFAULT 'en_EN.UTF-8',
  `locale_enabled` varchar(255) DEFAULT NULL,
  `allow_multi_pepfar` tinyint(1) NOT NULL DEFAULT '0',
  `allow_multi_topic` tinyint(1) NOT NULL DEFAULT '0',
  `display_funding_options` tinyint(1) NOT NULL DEFAULT '1',
  `display_test_scores_course` tinyint(1) NOT NULL DEFAULT '1',
  `display_test_scores_individual` tinyint(1) NOT NULL DEFAULT '1',
  `display_scores_limit` int(11) NOT NULL DEFAULT '5',
  `display_national_id` tinyint(1) NOT NULL DEFAULT '1',
  `display_trainer_affiliations` tinyint(1) NOT NULL DEFAULT '1',
  `display_training_custom1` tinyint(1) NOT NULL DEFAULT '1',
  `display_training_custom2` tinyint(1) NOT NULL DEFAULT '1',
  `display_training_pre_test` tinyint(1) NOT NULL DEFAULT '1',
  `display_training_post_test` tinyint(1) NOT NULL DEFAULT '1',
  `display_people_custom1` tinyint(1) NOT NULL DEFAULT '1',
  `display_people_custom2` tinyint(1) NOT NULL DEFAULT '1',
  `display_region_b` tinyint(1) NOT NULL DEFAULT '1',
  `display_people_active` tinyint(1) NOT NULL DEFAULT '1',
  `display_middle_name_last` tinyint(1) NOT NULL DEFAULT '0',
  `display_training_recommend` tinyint(1) NOT NULL DEFAULT '0',
  `display_training_trainers` tinyint(1) NOT NULL DEFAULT '1',
  `display_course_objectives` tinyint(1) NOT NULL DEFAULT '1',
  `display_training_topic` tinyint(1) NOT NULL DEFAULT '1',
  `display_training_got_curric` tinyint(1) NOT NULL DEFAULT '0',
  `display_training_got_comment` tinyint(1) NOT NULL DEFAULT '0',
  `display_training_refresher` tinyint(1) NOT NULL DEFAULT '0',
  `display_people_file_num` tinyint(1) NOT NULL DEFAULT '0',
  `display_people_home_phone` tinyint(1) NOT NULL DEFAULT '1',
  `display_people_fax` tinyint(1) NOT NULL DEFAULT '1',
  `module_evaluation_enabled` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `module_approvals_enabled` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `module_historical_data_enabled` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `module_unknown_participants_enabled` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `display_end_date` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `display_training_method` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `display_funding_amount` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `display_primary_language` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `display_secondary_language` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `display_people_title` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `display_people_suffix` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `display_people_age` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `display_people_home_address` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `display_people_second_email` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `display_middle_name` tinyint(1) NOT NULL DEFAULT '1',
  `display_funding_amounts` tinyint(1) NOT NULL DEFAULT '1',
  `display_region_c` tinyint(1) unsigned NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `_system`
--

LOCK TABLES `_system` WRITE;
/*!40000 ALTER TABLE `_system` DISABLE KEYS */;
INSERT INTO `_system` VALUES ('Country','','en_EN.UTF-8','en_EN.UTF-8',0,0,1,1,1,5,1,1,1,1,1,1,1,1,1,1,1,0,0,1,1,1,1,1,1,1,1,1,1,1,1,0,0,0,0,0,1,1,0,0,0,1,1,1);
/*!40000 ALTER TABLE `_system` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `acl`
--

DROP TABLE IF EXISTS `acl`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `acl` (
  `id` varchar(32) NOT NULL DEFAULT '',
  `acl` varchar(32) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `acl`
--

LOCK TABLES `acl` WRITE;
/*!40000 ALTER TABLE `acl` DISABLE KEYS */;
INSERT INTO `acl` VALUES ('add_edit_users','add_edit_users'),('approve_trainings','approve_trainings'),('edit_country_options','edit_country_options'),('edit_course','edit_course'),('edit_people','edit_people'),('training_organizer_option_all','training_organizer_option_all'),('training_title_option_all','training_title_option_all'),('view_course','view_course'),('view_create_reports','view_create_reports'),('view_people','view_people');
/*!40000 ALTER TABLE `acl` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `evaluation`
--

DROP TABLE IF EXISTS `evaluation`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `evaluation` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '',
  `modified_by` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
  `timestamp_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `timestamp_created` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `modified_by` (`modified_by`),
  KEY `created_by` (`created_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `evaluation`
--

LOCK TABLES `evaluation` WRITE;
/*!40000 ALTER TABLE `evaluation` DISABLE KEYS */;
/*!40000 ALTER TABLE `evaluation` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `evaluation_question`
--

DROP TABLE IF EXISTS `evaluation_question`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `evaluation_question` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `evaluation_id` int(11) NOT NULL,
  `question_text` varchar(255) NOT NULL DEFAULT '',
  `question_type` enum('Likert','Text') NOT NULL DEFAULT 'Likert',
  `weight` int(11) DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
  `timestamp_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `timestamp_created` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `modified_by` (`modified_by`),
  KEY `created_by` (`created_by`),
  KEY `evaluation_id` (`evaluation_id`),
  CONSTRAINT `evaluation_question_ibfk_1` FOREIGN KEY (`evaluation_id`) REFERENCES `evaluation` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `evaluation_question`
--

LOCK TABLES `evaluation_question` WRITE;
/*!40000 ALTER TABLE `evaluation_question` DISABLE KEYS */;
/*!40000 ALTER TABLE `evaluation_question` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `evaluation_question_response`
--

DROP TABLE IF EXISTS `evaluation_question_response`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `evaluation_question_response` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `evaluation_response_id` int(11) DEFAULT NULL,
  `evaluation_question_id` int(11) NOT NULL,
  `value_text` varchar(1024) DEFAULT '',
  `value_int` int(11) DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
  `timestamp_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `timestamp_created` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `modified_by` (`modified_by`),
  KEY `created_by` (`created_by`),
  KEY `evaluation_id` (`evaluation_response_id`),
  CONSTRAINT `eval_response` FOREIGN KEY (`evaluation_response_id`) REFERENCES `evaluation_response` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `evaluation_question_response`
--

LOCK TABLES `evaluation_question_response` WRITE;
/*!40000 ALTER TABLE `evaluation_question_response` DISABLE KEYS */;
/*!40000 ALTER TABLE `evaluation_question_response` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `evaluation_response`
--

DROP TABLE IF EXISTS `evaluation_response`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `evaluation_response` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `evaluation_to_training_id` int(11) NOT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
  `timestamp_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `timestamp_created` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `modified_by` (`modified_by`),
  KEY `created_by` (`created_by`),
  KEY `evaluation_to_training_id` (`evaluation_to_training_id`),
  CONSTRAINT `evaluation_response_ibfk_1` FOREIGN KEY (`evaluation_to_training_id`) REFERENCES `evaluation_to_training` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `evaluation_response`
--

LOCK TABLES `evaluation_response` WRITE;
/*!40000 ALTER TABLE `evaluation_response` DISABLE KEYS */;
/*!40000 ALTER TABLE `evaluation_response` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `evaluation_to_training`
--

DROP TABLE IF EXISTS `evaluation_to_training`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `evaluation_to_training` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `evaluation_id` int(11) NOT NULL,
  `training_id` int(11) NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  `timestamp_created` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_idx` (`evaluation_id`,`training_id`),
  KEY `created_by` (`created_by`),
  KEY `training_id` (`training_id`),
  KEY `e_id` (`evaluation_id`),
  CONSTRAINT `evaluation_to_training_ibfk_1` FOREIGN KEY (`evaluation_id`) REFERENCES `evaluation` (`id`),
  CONSTRAINT `evaluation_to_training_ibfk_2` FOREIGN KEY (`training_id`) REFERENCES `training` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `evaluation_to_training`
--

LOCK TABLES `evaluation_to_training` WRITE;
/*!40000 ALTER TABLE `evaluation_to_training` DISABLE KEYS */;
/*!40000 ALTER TABLE `evaluation_to_training` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `external_course`
--

DROP TABLE IF EXISTS `external_course`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `external_course` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `person_id` int(11) unsigned NOT NULL,
  `title` varchar(128) NOT NULL,
  `training_location` varchar(128) DEFAULT '',
  `training_start_date` date DEFAULT NULL,
  `training_length_value` int(11) DEFAULT NULL,
  `training_length_interval` enum('day') NOT NULL DEFAULT 'day',
  `training_funder` varchar(128) DEFAULT '',
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
  `timestamp_created` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `external_course`
--

LOCK TABLES `external_course` WRITE;
/*!40000 ALTER TABLE `external_course` DISABLE KEYS */;
/*!40000 ALTER TABLE `external_course` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `facility`
--

DROP TABLE IF EXISTS `facility`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `facility` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `facility_name` varchar(128) NOT NULL DEFAULT '',
  `uuid` char(38) DEFAULT '',
  `address_1` varchar(128) NOT NULL DEFAULT '',
  `address_2` varchar(128) NOT NULL DEFAULT '',
  `location_id` int(10) unsigned DEFAULT NULL,
  `postal_code` varchar(20) DEFAULT '',
  `phone` varchar(32) DEFAULT '',
  `fax` varchar(32) DEFAULT '',
  `sponsor_option_id` int(11) DEFAULT NULL,
  `type_option_id` int(11) DEFAULT NULL,
  `facility_comments` varchar(255) DEFAULT '',
  `modified_by` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
  `timestamp_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `timestamp_created` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `facility_name` (`facility_name`),
  KEY `created_by` (`created_by`),
  KEY `modified_by` (`modified_by`),
  KEY `sponsor_option_id` (`sponsor_option_id`),
  KEY `type_option_id` (`type_option_id`),
  KEY `facility_ibfk_5` (`location_id`),
  CONSTRAINT `facility_ibfk_1` FOREIGN KEY (`sponsor_option_id`) REFERENCES `facility_sponsor_option` (`id`),
  CONSTRAINT `facility_ibfk_2` FOREIGN KEY (`type_option_id`) REFERENCES `facility_type_option` (`id`),
  CONSTRAINT `facility_ibfk_5` FOREIGN KEY (`location_id`) REFERENCES `location` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `facility`
--

LOCK TABLES `facility` WRITE;
/*!40000 ALTER TABLE `facility` DISABLE KEYS */;
/*!40000 ALTER TABLE `facility` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `facility_sponsor_option`
--

DROP TABLE IF EXISTS `facility_sponsor_option`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `facility_sponsor_option` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `facility_sponsor_phrase` varchar(128) NOT NULL DEFAULT '',
  `modified_by` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
  `timestamp_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `timestamp_created` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name_unique` (`facility_sponsor_phrase`),
  KEY `created_by` (`created_by`),
  KEY `modified_by` (`modified_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `facility_sponsor_option`
--

LOCK TABLES `facility_sponsor_option` WRITE;
/*!40000 ALTER TABLE `facility_sponsor_option` DISABLE KEYS */;
/*!40000 ALTER TABLE `facility_sponsor_option` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `facility_type_option`
--

DROP TABLE IF EXISTS `facility_type_option`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `facility_type_option` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `facility_type_phrase` varchar(128) NOT NULL DEFAULT '',
  `modified_by` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
  `timestamp_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `timestamp_created` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name_unique` (`facility_type_phrase`),
  KEY `modified_by` (`modified_by`),
  KEY `created_by` (`created_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `facility_type_option`
--

LOCK TABLES `facility_type_option` WRITE;
/*!40000 ALTER TABLE `facility_type_option` DISABLE KEYS */;
/*!40000 ALTER TABLE `facility_type_option` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `file`
--

DROP TABLE IF EXISTS `file`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `file` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` int(10) unsigned NOT NULL,
  `parent_table` varchar(255) NOT NULL DEFAULT '0',
  `filename` varchar(255) NOT NULL,
  `filemime` varchar(255) NOT NULL,
  `filesize` int(10) unsigned NOT NULL DEFAULT '0',
  `modified_by` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
  `timestamp_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `timestamp_created` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `parent_key` (`parent_table`,`parent_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `file`
--

LOCK TABLES `file` WRITE;
/*!40000 ALTER TABLE `file` DISABLE KEYS */;
/*!40000 ALTER TABLE `file` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `location`
--

DROP TABLE IF EXISTS `location`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `location` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(36) DEFAULT NULL,
  `location_name` varchar(128) NOT NULL,
  `parent_id` int(10) unsigned DEFAULT NULL,
  `tier` smallint(5) unsigned NOT NULL,
  `is_default` tinyint(1) NOT NULL DEFAULT '0',
  `modified_by` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
  `timestamp_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `timestamp_created` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uuid_uniq` (`uuid`),
  KEY `parent_id` (`parent_id`),
  KEY `modified_by` (`modified_by`),
  KEY `created_by` (`created_by`),
  CONSTRAINT `parent_fk` FOREIGN KEY (`parent_id`) REFERENCES `location` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
--
-- Table structure for table `person`
--

DROP TABLE IF EXISTS `person`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `person` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uuid` char(38) DEFAULT '',
  `title_option_id` int(11) DEFAULT NULL,
  `first_name` varchar(32) NOT NULL DEFAULT '',
  `middle_name` varchar(32) DEFAULT '',
  `last_name` varchar(32) NOT NULL DEFAULT '',
  `suffix_option_id` int(11) DEFAULT NULL,
  `national_id` varchar(64) DEFAULT '',
  `file_number` varchar(64) DEFAULT NULL,
  `birthdate` date DEFAULT NULL,
  `gender` enum('male','female','na') NOT NULL DEFAULT 'na',
  `facility_id` int(11) NOT NULL,
  `phone_work` varchar(32) DEFAULT '',
  `phone_mobile` varchar(32) DEFAULT '',
  `fax` varchar(32) DEFAULT '',
  `phone_home` varchar(32) DEFAULT '',
  `email` varchar(64) DEFAULT '',
  `email_secondary` varchar(64) DEFAULT '',
  `primary_qualification_option_id` int(11) NOT NULL,
  `primary_responsibility_option_id` int(11) NOT NULL,
  `secondary_responsibility_option_id` int(11) NOT NULL,
  `comments` varchar(255) DEFAULT '',
  `person_custom_1_option_id` int(11) DEFAULT NULL,
  `person_custom_2_option_id` int(11) DEFAULT NULL,
  `home_address_1` varchar(128) DEFAULT '',
  `home_address_2` varchar(128) DEFAULT '',
  `home_location_id` int(10) unsigned DEFAULT NULL,
  `home_postal_code` int(11) DEFAULT NULL,
  `active` enum('active','inactive','deceased') NOT NULL DEFAULT 'active',
  `timestamp_created` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `timestamp_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_by` int(11) DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uuid_idx` (`uuid`),
  KEY `modified_by` (`modified_by`),
  KEY `created_by` (`created_by`),
  KEY `facility_id` (`facility_id`),
  KEY `home_location_ibfk_9` (`home_location_id`),
  CONSTRAINT `home_location_ibfk_9` FOREIGN KEY (`home_location_id`) REFERENCES `location` (`id`),
  CONSTRAINT `person_ibfk_1` FOREIGN KEY (`facility_id`) REFERENCES `facility` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `person`
--

LOCK TABLES `person` WRITE;
/*!40000 ALTER TABLE `person` DISABLE KEYS */;
/*!40000 ALTER TABLE `person` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `person_active_trainer_option`
--

DROP TABLE IF EXISTS `person_active_trainer_option`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `person_active_trainer_option` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `active_trainer_phrase` varchar(128) NOT NULL DEFAULT '',
  `modified_by` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
  `timestamp_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `timestamp_created` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name_unique` (`active_trainer_phrase`),
  KEY `modified_by` (`modified_by`),
  KEY `created_by` (`created_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `person_active_trainer_option`
--

LOCK TABLES `person_active_trainer_option` WRITE;
/*!40000 ALTER TABLE `person_active_trainer_option` DISABLE KEYS */;
/*!40000 ALTER TABLE `person_active_trainer_option` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `person_custom_1_option`
--

DROP TABLE IF EXISTS `person_custom_1_option`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `person_custom_1_option` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `custom1_phrase` varchar(128) NOT NULL DEFAULT '',
  `modified_by` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
  `timestamp_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `timestamp_created` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name_unique` (`custom1_phrase`),
  KEY `modified_by` (`modified_by`),
  KEY `created_by` (`created_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `person_custom_1_option`
--

LOCK TABLES `person_custom_1_option` WRITE;
/*!40000 ALTER TABLE `person_custom_1_option` DISABLE KEYS */;
/*!40000 ALTER TABLE `person_custom_1_option` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `person_custom_2_option`
--

DROP TABLE IF EXISTS `person_custom_2_option`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `person_custom_2_option` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `custom2_phrase` varchar(128) NOT NULL DEFAULT '',
  `modified_by` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
  `timestamp_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `timestamp_created` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name_unique` (`custom2_phrase`),
  KEY `created_by` (`created_by`),
  KEY `modified_by` (`modified_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `person_custom_2_option`
--

LOCK TABLES `person_custom_2_option` WRITE;
/*!40000 ALTER TABLE `person_custom_2_option` DISABLE KEYS */;
/*!40000 ALTER TABLE `person_custom_2_option` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `person_history`
--

DROP TABLE IF EXISTS `person_history`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `person_history` (
  `vid` int(11) NOT NULL AUTO_INCREMENT,
  `person_id` int(11) NOT NULL,
  `uuid` char(38) DEFAULT '',
  `title_option_id` int(11) DEFAULT NULL,
  `first_name` varchar(32) NOT NULL DEFAULT '',
  `middle_name` varchar(32) DEFAULT '',
  `last_name` varchar(32) NOT NULL DEFAULT '',
  `suffix_option_id` int(11) DEFAULT NULL,
  `national_id` varchar(64) DEFAULT '',
  `file_number` varchar(64) DEFAULT NULL,
  `birthdate` date DEFAULT NULL,
  `gender` enum('male','female','na') NOT NULL DEFAULT 'na',
  `facility_id` int(11) NOT NULL,
  `phone_work` varchar(32) DEFAULT '',
  `phone_mobile` varchar(32) DEFAULT '',
  `fax` varchar(32) DEFAULT '',
  `phone_home` varchar(32) DEFAULT '',
  `email` varchar(64) DEFAULT '',
  `primary_qualification_option_id` int(11) NOT NULL,
  `primary_responsibility_option_id` int(11) NOT NULL,
  `secondary_responsibility_option_id` int(11) NOT NULL,
  `comments` varchar(255) DEFAULT '',
  `person_custom_1_option_id` int(11) DEFAULT NULL,
  `person_custom_2_option_id` int(11) DEFAULT NULL,
  `active` enum('active','inactive','deceased') NOT NULL DEFAULT 'active',
  `timestamp_created` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified_by` int(11) DEFAULT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
  `home_address_1` varchar(128) DEFAULT '',
  `home_address_2` varchar(128) DEFAULT '',
  `home_location_id` int(11) DEFAULT NULL,
  `home_postal_code` int(11) DEFAULT NULL,
  `email_secondary` varchar(64) DEFAULT '',
  PRIMARY KEY (`vid`),
  KEY `modified_by` (`modified_by`),
  KEY `facility_id` (`facility_id`),
  KEY `person_id` (`person_id`),
  CONSTRAINT `person_history_ibfk_1` FOREIGN KEY (`person_id`) REFERENCES `person` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `person_history`
--

LOCK TABLES `person_history` WRITE;
/*!40000 ALTER TABLE `person_history` DISABLE KEYS */;
/*!40000 ALTER TABLE `person_history` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `person_qualification_option`
--

DROP TABLE IF EXISTS `person_qualification_option`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `person_qualification_option` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) DEFAULT NULL,
  `qualification_phrase` varchar(128) NOT NULL DEFAULT '',
  `is_default` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `modified_by` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
  `timestamp_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `timestamp_created` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name_unique` (`qualification_phrase`),
  KEY `modified_by` (`modified_by`),
  KEY `created_by` (`created_by`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `person_qualification_option`
--

LOCK TABLES `person_qualification_option` WRITE;
/*!40000 ALTER TABLE `person_qualification_option` DISABLE KEYS */;
INSERT INTO `person_qualification_option` VALUES (1,NULL,'Laboratory',0,NULL,NULL,0,'2008-12-03 18:04:17','0000-00-00 00:00:00'),(2,NULL,'Nurse',0,NULL,NULL,0,'2008-12-03 18:04:17','0000-00-00 00:00:00'),(3,NULL,'Other',0,NULL,NULL,0,'2008-12-03 18:04:17','0000-00-00 00:00:00'),(4,NULL,'Physician',0,NULL,NULL,0,'2008-12-03 18:04:17','0000-00-00 00:00:00'),(5,NULL,'Mid-Level Clinician',0,NULL,NULL,0,'2008-12-03 18:04:17','0000-00-00 00:00:00'),(6,NULL,'Community Based Worker',0,NULL,NULL,0,'2008-12-03 18:04:17','0000-00-00 00:00:00'),(7,NULL,'Pharmacy',0,NULL,NULL,0,'2008-12-03 18:04:17','0000-00-00 00:00:00'),(8,NULL,'Social Services',0,NULL,NULL,0,'2008-12-03 18:04:17','0000-00-00 00:00:00'),(9,NULL,'Dental Services',0,NULL,NULL,0,'2008-12-03 18:04:17','0000-00-00 00:00:00'),(10,NULL,'Paramedical',0,NULL,NULL,0,'2008-12-03 18:04:17','0000-00-00 00:00:00');
/*!40000 ALTER TABLE `person_qualification_option` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `person_responsibility_option`
--

DROP TABLE IF EXISTS `person_responsibility_option`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `person_responsibility_option` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `responsibility_phrase` varchar(128) NOT NULL DEFAULT '',
  `modified_by` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
  `timestamp_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `timestamp_created` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name_unique` (`responsibility_phrase`),
  KEY `created_by` (`created_by`),
  KEY `modified_by` (`modified_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `person_responsibility_option`
--

LOCK TABLES `person_responsibility_option` WRITE;
/*!40000 ALTER TABLE `person_responsibility_option` DISABLE KEYS */;
/*!40000 ALTER TABLE `person_responsibility_option` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `person_suffix_option`
--

DROP TABLE IF EXISTS `person_suffix_option`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `person_suffix_option` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `suffix_phrase` varchar(128) NOT NULL DEFAULT '',
  `modified_by` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
  `timestamp_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `timestamp_created` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name_unique` (`suffix_phrase`),
  KEY `created_by` (`created_by`),
  KEY `modified_by` (`modified_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `person_suffix_option`
--

LOCK TABLES `person_suffix_option` WRITE;
/*!40000 ALTER TABLE `person_suffix_option` DISABLE KEYS */;
/*!40000 ALTER TABLE `person_suffix_option` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `person_title_option`
--

DROP TABLE IF EXISTS `person_title_option`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `person_title_option` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title_phrase` varchar(128) NOT NULL DEFAULT '',
  `modified_by` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
  `timestamp_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `timestamp_created` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name_unique` (`title_phrase`),
  KEY `created_by` (`created_by`),
  KEY `modified_by` (`modified_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `person_title_option`
--

LOCK TABLES `person_title_option` WRITE;
/*!40000 ALTER TABLE `person_title_option` DISABLE KEYS */;
/*!40000 ALTER TABLE `person_title_option` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `person_to_training`
--

DROP TABLE IF EXISTS `person_to_training`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `person_to_training` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `person_id` int(11) NOT NULL,
  `training_id` int(11) NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  `timestamp_created` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_idx` (`person_id`,`training_id`),
  UNIQUE KEY `training_person_uniq` (`training_id`,`person_id`),
  KEY `created_by` (`created_by`),
  KEY `person_id` (`person_id`),
  KEY `training_id` (`training_id`),
  CONSTRAINT `person_to_training_ibfk_1` FOREIGN KEY (`person_id`) REFERENCES `person` (`id`),
  CONSTRAINT `person_to_training_ibfk_2` FOREIGN KEY (`training_id`) REFERENCES `training` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `person_to_training`
--

LOCK TABLES `person_to_training` WRITE;
/*!40000 ALTER TABLE `person_to_training` DISABLE KEYS */;
/*!40000 ALTER TABLE `person_to_training` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `person_to_training_topic_option`
--

DROP TABLE IF EXISTS `person_to_training_topic_option`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `person_to_training_topic_option` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `person_id` int(11) NOT NULL,
  `training_topic_option_id` int(11) NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  `timestamp_created` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_idx` (`person_id`,`training_topic_option_id`),
  KEY `created_by` (`created_by`),
  KEY `person_id` (`person_id`),
  KEY `training_topic_option_id` (`training_topic_option_id`),
  CONSTRAINT `person_to_training_topic_option_ibfk_1` FOREIGN KEY (`person_id`) REFERENCES `person` (`id`),
  CONSTRAINT `person_to_training_topic_option_ibfk_4` FOREIGN KEY (`training_topic_option_id`) REFERENCES `training_topic_option` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `person_to_training_topic_option`
--

LOCK TABLES `person_to_training_topic_option` WRITE;
/*!40000 ALTER TABLE `person_to_training_topic_option` DISABLE KEYS */;
/*!40000 ALTER TABLE `person_to_training_topic_option` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `score`
--

DROP TABLE IF EXISTS `score`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `score` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `person_to_training_id` int(11) NOT NULL,
  `training_date` date NOT NULL,
  `score_value` int(11) NOT NULL,
  `score_label` varchar(255) NOT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
  `timestamp_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `timestamp_created` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `score`
--

LOCK TABLES `score` WRITE;
/*!40000 ALTER TABLE `score` DISABLE KEYS */;
/*!40000 ALTER TABLE `score` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `trainer`
--

DROP TABLE IF EXISTS `trainer`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `trainer` (
  `person_id` int(11) NOT NULL,
  `type_option_id` int(11) NOT NULL DEFAULT '0',
  `active_trainer_option_id` int(11) DEFAULT NULL,
  `affiliation_option_id` int(11) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `modified_by` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
  `timestamp_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `timestamp_created` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`person_id`),
  UNIQUE KEY `person_idx` (`person_id`),
  KEY `modified_by` (`modified_by`),
  KEY `created_by` (`created_by`),
  KEY `type_option_id` (`type_option_id`),
  KEY `affiliation_option_id` (`affiliation_option_id`),
  CONSTRAINT `trainer_ibfk_1` FOREIGN KEY (`person_id`) REFERENCES `person` (`id`),
  CONSTRAINT `trainer_ibfk_2` FOREIGN KEY (`type_option_id`) REFERENCES `trainer_type_option` (`id`),
  CONSTRAINT `trainer_ibfk_3` FOREIGN KEY (`affiliation_option_id`) REFERENCES `trainer_affiliation_option` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `trainer`
--

LOCK TABLES `trainer` WRITE;
/*!40000 ALTER TABLE `trainer` DISABLE KEYS */;
/*!40000 ALTER TABLE `trainer` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `trainer_affiliation_option`
--

DROP TABLE IF EXISTS `trainer_affiliation_option`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `trainer_affiliation_option` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `trainer_affiliation_phrase` varchar(128) NOT NULL DEFAULT '',
  `modified_by` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
  `timestamp_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `timestamp_created` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name_unique` (`trainer_affiliation_phrase`),
  KEY `created_by` (`created_by`),
  KEY `modified_by` (`modified_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `trainer_affiliation_option`
--

LOCK TABLES `trainer_affiliation_option` WRITE;
/*!40000 ALTER TABLE `trainer_affiliation_option` DISABLE KEYS */;
/*!40000 ALTER TABLE `trainer_affiliation_option` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `trainer_history`
--

DROP TABLE IF EXISTS `trainer_history`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `trainer_history` (
  `vid` int(11) NOT NULL AUTO_INCREMENT,
  `person_id` int(11) NOT NULL,
  `type_option_id` int(11) NOT NULL DEFAULT '0',
  `active_trainer_option_id` int(11) DEFAULT NULL,
  `affiliation_option_id` int(11) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `modified_by` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
  `timestamp_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `timestamp_created` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`vid`),
  KEY `type_option_id` (`type_option_id`),
  KEY `affiliation_option_id` (`affiliation_option_id`),
  KEY `trainer_history_ibfk_1` (`person_id`),
  CONSTRAINT `trainer_history_ibfk_1` FOREIGN KEY (`person_id`) REFERENCES `person` (`id`),
  CONSTRAINT `trainer_history_ibfk_2` FOREIGN KEY (`type_option_id`) REFERENCES `trainer_type_option` (`id`),
  CONSTRAINT `trainer_history_ibfk_3` FOREIGN KEY (`affiliation_option_id`) REFERENCES `trainer_affiliation_option` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `trainer_history`
--

LOCK TABLES `trainer_history` WRITE;
/*!40000 ALTER TABLE `trainer_history` DISABLE KEYS */;
/*!40000 ALTER TABLE `trainer_history` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `trainer_language_option`
--

DROP TABLE IF EXISTS `trainer_language_option`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `trainer_language_option` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `language_phrase` varchar(128) NOT NULL DEFAULT '',
  `modified_by` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
  `timestamp_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `timestamp_created` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name_unique` (`language_phrase`),
  KEY `modified_by` (`modified_by`),
  KEY `created_by` (`created_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `trainer_language_option`
--

LOCK TABLES `trainer_language_option` WRITE;
/*!40000 ALTER TABLE `trainer_language_option` DISABLE KEYS */;
/*!40000 ALTER TABLE `trainer_language_option` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `trainer_skill_option`
--

DROP TABLE IF EXISTS `trainer_skill_option`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `trainer_skill_option` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `trainer_skill_phrase` varchar(128) NOT NULL DEFAULT '',
  `modified_by` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
  `timestamp_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `timestamp_created` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name_unique` (`trainer_skill_phrase`),
  KEY `modified_by` (`modified_by`),
  KEY `created_by` (`created_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `trainer_skill_option`
--

LOCK TABLES `trainer_skill_option` WRITE;
/*!40000 ALTER TABLE `trainer_skill_option` DISABLE KEYS */;
/*!40000 ALTER TABLE `trainer_skill_option` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `trainer_to_trainer_language_option`
--

DROP TABLE IF EXISTS `trainer_to_trainer_language_option`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `trainer_to_trainer_language_option` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `trainer_id` int(11) NOT NULL,
  `trainer_language_option_id` int(11) NOT NULL,
  `is_primary` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `created_by` int(11) DEFAULT NULL,
  `timestamp_created` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `created_by` (`created_by`),
  KEY `trainer_language_option_id` (`trainer_language_option_id`),
  KEY `trainer_id` (`trainer_id`),
  CONSTRAINT `trainer_to_trainer_language_option_ibfk_1` FOREIGN KEY (`trainer_language_option_id`) REFERENCES `trainer_language_option` (`id`),
  CONSTRAINT `trainer_to_trainer_language_option_ibfk_2` FOREIGN KEY (`trainer_id`) REFERENCES `trainer` (`person_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `trainer_to_trainer_language_option`
--

LOCK TABLES `trainer_to_trainer_language_option` WRITE;
/*!40000 ALTER TABLE `trainer_to_trainer_language_option` DISABLE KEYS */;
/*!40000 ALTER TABLE `trainer_to_trainer_language_option` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `trainer_to_trainer_skill_option`
--

DROP TABLE IF EXISTS `trainer_to_trainer_skill_option`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `trainer_to_trainer_skill_option` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `trainer_id` int(11) NOT NULL,
  `trainer_skill_option_id` int(11) NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  `timestamp_created` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `created_by` (`created_by`),
  KEY `trainer_id` (`trainer_id`),
  KEY `trainer_skill_option_id` (`trainer_skill_option_id`),
  CONSTRAINT `trainer_to_trainer_skill_option_ibfk_1` FOREIGN KEY (`trainer_id`) REFERENCES `trainer` (`person_id`),
  CONSTRAINT `trainer_to_trainer_skill_option_ibfk_2` FOREIGN KEY (`trainer_skill_option_id`) REFERENCES `trainer_skill_option` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `trainer_to_trainer_skill_option`
--

LOCK TABLES `trainer_to_trainer_skill_option` WRITE;
/*!40000 ALTER TABLE `trainer_to_trainer_skill_option` DISABLE KEYS */;
/*!40000 ALTER TABLE `trainer_to_trainer_skill_option` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `trainer_type_option`
--

DROP TABLE IF EXISTS `trainer_type_option`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `trainer_type_option` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `trainer_type_phrase` varchar(128) NOT NULL DEFAULT '',
  `modified_by` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
  `timestamp_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `timestamp_created` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name_unique` (`trainer_type_phrase`),
  KEY `created_by` (`created_by`),
  KEY `modified_by` (`modified_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `trainer_type_option`
--

LOCK TABLES `trainer_type_option` WRITE;
/*!40000 ALTER TABLE `trainer_type_option` DISABLE KEYS */;
/*!40000 ALTER TABLE `trainer_type_option` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `training`
--

DROP TABLE IF EXISTS `training`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `training` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `course_id` int(11) DEFAULT NULL,
  `training_title_option_id` int(11) NOT NULL,
  `has_known_participants` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `training_start_date` date NOT NULL,
  `training_end_date` date DEFAULT NULL,
  `training_length_value` int(11) NOT NULL,
  `training_length_interval` enum('hour','week','day') NOT NULL DEFAULT 'hour',
  `training_organizer_option_id` int(11) DEFAULT '0',
  `training_location_id` int(11) DEFAULT '0',
  `training_level_option_id` int(11) DEFAULT '0',
  `training_method_option_id` int(11) unsigned DEFAULT NULL,
  `training_custom_1_option_id` int(11) DEFAULT NULL,
  `training_custom_2_option_id` int(11) DEFAULT NULL,
  `training_got_curriculum_option_id` int(11) DEFAULT NULL,
  `training_primary_language_option_id` int(11) unsigned DEFAULT NULL,
  `training_secondary_language_option_id` int(11) unsigned DEFAULT NULL,
  `uuid` char(38) DEFAULT '',
  `comments` text NOT NULL,
  `got_comments` text NOT NULL,
  `objectives` text NOT NULL,
  `is_approved` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `is_tot` tinyint(1) NOT NULL,
  `is_refresher` tinyint(1) NOT NULL,
  `pre` float DEFAULT NULL,
  `post` float DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
  `timestamp_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `timestamp_created` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `created_by` (`created_by`),
  KEY `modified_by` (`modified_by`),
  KEY `course_id` (`course_id`),
  KEY `training_title_option_id` (`training_title_option_id`),
  CONSTRAINT `training_ibfk_2` FOREIGN KEY (`training_title_option_id`) REFERENCES `training_title_option` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `training`
--

LOCK TABLES `training` WRITE;
/*!40000 ALTER TABLE `training` DISABLE KEYS */;
/*!40000 ALTER TABLE `training` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `training_approval_history`
--

DROP TABLE IF EXISTS `training_approval_history`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `training_approval_history` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `training_id` int(11) NOT NULL,
  `approval_status` enum('resubmitted','rejected','approved','new') NOT NULL DEFAULT 'new',
  `message` text,
  `recipients` varchar(512) DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `timestamp_created` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `training_id` (`training_id`),
  KEY `created_by` (`created_by`),
  KEY `created_by_2` (`created_by`),
  KEY `time_idx` (`timestamp_created`),
  CONSTRAINT `training_idx` FOREIGN KEY (`training_id`) REFERENCES `training` (`id`),
  CONSTRAINT `user_idx` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `training_approval_history`
--

LOCK TABLES `training_approval_history` WRITE;
/*!40000 ALTER TABLE `training_approval_history` DISABLE KEYS */;
/*!40000 ALTER TABLE `training_approval_history` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `training_category_option`
--

DROP TABLE IF EXISTS `training_category_option`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `training_category_option` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `training_category_phrase` varchar(128) NOT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
  `timestamp_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `timestamp_created` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name_unique` (`training_category_phrase`),
  KEY `modified_by` (`modified_by`),
  KEY `created_by` (`created_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `training_category_option`
--

LOCK TABLES `training_category_option` WRITE;
/*!40000 ALTER TABLE `training_category_option` DISABLE KEYS */;
/*!40000 ALTER TABLE `training_category_option` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `training_category_option_to_training_title_option`
--

DROP TABLE IF EXISTS `training_category_option_to_training_title_option`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `training_category_option_to_training_title_option` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `training_category_option_id` int(11) NOT NULL,
  `training_title_option_id` int(11) NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  `timestamp_created` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_training_cat` (`training_category_option_id`,`training_title_option_id`),
  KEY `training_category_option_id` (`training_category_option_id`),
  KEY `created_by` (`created_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `training_category_option_to_training_title_option`
--

LOCK TABLES `training_category_option_to_training_title_option` WRITE;
/*!40000 ALTER TABLE `training_category_option_to_training_title_option` DISABLE KEYS */;
/*!40000 ALTER TABLE `training_category_option_to_training_title_option` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `training_custom_1_option`
--

DROP TABLE IF EXISTS `training_custom_1_option`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `training_custom_1_option` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `custom1_phrase` varchar(128) NOT NULL DEFAULT '',
  `modified_by` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
  `timestamp_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `timestamp_created` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name_unique` (`custom1_phrase`),
  KEY `modified_by` (`modified_by`),
  KEY `created_by` (`created_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `training_custom_1_option`
--

LOCK TABLES `training_custom_1_option` WRITE;
/*!40000 ALTER TABLE `training_custom_1_option` DISABLE KEYS */;
/*!40000 ALTER TABLE `training_custom_1_option` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `training_custom_2_option`
--

DROP TABLE IF EXISTS `training_custom_2_option`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `training_custom_2_option` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `custom2_phrase` varchar(128) NOT NULL DEFAULT '',
  `modified_by` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
  `timestamp_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `timestamp_created` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name_unique` (`custom2_phrase`),
  KEY `modified_by` (`modified_by`),
  KEY `created_by` (`created_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `training_custom_2_option`
--

LOCK TABLES `training_custom_2_option` WRITE;
/*!40000 ALTER TABLE `training_custom_2_option` DISABLE KEYS */;
/*!40000 ALTER TABLE `training_custom_2_option` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `training_funding_option`
--

DROP TABLE IF EXISTS `training_funding_option`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `training_funding_option` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `funding_phrase` varchar(128) NOT NULL DEFAULT '',
  `is_default` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `modified_by` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
  `timestamp_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `timestamp_created` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name_unique` (`funding_phrase`),
  KEY `created_by` (`created_by`),
  KEY `modified_by` (`modified_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `training_funding_option`
--

LOCK TABLES `training_funding_option` WRITE;
/*!40000 ALTER TABLE `training_funding_option` DISABLE KEYS */;
/*!40000 ALTER TABLE `training_funding_option` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `training_got_curriculum_option`
--

DROP TABLE IF EXISTS `training_got_curriculum_option`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `training_got_curriculum_option` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `training_got_curriculum_phrase` varchar(128) NOT NULL DEFAULT '',
  `modified_by` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
  `timestamp_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `timestamp_created` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name_unique` (`training_got_curriculum_phrase`),
  KEY `created_by` (`created_by`),
  KEY `modified_by` (`modified_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `training_got_curriculum_option`
--

LOCK TABLES `training_got_curriculum_option` WRITE;
/*!40000 ALTER TABLE `training_got_curriculum_option` DISABLE KEYS */;
/*!40000 ALTER TABLE `training_got_curriculum_option` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `training_level_option`
--

DROP TABLE IF EXISTS `training_level_option`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `training_level_option` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `training_level_phrase` varchar(128) NOT NULL DEFAULT '',
  `is_default` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `modified_by` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
  `timestamp_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `timestamp_created` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name_unique` (`training_level_phrase`),
  KEY `modified_by` (`modified_by`),
  KEY `created_by` (`created_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `training_level_option`
--

LOCK TABLES `training_level_option` WRITE;
/*!40000 ALTER TABLE `training_level_option` DISABLE KEYS */;
/*!40000 ALTER TABLE `training_level_option` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `training_location`
--

DROP TABLE IF EXISTS `training_location`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `training_location` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `training_location_name` varchar(128) NOT NULL DEFAULT '',
  `location_id` int(10) unsigned DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
  `timestamp_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `timestamp_created` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `modified_by` (`modified_by`),
  KEY `created_by` (`created_by`),
  KEY `training_location_ibfk_9` (`location_id`),
  CONSTRAINT `training_location_ibfk_1` FOREIGN KEY (`modified_by`) REFERENCES `user` (`id`),
  CONSTRAINT `training_location_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`),
  CONSTRAINT `training_location_ibfk_9` FOREIGN KEY (`location_id`) REFERENCES `location` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `training_location`
--

LOCK TABLES `training_location` WRITE;
/*!40000 ALTER TABLE `training_location` DISABLE KEYS */;
/*!40000 ALTER TABLE `training_location` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `training_method_option`
--

DROP TABLE IF EXISTS `training_method_option`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `training_method_option` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `training_method_phrase` varchar(128) NOT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
  `timestamp_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `timestamp_created` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name_unique` (`training_method_phrase`),
  KEY `modified_by` (`modified_by`),
  KEY `created_by` (`created_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `training_method_option`
--

LOCK TABLES `training_method_option` WRITE;
/*!40000 ALTER TABLE `training_method_option` DISABLE KEYS */;
/*!40000 ALTER TABLE `training_method_option` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `training_organizer_option`
--

DROP TABLE IF EXISTS `training_organizer_option`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `training_organizer_option` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `training_organizer_phrase` varchar(128) NOT NULL DEFAULT '',
  `is_default` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `modified_by` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
  `timestamp_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `timestamp_created` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name_unique` (`training_organizer_phrase`),
  KEY `created_by` (`created_by`),
  KEY `modified_by` (`modified_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `training_organizer_option`
--

LOCK TABLES `training_organizer_option` WRITE;
/*!40000 ALTER TABLE `training_organizer_option` DISABLE KEYS */;
/*!40000 ALTER TABLE `training_organizer_option` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `training_pepfar_categories_option`
--

DROP TABLE IF EXISTS `training_pepfar_categories_option`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `training_pepfar_categories_option` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pepfar_category_phrase` varchar(128) NOT NULL DEFAULT '',
  `is_default` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `id_training_method_option_id` int(11) DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
  `timestamp_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `timestamp_created` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name_unique` (`pepfar_category_phrase`),
  KEY `created_by` (`created_by`),
  KEY `modified_by` (`modified_by`),
  KEY `id_training_method_option_id` (`id_training_method_option_id`),
  CONSTRAINT `training_pepfar_categories_option_ibfk_1` FOREIGN KEY (`id_training_method_option_id`) REFERENCES `training_method_option` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `training_pepfar_categories_option`
--

LOCK TABLES `training_pepfar_categories_option` WRITE;
/*!40000 ALTER TABLE `training_pepfar_categories_option` DISABLE KEYS */;
/*!40000 ALTER TABLE `training_pepfar_categories_option` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `training_recommend`
--

DROP TABLE IF EXISTS `training_recommend`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `training_recommend` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `person_qualification_option_id` int(11) DEFAULT NULL,
  `training_topic_option_id` int(11) DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
  `timestamp_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `timestamp_created` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `person_qualification_option_id` (`person_qualification_option_id`,`training_topic_option_id`),
  KEY `created_by` (`created_by`),
  KEY `modified_by` (`modified_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `training_recommend`
--

LOCK TABLES `training_recommend` WRITE;
/*!40000 ALTER TABLE `training_recommend` DISABLE KEYS */;
/*!40000 ALTER TABLE `training_recommend` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `training_title_option`
--

DROP TABLE IF EXISTS `training_title_option`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `training_title_option` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `training_title_phrase` varchar(128) NOT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
  `timestamp_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `timestamp_created` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name_unique` (`training_title_phrase`),
  KEY `modified_by` (`modified_by`),
  KEY `created_by` (`created_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `training_title_option`
--

LOCK TABLES `training_title_option` WRITE;
/*!40000 ALTER TABLE `training_title_option` DISABLE KEYS */;
/*!40000 ALTER TABLE `training_title_option` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `training_to_person_qualification_option`
--

DROP TABLE IF EXISTS `training_to_person_qualification_option`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `training_to_person_qualification_option` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `training_id` int(11) NOT NULL,
  `person_qualification_option_id` int(11) NOT NULL,
  `person_count_na` int(11) NOT NULL DEFAULT '0',
  `person_count_male` int(11) NOT NULL DEFAULT '0',
  `person_count_female` int(11) NOT NULL DEFAULT '0',
  `created_by` int(11) DEFAULT NULL,
  `timestamp_created` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `training_qual_uniq` (`training_id`,`person_qualification_option_id`),
  KEY `person_qualification_option_id` (`person_qualification_option_id`),
  CONSTRAINT `training_to_person_qualification_option_ibfk_2` FOREIGN KEY (`person_qualification_option_id`) REFERENCES `person_qualification_option` (`id`),
  CONSTRAINT `training_to_person_qualification_option_ibfk_3` FOREIGN KEY (`training_id`) REFERENCES `training` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `training_to_person_qualification_option`
--

LOCK TABLES `training_to_person_qualification_option` WRITE;
/*!40000 ALTER TABLE `training_to_person_qualification_option` DISABLE KEYS */;
/*!40000 ALTER TABLE `training_to_person_qualification_option` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `training_to_trainer`
--

DROP TABLE IF EXISTS `training_to_trainer`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `training_to_trainer` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `trainer_id` int(11) NOT NULL,
  `training_id` int(11) NOT NULL,
  `duration_days` float NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  `timestamp_created` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_training` (`trainer_id`,`training_id`),
  KEY `created_by` (`created_by`),
  KEY `trainer_id` (`trainer_id`),
  KEY `training_id` (`training_id`),
  CONSTRAINT `training_to_trainer_ibfk_1` FOREIGN KEY (`trainer_id`) REFERENCES `trainer` (`person_id`),
  CONSTRAINT `training_to_trainer_ibfk_2` FOREIGN KEY (`training_id`) REFERENCES `training` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `training_to_trainer`
--

LOCK TABLES `training_to_trainer` WRITE;
/*!40000 ALTER TABLE `training_to_trainer` DISABLE KEYS */;
/*!40000 ALTER TABLE `training_to_trainer` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `training_to_training_funding_option`
--

DROP TABLE IF EXISTS `training_to_training_funding_option`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `training_to_training_funding_option` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `training_id` int(11) NOT NULL,
  `training_funding_option_id` int(11) NOT NULL,
  `funding_amount` float DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `timestamp_created` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_training_cat` (`training_funding_option_id`,`training_id`),
  KEY `training_id` (`training_id`),
  KEY `created_by` (`created_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `training_to_training_funding_option`
--

LOCK TABLES `training_to_training_funding_option` WRITE;
/*!40000 ALTER TABLE `training_to_training_funding_option` DISABLE KEYS */;
/*!40000 ALTER TABLE `training_to_training_funding_option` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `training_to_training_pepfar_categories_option`
--

DROP TABLE IF EXISTS `training_to_training_pepfar_categories_option`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `training_to_training_pepfar_categories_option` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `training_id` int(11) NOT NULL,
  `training_pepfar_categories_option_id` int(11) NOT NULL,
  `training_method_option_id` int(11) DEFAULT NULL,
  `duration_days` float NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  `timestamp_created` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_training_cat` (`training_pepfar_categories_option_id`,`training_id`),
  KEY `training_id` (`training_id`),
  KEY `created_by` (`created_by`),
  CONSTRAINT `training_to_training_pepfar_categories_option_ibfk_1` FOREIGN KEY (`training_id`) REFERENCES `training` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `training_to_training_pepfar_categories_option`
--

LOCK TABLES `training_to_training_pepfar_categories_option` WRITE;
/*!40000 ALTER TABLE `training_to_training_pepfar_categories_option` DISABLE KEYS */;
/*!40000 ALTER TABLE `training_to_training_pepfar_categories_option` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `training_to_training_topic_option`
--

DROP TABLE IF EXISTS `training_to_training_topic_option`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `training_to_training_topic_option` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `training_id` int(11) NOT NULL,
  `training_topic_option_id` int(11) NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  `timestamp_created` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_training_cat` (`training_topic_option_id`,`training_id`),
  KEY `training_id` (`training_id`),
  KEY `created_by` (`created_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `training_to_training_topic_option`
--

LOCK TABLES `training_to_training_topic_option` WRITE;
/*!40000 ALTER TABLE `training_to_training_topic_option` DISABLE KEYS */;
/*!40000 ALTER TABLE `training_to_training_topic_option` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `training_topic_option`
--

DROP TABLE IF EXISTS `training_topic_option`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `training_topic_option` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `training_topic_phrase` varchar(128) NOT NULL DEFAULT '',
  `is_default` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `modified_by` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
  `timestamp_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `timestamp_created` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name_unique` (`training_topic_phrase`),
  KEY `created_by` (`created_by`),
  KEY `modified_by` (`modified_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `training_topic_option`
--

LOCK TABLES `training_topic_option` WRITE;
/*!40000 ALTER TABLE `training_topic_option` DISABLE KEYS */;
/*!40000 ALTER TABLE `training_topic_option` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `translation`
--

DROP TABLE IF EXISTS `translation`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `translation` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `key_phrase` varchar(128) NOT NULL DEFAULT '',
  `phrase` varchar(128) NOT NULL DEFAULT '',
  `modified_by` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
  `timestamp_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `timestamp_created` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `modified_by` (`modified_by`),
  KEY `created_by` (`created_by`),
  CONSTRAINT `translation_ibfk_1` FOREIGN KEY (`modified_by`) REFERENCES `user` (`id`),
  CONSTRAINT `translation_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=39 DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `translation`
--

LOCK TABLES `translation` WRITE;
/*!40000 ALTER TABLE `translation` DISABLE KEYS */;
INSERT INTO `translation` VALUES (0,'Application Name','TrainSMART',1,NULL,0,'2010-09-20 00:31:29','0000-00-00 00:00:00'),(1,'Country','Country',1,NULL,0,'2008-04-28 20:17:48','0000-00-00 00:00:00'),(2,'Region A (Province)','Region',1,NULL,0,'2010-09-20 00:31:29','0000-00-00 00:00:00'),(3,'Region B (Health District)','Province',1,NULL,0,'2010-09-20 00:31:29','0000-00-00 00:00:00'),(4,'City or Town','City',1,NULL,0,'2010-09-20 00:31:29','0000-00-00 00:00:00'),(5,'Training Name','__training_name__',1,NULL,0,'2010-09-20 00:30:37','0000-00-00 00:00:00'),(6,'Training Organizer','__training_organizer__',1,NULL,0,'2010-09-20 00:30:37','0000-00-00 00:00:00'),(7,'Training Level','__training_level__',1,NULL,0,'2010-09-20 00:30:37','0000-00-00 00:00:00'),(8,'Pre Test Score','__pre_test_score__',1,NULL,0,'2010-09-20 00:30:37','0000-00-00 00:00:00'),(9,'Post Test Score','__post_test_score__',1,NULL,0,'2010-09-20 00:30:37','0000-00-00 00:00:00'),(10,'Training Custom 1','__custom_field_1__',1,NULL,0,'2010-09-20 00:30:37','0000-00-00 00:00:00'),(11,'Training Custom 2','__custom_field_2__',1,NULL,0,'2010-09-20 00:30:37','0000-00-00 00:00:00'),(12,'National ID','__national_id__',1,NULL,0,'2009-12-15 01:06:20','0000-00-00 00:00:00'),(13,'People Custom 1','__custom_field_1__',1,NULL,0,'2009-12-15 01:06:20','0000-00-00 00:00:00'),(14,'People Custom 2','__custom_field_2__',1,NULL,0,'2009-12-15 01:06:20','0000-00-00 00:00:00'),(15,'Is Active','__is_active__',1,NULL,0,'2009-12-15 01:06:20','2008-04-28 20:41:05'),(16,'PEPFAR Category','__pepfar_category__',1,NULL,0,'2010-09-20 00:30:37','2008-04-28 20:42:56'),(17,'First Name','__first_name__',1,NULL,0,'2009-12-15 01:06:20','2008-12-03 18:12:29'),(18,'Middle Name','__middle_name__',1,NULL,0,'2009-12-15 01:06:20','2008-12-03 18:12:38'),(19,'Last Name','__last_name__',1,NULL,0,'2009-12-15 01:06:20','2008-12-03 18:12:46'),(20,'Training of Trainers','__training_of_trainers__',1,NULL,0,'2010-09-20 00:30:37','0000-00-00 00:00:00'),(21,'Course Objectives','__course_objectives__',1,NULL,0,'2010-09-20 00:30:37','0000-00-00 00:00:00'),(22,'Training Category','__training_category__',1,NULL,0,'2010-09-20 00:30:37','0000-00-00 00:00:00'),(23,'Training Topic','__training_topic__',1,NULL,0,'2010-09-20 00:30:37','0000-00-00 00:00:00'),(24,'GOT Curriculum','__national_curriculum__',1,NULL,0,'2010-09-20 00:30:37','0000-00-00 00:00:00'),(25,'GOT Comment','__nat_curriculum_comment__',1,NULL,0,'2010-09-20 00:30:37','0000-00-00 00:00:00'),(26,'Refresher Course','__refresher_course__',1,NULL,0,'2010-09-20 00:30:37','0000-00-00 00:00:00'),(27,'Comments','__comments__',1,NULL,0,'2010-09-20 00:30:37','0000-00-00 00:00:00'),(28,'File Number','__file_number__',1,NULL,0,'2009-12-15 01:06:20','0000-00-00 00:00:00'),(30,'Primary Language','__1st_language__',1,NULL,0,'2010-09-20 00:30:37','2009-11-19 03:36:42'),(31,'Secondary Language','__2nd_language__',1,NULL,0,'2010-09-20 00:30:37','2009-11-19 03:36:57'),(32,'Funding Amount','__funding_amt__',1,NULL,0,'2010-09-20 00:30:37','2009-11-19 03:37:19'),(33,'Training Method','__training_method__',1,NULL,0,'2010-09-20 00:30:37','2009-11-19 03:37:48'),(34,'Title','__title__',1,NULL,0,'2009-12-15 01:06:20','2009-11-20 20:59:19'),(35,'Suffix','__suffix__',1,NULL,0,'2009-12-15 01:06:20','2009-11-20 20:59:30'),(36,'Age','Age',NULL,NULL,0,'2009-11-20 20:59:57','2009-11-20 20:59:57'),(37,'Facility','__facility__',1,NULL,0,'2009-12-15 01:06:29','2009-11-20 22:24:55'),(38,'Region C (Local Region)','County',1,NULL,0,'2010-09-20 00:31:29','0000-00-00 00:00:00');
/*!40000 ALTER TABLE `translation` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(32) NOT NULL DEFAULT '',
  `password` varchar(41) NOT NULL DEFAULT '',
  `email` varchar(64) NOT NULL DEFAULT '',
  `first_name` varchar(32) NOT NULL DEFAULT '',
  `last_name` varchar(32) NOT NULL DEFAULT '',
  `person_id` int(11) DEFAULT NULL,
  `locale` varchar(255) DEFAULT '',
  `modified_by` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `is_blocked` tinyint(1) NOT NULL DEFAULT '0',
  `timestamp_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `timestamp_created` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `timestamp_last_login` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `username_idx` (`username`),
  UNIQUE KEY `email_idx` (`email`),
  KEY `modified_by` (`modified_by`),
  KEY `created_by` (`created_by`),
  CONSTRAINT `template_ibfk_1` FOREIGN KEY (`modified_by`) REFERENCES `user` (`id`),
  CONSTRAINT `template_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `user`
--

LOCK TABLES `user` WRITE;
/*!40000 ALTER TABLE `user` DISABLE KEYS */;
INSERT INTO `user` VALUES (0,'system','','','','',NULL,'',NULL,NULL,0,'2008-03-11 21:17:59','2008-03-11 21:17:59','0000-00-00 00:00:00'),(1,'admin','6a204bd89f3c8348afd5c77c717a097a','admin@example.net','Admin','Admin',NULL,'',1,NULL,0,'2010-10-22 19:16:37','2008-02-27 20:15:43','2010-10-22 19:16:37');
/*!40000 ALTER TABLE `user` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_to_acl`
--

DROP TABLE IF EXISTS `user_to_acl`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `user_to_acl` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `acl_id` enum('edit_course','view_course','edit_people','view_people','view_create_reports','edit_country_options','add_edit_users','training_organizer_option_all','training_title_option_all','approve_trainings') NOT NULL DEFAULT 'view_course',
  `user_id` int(11) NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  `timestamp_created` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `created_by` (`created_by`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `user_to_acl_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `user_to_acl`
--

LOCK TABLES `user_to_acl` WRITE;
/*!40000 ALTER TABLE `user_to_acl` DISABLE KEYS */;
INSERT INTO `user_to_acl` VALUES (1,'add_edit_users',1,NULL,'2008-04-28 20:03:31'),(3,'edit_course',1,1,'2008-04-28 20:16:19'),(4,'edit_people',1,1,'2008-04-28 20:16:19'),(5,'view_create_reports',1,1,'2008-04-28 20:16:19'),(6,'training_organizer_option_all',1,NULL,'2008-12-03 18:10:51'),(9,'approve_trainings',1,1,'2009-11-20 22:39:34'),(14,'training_title_option_all',1,1,'2009-12-08 20:11:00'),(15,'edit_country_options',1,1,'2009-12-08 20:11:00');
/*!40000 ALTER TABLE `user_to_acl` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_to_organizer_access`
--

DROP TABLE IF EXISTS `user_to_organizer_access`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `user_to_organizer_access` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `training_organizer_option_id` int(11) DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  `timestamp_created` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `created_by` (`created_by`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `user_to_organizer_access`
--

LOCK TABLES `user_to_organizer_access` WRITE;
/*!40000 ALTER TABLE `user_to_organizer_access` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_to_organizer_access` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2010-11-03 21:11:30

insert into `acl` ( `id`, `acl`) values ( 'admin_files', 'admin_files');

ALTER TABLE `user_to_acl` CHANGE COLUMN `acl_id` `acl_id` enum('edit_course','view_course','edit_people','view_people','view_create_reports','edit_country_options','add_edit_users','training_organizer_option_all','training_title_option_all','approve_trainings','admin_files') NOT NULL DEFAULT 'view_course';


-- Desktop
#evaluation
ALTER TABLE `evaluation` ADD COLUMN `uuid` char(36) AFTER `id`;
ALTER TABLE `evaluation` ADD UNIQUE `uuid_idx`(uuid);
ALTER TABLE `evaluation` CHANGE COLUMN `uuid` `uuid` char(36) DEFAULT NULL;


DELIMITER ;;
CREATE TRIGGER `evaluation_insert` BEFORE INSERT ON `evaluation` FOR EACH ROW BEGIN
SET NEW.`uuid` = IFNULL(NEW.`uuid`,UUID());
END;;
DELIMITER ;
UPDATE evaluation
SET uuid = UUID();
#evaluation_question
ALTER TABLE `evaluation_question` ADD COLUMN `uuid` char(36) AFTER `id`;
ALTER TABLE `evaluation_question` ADD UNIQUE `uuid_idx`(uuid);
ALTER TABLE `evaluation_question` CHANGE COLUMN `uuid` `uuid` char(36) DEFAULT NULL;

DELIMITER ;;
CREATE TRIGGER `evaluation_question_insert` BEFORE INSERT ON `evaluation_question` FOR EACH ROW BEGIN
SET NEW.`uuid` = IFNULL(NEW.`uuid`,UUID());
END;;
DELIMITER ;
UPDATE evaluation_question
SET uuid = UUID();
#evaluation_question_response
ALTER TABLE `evaluation_question_response` ADD COLUMN `uuid` char(36) AFTER `id`;
ALTER TABLE `evaluation_question_response` ADD UNIQUE `uuid_idx`(uuid);
ALTER TABLE `evaluation_question_response` CHANGE COLUMN `uuid` `uuid` char(36) DEFAULT NULL;

DELIMITER ;;
CREATE TRIGGER `evaluation_question_response_insert` BEFORE INSERT ON `evaluation_question_response` FOR EACH ROW BEGIN
SET NEW.`uuid` = IFNULL(NEW.`uuid`,UUID());
END;;
DELIMITER ;
UPDATE evaluation_question_response
SET uuid = UUID();
#evaluation_response
ALTER TABLE `evaluation_response` ADD COLUMN `uuid` char(36) AFTER `id`;
ALTER TABLE `evaluation_response` ADD UNIQUE `uuid_idx`(uuid);
ALTER TABLE `evaluation_response` CHANGE COLUMN `uuid` `uuid` char(36) DEFAULT NULL;

DELIMITER ;;
CREATE TRIGGER `evaluation_response_insert` BEFORE INSERT ON `evaluation_response` FOR EACH ROW BEGIN
SET NEW.`uuid` = IFNULL(NEW.`uuid`,UUID());
END;;
DELIMITER ;
UPDATE evaluation_response
SET uuid = UUID();
#evaluation_to_training
ALTER TABLE `evaluation_to_training` ADD COLUMN `uuid` char(36) AFTER `id`;
ALTER TABLE `evaluation_to_training` ADD UNIQUE `uuid_idx`(uuid);
ALTER TABLE `evaluation_to_training` CHANGE COLUMN `uuid` `uuid` char(36) DEFAULT NULL;

DELIMITER ;;
CREATE TRIGGER `evaluation_to_training_insert` BEFORE INSERT ON `evaluation_to_training` FOR EACH ROW BEGIN
SET NEW.`uuid` = IFNULL(NEW.`uuid`,UUID());
END;;
DELIMITER ;
UPDATE evaluation_to_training
SET uuid = UUID();
#external_course
ALTER TABLE `external_course` ADD COLUMN `uuid` char(36) AFTER `id`;
ALTER TABLE `external_course` ADD UNIQUE `uuid_idx`(uuid);
ALTER TABLE `external_course` CHANGE COLUMN `uuid` `uuid` char(36) DEFAULT NULL;

DELIMITER ;;
CREATE TRIGGER `external_course_insert` BEFORE INSERT ON `external_course` FOR EACH ROW BEGIN
SET NEW.`uuid` = IFNULL(NEW.`uuid`,UUID());
END;;
DELIMITER ;
UPDATE external_course
SET uuid = UUID();
#facility
ALTER TABLE `facility` DROP COLUMN `uuid`;
ALTER TABLE `facility` ADD COLUMN `uuid` char(36) AFTER `id`;
ALTER TABLE `facility` ADD UNIQUE `uuid_idx`(uuid);
ALTER TABLE `facility` CHANGE COLUMN `uuid` `uuid` char(36) DEFAULT NULL;

DELIMITER ;;
CREATE TRIGGER `facility_insert` BEFORE INSERT ON `facility` FOR EACH ROW BEGIN
SET NEW.`uuid` = IFNULL(NEW.`uuid`,UUID());
END;;
DELIMITER ;
UPDATE facility
SET uuid = UUID();
#facility_sponsor_option
ALTER TABLE `facility_sponsor_option` ADD COLUMN `uuid` char(36) AFTER `id`;
ALTER TABLE `facility_sponsor_option` ADD UNIQUE `uuid_idx`(uuid);
ALTER TABLE `facility_sponsor_option` CHANGE COLUMN `uuid` `uuid` char(36) DEFAULT NULL;

DELIMITER ;;
CREATE TRIGGER `facility_sponsor_option_insert` BEFORE INSERT ON `facility_sponsor_option` FOR EACH ROW BEGIN
SET NEW.`uuid` = IFNULL(NEW.`uuid`,UUID());
END;;
DELIMITER ;
UPDATE facility_sponsor_option
SET uuid = UUID();
#facility_type_option
ALTER TABLE `facility_type_option` ADD COLUMN `uuid` char(36) AFTER `id`;
ALTER TABLE `facility_type_option` ADD UNIQUE `uuid_idx`(uuid);
ALTER TABLE `facility_type_option` CHANGE COLUMN `uuid` `uuid` char(36) DEFAULT NULL;

DELIMITER ;;
CREATE TRIGGER `facility_type_option_insert` BEFORE INSERT ON `facility_type_option` FOR EACH ROW BEGIN
SET NEW.`uuid` = IFNULL(NEW.`uuid`,UUID());
END;;
DELIMITER ;
UPDATE facility_type_option
SET uuid = UUID();
#file
ALTER TABLE `file` ADD COLUMN `uuid` char(36) AFTER `id`;
ALTER TABLE `file` ADD UNIQUE `uuid_idx`(uuid);
ALTER TABLE `file` CHANGE COLUMN `uuid` `uuid` char(36) DEFAULT NULL;

DELIMITER ;;
CREATE TRIGGER `file_insert` BEFORE INSERT ON `file` FOR EACH ROW BEGIN
SET NEW.`uuid` = IFNULL(NEW.`uuid`,UUID());
END;;
DELIMITER ;
UPDATE file
SET uuid = UUID();
#location
ALTER TABLE `location` DROP COLUMN `uuid`;
ALTER TABLE `location` ADD COLUMN `uuid` char(36) AFTER `id`;
ALTER TABLE `location` ADD UNIQUE `uuid_idx`(uuid);
ALTER TABLE `location` CHANGE COLUMN `uuid` `uuid` char(36) DEFAULT NULL;

DELIMITER ;;
CREATE TRIGGER `location_insert` BEFORE INSERT ON `location` FOR EACH ROW BEGIN
SET NEW.`uuid` = IFNULL(NEW.`uuid`,UUID());
END;;
DELIMITER ;
UPDATE location
SET uuid = UUID();
#person
ALTER TABLE `person` DROP COLUMN `uuid`;
ALTER TABLE `person` ADD COLUMN `uuid` char(36) AFTER `id`;
ALTER TABLE `person` ADD UNIQUE `uuid_idx`(uuid);
ALTER TABLE `person` CHANGE COLUMN `uuid` `uuid` char(36) DEFAULT NULL;

DELIMITER ;;
CREATE TRIGGER `person_insert` BEFORE INSERT ON `person` FOR EACH ROW BEGIN
SET NEW.`uuid` = IFNULL(NEW.`uuid`,UUID());
END;;
DELIMITER ;
UPDATE person
SET uuid = UUID();
#person_active_trainer_option
ALTER TABLE `person_active_trainer_option` ADD COLUMN `uuid` char(36) AFTER `id`;
ALTER TABLE `person_active_trainer_option` ADD UNIQUE `uuid_idx`(uuid);
ALTER TABLE `person_active_trainer_option` CHANGE COLUMN `uuid` `uuid` char(36) DEFAULT NULL;

DELIMITER ;;
CREATE TRIGGER `person_active_trainer_option_insert` BEFORE INSERT ON `person_active_trainer_option` FOR EACH ROW BEGIN
SET NEW.`uuid` = IFNULL(NEW.`uuid`,UUID());
END;;
DELIMITER ;
UPDATE person_active_trainer_option
SET uuid = UUID();
#person_custom_1_option
ALTER TABLE `person_custom_1_option` ADD COLUMN `uuid` char(36) AFTER `id`;
ALTER TABLE `person_custom_1_option` ADD UNIQUE `uuid_idx`(uuid);
ALTER TABLE `person_custom_1_option` CHANGE COLUMN `uuid` `uuid` char(36) DEFAULT NULL;

DELIMITER ;;
CREATE TRIGGER `person_custom_1_option_insert` BEFORE INSERT ON `person_custom_1_option` FOR EACH ROW BEGIN
SET NEW.`uuid` = IFNULL(NEW.`uuid`,UUID());
END;;
DELIMITER ;
UPDATE person_custom_1_option
SET uuid = UUID();
#person_custom_2_option
ALTER TABLE `person_custom_2_option` ADD COLUMN `uuid` char(36) AFTER `id`;
ALTER TABLE `person_custom_2_option` ADD UNIQUE `uuid_idx`(uuid);
ALTER TABLE `person_custom_2_option` CHANGE COLUMN `uuid` `uuid` char(36) DEFAULT NULL;

DELIMITER ;;
CREATE TRIGGER `person_custom_2_option_insert` BEFORE INSERT ON `person_custom_2_option` FOR EACH ROW BEGIN
SET NEW.`uuid` = IFNULL(NEW.`uuid`,UUID());
END;;
DELIMITER ;
UPDATE person_custom_2_option
SET uuid = UUID();
#person_history
ALTER TABLE `person_history` DROP COLUMN `uuid`;
ALTER TABLE `person_history` ADD COLUMN `uuid` char(36) AFTER `person_id`;
ALTER TABLE `person_history` CHANGE COLUMN `uuid` `uuid` char(36) DEFAULT NULL;


#person_qualification_option
ALTER TABLE `person_qualification_option` ADD COLUMN `uuid` char(36) AFTER `id`;
ALTER TABLE `person_qualification_option` ADD UNIQUE `uuid_idx`(uuid);
ALTER TABLE `person_qualification_option` CHANGE COLUMN `uuid` `uuid` char(36) DEFAULT NULL;

DELIMITER ;;
CREATE TRIGGER `person_qualification_option_insert` BEFORE INSERT ON `person_qualification_option` FOR EACH ROW BEGIN
SET NEW.`uuid` = IFNULL(NEW.`uuid`,UUID());
END;;
DELIMITER ;
UPDATE person_qualification_option
SET uuid = UUID();
#person_responsibility_option
ALTER TABLE `person_responsibility_option` ADD COLUMN `uuid` char(36) AFTER `id`;
ALTER TABLE `person_responsibility_option` ADD UNIQUE `uuid_idx`(uuid);
ALTER TABLE `person_responsibility_option` CHANGE COLUMN `uuid` `uuid` char(36) DEFAULT NULL;

DELIMITER ;;
CREATE TRIGGER `person_responsibility_option_insert` BEFORE INSERT ON `person_responsibility_option` FOR EACH ROW BEGIN
SET NEW.`uuid` = IFNULL(NEW.`uuid`,UUID());
END;;
DELIMITER ;
UPDATE person_responsibility_option
SET uuid = UUID();
#person_suffix_option
ALTER TABLE `person_suffix_option` ADD COLUMN `uuid` char(36) AFTER `id`;
ALTER TABLE `person_suffix_option` ADD UNIQUE `uuid_idx`(uuid);
ALTER TABLE `person_suffix_option` CHANGE COLUMN `uuid` `uuid` char(36) DEFAULT NULL;

DELIMITER ;;
CREATE TRIGGER `person_suffix_option_insert` BEFORE INSERT ON `person_suffix_option` FOR EACH ROW BEGIN
SET NEW.`uuid` = IFNULL(NEW.`uuid`,UUID());
END;;
DELIMITER ;
UPDATE person_suffix_option
SET uuid = UUID();
#person_title_option
ALTER TABLE `person_title_option` ADD COLUMN `uuid` char(36) AFTER `id`;
ALTER TABLE `person_title_option` ADD UNIQUE `uuid_idx`(uuid);
ALTER TABLE `person_title_option` CHANGE COLUMN `uuid` `uuid` char(36) DEFAULT NULL;

DELIMITER ;;
CREATE TRIGGER `person_title_option_insert` BEFORE INSERT ON `person_title_option` FOR EACH ROW BEGIN
SET NEW.`uuid` = IFNULL(NEW.`uuid`,UUID());
END;;
DELIMITER ;
UPDATE person_title_option
SET uuid = UUID();
#person_to_training
ALTER TABLE `person_to_training` ADD COLUMN `uuid` char(36) AFTER `id`;
ALTER TABLE `person_to_training` ADD UNIQUE `uuid_idx`(uuid);
ALTER TABLE `person_to_training` CHANGE COLUMN `uuid` `uuid` char(36) DEFAULT NULL;

DELIMITER ;;
CREATE TRIGGER `person_to_training_insert` BEFORE INSERT ON `person_to_training` FOR EACH ROW BEGIN
SET NEW.`uuid` = IFNULL(NEW.`uuid`,UUID());
END;;
DELIMITER ;
UPDATE person_to_training
SET uuid = UUID();
#person_to_training_topic_option
ALTER TABLE `person_to_training_topic_option` ADD COLUMN `uuid` char(36) AFTER `id`;
ALTER TABLE `person_to_training_topic_option` ADD UNIQUE `uuid_idx`(uuid);
ALTER TABLE `person_to_training_topic_option` CHANGE COLUMN `uuid` `uuid` char(36) DEFAULT NULL;

DELIMITER ;;
CREATE TRIGGER `person_to_training_topic_option_insert` BEFORE INSERT ON `person_to_training_topic_option` FOR EACH ROW BEGIN
SET NEW.`uuid` = IFNULL(NEW.`uuid`,UUID());
END;;
DELIMITER ;
UPDATE person_to_training_topic_option
SET uuid = UUID();
#score
ALTER TABLE `score` ADD COLUMN `uuid` char(36) AFTER `id`;
ALTER TABLE `score` ADD UNIQUE `uuid_idx`(uuid);
ALTER TABLE `score` CHANGE COLUMN `uuid` `uuid` char(36) DEFAULT NULL;

DELIMITER ;;
CREATE TRIGGER `score_insert` BEFORE INSERT ON `score` FOR EACH ROW BEGIN
SET NEW.`uuid` = IFNULL(NEW.`uuid`,UUID());
END;;
DELIMITER ;
UPDATE score
SET uuid = UUID();
#trainer
ALTER TABLE `trainer` ADD COLUMN `uuid` char(36) AFTER `person_id`;
ALTER TABLE `trainer` ADD UNIQUE `uuid_idx`(uuid);
ALTER TABLE `trainer` CHANGE COLUMN `uuid` `uuid` char(36) DEFAULT NULL;

DELIMITER ;;
CREATE TRIGGER `trainer_insert` BEFORE INSERT ON `trainer` FOR EACH ROW BEGIN
SET NEW.`uuid` = IFNULL(NEW.`uuid`,UUID());
END;;
DELIMITER ;
UPDATE trainer
SET uuid = UUID();
#trainer_affiliation_option
ALTER TABLE `trainer_affiliation_option` ADD COLUMN `uuid` char(36) AFTER `id`;
ALTER TABLE `trainer_affiliation_option` ADD UNIQUE `uuid_idx`(uuid);
ALTER TABLE `trainer_affiliation_option` CHANGE COLUMN `uuid` `uuid` char(36) DEFAULT NULL;

DELIMITER ;;
CREATE TRIGGER `trainer_affiliation_option_insert` BEFORE INSERT ON `trainer_affiliation_option` FOR EACH ROW BEGIN
SET NEW.`uuid` = IFNULL(NEW.`uuid`,UUID());
END;;
DELIMITER ;
UPDATE trainer_affiliation_option
SET uuid = UUID();
#trainer_history
ALTER TABLE `trainer_history` ADD COLUMN `uuid` char(36) AFTER `person_id`;
ALTER TABLE `trainer_history` CHANGE COLUMN `uuid` `uuid` char(36) DEFAULT NULL;


#trainer_language_option
ALTER TABLE `trainer_language_option` ADD COLUMN `uuid` char(36) AFTER `id`;
ALTER TABLE `trainer_language_option` ADD UNIQUE `uuid_idx`(uuid);
ALTER TABLE `trainer_language_option` CHANGE COLUMN `uuid` `uuid` char(36) DEFAULT NULL;

DELIMITER ;;
CREATE TRIGGER `trainer_language_option_insert` BEFORE INSERT ON `trainer_language_option` FOR EACH ROW BEGIN
SET NEW.`uuid` = IFNULL(NEW.`uuid`,UUID());
END;;
DELIMITER ;
UPDATE trainer_language_option
SET uuid = UUID();
#trainer_skill_option
ALTER TABLE `trainer_skill_option` ADD COLUMN `uuid` char(36) AFTER `id`;
ALTER TABLE `trainer_skill_option` ADD UNIQUE `uuid_idx`(uuid);
ALTER TABLE `trainer_skill_option` CHANGE COLUMN `uuid` `uuid` char(36) DEFAULT NULL;

DELIMITER ;;
CREATE TRIGGER `trainer_skill_option_insert` BEFORE INSERT ON `trainer_skill_option` FOR EACH ROW BEGIN
SET NEW.`uuid` = IFNULL(NEW.`uuid`,UUID());
END;;
DELIMITER ;
UPDATE trainer_skill_option
SET uuid = UUID();
#trainer_to_trainer_language_option
ALTER TABLE `trainer_to_trainer_language_option` ADD COLUMN `uuid` char(36) AFTER `id`;
ALTER TABLE `trainer_to_trainer_language_option` ADD UNIQUE `uuid_idx`(uuid);
ALTER TABLE `trainer_to_trainer_language_option` CHANGE COLUMN `uuid` `uuid` char(36) DEFAULT NULL;

DELIMITER ;;
CREATE TRIGGER `trainer_to_trainer_language_option_insert` BEFORE INSERT ON `trainer_to_trainer_language_option` FOR EACH ROW BEGIN
SET NEW.`uuid` = IFNULL(NEW.`uuid`,UUID());
END;;
DELIMITER ;
UPDATE trainer_to_trainer_language_option
SET uuid = UUID();
#trainer_to_trainer_skill_option
ALTER TABLE `trainer_to_trainer_skill_option` ADD COLUMN `uuid` char(36) AFTER `id`;
ALTER TABLE `trainer_to_trainer_skill_option` ADD UNIQUE `uuid_idx`(uuid);
ALTER TABLE `trainer_to_trainer_skill_option` CHANGE COLUMN `uuid` `uuid` char(36) DEFAULT NULL;

DELIMITER ;;
CREATE TRIGGER `trainer_to_trainer_skill_option_insert` BEFORE INSERT ON `trainer_to_trainer_skill_option` FOR EACH ROW BEGIN
SET NEW.`uuid` = IFNULL(NEW.`uuid`,UUID());
END;;
DELIMITER ;
UPDATE trainer_to_trainer_skill_option
SET uuid = UUID();
#trainer_type_option
ALTER TABLE `trainer_type_option` ADD COLUMN `uuid` char(36) AFTER `id`;
ALTER TABLE `trainer_type_option` ADD UNIQUE `uuid_idx`(uuid);
ALTER TABLE `trainer_type_option` CHANGE COLUMN `uuid` `uuid` char(36) DEFAULT NULL;

DELIMITER ;;
CREATE TRIGGER `trainer_type_option_insert` BEFORE INSERT ON `trainer_type_option` FOR EACH ROW BEGIN
SET NEW.`uuid` = IFNULL(NEW.`uuid`,UUID());
END;;
DELIMITER ;
UPDATE trainer_type_option
SET uuid = UUID();
#training
ALTER TABLE `training` DROP COLUMN `uuid`;
ALTER TABLE `training` ADD COLUMN `uuid` char(36) AFTER `id`;
ALTER TABLE `training` ADD UNIQUE `uuid_idx`(uuid);
ALTER TABLE `training` CHANGE COLUMN `uuid` `uuid` char(36) DEFAULT NULL;

DELIMITER ;;
CREATE TRIGGER `training_insert` BEFORE INSERT ON `training` FOR EACH ROW BEGIN
SET NEW.`uuid` = IFNULL(NEW.`uuid`,UUID());
END;;
DELIMITER ;
UPDATE training
SET uuid = UUID();
#training_approval_history
ALTER TABLE `training_approval_history` ADD COLUMN `uuid` char(36) AFTER `id`;
ALTER TABLE `training_approval_history` ADD UNIQUE `uuid_idx`(uuid);
ALTER TABLE `training_approval_history` CHANGE COLUMN `uuid` `uuid` char(36) DEFAULT NULL;

DELIMITER ;;
CREATE TRIGGER `training_approval_history_insert` BEFORE INSERT ON `training_approval_history` FOR EACH ROW BEGIN
SET NEW.`uuid` = IFNULL(NEW.`uuid`,UUID());
END;;
DELIMITER ;
UPDATE training_approval_history
SET uuid = UUID();
#training_category_option
ALTER TABLE `training_category_option` ADD COLUMN `uuid` char(36) AFTER `id`;
ALTER TABLE `training_category_option` ADD UNIQUE `uuid_idx`(uuid);
ALTER TABLE `training_category_option` CHANGE COLUMN `uuid` `uuid` char(36) DEFAULT NULL;

DELIMITER ;;
CREATE TRIGGER `training_category_option_insert` BEFORE INSERT ON `training_category_option` FOR EACH ROW BEGIN
SET NEW.`uuid` = IFNULL(NEW.`uuid`,UUID());
END;;
DELIMITER ;
UPDATE training_category_option
SET uuid = UUID();
#training_category_option_to_training_title_option
ALTER TABLE `training_category_option_to_training_title_option` ADD COLUMN `uuid` char(36) AFTER `id`;
ALTER TABLE `training_category_option_to_training_title_option` ADD UNIQUE `uuid_idx`(uuid);
ALTER TABLE `training_category_option_to_training_title_option` CHANGE COLUMN `uuid` `uuid` char(36) DEFAULT NULL;

DELIMITER ;;
CREATE TRIGGER `training_category_option_to_training_title_option_insert` BEFORE INSERT ON `training_category_option_to_training_title_option` FOR EACH ROW BEGIN
SET NEW.`uuid` = IFNULL(NEW.`uuid`,UUID());
END;;
DELIMITER ;
UPDATE training_category_option_to_training_title_option
SET uuid = UUID();
#training_custom_1_option
ALTER TABLE `training_custom_1_option` ADD COLUMN `uuid` char(36) AFTER `id`;
ALTER TABLE `training_custom_1_option` ADD UNIQUE `uuid_idx`(uuid);
ALTER TABLE `training_custom_1_option` CHANGE COLUMN `uuid` `uuid` char(36) DEFAULT NULL;

DELIMITER ;;
CREATE TRIGGER `training_custom_1_option_insert` BEFORE INSERT ON `training_custom_1_option` FOR EACH ROW BEGIN
SET NEW.`uuid` = IFNULL(NEW.`uuid`,UUID());
END;;
DELIMITER ;
UPDATE training_custom_1_option
SET uuid = UUID();
#training_custom_2_option
ALTER TABLE `training_custom_2_option` ADD COLUMN `uuid` char(36) AFTER `id`;
ALTER TABLE `training_custom_2_option` ADD UNIQUE `uuid_idx`(uuid);
ALTER TABLE `training_custom_2_option` CHANGE COLUMN `uuid` `uuid` char(36) DEFAULT NULL;

DELIMITER ;;
CREATE TRIGGER `training_custom_2_option_insert` BEFORE INSERT ON `training_custom_2_option` FOR EACH ROW BEGIN
SET NEW.`uuid` = IFNULL(NEW.`uuid`,UUID());
END;;
DELIMITER ;
UPDATE training_custom_2_option
SET uuid = UUID();
#training_funding_option
ALTER TABLE `training_funding_option` ADD COLUMN `uuid` char(36) AFTER `id`;
ALTER TABLE `training_funding_option` ADD UNIQUE `uuid_idx`(uuid);
ALTER TABLE `training_funding_option` CHANGE COLUMN `uuid` `uuid` char(36) DEFAULT NULL;

DELIMITER ;;
CREATE TRIGGER `training_funding_option_insert` BEFORE INSERT ON `training_funding_option` FOR EACH ROW BEGIN
SET NEW.`uuid` = IFNULL(NEW.`uuid`,UUID());
END;;
DELIMITER ;
UPDATE training_funding_option
SET uuid = UUID();
#training_got_curriculum_option
ALTER TABLE `training_got_curriculum_option` ADD COLUMN `uuid` char(36) AFTER `id`;
ALTER TABLE `training_got_curriculum_option` ADD UNIQUE `uuid_idx`(uuid);
ALTER TABLE `training_got_curriculum_option` CHANGE COLUMN `uuid` `uuid` char(36) DEFAULT NULL;

DELIMITER ;;
CREATE TRIGGER `training_got_curriculum_option_insert` BEFORE INSERT ON `training_got_curriculum_option` FOR EACH ROW BEGIN
SET NEW.`uuid` = IFNULL(NEW.`uuid`,UUID());
END;;
DELIMITER ;
UPDATE training_got_curriculum_option
SET uuid = UUID();
#training_level_option
ALTER TABLE `training_level_option` ADD COLUMN `uuid` char(36) AFTER `id`;
ALTER TABLE `training_level_option` ADD UNIQUE `uuid_idx`(uuid);
ALTER TABLE `training_level_option` CHANGE COLUMN `uuid` `uuid` char(36) DEFAULT NULL;

DELIMITER ;;
CREATE TRIGGER `training_level_option_insert` BEFORE INSERT ON `training_level_option` FOR EACH ROW BEGIN
SET NEW.`uuid` = IFNULL(NEW.`uuid`,UUID());
END;;
DELIMITER ;
UPDATE training_level_option
SET uuid = UUID();
#training_location
ALTER TABLE `training_location` ADD COLUMN `uuid` char(36) AFTER `id`;
ALTER TABLE `training_location` ADD UNIQUE `uuid_idx`(uuid);
ALTER TABLE `training_location` CHANGE COLUMN `uuid` `uuid` char(36) DEFAULT NULL;

DELIMITER ;;
CREATE TRIGGER `training_location_insert` BEFORE INSERT ON `training_location` FOR EACH ROW BEGIN
SET NEW.`uuid` = IFNULL(NEW.`uuid`,UUID());
END;;
DELIMITER ;
UPDATE training_location
SET uuid = UUID();
#training_method_option
ALTER TABLE `training_method_option` ADD COLUMN `uuid` char(36) AFTER `id`;
ALTER TABLE `training_method_option` ADD UNIQUE `uuid_idx`(uuid);
ALTER TABLE `training_method_option` CHANGE COLUMN `uuid` `uuid` char(36) DEFAULT NULL;

DELIMITER ;;
CREATE TRIGGER `training_method_option_insert` BEFORE INSERT ON `training_method_option` FOR EACH ROW BEGIN
SET NEW.`uuid` = IFNULL(NEW.`uuid`,UUID());
END;;
DELIMITER ;
UPDATE training_method_option
SET uuid = UUID();
#training_organizer_option
ALTER TABLE `training_organizer_option` ADD COLUMN `uuid` char(36) AFTER `id`;
ALTER TABLE `training_organizer_option` ADD UNIQUE `uuid_idx`(uuid);
ALTER TABLE `training_organizer_option` CHANGE COLUMN `uuid` `uuid` char(36) DEFAULT NULL;

DELIMITER ;;
CREATE TRIGGER `training_organizer_option_insert` BEFORE INSERT ON `training_organizer_option` FOR EACH ROW BEGIN
SET NEW.`uuid` = IFNULL(NEW.`uuid`,UUID());
END;;
DELIMITER ;
UPDATE training_organizer_option
SET uuid = UUID();
#training_pepfar_categories_option
ALTER TABLE `training_pepfar_categories_option` ADD COLUMN `uuid` char(36) AFTER `id`;
ALTER TABLE `training_pepfar_categories_option` ADD UNIQUE `uuid_idx`(uuid);
ALTER TABLE `training_pepfar_categories_option` CHANGE COLUMN `uuid` `uuid` char(36) DEFAULT NULL;

DELIMITER ;;
CREATE TRIGGER `training_pepfar_categories_option_insert` BEFORE INSERT ON `training_pepfar_categories_option` FOR EACH ROW BEGIN
SET NEW.`uuid` = IFNULL(NEW.`uuid`,UUID());
END;;
DELIMITER ;
UPDATE training_pepfar_categories_option
SET uuid = UUID();
#training_recommend
ALTER TABLE `training_recommend` ADD COLUMN `uuid` char(36) AFTER `id`;
ALTER TABLE `training_recommend` ADD UNIQUE `uuid_idx`(uuid);
ALTER TABLE `training_recommend` CHANGE COLUMN `uuid` `uuid` char(36) DEFAULT NULL;

DELIMITER ;;
CREATE TRIGGER `training_recommend_insert` BEFORE INSERT ON `training_recommend` FOR EACH ROW BEGIN
SET NEW.`uuid` = IFNULL(NEW.`uuid`,UUID());
END;;
DELIMITER ;
UPDATE training_recommend
SET uuid = UUID();
#training_title_option
ALTER TABLE `training_title_option` ADD COLUMN `uuid` char(36) AFTER `id`;
ALTER TABLE `training_title_option` ADD UNIQUE `uuid_idx`(uuid);
ALTER TABLE `training_title_option` CHANGE COLUMN `uuid` `uuid` char(36) DEFAULT NULL;

DELIMITER ;;
CREATE TRIGGER `training_title_option_insert` BEFORE INSERT ON `training_title_option` FOR EACH ROW BEGIN
SET NEW.`uuid` = IFNULL(NEW.`uuid`,UUID());
END;;
DELIMITER ;
UPDATE training_title_option
SET uuid = UUID();
#training_to_person_qualification_option
ALTER TABLE `training_to_person_qualification_option` ADD COLUMN `uuid` char(36) AFTER `id`;
ALTER TABLE `training_to_person_qualification_option` ADD UNIQUE `uuid_idx`(uuid);
ALTER TABLE `training_to_person_qualification_option` CHANGE COLUMN `uuid` `uuid` char(36) DEFAULT NULL;

DELIMITER ;;
CREATE TRIGGER `training_to_person_qualification_option_insert` BEFORE INSERT ON `training_to_person_qualification_option` FOR EACH ROW BEGIN
SET NEW.`uuid` = IFNULL(NEW.`uuid`,UUID());
END;;
DELIMITER ;
UPDATE training_to_person_qualification_option
SET uuid = UUID();
#training_to_trainer
ALTER TABLE `training_to_trainer` ADD COLUMN `uuid` char(36) AFTER `id`;
ALTER TABLE `training_to_trainer` ADD UNIQUE `uuid_idx`(uuid);
ALTER TABLE `training_to_trainer` CHANGE COLUMN `uuid` `uuid` char(36) DEFAULT NULL;

DELIMITER ;;
CREATE TRIGGER `training_to_trainer_insert` BEFORE INSERT ON `training_to_trainer` FOR EACH ROW BEGIN
SET NEW.`uuid` = IFNULL(NEW.`uuid`,UUID());
END;;
DELIMITER ;
UPDATE training_to_trainer
SET uuid = UUID();
#training_to_training_funding_option
ALTER TABLE `training_to_training_funding_option` ADD COLUMN `uuid` char(36) AFTER `id`;
ALTER TABLE `training_to_training_funding_option` ADD UNIQUE `uuid_idx`(uuid);
ALTER TABLE `training_to_training_funding_option` CHANGE COLUMN `uuid` `uuid` char(36) DEFAULT NULL;

DELIMITER ;;
CREATE TRIGGER `training_to_training_funding_option_insert` BEFORE INSERT ON `training_to_training_funding_option` FOR EACH ROW BEGIN
SET NEW.`uuid` = IFNULL(NEW.`uuid`,UUID());
END;;
DELIMITER ;
UPDATE training_to_training_funding_option
SET uuid = UUID();
#training_to_training_pepfar_categories_option
ALTER TABLE `training_to_training_pepfar_categories_option` ADD COLUMN `uuid` char(36) AFTER `id`;
ALTER TABLE `training_to_training_pepfar_categories_option` ADD UNIQUE `uuid_idx`(uuid);
ALTER TABLE `training_to_training_pepfar_categories_option` CHANGE COLUMN `uuid` `uuid` char(36) DEFAULT NULL;

DELIMITER ;;
CREATE TRIGGER `training_to_training_pepfar_categories_option_insert` BEFORE INSERT ON `training_to_training_pepfar_categories_option` FOR EACH ROW BEGIN
SET NEW.`uuid` = IFNULL(NEW.`uuid`,UUID());
END;;
DELIMITER ;
UPDATE training_to_training_pepfar_categories_option
SET uuid = UUID();
#training_to_training_topic_option
ALTER TABLE `training_to_training_topic_option` ADD COLUMN `uuid` char(36) AFTER `id`;
ALTER TABLE `training_to_training_topic_option` ADD UNIQUE `uuid_idx`(uuid);
ALTER TABLE `training_to_training_topic_option` CHANGE COLUMN `uuid` `uuid` char(36) DEFAULT NULL;

DELIMITER ;;
CREATE TRIGGER `training_to_training_topic_option_insert` BEFORE INSERT ON `training_to_training_topic_option` FOR EACH ROW BEGIN
SET NEW.`uuid` = IFNULL(NEW.`uuid`,UUID());
END;;
DELIMITER ;
UPDATE training_to_training_topic_option
SET uuid = UUID();
#training_topic_option
ALTER TABLE `training_topic_option` ADD COLUMN `uuid` char(36) AFTER `id`;
ALTER TABLE `training_topic_option` ADD UNIQUE `uuid_idx`(uuid);
ALTER TABLE `training_topic_option` CHANGE COLUMN `uuid` `uuid` char(36) DEFAULT NULL;

DELIMITER ;;
CREATE TRIGGER `training_topic_option_insert` BEFORE INSERT ON `training_topic_option` FOR EACH ROW BEGIN
SET NEW.`uuid` = IFNULL(NEW.`uuid`,UUID());
END;;
DELIMITER ;
UPDATE training_topic_option
SET uuid = UUID();
#translation
ALTER TABLE `translation` ADD COLUMN `uuid` char(36) AFTER `id`;
ALTER TABLE `translation` ADD UNIQUE `uuid_idx`(uuid);
ALTER TABLE `translation` CHANGE COLUMN `uuid` `uuid` char(36) DEFAULT NULL;

DELIMITER ;;
CREATE TRIGGER `translation_insert` BEFORE INSERT ON `translation` FOR EACH ROW BEGIN
SET NEW.`uuid` = IFNULL(NEW.`uuid`,UUID());
END;;
DELIMITER ;
UPDATE translation
SET uuid = UUID();
#user
ALTER TABLE `user` ADD COLUMN `uuid` char(36) AFTER `id`;
ALTER TABLE `user` ADD UNIQUE `uuid_idx`(uuid);
ALTER TABLE `user` CHANGE COLUMN `uuid` `uuid` char(36) DEFAULT NULL;

DELIMITER ;;
CREATE TRIGGER `user_insert` BEFORE INSERT ON `user` FOR EACH ROW BEGIN
SET NEW.`uuid` = IFNULL(NEW.`uuid`,UUID());
END;;
DELIMITER ;
UPDATE user
SET uuid = UUID();


CREATE TABLE `syncfile` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `filename` varchar(255) DEFAULT NULL,
  `filepath` varchar(255) NOT NULL,
  `application_id` char(36) NOT NULL,
  `application_version` float(255,2) NOT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `timestamp_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `timestamp_created` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `timestamp_last_sync` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `timestamp_completed` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



-- ----------------------------
--  Table structure for `synclog`
-- ----------------------------
CREATE TABLE `synclog` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `fid` int(11) DEFAULT NULL,
  `item_type` varchar(255) DEFAULT NULL,
  `left_id` int(11) DEFAULT NULL,
  `left_data` mediumtext,
  `right_id` int(11) DEFAULT NULL,
  `right_data` mediumtext,
  `action` varchar(36) DEFAULT NULL,
  `is_skipped` tinyint(1) unsigned DEFAULT '0',
  `message` mediumtext,
  `modified_by` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `timestamp_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `timestamp_created` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `timestamp_completed` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `syncalias` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `syncfile_id` int(11) unsigned DEFAULT NULL,
  `item_type` varchar(255) DEFAULT NULL,
  `left_id` int(11) unsigned NOT NULL,
  `left_uuid` char(36) NOT NULL DEFAULT '',
  `right_id` int(11) unsigned NOT NULL,
  `right_uuid` char(36) NOT NULL DEFAULT '',
  `created_by` int(11) DEFAULT NULL,
  `timestamp_created` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `syncfile_id` (`syncfile_id`),
  KEY `syncfile_id_2` (`syncfile_id`),
  KEY `syncfile_id_3` (`syncfile_id`),
  KEY `syncfile_id_4` (`syncfile_id`),
  CONSTRAINT `file_fk` FOREIGN KEY (`syncfile_id`) REFERENCES `syncfile` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


ALTER TABLE `person` CHANGE COLUMN `primary_responsibility_option_id` `primary_responsibility_option_id` int(11) DEFAULT '0', CHANGE COLUMN `secondary_responsibility_option_id` `secondary_responsibility_option_id` int(11) DEFAULT '0';

ALTER TABLE `person_history` CHANGE COLUMN `primary_responsibility_option_id` `primary_responsibility_option_id` int(11) DEFAULT '0', CHANGE COLUMN `secondary_responsibility_option_id` `secondary_responsibility_option_id` int(11) DEFAULT '0';

ALTER TABLE `facility` DROP INDEX `facility_name`, ADD UNIQUE `facility_name`(facility_name, location_id);

CREATE TABLE `age_range_option` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `age_range_phrase` varchar(128) NOT NULL DEFAULT '',
  `is_default` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `modified_by` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
  `timestamp_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `timestamp_created` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name_unique` (`age_range_phrase`),
  KEY `created_by` (`created_by`),
  KEY `modified_by` (`modified_by`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;

insert into `age_range_option` values('1',0x756e6b6e6f776e,'1',null,null,'0','2011-03-31 15:58:37','0000-00-00 00:00:00'),
 ('2',0x3c3130,'0',null,null,'0','2011-03-31 15:58:53','0000-00-00 00:00:00'),
 ('3',0x31302d3134,'0',null,null,'0','2011-03-31 15:58:59','0000-00-00 00:00:00'),
 ('4',0x31352d3139,'0',null,null,'0','2011-03-31 15:59:05','0000-00-00 00:00:00'),
 ('5',0x32302d3235,'0',null,null,'0','2011-03-31 15:59:11','0000-00-00 00:00:00'),
 ('6',0x32352b,'0',null,null,'0','2011-03-31 15:59:17','0000-00-00 00:00:00');
 
 ALTER TABLE `training_to_person_qualification_option` ADD COLUMN `age_range_option_id` int(11) UNSIGNED NOT NULL DEFAULT '0' AFTER `person_count_female`;


ALTER TABLE `training_to_person_qualification_option` DROP INDEX `training_qual_uniq`, ADD UNIQUE `training_qual_uniq`(training_id, person_qualification_option_id, age_range_option_id);


/* ======================================================== */
/* ======================================================== */
/* 10/07/2011 																							*/
/* ======================================================== */
/* ======================================================== */

# sync data models for some databases (just do for all)
delimiter |
create procedure sync_schema () begin
if not exists(select * from information_schema.columns where table_schema = database() and table_name = 'translation' and column_name = 'uuid') then
  alter table translation add column uuid char(36) default null after id;
	alter table translation add unique key uuid_idx (uuid);
end if;
end|
delimiter ;
call sync_schema();
drop procedure sync_schema;


# create new security acl for ability to download/upload application
alter table user_to_acl change column acl_id acl_id enum('edit_course','view_course','edit_people','view_people','view_create_reports','edit_country_options','add_edit_users','training_organizer_option_all','training_title_option_all','approve_trainings','admin_files','use_offline_app') NOT NULL DEFAULT 'view_course';
delete from acl where id = 'use_offline_app';
insert into acl (id, acl) values ('use_offline_app', 'use_offline_app');
alter table _system add column display_external_classes tinyint(1) NOT NULL default 0;
alter table _system add column display_responsibility_me tinyint(1) NOT NULL default 0;
alter table _system add column display_highest_ed_level tinyint(1) NOT NULL default 0;
alter table _system add column display_attend_reason tinyint(1) NOT NULL default 0;
alter table _system add column display_primary_responsibility tinyint(1) NOT NULL default 0;
alter table _system add column display_secondary_responsibility tinyint(1) NOT NULL default 0;

CREATE TABLE  `person_education_level_option` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uuid` char(36) DEFAULT NULL,
  `education_level_phrase` varchar(128) NOT NULL DEFAULT '',
  `modified_by` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
  `timestamp_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `timestamp_created` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name_unique` (`education_level_phrase`),
  UNIQUE KEY `uuid_idx` (`uuid`),
  KEY `created_by` (`created_by`),
  KEY `modified_by` (`modified_by`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE  `person_attend_reason_option` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uuid` char(36) DEFAULT NULL,
  `attend_reason_phrase` varchar(128) NOT NULL DEFAULT '',
  `modified_by` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
  `timestamp_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `timestamp_created` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name_unique` (`attend_reason_phrase`),
  UNIQUE KEY `uuid_idx` (`uuid`),
  KEY `created_by` (`created_by`),
  KEY `modified_by` (`modified_by`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

delete from person_attend_reason_option where id = 0 or attend_reason_phrase = 'Other';
insert into person_attend_reason_option (uuid, attend_reason_phrase) values (uuid(), 'Other');
update person_attend_reason_option set id = 0 where lcase(attend_reason_phrase) like '%other%';


alter table person add column higest_edu_level_option_id int NULL;
alter table person add column attend_reason_option_id int NULL;
alter table person add column attend_reason_other varchar(255) NULL;
alter table person add column me_responsibility varchar(255) NULL;

delete from translation where key_phrase = 'M&E Responsibility' and phrase = 'M&E Responsibility';
delete from translation where key_phrase = 'Highest Edu Level' and phrase = 'Highest Edu Level';
delete from translation where key_phrase = 'Highest Education Level' and phrase = 'Highest Education Level';
delete from translation where key_phrase = 'Reason Attending' and phrase = 'Reason Attending';
delete from translation where key_phrase = 'Primary Responsibility' and phrase = 'Primary Responsibility';
delete from translation where key_phrase = 'Secondary Responsibility' and phrase = 'Secondary Responsibility';
insert into translation (uuid, key_phrase, phrase) values (uuid(), 'M&E Responsibility', 'M&E Responsibility');
insert into translation (uuid, key_phrase, phrase) values (uuid(), 'Highest Education Level', 'Highest Education Level');
insert into translation (uuid, key_phrase, phrase) values (uuid(), 'Reason Attending', 'Reason Attending');
insert into translation (uuid, key_phrase, phrase) values (uuid(), 'Primary Responsibility', 'Primary Responsibility');
insert into translation (uuid, key_phrase, phrase) values (uuid(), 'Secondary Responsibility', 'Secondary Responsibility');



/* ======================================================== */
/* ======================================================== */
/* 10/10/2011 																							*/
/* ======================================================== */
/* ======================================================== */

delimiter |
create procedure sync_schema () begin
if exists(select * from information_schema.columns where table_schema = database() and table_name = 'person' and column_name = 'highest_level_option_id') then
  alter table person change column highest_level_option_id highest_edu_level_option_id INT NULL;
end if;
end|
delimiter ;
call sync_schema();
drop procedure sync_schema;



/* ======================================================== */
/* ======================================================== */
/* 10/11/2011 																							*/
/* ======================================================== */
/* ======================================================== */

alter table person change column primary_responsibility_option_id primary_responsibility_option_id int null;
alter table person change column secondary_responsibility_option_id secondary_responsibility_option_id int null;


/* ======================================================== */
/* ======================================================== */
/* 10/17/2011 																							*/
/* ======================================================== */
/* ======================================================== */

# sync data models for some databases (just do for all)
DELIMITER |
CREATE PROCEDURE sync_schema () BEGIN
IF EXISTS(SELECT * FROM information_schema.columns WHERE table_schema = database() AND table_name = 'person_secondary_responsibility_option') THEN
  DROP TABLE person_secondary_responsibility_option;
END IF;
IF EXISTS(SELECT * FROM information_schema.columns WHERE table_schema = database() AND table_name = 'person_primary_responsibility_option') THEN
  RENAME TABLE person_primary_responsibility_option TO person_responsibility_option;
END IF;
IF NOT EXISTS(SELECT * FROM information_schema.columns WHERE table_schema = database() AND table_name = 'person_responsibility_option' AND column_name = 'uuid') THEN
  ALTER TABLE person_responsibility_option add column UUID CHAR(36) default null after id;
	ALTER TABLE person_responsibility_option add unique key uuid_idx (UUID);
END IF;
END|
DELIMITER ;
CALL sync_schema();
DROP PROCEDURE sync_schema;


RENAME TABLE person_responsibility_option TO person_primary_responsibility_option;

CREATE TABLE  `person_secondary_responsibility_option` (
  `id` int(11) NOT NULL,
  `uuid` char(36) DEFAULT NULL,
  `responsibility_phrase` varchar(128) NOT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
  `timestamp_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `timestamp_created` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name_unique` (`responsibility_phrase`),
  UNIQUE KEY `uuid_idx` (`uuid`),
  KEY `created_by` (`created_by`),
  KEY `modified_by` (`modified_by`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

TRUNCATE TABLE person_secondary_responsibility_option;

INSERT INTO person_secondary_responsibility_option (
  `id`,
  `uuid`,
  `responsibility_phrase`,
  `modified_by`,
  `created_by`,
  `is_deleted`,
  `timestamp_updated`,
  `timestamp_created`
)
SELECT 
  `id`,
  `uuid`,
  `responsibility_phrase`,
  `modified_by`,
  `created_by`,
  `is_deleted`,
  `timestamp_updated`,
  `timestamp_created`
FROM person_primary_responsibility_option
ORDER BY ID;

# Needed for stupid Mysql barf on primary key value of zero
SET SQL_MODE=NO_AUTO_VALUE_ON_ZERO;

ALTER TABLE person_secondary_responsibility_option CHANGE COLUMN `id` `id` int NOT NULL AUTO_INCREMENT;

    

/* ======================================================== */
/* ======================================================== */
/* 11/02/2011 																							*/
/* ======================================================== */
/* ======================================================== */

delimiter |
create procedure sync_schema () begin
if exists(select * from information_schema.columns where table_schema = database() and table_name = 'person' and column_name = 'higest_edu_level_option_id') then
  alter table person change column higest_edu_level_option_id highest_edu_level_option_id INT NULL;
end if;
end|
delimiter ;
call sync_schema();
drop procedure sync_schema;

