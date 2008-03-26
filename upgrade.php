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

      break;

    }
    // All versions: Store new version number
    $__pcpin_upgrade['session']->_db_query('DELETE FROM `'.PCPIN_DB_PREFIX.'version`');
    $__pcpin_upgrade['session']->_db_query('INSERT INTO `'.PCPIN_DB_PREFIX.'version` ( `version`, `version_check_key`, `last_version_check` ) VALUES ( "'.$__pcpin_upgrade['session']->_db_escapeStr($__pcpin_upgrade['file_version'], false).'", "-", NOW() )');
  }
} else {
  die('Fatal error: Your installation is broken. Reinstall needed!');
}

unset($__pcpin_upgrade);

// Trying to delete this file
@unlink('./upgrade.php');
if (file_exists('./upgrade.php')) {
  die('<html><body><center><br /><br /><br /><br /><h3>Upgrade completed.</h3><br />Please delete file <b>upgrade.php</b> in order to continue.</center></body></html>');
}


?>