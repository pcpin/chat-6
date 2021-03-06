/* DELETING PCPIN CHAT 5 TABLES */
DROP TABLE IF EXISTS `$$$DB_PREFIX$$$advertisement`; /* PCPIN6_QUERY_SEPARATOR */
DROP TABLE IF EXISTS `$$$DB_PREFIX$$$badword`; /* PCPIN6_QUERY_SEPARATOR */
DROP TABLE IF EXISTS `$$$DB_PREFIX$$$ban`; /* PCPIN6_QUERY_SEPARATOR */
DROP TABLE IF EXISTS `$$$DB_PREFIX$$$configuration`; /* PCPIN6_QUERY_SEPARATOR */
DROP TABLE IF EXISTS `$$$DB_PREFIX$$$cssclass`; /* PCPIN6_QUERY_SEPARATOR */
DROP TABLE IF EXISTS `$$$DB_PREFIX$$$cssproperty`; /* PCPIN6_QUERY_SEPARATOR */
DROP TABLE IF EXISTS `$$$DB_PREFIX$$$cssurl`; /* PCPIN6_QUERY_SEPARATOR */
DROP TABLE IF EXISTS `$$$DB_PREFIX$$$fk_advertisement`; /* PCPIN6_QUERY_SEPARATOR */
DROP TABLE IF EXISTS `$$$DB_PREFIX$$$fk_cssvalue`; /* PCPIN6_QUERY_SEPARATOR */
DROP TABLE IF EXISTS `$$$DB_PREFIX$$$globalmessage`; /* PCPIN6_QUERY_SEPARATOR */
DROP TABLE IF EXISTS `$$$DB_PREFIX$$$maxusers`; /* PCPIN6_QUERY_SEPARATOR */
DROP TABLE IF EXISTS `$$$DB_PREFIX$$$room`; /* PCPIN6_QUERY_SEPARATOR */
DROP TABLE IF EXISTS `$$$DB_PREFIX$$$roompass`; /* PCPIN6_QUERY_SEPARATOR */
DROP TABLE IF EXISTS `$$$DB_PREFIX$$$session`; /* PCPIN6_QUERY_SEPARATOR */
DROP TABLE IF EXISTS `$$$DB_PREFIX$$$smilie`; /* PCPIN6_QUERY_SEPARATOR */
DROP TABLE IF EXISTS `$$$DB_PREFIX$$$systemmessage`; /* PCPIN6_QUERY_SEPARATOR */
DROP TABLE IF EXISTS `$$$DB_PREFIX$$$user`; /* PCPIN6_QUERY_SEPARATOR */
DROP TABLE IF EXISTS `$$$DB_PREFIX$$$usermessage`; /* PCPIN6_QUERY_SEPARATOR */
DROP TABLE IF EXISTS `$$$DB_PREFIX$$$version`; /* PCPIN6_QUERY_SEPARATOR */


/* NEW PCPIN CHAT 6 TABLES */

