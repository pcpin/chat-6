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

// Initialize
require_once('./init.inc.php');

function _pcpin_upgrade(&$__pcpin_init_class) {

  _pcpin_loadClass('config');
  _pcpin_loadClass('session');
  _pcpin_loadClass('version');

  $__pcpin_upgrade=array();

  $__pcpin_upgrade['file_version']=6.20;
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
        case 6.11:
          // PCPIN Chat *.* ==> PCPIN Chat 6.20

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

          // 0000369: A possibility to turn sounds off/on for users
          // http://bugs.pcpin.com/view.php?id=369
          $__pcpin_upgrade['session']->_db_query("ALTER TABLE `".PCPIN_DB_PREFIX."user` ADD `allow_sounds` ENUM( 'y', 'n' ) DEFAULT 'y' NOT NULL");

          // 0000374: "Optimize Database" menu in Administrator area
          // http://bugs.pcpin.com/view.php?id=374
          $__pcpin_upgrade['session']->_db_query("INSERT INTO `".PCPIN_DB_PREFIX."language_expression` ( `language_id`, `code`, `value`, `multi_row` ) SELECT DISTINCT `".PCPIN_DB_PREFIX."language_expression`.`language_id` AS `language_id`, 'optimize_database' AS `code`, 0x4f7074696d697a65206461746162617365 AS `value`, 'n' AS `multi_row` FROM `".PCPIN_DB_PREFIX."language_expression`");

          // 0000382: Store last used room selection view setting
          // http://bugs.pcpin.com/view.php?id=382
          $__pcpin_upgrade['session']->_db_query("ALTER TABLE `".PCPIN_DB_PREFIX."user` ADD `room_selection_view` ENUM( 's', 'a' ) DEFAULT 's' NOT NULL");
          $__pcpin_upgrade['session']->_db_query("UPDATE `".PCPIN_DB_PREFIX."config` SET `_conf_type` = 'string_choice', `_conf_choices` = 'a={LNG_ADVANCED_VIEW}|s={LNG_SIMPLIFIED_VIEW}' WHERE `_conf_id` = 46 LIMIT 1");
          $__pcpin_upgrade['session']->_db_query("UPDATE `".PCPIN_DB_PREFIX."config` SET `_conf_value` = IF( `_conf_value` = '0', 'a', 's' ) WHERE `_conf_id` = 46 LIMIT 1");

          // 0000327: More user management features in admin area
          // http://bugs.pcpin.com/view.php?id=327
          $__pcpin_upgrade['session']->_db_query("INSERT INTO `".PCPIN_DB_PREFIX."language_expression` ( `language_id`, `code`, `value`, `multi_row` ) SELECT DISTINCT `".PCPIN_DB_PREFIX."language_expression`.`language_id` AS `language_id`, 'add_new_user' AS `code`, 0x416464206e65772075736572 AS `value`, 'n' AS `multi_row` FROM `".PCPIN_DB_PREFIX."language_expression`");
          $__pcpin_upgrade['session']->_db_query("INSERT INTO `".PCPIN_DB_PREFIX."language_expression` ( `language_id`, `code`, `value`, `multi_row` ) SELECT DISTINCT `".PCPIN_DB_PREFIX."language_expression`.`language_id` AS `language_id`, 'new_user_added' AS `code`, 0x4e6577207573657220686173206265656e2063726561746564207375636365737366756c6c79 AS `value`, 'n' AS `multi_row` FROM `".PCPIN_DB_PREFIX."language_expression`");
          $__pcpin_upgrade['session']->_db_query("INSERT INTO `".PCPIN_DB_PREFIX."language_expression` ( `language_id`, `code`, `value`, `multi_row` ) SELECT DISTINCT `".PCPIN_DB_PREFIX."language_expression`.`language_id` AS `language_id`, '_conf_allow_user_registration' AS `code`, 0x416c6c6f77206163636f756e7420726567697374726174696f6e3f AS `value`, 'n' AS `multi_row` FROM `".PCPIN_DB_PREFIX."language_expression`");
          $__pcpin_upgrade['session']->_db_query("INSERT INTO `".PCPIN_DB_PREFIX."config` ( `_conf_id` , `_conf_group` , `_conf_subgroup` , `_conf_name` , `_conf_value` , `_conf_type` , `_conf_choices` , `_conf_description` ) VALUES ( '', 'account', '{LNG_USERS}', 'allow_user_registration', '1', 'boolean_choice', '1={LNG_YES}|0={LNG_NO}', '{LNG__CONF_ALLOW_USER_REGISTRATION}\r\n{LNG_SETTING_IGNORED_IN_SLAVE_MODE}' )");
          $__pcpin_upgrade['session']->_db_query("UPDATE `".PCPIN_DB_PREFIX."config` SET `_conf_type` = 'int_choice', `_conf_choices` = '0={LNG_NO}|1={LNG_YES}: {LNG_ACTIVATION_EMAIL}|2={LNG_YES}: {LNG_ACTIVATION_BY_ADMIN}' WHERE `_conf_name` = 'activate_new_accounts' LIMIT 1");
          $__pcpin_upgrade['session']->_db_query("INSERT INTO `".PCPIN_DB_PREFIX."language_expression` ( `language_id`, `code`, `value`, `multi_row` ) SELECT DISTINCT `".PCPIN_DB_PREFIX."language_expression`.`language_id` AS `language_id`, 'activation_email' AS `code`, 0x41637469766174696f6e20656d61696c AS `value`, 'n' AS `multi_row` FROM `".PCPIN_DB_PREFIX."language_expression`");
          $__pcpin_upgrade['session']->_db_query("INSERT INTO `".PCPIN_DB_PREFIX."language_expression` ( `language_id`, `code`, `value`, `multi_row` ) SELECT DISTINCT `".PCPIN_DB_PREFIX."language_expression`.`language_id` AS `language_id`, 'activation_by_admin' AS `code`, 0x41637469766174696f6e2062792041646d696e AS `value`, 'n' AS `multi_row` FROM `".PCPIN_DB_PREFIX."language_expression`");
          $__pcpin_upgrade['session']->_db_query("INSERT INTO `".PCPIN_DB_PREFIX."language_expression` ( `language_id`, `code`, `value`, `multi_row` ) SELECT DISTINCT `".PCPIN_DB_PREFIX."language_expression`.`language_id` AS `language_id`, 'email_new_user_activation_notification' AS `code`, 0x48656c6c6f2c0d0a0d0a54686520666f6c6c6f77696e67206163636f756e74206174205b434841545f4e414d455d20686173206265656e206372656174656420616e64206e6565647320746f206265206163746976617465643a0d0a0d0a557365726e616d653a205b555345524e414d455d0d0a452d4d61696c20616464726573733a205b454d41494c5f414444524553535d0d0a52656d6f746520495020616464726573733a205b52454d4f54455f49505d0d0a0d0a0d0a2d2d2d2d2d2d2d2d2d2d2d2d2d2d2d2d2d2d2d2d2d2d2d2d2d2d2d0d0a5468616e6b20596f752c0d0a5b53454e4445525d AS `value`, 'y' AS `multi_row` FROM `".PCPIN_DB_PREFIX."language_expression`");
          $__pcpin_upgrade['session']->_db_query("INSERT INTO `".PCPIN_DB_PREFIX."language_expression` ( `language_id`, `code`, `value`, `multi_row` ) SELECT DISTINCT `".PCPIN_DB_PREFIX."language_expression`.`language_id` AS `language_id`, 'account_will_be_activated_by_admin' AS `code`, 0x596f7572206163636f756e7420686173206265656e206372656174656420616e642073656e7420746f207468652041646d696e6973747261746f7220666f722061637469766174696f6e2e AS `value`, 'n' AS `multi_row` FROM `".PCPIN_DB_PREFIX."language_expression`");

          // 0000333: Automatically display enlarged thumbnail of user's profile image in the userlist on mouseover
          // http://bugs.pcpin.com/view.php?id=333
          $__pcpin_upgrade['session']->_db_query("ALTER TABLE `".PCPIN_DB_PREFIX."tmpdata` CHANGE `filename` `filename` char(255) default '' NOT NULL");

          // 0000363: Typo in German language pack
          // http://bugs.pcpin.com/view.php?id=363
          $__pcpin_upgrade['session']->_db_query("UPDATE `".PCPIN_DB_PREFIX."language_expression` AS `le` LEFT JOIN `".PCPIN_DB_PREFIX."language` AS `la` ON `la`.`id` = `le`.`language_id` SET `le`.`value` = 0x47442d556e7465727374c3bc747a756e6720616b746976696572656e3f WHERE `le`.`code` = '_conf_allow_gd' AND `la`.`iso_name` = 'de'");

          // 0000393: Typo in German language pack
          // http://bugs.pcpin.com/view.php?id=393
          $__pcpin_upgrade['session']->_db_query("UPDATE `".PCPIN_DB_PREFIX."language_expression` AS `le` LEFT JOIN `".PCPIN_DB_PREFIX."language` AS `la` ON `la`.`id` = `le`.`language_id` SET `le`.`value` = 'Sie sind bereits eingeloggt' WHERE `le`.`code` = 'you_already_logged_in' AND `la`.`iso_name` = 'de'");
          $__pcpin_upgrade['session']->_db_query("UPDATE `".PCPIN_DB_PREFIX."language_expression` AS `le` LEFT JOIN `".PCPIN_DB_PREFIX."language` AS `la` ON `la`.`id` = `le`.`language_id` SET `le`.`value` = 'Benutzer [USER] ist zur Zeit nicht eingeloggt' WHERE `le`.`code` = 'user_is_not_logged_in' AND `la`.`iso_name` = 'de'");
          $__pcpin_upgrade['session']->_db_query("UPDATE `".PCPIN_DB_PREFIX."language_expression` AS `le` LEFT JOIN `".PCPIN_DB_PREFIX."language` AS `la` ON `la`.`id` = `le`.`language_id` SET `le`.`value` = 'Benutzer [USER] ist zur Zeit eingeloggt' WHERE `le`.`code` = 'user_is_logged_in' AND `la`.`iso_name` = 'de'");
          $__pcpin_upgrade['session']->_db_query("UPDATE `".PCPIN_DB_PREFIX."language_expression` AS `le` LEFT JOIN `".PCPIN_DB_PREFIX."language` AS `la` ON `la`.`id` = `le`.`language_id` SET `le`.`value` = '/exitroom\nDen Raum verlassen, aber eingeloggt bleiben.' WHERE `le`.`code` = 'cmd_help_exitroom' AND `la`.`iso_name` = 'de'");

          // Customizable user profile fields
          // http://bugs.pcpin.com/view.php?id=325
          $__pcpin_upgrade['session']->_db_query("CREATE TABLE `".PCPIN_DB_PREFIX."userdata_field` ( `id` int(10) unsigned NOT NULL auto_increment, `name` varchar(255) NOT NULL default '', `default_value` text NOT NULL, `custom` enum('y','n') default 'y', `type` enum('string','text','url','email','choice','multichoice') NOT NULL default 'text', `choices` text NOT NULL, `visibility` enum('public','registered','moderator','admin') NOT NULL default 'public', `writeable` enum('user','admin') NOT NULL default 'user', `order` int(10) unsigned NOT NULL, `disabled` enum('n','y') NOT NULL default 'n', PRIMARY KEY  (`id`), KEY `name` (`name`), KEY `visibility` (`visibility`), KEY `order` (`order`), KEY `disabled` (`disabled`) ) TYPE=MyISAM DEFAULT CHARSET=utf8 PACK_KEYS=0");
          $__pcpin_upgrade['session']->_db_query("INSERT INTO `".PCPIN_DB_PREFIX."userdata_field` (`id`, `name`, `default_value`, `custom`, `type`, `choices`, `visibility`, `order`) VALUES ('1', 'homepage', '', 'n', 'url', '', 'public', '0'), ('2', 'gender', '-', 'n', 'choice', '-\nm\nf', 'public', '1'), ('3', 'age', '', 'n', 'number', '', 'public', '2'), ('4', 'messenger_icq', '', 'n', 'string', '', 'public', '3'), ('5', 'messenger_msn', '', 'n', 'string', '', 'public', '4'), ('6', 'messenger_aim', '', 'n', 'string', '', 'public', '5'), ('7', 'messenger_yim', '', 'n', 'string', '', 'public', '6'), ('8', 'location', '', 'n', 'string', '', 'public', '7'), ('9', 'occupation', '', 'n', 'string', '', 'public', '8'), ('10', 'interests', '', 'n', 'text', '', 'public', '9')");
          $__pcpin_upgrade['session']->_db_query("CREATE TABLE `".PCPIN_DB_PREFIX."userdata_tmp` ( `user_id` INT NOT NULL DEFAULT '0' , `field_id` INT UNSIGNED DEFAULT '0' NOT NULL , `field_value` TEXT NOT NULL , INDEX ( `user_id` ) , INDEX ( `field_id` ) )");
          $__pcpin_upgrade['session']->_db_query("INSERT INTO `".PCPIN_DB_PREFIX."userdata_tmp` ( `user_id`, `field_id`, `field_value` ) SELECT `user_id`, '1', `homepage` FROM `".PCPIN_DB_PREFIX."userdata`");
          $__pcpin_upgrade['session']->_db_query("INSERT INTO `".PCPIN_DB_PREFIX."userdata_tmp` ( `user_id`, `field_id`, `field_value` ) SELECT `user_id`, '2', `gender` FROM `".PCPIN_DB_PREFIX."userdata`");
          $__pcpin_upgrade['session']->_db_query("INSERT INTO `".PCPIN_DB_PREFIX."userdata_tmp` ( `user_id`, `field_id`, `field_value` ) SELECT `user_id`, '3', `age` FROM `".PCPIN_DB_PREFIX."userdata`");
          $__pcpin_upgrade['session']->_db_query("INSERT INTO `".PCPIN_DB_PREFIX."userdata_tmp` ( `user_id`, `field_id`, `field_value` ) SELECT `user_id`, '4', `icq` FROM `".PCPIN_DB_PREFIX."userdata`");
          $__pcpin_upgrade['session']->_db_query("INSERT INTO `".PCPIN_DB_PREFIX."userdata_tmp` ( `user_id`, `field_id`, `field_value` ) SELECT `user_id`, '5', `msn` FROM `".PCPIN_DB_PREFIX."userdata`");
          $__pcpin_upgrade['session']->_db_query("INSERT INTO `".PCPIN_DB_PREFIX."userdata_tmp` ( `user_id`, `field_id`, `field_value` ) SELECT `user_id`, '6', `aim` FROM `".PCPIN_DB_PREFIX."userdata`");
          $__pcpin_upgrade['session']->_db_query("INSERT INTO `".PCPIN_DB_PREFIX."userdata_tmp` ( `user_id`, `field_id`, `field_value` ) SELECT `user_id`, '7', `yim` FROM `".PCPIN_DB_PREFIX."userdata`");
          $__pcpin_upgrade['session']->_db_query("INSERT INTO `".PCPIN_DB_PREFIX."userdata_tmp` ( `user_id`, `field_id`, `field_value` ) SELECT `user_id`, '8', `location` FROM `".PCPIN_DB_PREFIX."userdata`");
          $__pcpin_upgrade['session']->_db_query("INSERT INTO `".PCPIN_DB_PREFIX."userdata_tmp` ( `user_id`, `field_id`, `field_value` ) SELECT `user_id`, '9', `occupation` FROM `".PCPIN_DB_PREFIX."userdata`");
          $__pcpin_upgrade['session']->_db_query("INSERT INTO `".PCPIN_DB_PREFIX."userdata_tmp` ( `user_id`, `field_id`, `field_value` ) SELECT `user_id`, '10', `interests` FROM `".PCPIN_DB_PREFIX."userdata`");
          $__pcpin_upgrade['session']->_db_query("DROP TABLE `".PCPIN_DB_PREFIX."userdata`");
          $__pcpin_upgrade['session']->_db_query("ALTER TABLE `".PCPIN_DB_PREFIX."userdata_tmp` RENAME `".PCPIN_DB_PREFIX."userdata`");
          $__pcpin_upgrade['session']->_db_query("INSERT INTO `".PCPIN_DB_PREFIX."language_expression` ( `language_id`, `code`, `value`, `multi_row` ) SELECT DISTINCT `".PCPIN_DB_PREFIX."language_expression`.`language_id` AS `language_id`, 'ignore_list' AS `code`, 0x49676e6f7265206c697374 AS `value`, 'n' AS `multi_row` FROM `".PCPIN_DB_PREFIX."language_expression`");
          $__pcpin_upgrade['session']->_db_query("DELETE FROM `".PCPIN_DB_PREFIX."language_expression` WHERE `code` = 'select_new_level_or_cancel'");
          $__pcpin_upgrade['session']->_db_query("DELETE FROM `".PCPIN_DB_PREFIX."language_expression` WHERE `code` = 'delete_yourself_error'");
          $__pcpin_upgrade['session']->_db_query("UPDATE `".PCPIN_DB_PREFIX."config` SET `_conf_description` = REPLACE( `_conf_description`, '\r\n{LNG_SETTING_IGNORED_IN_SLAVE_MODE}', '' )");
          $__pcpin_upgrade['session']->_db_query("DELETE FROM `".PCPIN_DB_PREFIX."language_expression` WHERE `code` = 'setting_ignored_in_slave_mode'");
          $__pcpin_upgrade['session']->_db_query("INSERT INTO `".PCPIN_DB_PREFIX."language_expression` ( `language_id`, `code`, `value`, `multi_row` ) SELECT DISTINCT `".PCPIN_DB_PREFIX."language_expression`.`language_id` AS `language_id`, 'default_value' AS `code`, 0x44656661756c742076616c7565 AS `value`, 'n' AS `multi_row` FROM `".PCPIN_DB_PREFIX."language_expression`");
          $__pcpin_upgrade['session']->_db_query("INSERT INTO `".PCPIN_DB_PREFIX."language_expression` ( `language_id`, `code`, `value`, `multi_row` ) SELECT DISTINCT `".PCPIN_DB_PREFIX."language_expression`.`language_id` AS `language_id`, 'custom_profile_fields' AS `code`, 0x437573746f6d2070726f66696c65206669656c6473 AS `value`, 'n' AS `multi_row` FROM `".PCPIN_DB_PREFIX."language_expression`");
          $__pcpin_upgrade['session']->_db_query("INSERT INTO `".PCPIN_DB_PREFIX."language_expression` ( `language_id`, `code`, `value`, `multi_row` ) SELECT DISTINCT `".PCPIN_DB_PREFIX."language_expression`.`language_id` AS `language_id`, 'type' AS `code`, 0x54797065 AS `value`, 'n' AS `multi_row` FROM `".PCPIN_DB_PREFIX."language_expression`");
          $__pcpin_upgrade['session']->_db_query("INSERT INTO `".PCPIN_DB_PREFIX."language_expression` ( `language_id`, `code`, `value`, `multi_row` ) SELECT DISTINCT `".PCPIN_DB_PREFIX."language_expression`.`language_id` AS `language_id`, 'writeable' AS `code`, 0x577269746561626c65 AS `value`, 'n' AS `multi_row` FROM `".PCPIN_DB_PREFIX."language_expression`");
          $__pcpin_upgrade['session']->_db_query("INSERT INTO `".PCPIN_DB_PREFIX."language_expression` ( `language_id`, `code`, `value`, `multi_row` ) SELECT DISTINCT `".PCPIN_DB_PREFIX."language_expression`.`language_id` AS `language_id`, 'visibility' AS `code`, 0x5669736962696c697479 AS `value`, 'n' AS `multi_row` FROM `".PCPIN_DB_PREFIX."language_expression`");
          $__pcpin_upgrade['session']->_db_query("INSERT INTO `".PCPIN_DB_PREFIX."language_expression` ( `language_id`, `code`, `value`, `multi_row` ) SELECT DISTINCT `".PCPIN_DB_PREFIX."language_expression`.`language_id` AS `language_id`, 'choices' AS `code`, 0x43686f69636573 AS `value`, 'n' AS `multi_row` FROM `".PCPIN_DB_PREFIX."language_expression`");
          $__pcpin_upgrade['session']->_db_query("INSERT INTO `".PCPIN_DB_PREFIX."language_expression` ( `language_id`, `code`, `value`, `multi_row` ) SELECT DISTINCT `".PCPIN_DB_PREFIX."language_expression`.`language_id` AS `language_id`, 'create_new_field' AS `code`, 0x437265617465206e6577206669656c64 AS `value`, 'n' AS `multi_row` FROM `".PCPIN_DB_PREFIX."language_expression`");
          $__pcpin_upgrade['session']->_db_query("INSERT INTO `".PCPIN_DB_PREFIX."language_expression` ( `language_id`, `code`, `value`, `multi_row` ) SELECT DISTINCT `".PCPIN_DB_PREFIX."language_expression`.`language_id` AS `language_id`, 'simple_choice' AS `code`, 0x53696d706c652063686f696365 AS `value`, 'n' AS `multi_row` FROM `".PCPIN_DB_PREFIX."language_expression`");
          $__pcpin_upgrade['session']->_db_query("INSERT INTO `".PCPIN_DB_PREFIX."language_expression` ( `language_id`, `code`, `value`, `multi_row` ) SELECT DISTINCT `".PCPIN_DB_PREFIX."language_expression`.`language_id` AS `language_id`, 'textarea' AS `code`, 0x5465787461726561 AS `value`, 'n' AS `multi_row` FROM `".PCPIN_DB_PREFIX."language_expression`");
          $__pcpin_upgrade['session']->_db_query("INSERT INTO `".PCPIN_DB_PREFIX."language_expression` ( `language_id`, `code`, `value`, `multi_row` ) SELECT DISTINCT `".PCPIN_DB_PREFIX."language_expression`.`language_id` AS `language_id`, 'single_text_field' AS `code`, 0x53696e676c652074657874206669656c64 AS `value`, 'n' AS `multi_row` FROM `".PCPIN_DB_PREFIX."language_expression`");
          $__pcpin_upgrade['session']->_db_query("INSERT INTO `".PCPIN_DB_PREFIX."language_expression` ( `language_id`, `code`, `value`, `multi_row` ) SELECT DISTINCT `".PCPIN_DB_PREFIX."language_expression`.`language_id` AS `language_id`, 'multiple_choice' AS `code`, 0x4d756c7469706c652063686f696365 AS `value`, 'n' AS `multi_row` FROM `".PCPIN_DB_PREFIX."language_expression`");
          $__pcpin_upgrade['session']->_db_query("INSERT INTO `".PCPIN_DB_PREFIX."language_expression` ( `language_id`, `code`, `value`, `multi_row` ) SELECT DISTINCT `".PCPIN_DB_PREFIX."language_expression`.`language_id` AS `language_id`, 'profile_owner' AS `code`, 0x50726f66696c65206f776e6572 AS `value`, 'n' AS `multi_row` FROM `".PCPIN_DB_PREFIX."language_expression`");
          $__pcpin_upgrade['session']->_db_query("INSERT INTO `".PCPIN_DB_PREFIX."language_expression` ( `language_id`, `code`, `value`, `multi_row` ) SELECT DISTINCT `".PCPIN_DB_PREFIX."language_expression`.`language_id` AS `language_id`, 'sure_delete_field' AS `code`, 0x41726520796f75207375726520796f752077616e7420746f2064656c6574652074686973206669656c643f AS `value`, 'n' AS `multi_row` FROM `".PCPIN_DB_PREFIX."language_expression`");
          $__pcpin_upgrade['session']->_db_query("INSERT INTO `".PCPIN_DB_PREFIX."language_expression` ( `language_id`, `code`, `value`, `multi_row` ) SELECT DISTINCT `".PCPIN_DB_PREFIX."language_expression`.`language_id` AS `language_id`, 'field_deleted' AS `code`, 0x4669656c642064656c65746564 AS `value`, 'n' AS `multi_row` FROM `".PCPIN_DB_PREFIX."language_expression`");
          $__pcpin_upgrade['session']->_db_query("INSERT INTO `".PCPIN_DB_PREFIX."language_expression` ( `language_id`, `code`, `value`, `multi_row` ) SELECT DISTINCT `".PCPIN_DB_PREFIX."language_expression`.`language_id` AS `language_id`, 'field_choices_description' AS `code`, 0x456e74657220796f7572206f7074696f6e7320686572652c206576657279206f7074696f6e20696e206f6e65206c696e65 AS `value`, 'n' AS `multi_row` FROM `".PCPIN_DB_PREFIX."language_expression`");
          $__pcpin_upgrade['session']->_db_query("INSERT INTO `".PCPIN_DB_PREFIX."language_expression` ( `language_id`, `code`, `value`, `multi_row` ) SELECT DISTINCT `".PCPIN_DB_PREFIX."language_expression`.`language_id` AS `language_id`, 'no_options_specified' AS `code`, 0x506c656173652073706563696679206174206c65617374206f6e65206f7074696f6e AS `value`, 'n' AS `multi_row` FROM `".PCPIN_DB_PREFIX."language_expression`");
          $__pcpin_upgrade['session']->_db_query("INSERT INTO `".PCPIN_DB_PREFIX."language_expression` ( `language_id`, `code`, `value`, `multi_row` ) SELECT DISTINCT `".PCPIN_DB_PREFIX."language_expression`.`language_id` AS `language_id`, 'field_created' AS `code`, 0x4669656c642063726561746564 AS `value`, 'n' AS `multi_row` FROM `".PCPIN_DB_PREFIX."language_expression`");
          $__pcpin_upgrade['session']->_db_query("INSERT INTO `".PCPIN_DB_PREFIX."language_expression` ( `language_id`, `code`, `value`, `multi_row` ) SELECT DISTINCT `".PCPIN_DB_PREFIX."language_expression`.`language_id` AS `language_id`, 'edit_field' AS `code`, 0x45646974206669656c64 AS `value`, 'n' AS `multi_row` FROM `".PCPIN_DB_PREFIX."language_expression`");

          // 0000409: Store IP address in message log
          // http://bugs.pcpin.com/view.php?id=409
          $__pcpin_upgrade['session']->_db_query("ALTER TABLE `".PCPIN_DB_PREFIX."message_log` ADD `author_ip` VARCHAR( 15 ) NOT NULL default '' AFTER `author_id`");
          $__pcpin_upgrade['session']->_db_query("ALTER TABLE `".PCPIN_DB_PREFIX."message_log` ADD INDEX ( `author_ip` )");
          
          // 0000406: Database performance optimisation
          // http://bugs.pcpin.com/view.php?id=406
          $__pcpin_upgrade['session']->_db_query("ALTER TABLE `".PCPIN_DB_PREFIX."attachment` CHANGE `filename` `filename` VARCHAR( 255 ) CHARACTER SET utf8 default '' NOT NULL");
          $__pcpin_upgrade['session']->_db_query("ALTER TABLE `".PCPIN_DB_PREFIX."badword` CHANGE `word` `word` VARCHAR( 255 ) CHARACTER SET utf8 default '' NOT NULL , CHANGE `replacement` `replacement` VARCHAR( 255 ) CHARACTER SET utf8 default '' NOT NULL");
          $__pcpin_upgrade['session']->_db_query("ALTER TABLE `".PCPIN_DB_PREFIX."banner` CHANGE `name` `name` VARCHAR( 255 ) CHARACTER SET utf8 default '' NOT NULL");
          $__pcpin_upgrade['session']->_db_query("ALTER TABLE `".PCPIN_DB_PREFIX."binaryfile` CHANGE `mime_type` `mime_type` VARCHAR( 255 ) CHARACTER SET utf8 default '' NOT NULL , CHANGE `protected` `protected` VARCHAR( 255 ) CHARACTER SET utf8 default '' NOT NULL");
          $__pcpin_upgrade['session']->_db_query("ALTER TABLE `".PCPIN_DB_PREFIX."category` CHANGE `name` `name` VARCHAR( 255 ) CHARACTER SET utf8 default '' NOT NULL");
          $__pcpin_upgrade['session']->_db_query("ALTER TABLE `".PCPIN_DB_PREFIX."config` CHANGE `_conf_subgroup` `_conf_subgroup` VARCHAR( 255 ) CHARACTER SET utf8 default '' NOT NULL , CHANGE `_conf_name` `_conf_name` VARCHAR( 255 ) CHARACTER SET utf8 default '' NOT NULL , CHANGE `_conf_type` `_conf_type` VARCHAR( 255 ) CHARACTER SET utf8 default '' NOT NULL");
          $__pcpin_upgrade['session']->_db_query("ALTER TABLE `".PCPIN_DB_PREFIX."disallowed_name` CHANGE `name` `name` VARCHAR( 255 ) CHARACTER SET utf8 default '' NOT NULL");
          $__pcpin_upgrade['session']->_db_query("ALTER TABLE `".PCPIN_DB_PREFIX."failed_login` CHANGE `ip` `ip` VARCHAR( 15 ) NOT NULL");
          $__pcpin_upgrade['session']->_db_query("ALTER TABLE `".PCPIN_DB_PREFIX."invitation` CHANGE `author_nickname` `author_nickname` VARCHAR( 255 ) CHARACTER SET utf8 default '' NOT NULL , CHANGE `room_name` `room_name` VARCHAR( 255 ) CHARACTER SET utf8 default '' NOT NULL");
          $__pcpin_upgrade['session']->_db_query("ALTER TABLE `".PCPIN_DB_PREFIX."language` CHANGE `name` `name` VARCHAR( 255 ) CHARACTER SET utf8 default '' NOT NULL , CHANGE `local_name` `local_name` VARCHAR( 255 ) CHARACTER SET utf8 default '' NOT NULL");
          $__pcpin_upgrade['session']->_db_query("ALTER TABLE `".PCPIN_DB_PREFIX."language_expression` CHANGE `code` `code` VARCHAR( 255 ) CHARACTER SET utf8 default '' NOT NULL");
          $__pcpin_upgrade['session']->_db_query("ALTER TABLE `".PCPIN_DB_PREFIX."message` CHANGE `author_nickname` `author_nickname` VARCHAR( 255 ) CHARACTER SET utf8 default '' NOT NULL , CHANGE `css_properties` `css_properties` VARCHAR( 255 ) CHARACTER SET utf8 default '' NOT NULL");
          $__pcpin_upgrade['session']->_db_query("ALTER TABLE `".PCPIN_DB_PREFIX."message_log` CHANGE `category_name` `category_name` VARCHAR( 255 ) CHARACTER SET utf8 default '' NOT NULL , CHANGE `room_name` `room_name` VARCHAR( 255 ) CHARACTER SET utf8 default '' NOT NULL , CHANGE `target_category_name` `target_category_name` VARCHAR( 255 ) CHARACTER SET utf8 default '' NOT NULL , CHANGE `target_room_name` `target_room_name` VARCHAR( 255 ) CHARACTER SET utf8 default '' NOT NULL , CHANGE `author_ip` `author_ip` VARCHAR( 15 ) CHARACTER SET utf8 default '' NOT NULL , CHANGE `author_nickname` `author_nickname` VARCHAR( 255 ) CHARACTER SET utf8 default '' NOT NULL , CHANGE `target_user_nickname` `target_user_nickname` VARCHAR( 255 ) CHARACTER SET utf8 default '' NOT NULL , CHANGE `css_properties` `css_properties` VARCHAR( 255 ) CHARACTER SET utf8 default '' NOT NULL");
          $__pcpin_upgrade['session']->_db_query("ALTER TABLE `".PCPIN_DB_PREFIX."message_log_attachment` CHANGE `filename` `filename` VARCHAR( 255 ) CHARACTER SET utf8 default '' NOT NULL , CHANGE `mime_type` `mime_type` VARCHAR( 255 ) CHARACTER SET utf8 default '' NOT NULL");
          $__pcpin_upgrade['session']->_db_query("ALTER TABLE `".PCPIN_DB_PREFIX."nickname` CHANGE `nickname` `nickname` VARCHAR( 255 ) CHARACTER SET utf8 default '' NOT NULL , CHANGE `nickname_plain` `nickname_plain` VARCHAR( 255 ) CHARACTER SET utf8 default '' NOT NULL");
          $__pcpin_upgrade['session']->_db_query("ALTER TABLE `".PCPIN_DB_PREFIX."room` CHANGE `name` `name` VARCHAR( 255 ) CHARACTER SET utf8 default '' NOT NULL , CHANGE `description` `description` VARCHAR( 255 ) CHARACTER SET utf8 default '' NOT NULL");
          $__pcpin_upgrade['session']->_db_query("ALTER TABLE `".PCPIN_DB_PREFIX."session` CHANGE `_s_security_code` `_s_security_code` VARCHAR( 255 ) CHARACTER SET utf8 default '' NOT NULL , CHANGE `_s_client_agent_name` `_s_client_agent_name` VARCHAR( 255 ) CHARACTER SET utf8 default '' NOT NULL , CHANGE `_s_client_agent_version` `_s_client_agent_version` VARCHAR( 255 ) CHARACTER SET utf8 default '' NOT NULL , CHANGE `_s_client_os` `_s_client_os` VARCHAR( 255 ) CHARACTER SET utf8 default '' NOT NULL , CHANGE `_s_online_status_message` `_s_online_status_message` VARCHAR( 255 ) CHARACTER SET utf8 default '' NOT NULL");
          $__pcpin_upgrade['session']->_db_query("ALTER TABLE `".PCPIN_DB_PREFIX."smilie` CHANGE `code` `code` VARCHAR( 32 ) CHARACTER SET utf8 default '' NOT NULL , CHANGE `description` `description` VARCHAR( 255 ) CHARACTER SET utf8 default '' NOT NULL");
          $__pcpin_upgrade['session']->_db_query("ALTER TABLE `".PCPIN_DB_PREFIX."tmpdata` CHANGE `filename` `filename` VARCHAR( 255 ) CHARACTER SET utf8 default '' NOT NULL");
          $__pcpin_upgrade['session']->_db_query("ALTER TABLE `".PCPIN_DB_PREFIX."user` CHANGE `login` `login` VARCHAR( 30 ) CHARACTER SET utf8 default '' NOT NULL , CHANGE `email` `email` VARCHAR( 255 ) CHARACTER SET utf8 default '' NOT NULL , CHANGE `email_new` `email_new` VARCHAR( 255 ) CHARACTER SET utf8 default '' NOT NULL , CHANGE `email_new_activation_code` `email_new_activation_code` VARCHAR( 32 ) CHARACTER SET utf8 default '' NOT NULL , CHANGE `activation_code` `activation_code` VARCHAR( 255 ) CHARACTER SET utf8 default '' NOT NULL , CHANGE `date_format` `date_format` VARCHAR( 255 ) CHARACTER SET utf8 default '' NOT NULL , CHANGE `banned_by_username` `banned_by_username` VARCHAR( 255 ) CHARACTER SET utf8 default '' NOT NULL , CHANGE `ban_reason` `ban_reason` VARCHAR( 255 ) CHARACTER SET utf8 default '' NOT NULL , CHANGE `global_muted_by_username` `global_muted_by_username` VARCHAR( 255 ) CHARACTER SET utf8 default '' NOT NULL , CHANGE `global_muted_reason` `global_muted_reason` VARCHAR( 255 ) CHARACTER SET utf8 default '' NOT NULL");
          $__pcpin_upgrade['session']->_db_query("ALTER TABLE `".PCPIN_DB_PREFIX."version` CHANGE `new_version_url` `new_version_url` VARCHAR( 255 ) CHARACTER SET utf8 default '' NOT NULL");

        break;

      }
      // All versions: Clear database table data cache
      $__pcpin_upgrade['session']->_db_query("TRUNCATE TABLE `".PCPIN_DB_PREFIX."cache`");
      // All versions: Store new version number
      $__pcpin_upgrade['session']->_db_query('DELETE FROM `'.PCPIN_DB_PREFIX.'version`');
      $__pcpin_upgrade['session']->_db_query('INSERT INTO `'.PCPIN_DB_PREFIX.'version` ( `version`, `version_check_key`, `last_version_check` ) VALUES ( "'.$__pcpin_upgrade['session']->_db_escapeStr($__pcpin_upgrade['file_version'], false).'", "-", "'.date('Y-m-d H:i:s').'" )');
    }
  } else {
    die('Fatal error: Your installation is broken. Reinstall needed!');
  }

}


?>