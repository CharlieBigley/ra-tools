# 4 files in total
# 30/11/22 created
# 14/06/23 CB added ra_groups/details; lat/lon to decimal(14,12)
# 16/07/23 CB aras/title default ''
# 02/08/23 remove area / title
# 09/10/23 add Areas / cluster
# 22/01/24 sdd table clusters
# 21/10/24 default lat/long to 0
# 10/11/24 CB added generated j4_ra_tables
# 21/12/24 CB added state to areas and groups, deleted walks
# 22/02/25 CB added data for Clusters
# 09/04/25 CB added ra_logfile
# 26/05/25 CB added ra_emails
# 07/07/25 CB added ra_apisites
# 23/01/26 CB changed clusters and email
#-------------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `#__ra_api_sites` (
    `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `sub_system` VARCHAR(10)  NOT NULL DEFAULT "RA Events",
    `title` VARCHAR(100)  NOT NULL ,
    `url` VARCHAR(100)  NOT NULL ,
    `token` VARCHAR(255)  NOT NULL ,
    `colour` VARCHAR(25)  NOT NULL ,
    `state` TINYINT(1)  NULL  DEFAULT 1,
    `ordering` INT NULL  DEFAULT 0,
    `checked_out` INT(11)  UNSIGNED,
    `checked_out_time` DATETIME NULL  DEFAULT NULL ,
    `created` DATETIME NULL  DEFAULT NULL ,
    `created_by` INT(11)  NULL  DEFAULT 0,
    `modified` DATETIME NULL  DEFAULT NULL ,
    `modified_by` INT(11)  NULL  DEFAULT 0,
PRIMARY KEY (`id`)
) DEFAULT COLLATE=utf8mb4_unicode_ci; 
# ------------------------------------------------------------------------------
INSERT INTO `#__ra_api_sites`
    (`sub_system`, `title`, `url`, `token`, `colour`, `state`, `created`, `created_by`) VALUES
    ('RA Tools','Staffordshire Area', 'https://staffordshireramblers.org', 'c2hhMjU2Ojk3OTo5ODQ4NGMzOTNhMGJmM2U5NWY3NzcyODViNTI2NzFkYzY2MmQwZTZmMzliMmNiMTlkNmUzNzI0MjNkNGUyOThk',
    'rgba(133,132,191,0.1)', 1, '2025-12-25 06:00:00', 1 );
INSERT INTO `#__ra_api_sites`
    (`sub_system`, `title`, `url`, `token`, `colour`, `state`, `created`, `created_by`) VALUES
    ('RA Walks', 'Central Office','https://ramblers.org.uk', '742d93e8f409bf2b5aec6f64cf6f405e',
    'rgba(133,132,191,0.1)', 1, '2025-12-25 06:00:00', 1);