DROP TABLE IF EXISTS `$$$DB_PREFIX$$$attachment`; /* PCPIN6_QUERY_SEPARATOR */
CREATE TABLE `$$$DB_PREFIX$$$attachment` (
  `id` int(11) NOT NULL auto_increment,
  `message_id` int(11) NOT NULL default '0',
  `binaryfile_id` int(11) NOT NULL default '0',
  `filename` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`),
  KEY `message_id` (`message_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci PACK_KEYS=0; /* PCPIN6_QUERY_SEPARATOR */

DROP TABLE IF EXISTS `$$$DB_PREFIX$$$avatar`; /* PCPIN6_QUERY_SEPARATOR */
CREATE TABLE `$$$DB_PREFIX$$$avatar` (
  `id` int(11) NOT NULL auto_increment,
  `user_id` int(11) NOT NULL default '0',
  `primary` enum('y','n') NOT NULL default 'y',
  `binaryfile_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `user_id` (`user_id`,`primary`),
  KEY `binaryfile_id` (`binaryfile_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci PACK_KEYS=0; /* PCPIN6_QUERY_SEPARATOR */

DROP TABLE IF EXISTS `$$$DB_PREFIX$$$badword`; /* PCPIN6_QUERY_SEPARATOR */
CREATE TABLE `$$$DB_PREFIX$$$badword` (
  `id` int(11) NOT NULL auto_increment,
  `word` varchar(255) NOT NULL default '',
  `replacement` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `word` (`word`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci PACK_KEYS=0; /* PCPIN6_QUERY_SEPARATOR */

DROP TABLE IF EXISTS `$$$DB_PREFIX$$$banner`; /* PCPIN6_QUERY_SEPARATOR */
CREATE TABLE `$$$DB_PREFIX$$$banner` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL default '',
  `active` enum('n','y') NOT NULL default 'n',
  `source_type` enum('u','c') NOT NULL default 'c',
  `source` longblob NOT NULL,
  `display_position` enum('t','b','p','m') NOT NULL default 't',
  `views` int(11) NOT NULL default '0',
  `max_views` int(11) NOT NULL default '0',
  `start_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `expiration_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `width` int(11) NOT NULL default '0',
  `height` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `name` (`name`),
  KEY `active` (`active`),
  KEY `display_position` (`display_position`),
  KEY `date` (`start_date`,`expiration_date`),
  KEY `views` (`views`),
  KEY `max_views` (`max_views`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci PACK_KEYS=0; /* PCPIN6_QUERY_SEPARATOR */

DROP TABLE IF EXISTS `$$$DB_PREFIX$$$binaryfile`; /* PCPIN6_QUERY_SEPARATOR */
CREATE TABLE `$$$DB_PREFIX$$$binaryfile` (
  `id` int(11) NOT NULL auto_increment,
  `body` longblob NOT NULL,
  `size` int(11) NOT NULL default '0',
  `mime_type` varchar(255) NOT NULL default '',
  `last_modified` datetime NOT NULL default '0000-00-00 00:00:00',
  `width` int(11) NOT NULL default '0',
  `height` int(11) NOT NULL default '0',
  `protected` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`),
  KEY `protected` (`protected`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci PACK_KEYS=0; /* PCPIN6_QUERY_SEPARATOR */

DROP TABLE IF EXISTS `$$$DB_PREFIX$$$cache`; /* PCPIN6_QUERY_SEPARATOR */
CREATE TABLE `$$$DB_PREFIX$$$cache` (
  `id` char(255) NOT NULL default '',
  `contents` longblob NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci; /* PCPIN6_QUERY_SEPARATOR */

DROP TABLE IF EXISTS `$$$DB_PREFIX$$$category`; /* PCPIN6_QUERY_SEPARATOR */
CREATE TABLE `$$$DB_PREFIX$$$category` (
  `id` int(11) NOT NULL auto_increment,
  `parent_id` int(11) NOT NULL default '0',
  `name` varchar(255) NOT NULL default '',
  `description` longtext NOT NULL,
  `creatable_rooms` enum('n','r','g') NOT NULL default 'n',
  `listpos` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `name` (`name`),
  KEY `parent_id` (`parent_id`),
  KEY `listpos` (`listpos`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci PACK_KEYS=0; /* PCPIN6_QUERY_SEPARATOR */

DROP TABLE IF EXISTS `$$$DB_PREFIX$$$config`; /* PCPIN6_QUERY_SEPARATOR */
CREATE TABLE `$$$DB_PREFIX$$$config` (
  `_conf_id` int(11) NOT NULL auto_increment,
  `_conf_group` enum('server','security','account','chat','design','banners','slave') NOT NULL default 'chat',
  `_conf_subgroup` varchar(255) NOT NULL default '',
  `_conf_name` varchar(255) NOT NULL default '',
  `_conf_value` text NOT NULL,
  `_conf_type` varchar(255) NOT NULL default '',
  `_conf_choices` text NOT NULL,
  `_conf_description` longtext NOT NULL,
  PRIMARY KEY  (`_conf_id`),
  UNIQUE KEY `_conf_name` (`_conf_name`),
  KEY `_conf_group` (`_conf_group`,`_conf_subgroup`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci PACK_KEYS=0; /* PCPIN6_QUERY_SEPARATOR */

DROP TABLE IF EXISTS `$$$DB_PREFIX$$$disallowed_name`; /* PCPIN6_QUERY_SEPARATOR */
CREATE TABLE `$$$DB_PREFIX$$$disallowed_name` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci PACK_KEYS=0; /* PCPIN6_QUERY_SEPARATOR */

DROP TABLE IF EXISTS `$$$DB_PREFIX$$$failed_login`; /* PCPIN6_QUERY_SEPARATOR */
CREATE TABLE `$$$DB_PREFIX$$$failed_login` (
  `ip` varchar(15) NOT NULL,
  `count` int(11) NOT NULL default '0',
  PRIMARY KEY  (`ip`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci; /* PCPIN6_QUERY_SEPARATOR */

DROP TABLE IF EXISTS `$$$DB_PREFIX$$$invitation`; /* PCPIN6_QUERY_SEPARATOR */
CREATE TABLE `$$$DB_PREFIX$$$invitation` (
  `id` int(11) NOT NULL auto_increment,
  `author_id` int(11) NOT NULL default '0',
  `author_nickname` varchar(255) NOT NULL default '',
  `target_user_id` int(11) NOT NULL default '0',
  `room_id` int(11) NOT NULL default '0',
  `room_name` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `unique` (`author_id`,`target_user_id`,`room_id`),
  KEY `target_user_id` (`target_user_id`),
  KEY `room_id` (`room_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci PACK_KEYS=0; /* PCPIN6_QUERY_SEPARATOR */

DROP TABLE IF EXISTS `$$$DB_PREFIX$$$ipfilter`; /* PCPIN6_QUERY_SEPARATOR */
CREATE TABLE `$$$DB_PREFIX$$$ipfilter` (
  `id` int(11) NOT NULL auto_increment,
  `address` VARCHAR( 45 ) CHARACTER SET ascii COLLATE ascii_general_ci NOT NULL DEFAULT '',
  `added_on` datetime NOT NULL default '0000-00-00 00:00:00',
  `expires` datetime NOT NULL default '0000-00-00 00:00:00',
  `description` text NOT NULL,
  `action` enum('d','a') NOT NULL default 'd',
  `type` ENUM( 'IPv4', 'IPv6' ) CHARACTER SET ascii COLLATE ascii_bin DEFAULT 'IPv4' NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `address_2` (`address`,`action`),
  KEY `address` (`address`),
  KEY `added_on` (`added_on`),
  KEY `expires` (`expires`),
  KEY `action` (`action`),
  KEY `type` ( `type` )
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci PACK_KEYS=0; /* PCPIN6_QUERY_SEPARATOR */

DROP TABLE IF EXISTS `$$$DB_PREFIX$$$language`; /* PCPIN6_QUERY_SEPARATOR */
CREATE TABLE `$$$DB_PREFIX$$$language` (
  `id` int(11) NOT NULL auto_increment,
  `iso_name` char(2) NOT NULL default '',
  `name` varchar(255) NOT NULL default '',
  `local_name` varchar(255) NOT NULL default '',
  `active` enum('n','y') NOT NULL default 'n',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `iso_name` (`iso_name`),
  KEY `active` (`active`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci PACK_KEYS=0; /* PCPIN6_QUERY_SEPARATOR */

DROP TABLE IF EXISTS `$$$DB_PREFIX$$$language_expression`; /* PCPIN6_QUERY_SEPARATOR */
CREATE TABLE `$$$DB_PREFIX$$$language_expression` (
  `language_id` int(11) NOT NULL default '0',
  `code` varchar(255) NOT NULL default '',
  `value` longblob NOT NULL,
  `multi_row` enum('n','y') NOT NULL default 'n',
  KEY `language_id` (`language_id`),
  KEY `code` (`code`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci PACK_KEYS=0; /* PCPIN6_QUERY_SEPARATOR */

DROP TABLE IF EXISTS `$$$DB_PREFIX$$$message`; /* PCPIN6_QUERY_SEPARATOR */
CREATE TABLE `$$$DB_PREFIX$$$message` (
  `id` int(11) NOT NULL auto_increment,
  `type` int(11) NOT NULL default '0',
  `offline` enum('y','n') NOT NULL default 'n',
  `date` datetime NOT NULL default '0000-00-00 00:00:00',
  `author_id` int(11) NOT NULL default '0',
  `author_nickname` varchar(255) NOT NULL default '',
  `target_room_id` int(11) NOT NULL default '0',
  `target_user_id` int(11) NOT NULL default '0',
  `privacy` int(11) NOT NULL default '0',
  `body` text NOT NULL,
  `css_properties` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`),
  KEY `type` (`type`),
  KEY `offline` (`offline`),
  KEY `date` (`date`),
  KEY `author_id` (`author_id`),
  KEY `target_room_id` (`target_room_id`),
  KEY `target_user_id` (`target_user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci PACK_KEYS=0; /* PCPIN6_QUERY_SEPARATOR */

DROP TABLE IF EXISTS `$$$DB_PREFIX$$$message_log`; /* PCPIN6_QUERY_SEPARATOR */
CREATE TABLE `$$$DB_PREFIX$$$message_log` (
  `message_id` int(11) NOT NULL,
  `type` int(11) NOT NULL,
  `offline` enum('n','y') NOT NULL default 'n',
  `date` datetime NOT NULL,
  `category_id` int(11) NOT NULL,
  `category_name` varchar(255) NOT NULL default '',
  `room_id` int(11) NOT NULL,
  `room_name` varchar(255) NOT NULL default '',
  `target_category_id` int(11) NOT NULL,
  `target_category_name` varchar(255) NOT NULL default '',
  `target_room_id` int(11) NOT NULL,
  `target_room_name` varchar(255) NOT NULL default '',
  `author_id` int(11) NOT NULL,
  `author_ip` varchar(15) NOT NULL default '',
  `author_nickname` varchar(255) NOT NULL default '',
  `target_user_id` int(11) NOT NULL,
  `target_user_nickname` varchar(255) NOT NULL default '',
  `privacy` int(11) NOT NULL,
  `body` text NOT NULL,
  `css_properties` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`message_id`),
  KEY `type` (`type`),
  KEY `date` (`date`),
  KEY `category_id` (`category_id`,`category_name`),
  KEY `room_id` (`room_id`,`room_name`),
  KEY `target_category_id` (`target_category_id`,`target_category_name`),
  KEY `target_room_id` (`target_room_id`,`target_room_name`),
  KEY `author_id` (`author_id`,`author_nickname`),
  KEY `target_user_id` (`target_user_id`,`target_user_nickname`),
  KEY `author_ip` (`author_ip`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci PACK_KEYS=0; /* PCPIN6_QUERY_SEPARATOR */

DROP TABLE IF EXISTS `$$$DB_PREFIX$$$message_log_attachment`; /* PCPIN6_QUERY_SEPARATOR */
CREATE TABLE `$$$DB_PREFIX$$$message_log_attachment` (
  `message_id` int(11) NOT NULL,
  `filename` varchar(255) NOT NULL default '',
  `body` longblob NOT NULL,
  `size` int(11) NOT NULL default '0',
  `mime_type` varchar(255) NOT NULL default '',
  KEY `message_id` (`message_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci PACK_KEYS=0; /* PCPIN6_QUERY_SEPARATOR */

DROP TABLE IF EXISTS `$$$DB_PREFIX$$$nickname`; /* PCPIN6_QUERY_SEPARATOR */
CREATE TABLE `$$$DB_PREFIX$$$nickname` (
  `id` int(11) NOT NULL auto_increment,
  `user_id` int(11) NOT NULL default '0',
  `nickname` varchar(255) NOT NULL default '',
  `nickname_plain` varchar(255) NOT NULL default '',
  `default` enum('y','n') NOT NULL default 'n',
  PRIMARY KEY  (`id`),
  KEY `user_id` (`user_id`),
  KEY `nickname` (`nickname`),
  KEY `nickname_plain` (`nickname_plain`),
  KEY `default` (`default`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci PACK_KEYS=0; /* PCPIN6_QUERY_SEPARATOR */

DROP TABLE IF EXISTS `$$$DB_PREFIX$$$room`; /* PCPIN6_QUERY_SEPARATOR */
CREATE TABLE `$$$DB_PREFIX$$$room` (
  `id` int(11) NOT NULL auto_increment,
  `type` enum('p','u') NOT NULL default 'p',
  `date_created` datetime NOT NULL default '0000-00-00 00:00:00',
  `name` varchar(255) NOT NULL default '',
  `category_id` int(11) NOT NULL default '0',
  `description` varchar(255) NOT NULL default '',
  `users_count` int(11) NOT NULL default '0',
  `default_message_color` char(6) NOT NULL default '',
  `password` char(32) NOT NULL default '',
  `background_image` int(11) NOT NULL default '0',
  `last_ping` datetime NOT NULL default '0000-00-00 00:00:00',
  `listpos` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `type` (`type`),
  KEY `users_count` (`users_count`),
  KEY `last_ping` (`last_ping`),
  KEY `listpos` (`listpos`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci PACK_KEYS=0; /* PCPIN6_QUERY_SEPARATOR */

DROP TABLE IF EXISTS `$$$DB_PREFIX$$$session`; /* PCPIN6_QUERY_SEPARATOR */
CREATE TABLE `$$$DB_PREFIX$$$session` (
  `_s_id` char(32) NOT NULL default '',
  `_s_ip` VARCHAR( 45 ) CHARACTER SET ascii COLLATE ascii_general_ci NOT NULL DEFAULT '',
  `_s_created` datetime NOT NULL default '0000-00-00 00:00:00',
  `_s_last_ping` datetime NOT NULL default '0000-00-00 00:00:00',
  `_s_language_id` int(11) NOT NULL default '0',
  `_s_user_id` int(11) NOT NULL default '0',
  `_s_security_code` varchar(255) NOT NULL default '',
  `_s_security_code_img` blob NOT NULL,
  `_s_client_agent_name` varchar(255) NOT NULL default '',
  `_s_client_agent_version` varchar(255) NOT NULL default '',
  `_s_client_os` varchar(255) NOT NULL default '',
  `_s_room_id` int(11) NOT NULL default '0',
  `_s_room_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `_s_last_message_id` int(11) NOT NULL default '0',
  `_s_last_sent_message_time` datetime NOT NULL default '0000-00-00 00:00:00',
  `_s_last_sent_message_hash` char(32) NOT NULL default '',
  `_s_last_sent_message_repeats_count` int(10) unsigned NOT NULL default '0',
  `_s_kicked` enum('y','n') NOT NULL default 'n',
  `_s_online_status` int(11) NOT NULL default '0',
  `_s_online_status_message` varchar(255) NOT NULL default '',
  `_s_stealth_mode` enum('y','n') NOT NULL default 'n',
  `_s_backend` enum('n','y') NOT NULL default 'n',
  `_s_page_unloaded` enum('n','y') NOT NULL default 'n',
  PRIMARY KEY  (`_s_id`),
  KEY `_s_user_id` (`_s_user_id`),
  KEY `_s_last_ping` (`_s_last_ping`),
  KEY `_s_room_id` (`_s_room_id`),
  KEY `_s_room_date` (`_s_room_date`),
  KEY `_s_last_message_id` (`_s_last_message_id`),
  KEY `_s_kicked` (`_s_kicked`),
  KEY `_s_online_status` (`_s_online_status`),
  KEY `_s_page_unloaded` (`_s_page_unloaded`),
  KEY `_s_backend` (`_s_backend`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci PACK_KEYS=0; /* PCPIN6_QUERY_SEPARATOR */

DROP TABLE IF EXISTS `$$$DB_PREFIX$$$smilie`; /* PCPIN6_QUERY_SEPARATOR */
CREATE TABLE `$$$DB_PREFIX$$$smilie` (
  `id` int(11) NOT NULL auto_increment,
  `binaryfile_id` int(11) NOT NULL default '0',
  `code` varchar(32) NOT NULL default '',
  `description` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`),
  KEY `text` (`code`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci PACK_KEYS=0; /* PCPIN6_QUERY_SEPARATOR */

DROP TABLE IF EXISTS `$$$DB_PREFIX$$$tmpdata`; /* PCPIN6_QUERY_SEPARATOR */
CREATE TABLE `$$$DB_PREFIX$$$tmpdata` (
  `id` int(11) NOT NULL auto_increment,
  `type` int(11) NOT NULL default '0',
  `user_id` int(11) NOT NULL default '0',
  `binaryfile_id` int(11) NOT NULL default '0',
  `filename` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`),
  KEY `type` (`type`),
  KEY `user_id` (`user_id`),
  KEY `binaryfile_id` (`binaryfile_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci PACK_KEYS=0; /* PCPIN6_QUERY_SEPARATOR */

DROP TABLE IF EXISTS `$$$DB_PREFIX$$$user`; /* PCPIN6_QUERY_SEPARATOR */
CREATE TABLE `$$$DB_PREFIX$$$user` (
  `id` int(11) NOT NULL auto_increment,
  `login` varchar(30) NOT NULL default '',
  `password` char(32) NOT NULL default '',
  `password_new` char(32) NOT NULL default '',
  `email` varchar(255) NOT NULL default '',
  `email_new` varchar(255) NOT NULL default '',
  `email_new_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `email_new_activation_code` varchar(32) NOT NULL default '',
  `hide_email` int(11) NOT NULL default '0',
  `previous_login` datetime NOT NULL default '0000-00-00 00:00:00',
  `last_login` datetime NOT NULL default '0000-00-00 00:00:00',
  `joined` datetime NOT NULL default '0000-00-00 00:00:00',
  `activated` enum('n','y') NOT NULL default 'n',
  `activation_code` varchar(255) NOT NULL default '',
  `time_online` int(11) NOT NULL default '0',
  `date_format` varchar(255) NOT NULL default '',
  `last_message_id` int(11) NOT NULL default '0',
  `moderated_rooms` longtext NOT NULL,
  `moderated_categories` longtext NOT NULL,
  `is_admin` enum('y','n') NOT NULL default 'n',
  `banned_by` int(11) NOT NULL,
  `banned_by_username` varchar(255) NOT NULL default '',
  `banned_until` datetime NOT NULL default '0000-00-00 00:00:00',
  `banned_permanently` enum('y','n') NOT NULL default 'n',
  `ban_reason` varchar(255) NOT NULL default '',
  `muted_users` longtext NOT NULL,
  `global_muted_by` int(11) NOT NULL default '0',
  `global_muted_by_username` varchar(255) NOT NULL default '',
  `global_muted_until` datetime NOT NULL default '0000-00-00 00:00:00',
  `global_muted_permanently` enum('n','y') NOT NULL default 'n',
  `global_muted_reason` varchar(255) NOT NULL default '',
  `time_zone_offset` int(11) NOT NULL default '0',
  `is_guest` enum('y','n') NOT NULL default 'n',
  `show_message_time` enum('y','n') NOT NULL default 'y',
  `outgoing_message_color` char(6) NOT NULL default '',
  `language_id` int(11) NOT NULL default '0',
  `allow_sounds` enum('y','n') NOT NULL default 'y',
  `room_selection_view` enum('s','a') NOT NULL default 's',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `login` (`login`),
  KEY `email` (`email`),
  KEY `email_new_activation_code` (`email_new_activation_code`),
  KEY `is_admin` (`is_admin`),
  KEY `is_guest` (`is_guest`),
  KEY `activated` (`activated`),
  KEY `email_new` (`email_new`,`email_new_date`),
  KEY `joined` (`joined`,`last_login`),
  KEY `banned` (`banned_until`,`banned_permanently`),
  KEY `global_muted` (`global_muted_until`,`global_muted_permanently`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci PACK_KEYS=0; /* PCPIN6_QUERY_SEPARATOR */

DROP TABLE IF EXISTS `$$$DB_PREFIX$$$userdata`; /* PCPIN6_QUERY_SEPARATOR */
CREATE TABLE `$$$DB_PREFIX$$$userdata` (
  `user_id` int(11) NOT NULL default '0',
  `field_id` int(10) unsigned NOT NULL default '0',
  `field_value` text NOT NULL,
  KEY `user_id` (`user_id`),
  KEY `field_id` (`field_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci; /* PCPIN6_QUERY_SEPARATOR */

DROP TABLE IF EXISTS `$$$DB_PREFIX$$$userdata_field`; /* PCPIN6_QUERY_SEPARATOR */
CREATE TABLE `$$$DB_PREFIX$$$userdata_field` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(255) NOT NULL default '',
  `default_value` text NOT NULL,
  `custom` enum('y','n') default 'y',
  `type` enum('string','text','url','email','choice','multichoice') NOT NULL default 'text',
  `choices` text NOT NULL,
  `visibility` enum('public','registered','moderator','admin') NOT NULL default 'public',
  `writeable` enum('user','admin') NOT NULL default 'user',
  `order` int(10) unsigned NOT NULL,
  `disabled` enum('n','y') NOT NULL default 'n',
  PRIMARY KEY  (`id`),
  KEY `name` (`name`),
  KEY `visibility` (`visibility`),
  KEY `order` (`order`),
  KEY `disabled` (`disabled`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci PACK_KEYS=0; /* PCPIN6_QUERY_SEPARATOR */

DROP TABLE IF EXISTS `$$$DB_PREFIX$$$version`; /* PCPIN6_QUERY_SEPARATOR */
CREATE TABLE `$$$DB_PREFIX$$$version` (
  `version` decimal(3,2) NOT NULL default '0.00',
  `version_check_key` char(32) NOT NULL default '',
  `last_version_check` datetime NOT NULL default '0000-00-00 00:00:00',
  `new_version_available` decimal(3,2) NOT NULL default '0.00',
  `new_version_url` varchar(255) NOT NULL default ''
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci PACK_KEYS=0; /* PCPIN6_QUERY_SEPARATOR */