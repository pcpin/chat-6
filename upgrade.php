<?php
/**
 *    This file is part of "PCPIN Chat 6".
 *
 *    "PCPIN Chat 6" is free software; you can redistribute it and/or modify
 *    it under the terms of the GNU General Public License as published by
 *    the Free Software Foundation; either version 3 of the License, or
 *    (at your option) any later version.
 *
 *    "PCPIN Chat 6" is distributed in the hope that it will be useful,
 *    but WITHOUT ANY WARRANTY; without even the implied warranty of
 *    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *    GNU General Public License for more details.
 *
 *    You should have received a copy of the GNU General Public License
 *    along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

_pcpin_loadClass('config');
_pcpin_loadClass('session');
_pcpin_loadClass('version');

$__pcpin_upgrade=array();

$__pcpin_upgrade['file_version']=6.10;
$__pcpin_upgrade['init_class']=$__pcpin_init_class; // copy, not reference!
$__pcpin_upgrade['init_class']->_conf_all=array(1); // just a dummy
$__pcpin_upgrade['session']=new PCPIN_Session($__pcpin_upgrade['init_class'], '', true);
$__pcpin_upgrade['version']=new PCPIN_Version($__pcpin_upgrade['session']);



if ($__pcpin_upgrade['version']->_db_getList('version', 'version DESC', 1)) {
  $__pcpin_upgrade['db_version']=$__pcpin_upgrade['version']->_db_list[0]['version'];
  $__pcpin_upgrade['version']->_db_freeList();
  if ($__pcpin_upgrade['db_version']<$__pcpin_upgrade['file_version']) {
    // Database upgrade needed
    switch ($__pcpin_upgrade['db_version']) {

      case 6.00:
      case 6.01:
        // PCPIN Chat 6.02: Add more font sizes. See http://bugs.pcpin.com/view.php?id=224
        $__pcpin_upgrade['session']->_db_query("UPDATE `".PCPIN_DB_PREFIX."config` SET `_conf_choices` = '9=9|10=10|11=11|12=12|13=13|14=14|15=15|16=16|17=17|18=18|19=19|20=20' WHERE `_conf_name` = 'default_font_size' LIMIT 1");
        $__pcpin_upgrade['session']->_db_query("UPDATE `".PCPIN_DB_PREFIX."config` SET `_conf_choices` = '9=9|10=10|11=11|12=12|13=13|14=14|15=15|16=16|17=17|18=18|19=19|20=20' WHERE `_conf_name` = 'font_sizes' LIMIT 1");
      case 6.02:
      case 6.03:
      case 6.04:
      case 6.05:
      case 6.06:
      case 6.07:
        // PCPIN Chat 6.10

        // Save failed login attempts. See http://bugs.pcpin.com/view.php?id=297
        $__pcpin_upgrade['session']->_db_query("DROP TABLE IF EXISTS `".PCPIN_DB_PREFIX."failed_login`");
        $__pcpin_upgrade['session']->_db_query("CREATE TABLE IF NOT EXISTS `".PCPIN_DB_PREFIX."failed_login` ( `ip` varchar(15) NOT NULL default '', `count` int(11) default 0 NOT NULL, PRIMARY KEY  (`ip`) ) TYPE=MyISAM");
        $__pcpin_upgrade['session']->_db_query("INSERT INTO `".PCPIN_DB_PREFIX."config` ( `_conf_group` , `_conf_subgroup` , `_conf_name` , `_conf_value` , `_conf_type` , `_conf_choices` , `_conf_description` ) VALUES ( 'security', '{LNG_LOG_IN}', 'ip_failed_login_limit', '10', 'int_range', '0|*', '{LNG__CONF_IP_FAILED_LOGIN_LIMIT}' )");
        $__pcpin_upgrade['session']->_db_query("INSERT INTO `".PCPIN_DB_PREFIX."config` ( `_conf_group` , `_conf_subgroup` , `_conf_name` , `_conf_value` , `_conf_type` , `_conf_choices` , `_conf_description` ) VALUES ( 'security', '{LNG_LOG_IN}', 'ip_failed_login_ban', '3', 'int_range', '1|*', '{LNG__CONF_IP_FAILED_LOGIN_BAN}' )");
        $__pcpin_upgrade['session']->_db_query("INSERT INTO `".PCPIN_DB_PREFIX."language_expression` ( `language_id`, `code`, `value`, `multi_row` ) SELECT DISTINCT `".PCPIN_DB_PREFIX."language_expression`.`language_id` AS `language_id`, '_conf_ip_failed_login_limit' AS `code`, 0x416674657220686f77206d616e79206661696c6564206c6f6720696e20617474656d7074732062616e20736f7572636520495020616464726573733f0d0a303a20446f206e6f742062616e AS `value`, 'y' AS `multi_row` FROM `".PCPIN_DB_PREFIX."language_expression`");
        $__pcpin_upgrade['session']->_db_query("INSERT INTO `".PCPIN_DB_PREFIX."language_expression` ( `language_id`, `code`, `value`, `multi_row` ) SELECT DISTINCT `".PCPIN_DB_PREFIX."language_expression`.`language_id` AS `language_id`, '_conf_ip_failed_login_ban' AS `code`, 0x466f7220686f77206d616e7920686f7572732062616e2049502061646472657373657320616674657220746f6f206d616e79206661696c6564206c6f67696e20617474656d7074733f AS `value`, 'n' AS `multi_row` FROM `".PCPIN_DB_PREFIX."language_expression`");
        $__pcpin_upgrade['session']->_db_query("INSERT INTO `".PCPIN_DB_PREFIX."language_expression` ( `language_id`, `code`, `value`, `multi_row` ) SELECT DISTINCT `".PCPIN_DB_PREFIX."language_expression`.`language_id` AS `language_id`, 'too_many_failed_logins' AS `code`, 0x546f6f206d616e79206661696c6564206c6f67696e20617474656d707473 AS `value`, 'n' AS `multi_row` FROM `".PCPIN_DB_PREFIX."language_expression`");
        $__pcpin_upgrade['session']->_db_query("ALTER TABLE `".PCPIN_DB_PREFIX."language_expression` ORDER BY `language_id` ASC, `code` ASC");
        $__pcpin_upgrade['session']->_db_query("ALTER TABLE `".PCPIN_DB_PREFIX."user`  ADD `language_id` INT NOT NULL DEFAULT 0");

        // Force logout when user close browser window. See http://bugs.pcpin.com/view.php?id=314
        $__pcpin_upgrade['session']->_db_query("ALTER TABLE `".PCPIN_DB_PREFIX."session` ADD `_s_page_unloaded` ENUM( 'n', 'y' ) DEFAULT 'n' NOT NULL");
        $__pcpin_upgrade['session']->_db_query("ALTER TABLE `".PCPIN_DB_PREFIX."session` ADD INDEX ( `_s_page_unloaded` )");
        $__pcpin_upgrade['session']->_db_query("ALTER TABLE `".PCPIN_DB_PREFIX."session` ADD INDEX ( `_s_backend` )");
        $__pcpin_upgrade['session']->_db_query("UPDATE `".PCPIN_DB_PREFIX."config` SET `_conf_choices` = '10|120' WHERE `_conf_name` = 'session_timeout' LIMIT 1");
        $__pcpin_upgrade['session']->_db_query("UPDATE `".PCPIN_DB_PREFIX."config` SET `_conf_value` = '30' WHERE `_conf_name` = 'session_timeout' AND `_conf_value` > '30' LIMIT 1");
        $__pcpin_upgrade['session']->_db_query("UPDATE `".PCPIN_DB_PREFIX."config` SET `_conf_choices` = '1|20' WHERE `_conf_name` = 'updater_interval' LIMIT 1");
        $__pcpin_upgrade['session']->_db_query("UPDATE `".PCPIN_DB_PREFIX."config` SET `_conf_value` = '3' WHERE `_conf_name` = 'updater_interval' AND `_conf_value` > '3' LIMIT 1");

        // Event sounds. See http://bugs.pcpin.com/view.php?id=329
        $__pcpin_upgrade['session']->_db_query("INSERT INTO `".PCPIN_DB_PREFIX."language_expression` ( `language_id`, `code`, `value`, `multi_row` ) SELECT DISTINCT `".PCPIN_DB_PREFIX."language_expression`.`language_id` AS `language_id`, '_conf_allow_sounds' AS `code`, 0x416c6c6f7720736f756e6420656666656374733f AS `value`, 'n' AS `multi_row` FROM `".PCPIN_DB_PREFIX."language_expression`");
        $__pcpin_upgrade['session']->_db_query("INSERT INTO `".PCPIN_DB_PREFIX."language_expression` ( `language_id`, `code`, `value`, `multi_row` ) SELECT DISTINCT `".PCPIN_DB_PREFIX."language_expression`.`language_id` AS `language_id`, 'sounds' AS `code`, 0x536f756e6473 AS `value`, 'n' AS `multi_row` FROM `".PCPIN_DB_PREFIX."language_expression`");
        $__pcpin_upgrade['session']->_db_query("INSERT INTO `".PCPIN_DB_PREFIX."config` ( `_conf_group` , `_conf_subgroup` , `_conf_name` , `_conf_value` , `_conf_type` , `_conf_choices` , `_conf_description` ) VALUES ( 'chat', '{LNG_SOUNDS}', 'allow_sounds', '0', 'boolean_choice', '1={LNG_YES}|0={LNG_NO}', '{LNG__CONF_ALLOW_SOUNDS}' )");

        // 0000361: Performance optimisation
        // http://bugs.pcpin.com/view.php?id=361
        $__pcpin_upgrade['session']->_db_query("ALTER TABLE `".PCPIN_DB_PREFIX."language_expression` DROP PRIMARY KEY");
        $__pcpin_upgrade['session']->_db_query("ALTER TABLE `".PCPIN_DB_PREFIX."language_expression` ADD INDEX ( `language_id` )");
        $__pcpin_upgrade['session']->_db_query("ALTER TABLE `".PCPIN_DB_PREFIX."config` ORDER BY `_conf_group` ASC, `_conf_subgroup` ASC, `_conf_id` ASC");
        $__pcpin_upgrade['session']->_db_query("CREATE TABLE `".PCPIN_DB_PREFIX."cache` ( `id` CHAR( 255 ) NOT NULL , `contents` LONGBLOB NOT NULL , PRIMARY KEY ( `id` ) )");
      break;

      case 6.10:
        // PCPIN Chat 6.11

        // 0000367: Wrong encoded characters in database tables
        // http://bugs.pcpin.com/view.php?id=367
        $__pcpin_upgrade['session']->_db_query("ALTER TABLE `".PCPIN_DB_PREFIX."attachment` DEFAULT CHARACTER SET utf8");
        $__pcpin_upgrade['session']->_db_query("ALTER TABLE `".PCPIN_DB_PREFIX."avatar` DEFAULT CHARACTER SET utf8");
        $__pcpin_upgrade['session']->_db_query("ALTER TABLE `".PCPIN_DB_PREFIX."badword` DEFAULT CHARACTER SET utf8");
        $__pcpin_upgrade['session']->_db_query("ALTER TABLE `".PCPIN_DB_PREFIX."banner` DEFAULT CHARACTER SET utf8");
        $__pcpin_upgrade['session']->_db_query("ALTER TABLE `".PCPIN_DB_PREFIX."binaryfile` DEFAULT CHARACTER SET utf8");
        $__pcpin_upgrade['session']->_db_query("ALTER TABLE `".PCPIN_DB_PREFIX."cache` DEFAULT CHARACTER SET utf8");
        $__pcpin_upgrade['session']->_db_query("ALTER TABLE `".PCPIN_DB_PREFIX."category` DEFAULT CHARACTER SET utf8");
        $__pcpin_upgrade['session']->_db_query("ALTER TABLE `".PCPIN_DB_PREFIX."config` DEFAULT CHARACTER SET utf8");
        $__pcpin_upgrade['session']->_db_query("ALTER TABLE `".PCPIN_DB_PREFIX."disallowed_name` DEFAULT CHARACTER SET utf8");
        $__pcpin_upgrade['session']->_db_query("ALTER TABLE `".PCPIN_DB_PREFIX."failed_login` DEFAULT CHARACTER SET utf8");
        $__pcpin_upgrade['session']->_db_query("ALTER TABLE `".PCPIN_DB_PREFIX."invitation` DEFAULT CHARACTER SET utf8");
        $__pcpin_upgrade['session']->_db_query("ALTER TABLE `".PCPIN_DB_PREFIX."ipfilter` DEFAULT CHARACTER SET utf8");
        $__pcpin_upgrade['session']->_db_query("ALTER TABLE `".PCPIN_DB_PREFIX."language` DEFAULT CHARACTER SET utf8");
        $__pcpin_upgrade['session']->_db_query("ALTER TABLE `".PCPIN_DB_PREFIX."language_expression` DEFAULT CHARACTER SET utf8");
        $__pcpin_upgrade['session']->_db_query("ALTER TABLE `".PCPIN_DB_PREFIX."message` DEFAULT CHARACTER SET utf8");
        $__pcpin_upgrade['session']->_db_query("ALTER TABLE `".PCPIN_DB_PREFIX."message_log` DEFAULT CHARACTER SET utf8");
        $__pcpin_upgrade['session']->_db_query("ALTER TABLE `".PCPIN_DB_PREFIX."message_log_attachment` DEFAULT CHARACTER SET utf8");
        $__pcpin_upgrade['session']->_db_query("ALTER TABLE `".PCPIN_DB_PREFIX."nickname` DEFAULT CHARACTER SET utf8");
        $__pcpin_upgrade['session']->_db_query("ALTER TABLE `".PCPIN_DB_PREFIX."room` DEFAULT CHARACTER SET utf8");
        $__pcpin_upgrade['session']->_db_query("ALTER TABLE `".PCPIN_DB_PREFIX."session` DEFAULT CHARACTER SET utf8");
        $__pcpin_upgrade['session']->_db_query("ALTER TABLE `".PCPIN_DB_PREFIX."smilie` DEFAULT CHARACTER SET utf8");
        $__pcpin_upgrade['session']->_db_query("ALTER TABLE `".PCPIN_DB_PREFIX."tmpdata` DEFAULT CHARACTER SET utf8");
        $__pcpin_upgrade['session']->_db_query("ALTER TABLE `".PCPIN_DB_PREFIX."user` DEFAULT CHARACTER SET utf8");
        $__pcpin_upgrade['session']->_db_query("ALTER TABLE `".PCPIN_DB_PREFIX."userdata` DEFAULT CHARACTER SET utf8");
        $__pcpin_upgrade['session']->_db_query("ALTER TABLE `".PCPIN_DB_PREFIX."version` DEFAULT CHARACTER SET utf8");
        $__pcpin_upgrade['session']->_db_query("ALTER TABLE `".PCPIN_DB_PREFIX."attachment` CHANGE `filename` `filename` CHAR( 255 ) CHARACTER SET utf8 NOT NULL DEFAULT ''");
        $__pcpin_upgrade['session']->_db_query("ALTER TABLE `".PCPIN_DB_PREFIX."avatar` CHANGE `primary` `primary` ENUM( 'y', 'n' ) NOT NULL DEFAULT 'y'");
        $__pcpin_upgrade['session']->_db_query("ALTER TABLE `".PCPIN_DB_PREFIX."badword` CHANGE `word` `word` CHAR( 255 ) CHARACTER SET utf8 NOT NULL DEFAULT '', CHANGE `replacement` `replacement` CHAR( 255 ) CHARACTER SET utf8 NOT NULL DEFAULT ''");
        $__pcpin_upgrade['session']->_db_query("ALTER TABLE `".PCPIN_DB_PREFIX."banner` CHANGE `name` `name` CHAR( 255 ) CHARACTER SET utf8 NOT NULL DEFAULT '', CHANGE `active` `active` ENUM( 'n', 'y' ) NOT NULL DEFAULT 'n', CHANGE `source_type` `source_type` ENUM( 'u', 'c' ) NOT NULL DEFAULT 'c', CHANGE `display_position` `display_position` ENUM( 't', 'b', 'p', 'm' ) NOT NULL DEFAULT 't'");
        $__pcpin_upgrade['session']->_db_query("ALTER TABLE `".PCPIN_DB_PREFIX."binaryfile` CHANGE `mime_type` `mime_type` CHAR( 255 ) CHARACTER SET utf8 NOT NULL DEFAULT '' , CHANGE `protected` `protected` CHAR( 255 ) CHARACTER SET utf8 NOT NULL DEFAULT ''");
        $__pcpin_upgrade['session']->_db_query("ALTER TABLE `".PCPIN_DB_PREFIX."cache` CHANGE `id` `id` CHAR( 255 ) CHARACTER SET utf8 NOT NULL DEFAULT ''");
        $__pcpin_upgrade['session']->_db_query("ALTER TABLE `".PCPIN_DB_PREFIX."category` CHANGE `name` `name` CHAR( 32 ) CHARACTER SET utf8 NOT NULL DEFAULT '' , CHANGE `description` `description` LONGTEXT CHARACTER SET utf8 NOT NULL DEFAULT '' , CHANGE `creatable_rooms` `creatable_rooms` ENUM( 'n', 'r', 'g' ) NOT NULL DEFAULT 'n'");
        $__pcpin_upgrade['session']->_db_query("ALTER TABLE `".PCPIN_DB_PREFIX."config` CHANGE `_conf_group` `_conf_group` ENUM( 'server', 'security', 'account', 'chat', 'design', 'banners', 'slave' ) NOT NULL DEFAULT 'chat', CHANGE `_conf_subgroup` `_conf_subgroup` CHAR( 255 ) CHARACTER SET utf8 NOT NULL DEFAULT '' , CHANGE `_conf_name` `_conf_name` CHAR( 255 ) CHARACTER SET utf8 NOT NULL DEFAULT '' , CHANGE `_conf_value` `_conf_value` TEXT CHARACTER SET utf8 NOT NULL DEFAULT '' , CHANGE `_conf_type` `_conf_type` CHAR( 255 ) CHARACTER SET utf8 NOT NULL DEFAULT '' , CHANGE `_conf_choices` `_conf_choices` TEXT CHARACTER SET utf8 NOT NULL DEFAULT '' , CHANGE `_conf_description` `_conf_description` LONGTEXT CHARACTER SET utf8 NOT NULL DEFAULT ''");
        $__pcpin_upgrade['session']->_db_query("ALTER TABLE `".PCPIN_DB_PREFIX."disallowed_name` CHANGE `name` `name` CHAR( 255 ) CHARACTER SET utf8 NOT NULL DEFAULT ''");
        $__pcpin_upgrade['session']->_db_query("ALTER TABLE `".PCPIN_DB_PREFIX."failed_login` CHANGE `ip` `ip` CHAR( 15 ) CHARACTER SET utf8 NOT NULL DEFAULT ''");
        $__pcpin_upgrade['session']->_db_query("ALTER TABLE `".PCPIN_DB_PREFIX."invitation` CHANGE `author_nickname` `author_nickname` CHAR( 255 ) CHARACTER SET utf8 NOT NULL DEFAULT '' , CHANGE `room_name` `room_name` CHAR( 255 ) CHARACTER SET utf8 NOT NULL DEFAULT ''");
        $__pcpin_upgrade['session']->_db_query("ALTER TABLE `".PCPIN_DB_PREFIX."ipfilter` CHANGE `address` `address` CHAR( 15 ) CHARACTER SET utf8 NOT NULL DEFAULT '' , CHANGE `description` `description` TEXT CHARACTER SET utf8 NOT NULL DEFAULT '' , CHANGE `action` `action` ENUM( 'd', 'a' ) NOT NULL DEFAULT 'd'");
        $__pcpin_upgrade['session']->_db_query("ALTER TABLE `".PCPIN_DB_PREFIX."language` CHANGE `iso_name` `iso_name` CHAR( 2 ) CHARACTER SET utf8 NOT NULL DEFAULT '' , CHANGE `name` `name` CHAR( 255 ) CHARACTER SET utf8 NOT NULL DEFAULT '', CHANGE `local_name` `local_name` CHAR( 255 ) CHARACTER SET utf8 NOT NULL DEFAULT '' , CHANGE `active` `active` ENUM( 'n', 'y' ) NOT NULL DEFAULT 'n'");
        $__pcpin_upgrade['session']->_db_query("ALTER TABLE `".PCPIN_DB_PREFIX."language_expression` CHANGE `code` `code` CHAR( 255 ) CHARACTER SET utf8 NOT NULL DEFAULT '' , CHANGE `multi_row` `multi_row` ENUM( 'n', 'y' ) NOT NULL DEFAULT 'n'");
        $__pcpin_upgrade['session']->_db_query("ALTER TABLE `".PCPIN_DB_PREFIX."message` CHANGE `offline` `offline` ENUM( 'y', 'n' ) NOT NULL DEFAULT 'n' , CHANGE `author_nickname` `author_nickname` CHAR( 255 ) CHARACTER SET utf8 NOT NULL DEFAULT '0' , CHANGE `body` `body` TEXT CHARACTER SET utf8 NOT NULL DEFAULT '' , CHANGE `css_properties` `css_properties` CHAR( 255 ) CHARACTER SET utf8 NOT NULL DEFAULT ''");
        $__pcpin_upgrade['session']->_db_query("ALTER TABLE `".PCPIN_DB_PREFIX."message_log` CHANGE `offline` `offline` ENUM( 'n', 'y' ) NOT NULL DEFAULT 'n', CHANGE `category_name` `category_name` CHAR( 255 ) CHARACTER SET utf8 NOT NULL DEFAULT '' , CHANGE `room_name` `room_name` CHAR( 255 ) CHARACTER SET utf8 NOT NULL DEFAULT '' , CHANGE `target_category_name` `target_category_name` CHAR( 255 ) CHARACTER SET utf8 NOT NULL DEFAULT '' , CHANGE `target_room_name` `target_room_name` CHAR( 255 ) CHARACTER SET utf8 NOT NULL DEFAULT '' , CHANGE `author_nickname` `author_nickname` CHAR( 255 ) CHARACTER SET utf8 NOT NULL DEFAULT '' , CHANGE `target_user_nickname` `target_user_nickname` CHAR( 255 ) CHARACTER SET utf8 NOT NULL DEFAULT '' , CHANGE `body` `body` TEXT CHARACTER SET utf8 NOT NULL , CHANGE `css_properties` `css_properties` CHAR( 255 ) CHARACTER SET utf8 NOT NULL DEFAULT ''");
        $__pcpin_upgrade['session']->_db_query("ALTER TABLE `".PCPIN_DB_PREFIX."message_log_attachment` CHANGE `filename` `filename` CHAR( 255 ) CHARACTER SET utf8 NOT NULL DEFAULT '' , CHANGE `mime_type` `mime_type` CHAR( 255 ) CHARACTER SET utf8 NOT NULL DEFAULT ''");
        $__pcpin_upgrade['session']->_db_query("ALTER TABLE `".PCPIN_DB_PREFIX."nickname` CHANGE `nickname` `nickname` CHAR( 255 ) CHARACTER SET utf8 NOT NULL DEFAULT '' , CHANGE `nickname_plain` `nickname_plain` CHAR( 255 ) CHARACTER SET utf8 NOT NULL DEFAULT '' , CHANGE `default` `default` ENUM( 'y', 'n' ) NOT NULL DEFAULT 'n'");
        $__pcpin_upgrade['session']->_db_query("ALTER TABLE `".PCPIN_DB_PREFIX."room` CHANGE `type` `type` ENUM( 'p', 'u' ) NOT NULL DEFAULT 'p', CHANGE `name` `name` CHAR( 32 ) CHARACTER SET utf8 NOT NULL DEFAULT '' , CHANGE `description` `description` CHAR( 255 ) CHARACTER SET utf8 NOT NULL DEFAULT '' , CHANGE `default_message_color` `default_message_color` CHAR( 6 ) CHARACTER SET utf8 NOT NULL DEFAULT '' , CHANGE `password` `password` CHAR( 32 ) CHARACTER SET utf8 NOT NULL DEFAULT ''");
        $__pcpin_upgrade['session']->_db_query("ALTER TABLE `".PCPIN_DB_PREFIX."session` CHANGE `_s_id` `_s_id` CHAR( 32 ) CHARACTER SET utf8 NOT NULL DEFAULT '' , CHANGE `_s_ip` `_s_ip` CHAR( 15 ) CHARACTER SET utf8 NOT NULL DEFAULT '' , CHANGE `_s_security_code` `_s_security_code` CHAR( 255 ) CHARACTER SET utf8 NOT NULL DEFAULT '' , CHANGE `_s_client_agent_name` `_s_client_agent_name` CHAR( 255 ) CHARACTER SET utf8 NOT NULL DEFAULT '' , CHANGE `_s_client_agent_version` `_s_client_agent_version` CHAR( 255 ) CHARACTER SET utf8 NOT NULL DEFAULT '' , CHANGE `_s_client_os` `_s_client_os` CHAR( 255 ) CHARACTER SET utf8 NOT NULL DEFAULT '' , CHANGE `_s_kicked` `_s_kicked` ENUM( 'y', 'n' ) NOT NULL DEFAULT 'n', CHANGE `_s_online_status_message` `_s_online_status_message` CHAR( 255 ) CHARACTER SET utf8 NOT NULL DEFAULT '' , CHANGE `_s_stealth_mode` `_s_stealth_mode` ENUM( 'y', 'n' ) NOT NULL DEFAULT 'n', CHANGE `_s_backend` `_s_backend` ENUM( 'n', 'y' ) NOT NULL DEFAULT 'n', CHANGE `_s_page_unloaded` `_s_page_unloaded` ENUM( 'n', 'y' ) NOT NULL DEFAULT 'n'");
        $__pcpin_upgrade['session']->_db_query("ALTER TABLE `".PCPIN_DB_PREFIX."smilie` CHANGE `code` `code` CHAR( 32 ) CHARACTER SET utf8 NOT NULL DEFAULT '' , CHANGE `description` `description` CHAR( 255 ) CHARACTER SET utf8 NOT NULL DEFAULT ''");
        $__pcpin_upgrade['session']->_db_query("ALTER TABLE `".PCPIN_DB_PREFIX."tmpdata` CHANGE `filename` `filename` CHAR( 255 ) CHARACTER SET utf8 NOT NULL");
        $__pcpin_upgrade['session']->_db_query("ALTER TABLE `".PCPIN_DB_PREFIX."user` CHANGE `login` `login` CHAR( 30 ) CHARACTER SET utf8 NOT NULL DEFAULT '' , CHANGE `password` `password` CHAR( 32 ) CHARACTER SET utf8 NOT NULL DEFAULT '' , CHANGE `password_new` `password_new` CHAR( 32 ) CHARACTER SET utf8 NOT NULL DEFAULT '' , CHANGE `email` `email` CHAR( 255 ) CHARACTER SET utf8 NOT NULL DEFAULT '' , CHANGE `email_new` `email_new` CHAR( 255 ) CHARACTER SET utf8 NOT NULL DEFAULT '' , CHANGE `email_new_activation_code` `email_new_activation_code` CHAR( 32 ) CHARACTER SET utf8 NOT NULL DEFAULT '' , CHANGE `activated` `activated` ENUM( 'n', 'y' ) NOT NULL DEFAULT 'n', CHANGE `activation_code` `activation_code` CHAR( 255 ) CHARACTER SET utf8 NOT NULL DEFAULT '' , CHANGE `date_format` `date_format` CHAR( 255 ) CHARACTER SET utf8 NOT NULL DEFAULT '' , CHANGE `moderated_rooms` `moderated_rooms` LONGTEXT CHARACTER SET utf8 NOT NULL DEFAULT '' , CHANGE `moderated_categories` `moderated_categories` LONGTEXT CHARACTER SET utf8 NOT NULL DEFAULT '' , CHANGE `is_admin` `is_admin` ENUM( 'y', 'n' ) NOT NULL DEFAULT 'n', CHANGE `banned_by_username` `banned_by_username` CHAR( 255 ) CHARACTER SET utf8 NOT NULL DEFAULT '' , CHANGE `banned_permanently` `banned_permanently` ENUM( 'y', 'n' ) NOT NULL DEFAULT 'n', CHANGE `ban_reason` `ban_reason` CHAR( 255 ) CHARACTER SET utf8 NOT NULL DEFAULT '' , CHANGE `muted_users` `muted_users` LONGTEXT CHARACTER SET utf8 NOT NULL DEFAULT '' , CHANGE `global_muted_by_username` `global_muted_by_username` CHAR( 255 ) CHARACTER SET utf8 NOT NULL DEFAULT '' , CHANGE `global_muted_permanently` `global_muted_permanently` ENUM( 'n', 'y' ) NOT NULL DEFAULT 'n', CHANGE `global_muted_reason` `global_muted_reason` CHAR( 255 ) CHARACTER SET utf8 NOT NULL DEFAULT '' , CHANGE `is_guest` `is_guest` ENUM( 'y', 'n' ) NOT NULL DEFAULT 'n', CHANGE `show_message_time` `show_message_time` ENUM( 'y', 'n' ) NOT NULL DEFAULT 'y', CHANGE `outgoing_message_color` `outgoing_message_color` CHAR( 6 ) CHARACTER SET utf8 NOT NULL DEFAULT ''");
        $__pcpin_upgrade['session']->_db_query("ALTER TABLE `".PCPIN_DB_PREFIX."userdata` CHANGE `homepage` `homepage` CHAR( 255 ) CHARACTER SET utf8 NOT NULL DEFAULT '' , CHANGE `gender` `gender` ENUM( 'm', 'f', '-' ) NOT NULL DEFAULT '-', CHANGE `age` `age` CHAR( 255 ) CHARACTER SET utf8 NOT NULL DEFAULT '' , CHANGE `icq` `icq` CHAR( 255 ) CHARACTER SET utf8 NOT NULL DEFAULT '' , CHANGE `msn` `msn` CHAR( 255 ) CHARACTER SET utf8 NOT NULL DEFAULT '' , CHANGE `aim` `aim` CHAR( 255 ) CHARACTER SET utf8 NOT NULL DEFAULT '' , CHANGE `yim` `yim` CHAR( 255 ) CHARACTER SET utf8 NOT NULL DEFAULT '' , CHANGE `location` `location` CHAR( 255 ) CHARACTER SET utf8 NOT NULL DEFAULT '' , CHANGE `occupation` `occupation` CHAR( 255 ) CHARACTER SET utf8 NOT NULL DEFAULT '' , CHANGE `interests` `interests` TEXT CHARACTER SET utf8 NOT NULL DEFAULT ''");
        $__pcpin_upgrade['session']->_db_query("ALTER TABLE `".PCPIN_DB_PREFIX."version` CHANGE `version_check_key` `version_check_key` CHAR( 32 ) CHARACTER SET utf8 NOT NULL DEFAULT '' , CHANGE `new_version_url` `new_version_url` CHAR( 255 ) CHARACTER SET utf8 NOT NULL DEFAULT ''");

        // 0000369: A possibility to turn sounds off/on for users
        // http://bugs.pcpin.com/view.php?id=369
        $__pcpin_upgrade['session']->_db_query("ALTER TABLE `".PCPIN_DB_PREFIX."user` ADD `allow_sounds` ENUM( 'y', 'n' ) DEFAULT 'y' NOT NULL");
        $__pcpin_upgrade['session']->_db_query("TRUNCATE TABLE `".PCPIN_DB_PREFIX."cache`");
        
      break;

    }
    // All versions: Store new version number
    $__pcpin_upgrade['session']->_db_query('DELETE FROM `'.PCPIN_DB_PREFIX.'version`');
    $__pcpin_upgrade['session']->_db_query('INSERT INTO `'.PCPIN_DB_PREFIX.'version` ( `version`, `version_check_key`, `last_version_check` ) VALUES ( "'.$__pcpin_upgrade['session']->_db_escapeStr($__pcpin_upgrade['file_version'], false).'", "-", "'.date('Y-m-d H:i:s').'" )');
  }
} else {
  die('Fatal error: Your installation is broken. Reinstall needed!');
}

unset($__pcpin_upgrade);

// Trying to delete this file
@unlink('./upgrade.php');
@clearstatcache();
if (file_exists('./upgrade.php')) {
  die('<html><body><center><br /><br /><br /><br /><h3>Upgrade completed.</h3><br />Please delete file <b>upgrade.php</b> in order to continue.</center></body></html>');
}


?>