# ------------------------------------------------------------------------------
-- Table structure for table `#__ra_areas`
--
CREATE TABLE IF NOT EXISTS `#__ra_areas` (
    `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
    `nation_id` int NOT NULL DEFAULT '1',
    `code` VARCHAR(2) NOT NULL,
    `name` VARCHAR(100) NOT NULL,
    `bespoke` VARCHAR(1) NOT NULL DEFAULT 0,
    `details` mediumtext NOT NULL,
    `website` VARCHAR(150) NOT NULL,
    `co_url` VARCHAR(150)  NOT NULL,
    `cluster` VARCHAR(3) NULL,
    `latitude` decimal(14,12) NOT NULL DEFAULT '0',
    `longitude` decimal(15,13) NOT NULL DEFAULT '0',
    `state` INT(11)  NULL  DEFAULT 0,
    `created` DATETIME NULL  DEFAULT NULL ,
    `created_by` INT(11)  NULL  DEFAULT 0,
    `modified` DATETIME NULL  DEFAULT NULL ,
    `modified_by` INT(11)  NULL  DEFAULT 0,
    `checked_out` INT(11)  UNSIGNED,
    `checked_out_time` DATETIME NULL  DEFAULT NULL ,
 PRIMARY KEY (`id`),
 UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;
# ------------------------------------------------------------------------------
DROP TABLE IF EXISTS `#__ra_clusters`;
CREATE TABLE  `#__ra_clusters` (
    `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
    `code` VARCHAR(3) NOT NULL,
    `name` VARCHAR(20) NOT NULL,
    `contact_id` INT NULL,
    `area_list` VARCHAR(255)  NULL,
    `website` VARCHAR(100)  NULL,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;
INSERT INTO `#__ra_clusters`(code, name,area_list) values 
    ('ME','Midlands and East','BF,LI,NP,NR,NE,SS,NS,WO,CH,DE'),
    ('N','North and North West','ER,HF,HW,LD,LE,LL,LN,MC,ML,MR,MW,NN,NS,NY,SD,SS,WK,WR'),
    ('SE','South East','BU,CB,ES,WX,KT,IL,IW,NO,OX,SK,SR,SX'),
    ('SSW','South and South West','AV,BK,CL,DN,DT,GR,IW,OX,SO,WE'),
    ('WA','Wales','CA,CE,SW,GG,LW,PE'),
    ('SC','Scotland','CY,CF,GP,SC,LB,SL,RB,WS');
# ------------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `#__ra_emails` (
    `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
    `sub_system` VARCHAR(10)  NULL  DEFAULT "",
    `record_type` VARCHAR(2)  NULL  DEFAULT "",
    `ref` INT  DEFAULT "0", 
    `date_sent` VARCHAR(20)  NULL  DEFAULT "",
    `sender_name` VARCHAR(100)  NULL  DEFAULT "",
    `sender_email` TEXT DEFAULT "",
    `addressee_name` VARCHAR(100)  NULL  DEFAULT "",
    `addressee_email` TEXT,
    `title` VARCHAR(100)  NOT NULL ,
    `body` TEXT NOT NULL ,
    `attachments` TEXT NULL ,
    `state` TINYINT(1)  NULL  DEFAULT 1,
    `created` DATETIME NULL  DEFAULT NULL ,
    `created_by` INT(11)  NULL  DEFAULT 0,
    `modified` DATETIME NULL  DEFAULT NULL ,
    `modified_by` INT(11)  NULL  DEFAULT 0,
    `checked_out` INT(11)  UNSIGNED,
    `checked_out_time` DATETIME NULL  DEFAULT NULL ,
PRIMARY KEY (`id`)
    ,KEY `idx_ref` (`ref`)
    ,KEY `idx_state` (`state`)
    ,KEY `idx_checked_out` (`checked_out`)
    ,KEY `idx_created_by` (`created_by`)
    ,KEY `idx_modified_by` (`modified_by`)
) DEFAULT COLLATE=utf8mb4_unicode_ci;
# ------------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `#__ra_groups` (
    `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
    `area_id` int NOT NULL DEFAULT '1',
    `code` VARCHAR(4) NOT NULL,
    `name` VARCHAR(100) NOT NULL,
    `bespoke` VARCHAR(1) NOT NULL DEFAULT 0,
    `details` mediumtext NOT NULL,
    `group_type` VARCHAR(1) NOT NULL DEFAULT 'G',
    `website` VARCHAR(150) NOT NULL,
    `co_url` VARCHAR(150) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
    `latitude` decimal(14,12) NOT NULL DEFAULT '0',
    `longitude` decimal(14,12) NOT NULL DEFAULT '0',
    `state` INT(11)  NULL  DEFAULT 0,`created` DATETIME NULL  DEFAULT NULL ,
    `created_by` INT(11)  NULL  DEFAULT 0,
    `modified` DATETIME NULL  DEFAULT NULL ,
    `modified_by` INT(11)  NULL  DEFAULT 0,
    `checked_out` INT(11)  UNSIGNED,
    `checked_out_time` DATETIME NULL  DEFAULT NULL ,
 PRIMARY KEY (`id`),
 UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;
# ------------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `#__ra_logfile` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `log_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `sub_system` char(10) NOT NULL, 
  `record_type` char(2) NOT NULL,
  `ref` varchar(10) DEFAULT NULL,
  `message` mediumtext CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;
# ------------------------------------------------------------------------------
DROP TABLE IF EXISTS `#__ra_nations`;
CREATE TABLE `#__ra_nations` (
  `id` int NOT NULL AUTO_INCREMENT,
  `code` VARCHAR(2) NOT NULL,
  `name` VARCHAR(100) NOT NULL,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;


INSERT INTO `#__ra_nations` ( `code`, `name`) VALUES
('EN', 'England'),
('SC', 'Scotland'),
('WA', 'Wales');


