-- --------------------------------------------------------

--
-- Table structure for table `{wp_prefix}psp_link_builder`
--

CREATE TABLE IF NOT EXISTS `{wp_prefix}psp_link_builder` (
	`id` INT(10) NOT NULL AUTO_INCREMENT,
	`hits` INT(10) NULL DEFAULT '0',
	`phrase` VARCHAR(100) NULL DEFAULT NULL,
	`url` VARCHAR(200) NULL DEFAULT NULL,
	`rel` ENUM('no','alternate','author','bookmark','help','license','next','nofollow','noreferrer','prefetch','prev','search','tag') NULL DEFAULT 'no',
	`title` VARCHAR(100) NULL DEFAULT NULL,
	`target` ENUM('no','_blank','_parent','_self','_top') NULL DEFAULT 'no',
	`post_id` INT(10) NULL DEFAULT '0',
	`created` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
	`publish` CHAR(1) NULL DEFAULT 'Y',
	`max_replacements` SMALLINT(2) NULL DEFAULT '1',
	`attr_title` TEXT NULL,
	PRIMARY KEY (`id`),
	UNIQUE INDEX `unique` (`phrase`, `url`),
	KEY `url` (`url`),
	KEY `publish` (`publish`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;

-- --------------------------------------------------------

--
-- Table structure for table `{wp_prefix}psp_link_redirect`
--

CREATE TABLE IF NOT EXISTS `{wp_prefix}psp_link_redirect` (
	`id` INT(10) NOT NULL AUTO_INCREMENT,
	`hits` INT(10) NULL DEFAULT '0',
	`url` VARCHAR(255) NULL DEFAULT NULL,
	`url_redirect` VARCHAR(150) NULL DEFAULT NULL,
	`created` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
	`redirect_type` VARCHAR(25) NULL DEFAULT '',
	`redirect_rule` VARCHAR(25) NULL DEFAULT 'custom_url',
	`target_status_code` VARCHAR(25) NULL DEFAULT '',
	`target_status_details` TEXT NULL,
	`group_id` INT(5) NULL DEFAULT '1',
	`post_id` INT(10) NULL DEFAULT '0',
	`publish` CHAR(1) NULL DEFAULT 'Y',
	PRIMARY KEY (`id`),
	UNIQUE INDEX `unique` (`url`),
	INDEX `url_redirect` (`url_redirect`),
	INDEX `redirect_type` (`redirect_type`),
	INDEX `redirect_rule` (`redirect_rule`),
	INDEX `target_status_code` (`target_status_code`),
	INDEX `group_id` (`group_id`),
	INDEX `post_id` (`post_id`),
	INDEX `publish` (`publish`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;

-- --------------------------------------------------------

--
-- Table structure for table `{wp_prefix}psp_monitor_404`
--

CREATE TABLE IF NOT EXISTS `{wp_prefix}psp_monitor_404` (
	`id` int(10) NOT NULL AUTO_INCREMENT,
	`hits` int(10) DEFAULT '1',
	`url` varchar(200) DEFAULT NULL,
	`referrers` text,
	`user_agents` text,
	`data` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
	PRIMARY KEY (`id`),
	UNIQUE KEY `unique` (`url`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;

-- --------------------------------------------------------

--
-- Table structure for table `{wp_prefix}psp_web_directories`
--

CREATE TABLE IF NOT EXISTS `{wp_prefix}psp_web_directories` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`directory_name` varchar(255) DEFAULT NULL,
	`submit_url` varchar(255) DEFAULT NULL,
	`pagerank` double DEFAULT NULL,
	`alexa` double DEFAULT NULL,
	`status` smallint(1) DEFAULT '0',
	PRIMARY KEY (`id`),
	UNIQUE KEY `submit_url` (`submit_url`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;

-- --------------------------------------------------------

--
-- Table structure for table `{wp_prefix}psp_serp_reporter`
--

CREATE TABLE IF NOT EXISTS `{wp_prefix}psp_serp_reporter` (
	`id` INT(10) NOT NULL AUTO_INCREMENT,
	`focus_keyword` VARCHAR(100) NULL DEFAULT NULL,
	`url` VARCHAR(200) NULL DEFAULT NULL,
	`search_engine` VARCHAR(30) NULL DEFAULT 'google.com',
	`position` INT(10) NULL DEFAULT '999',
	`position_prev` INT(10) NULL DEFAULT '999',
	`position_worst` INT(10) NULL DEFAULT '999',
	`position_best` INT(10) NULL DEFAULT '999',
	`post_id` INT(10) NULL DEFAULT '0',
	`visits` INT(10) NULL DEFAULT '0',
	`created` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
	`publish` char(1) DEFAULT 'Y',
	`last_check_status` VARCHAR(20) NULL DEFAULT NULL,
	`last_check_msg` TEXT NULL,
	`last_check_data` DATETIME NULL DEFAULT NULL,
	PRIMARY KEY (`id`),
	UNIQUE INDEX `unique` (`focus_keyword`, `url`, `search_engine`),
	KEY `search_engine` (`search_engine`),
	KEY `url` (`url`),
	KEY `position` (`position`),
	KEY `position_prev` (`position_prev`),
	KEY `post_id` (`post_id`),
	KEY `publish` (`publish`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;

-- --------------------------------------------------------

--
-- Table structure for table `{wp_prefix}psp_serp_reporter2rank`
--

CREATE TABLE IF NOT EXISTS `{wp_prefix}psp_serp_reporter2rank` (
	`id` INT(10) NOT NULL AUTO_INCREMENT,
	`report_id` INT(10) NULL DEFAULT '0',
	`report_day` DATE NULL DEFAULT NULL,
	`position` INT(10) NULL DEFAULT '0',
	`top100` TEXT NULL,
	PRIMARY KEY (`id`),
	UNIQUE INDEX `unique` (`report_id`, `report_day`),
	KEY `report_day` (`report_day`),
	KEY `position` (`position`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;

-- --------------------------------------------------------

--
-- Table structure for table `{wp_prefix}psp_post_planner_cron`
--

CREATE TABLE IF NOT EXISTS `{wp_prefix}psp_post_planner_cron` (
	`id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
	`id_post` BIGINT(20) NOT NULL,
	`post_to` TEXT NULL,
	`post_to-page_group` VARCHAR(255) NULL DEFAULT NULL,
	`post_privacy` VARCHAR(255) NULL DEFAULT NULL,
	`email_at_post` ENUM('off','on') NOT NULL DEFAULT 'off',
	`status` SMALLINT(1) NOT NULL DEFAULT '0',
	`response` TEXT NULL,
	`started_at` TIMESTAMP NULL DEFAULT NULL,
	`ended_at` TIMESTAMP NULL DEFAULT NULL,
	`run_date` DATETIME NULL DEFAULT NULL,
	`repeat_status` ENUM('off','on') NOT NULL DEFAULT 'off' COMMENT 'one-time | repeating',
	`repeat_interval` INT(11) NULL DEFAULT NULL COMMENT 'minutes',
	`timestamp` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	`attempts` SMALLINT(6) NOT NULL,
	`deleted` TINYINT(1) NOT NULL DEFAULT '0',
	PRIMARY KEY (`id`),
	UNIQUE INDEX `id_post` (`id_post`),
	KEY `status` (`status`),
	KEY `deleted` (`deleted`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;

-- --------------------------------------------------------

--
-- Table structure for table `{wp_prefix}psp_link_builder`
--

CREATE TABLE IF NOT EXISTS `{wp_prefix}psp_link_builder` (
	`id` INT(10) NOT NULL AUTO_INCREMENT,
	`hits` INT(10) NULL DEFAULT '0',
	`phrase` VARCHAR(100) NULL DEFAULT NULL,
	`url` VARCHAR(200) NULL DEFAULT NULL,
	`rel` ENUM('no','alternate','author','bookmark','help','license','next','nofollow','noreferrer','prefetch','prev','search','tag') NULL DEFAULT 'no',
	`title` VARCHAR(100) NULL DEFAULT NULL,
	`target` ENUM('no','_blank','_parent','_self','_top') NULL DEFAULT 'no',
	`post_id` INT(10) NULL DEFAULT '0',
	`created` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
	`publish` CHAR(1) NULL DEFAULT 'Y',
	`max_replacements` SMALLINT(2) NULL DEFAULT '1',
	`attr_title` TEXT NULL,
	PRIMARY KEY (`id`),
	UNIQUE INDEX `unique` (`phrase`, `url`),
	KEY `url` (`url`),
	KEY `publish` (`publish`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;

-- --------------------------------------------------------

--
-- Table structure for table `{wp_prefix}psp_alexa_rank`
--

CREATE TABLE `{wp_prefix}psp_alexa_rank` (
	`id` INT(10) NOT NULL AUTO_INCREMENT,
	`domain` VARCHAR(50) NOT NULL DEFAULT '0',
	`global_rank` INT(10) NOT NULL DEFAULT '0',
	`rank_delta` VARCHAR(150) NOT NULL DEFAULT '0',
	`country_rank` INT(10) NOT NULL DEFAULT '0',
	`country_code` VARCHAR(4) NOT NULL DEFAULT '0',
	`country_name` VARCHAR(50) NOT NULL DEFAULT '0',
	`update_date` DATE NOT NULL,
	PRIMARY KEY (`id`),
	UNIQUE INDEX `update_date` (`update_date`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;
