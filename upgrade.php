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

// No direct access
if (!defined('PCPIN_UPGRADE')) {
  header('Location: ./index.php');
  die();
}

_pcpin_loadClass('config');
_pcpin_loadClass('session');
_pcpin_loadClass('version');
_pcpin_loadClass('user');

$__pcpin_upgrade=array();
$__pcpin_upgrade['init_class']=$__pcpin_init_class; // copy, not reference!
$__pcpin_upgrade['init_class']->_conf_all=array(1); // just a dummy
$__pcpin_upgrade['session']=new PCPIN_Session($__pcpin_upgrade['init_class'], '', true);
$__pcpin_upgrade['user']=new PCPIN_User($__pcpin_upgrade['session']);

if (isset($_POST['_pcpin_update_query'])) {
  _pcpin_upgrade_check_auth();
  $query=trim(base64_decode($_POST['_pcpin_update_query']));
  $query=trim($query, ';');
  $query=trim($query);
  if ($query!='') {
    $__pcpin_upgrade['session']->_db_query($query);
  }
?>
<html><body onload="window.parent.make_update_step()"></body></html>
<?php
  die();
} else {
  _pcpin_get_installed_version();
  define('PCPIN_UPGRADE_NEW_VERSION', 6.23);
  define('PCPIN_UPGRADE_INSTALLED_VERSION', $__pcpin_upgrade['db_version']);
  if (PCPIN_UPGRADE_INSTALLED_VERSION==0) {
    die('Fatal error: Your installation is broken. Reinstall needed!');
  }
  if (PCPIN_UPGRADE_INSTALLED_VERSION>=PCPIN_UPGRADE_NEW_VERSION) {
    die('Installed PCPIN Chat version is already up to date. Delete file <b>upgrade.php</b> now!');
  }

  _pcpin_upgrade_check_auth();

  $queries=_pcpin_get_upgrade_queries();

  $tables=$__pcpin_upgrade['session']->_db_listTables();
  foreach ($tables as $table) {
    array_unshift($queries, 'REPAIR TABLE `'.$table.'`');
    array_push($queries, 'OPTIMIZE TABLE `'.$table.'`');
  }

?>
<html>
<head>
  <title>PCPIN Chat Upgrade</title>
  <script type="text/javascript">
    var sql_queries=new Array();
    var percents_per_step=100/<?php echo count($queries); ?>;
    var current_update_step=0;
    var update_step_timeout=0;
    var update_step_form=null;
    var update_step_form_input=null;
    var progress_image=null;
    var progress_grey_image=null;
<?php
  foreach ($queries as $query) {
?>
    sql_queries.push('<?php echo base64_encode($query); ?>');
<?php
  }
?>

    function startUpgrade() {
      if (confirm('Have you secured all your data?')) {
        document.getElementById('upgrade_intro').style.display='none';
        document.getElementById('start_update_btn').style.display='none';
        document.getElementById('update_progress').style.display='';
        update_step_form=document.getElementById('update_step_form');
        update_step_form_input=document.getElementById('_pcpin_update_query');
        progress_image=document.getElementById('progress_image');
        progress_grey_image=document.getElementById('progress_grey_image');
        make_update_step();
      }
    }

    function show_update_progress() {
      var progress=Math.round(current_update_step*percents_per_step);
      var html='';
      if (progress<100) html+=' ';
      if (progress<10) html+=' ';
      html+=progress+'% [';
      for (var i=0; i<100; i++) {
        html+=((i<progress)? '#' : '-');
      }
      html+=']';
      document.getElementById('update_progress_bar').innerHTML=html;
    }

    function make_update_step() {
      clearTimeout(update_step_timeout);
      if (sql_queries.length>current_update_step) {
        progress_image.style.display='none';
        progress_grey_image.style.display='';
        show_update_progress();
        update_step_form_input.value=sql_queries[current_update_step++];
        setTimeout('progress_grey_image.style.display=\'none\'; progress_image.style.display=\'\'; update_step_form.submit();', 500);
        update_step_timeout=setTimeout('make_update_step()', 60500);
      } else {
        document.getElementById('update_progress').style.display='none';
        document.getElementById('upgrade_completed').style.display='';
      }
    }

  </script>
</head>
<body>
<div style="width:100%;text-align:center;">
  <h4>Welcome to PCPIN Chat Upgrade!</h4>
  <br /><br />
  <div id="upgrade_intro">
    PCPIN Chat version <b><?php echo htmlspecialchars(number_format(PCPIN_UPGRADE_INSTALLED_VERSION, 2, '.', '')); ?></b> was detected on your server
  </div>
  <br /><br /><br />
  <button id="start_update_btn" type="button" onclick="startUpgrade()" title="Start upgrade!">Upgrade PCPIN Chat to version <?php echo htmlspecialchars(number_format(PCPIN_UPGRADE_NEW_VERSION, 2, '.', '')); ?></button>
  <div id="update_progress" style="display:none;text-align:center;">
    Upgrade process may take several minutes. <b>DO NOT</b> interrupt it!
    <br />
    <pre id="update_progress_bar"></pre>
    <br />
    <img id="progress_image" src="./pic/progress_16x16.gif" alt="" border="0" />
    <img id="progress_grey_image" src="./pic/progress_grey_16x16.gif" alt="" border="0" style="display:none" />
  </div>
  <div id="upgrade_completed" style="display:none">
    <br /><br />
    <h3>Upgrade completed.</h3>
    <br /><br />
    Please delete file <b>upgrade.php</b> now.
  </div>
</div>
<form id="update_step_form" method="post" target="_updater_frame" action="#">
<input type="hidden" name="_pcpin_update_query" id="_pcpin_update_query" value="" />
</form>
<iframe src="./dummy.html" name="_updater_frame" width="1" height="1" frameborder="0" scrolling="No" style="border:0px"></iframe>
</body></html>
<?php
}

function _pcpin_get_installed_version() {
  global $__pcpin_upgrade;
  $__pcpin_upgrade['version']=new PCPIN_Version($__pcpin_upgrade['session']);
  if ($__pcpin_upgrade['version']->_db_getList('version', 'version DESC', 1)) {
    $__pcpin_upgrade['db_version']=$__pcpin_upgrade['version']->_db_list[0]['version'];
    $__pcpin_upgrade['version']->_db_freeList();
  } else {
    $__pcpin_upgrade['db_version']=0;
  }
}


function _pcpin_upgrade_check_auth() {
  global $__pcpin_upgrade;
  $auth_ok=false;
  if (   isset($_SERVER['PHP_AUTH_USER']) && is_scalar($_SERVER['PHP_AUTH_USER']) && $_SERVER['PHP_AUTH_USER']!=''
      && isset($_SERVER['PHP_AUTH_PW']) && is_scalar($_SERVER['PHP_AUTH_PW'])
      && $__pcpin_upgrade['user']->_db_getList('id', 'login =# '.$_SERVER['PHP_AUTH_USER'], 'password = '.md5($_SERVER['PHP_AUTH_PW']), 1)
      ) {
    $__pcpin_upgrade['user']->_db_freeList();
    $auth_ok=true;
  }
  if(!$auth_ok) {
    header('WWW-Authenticate: Basic realm="Enter administrator username and password"');
    header('HTTP/1.0 401 Unauthorized');
    header('status: 401 Unauthorized');
    die('<a href="./index.php"><h1>Enter administrator username and password</h1></a>');
  }
}


function _pcpin_get_upgrade_queries() {
  $queries=array();

  switch (PCPIN_UPGRADE_INSTALLED_VERSION) {

    case 6.00:
    case 6.01:
      // PCPIN Chat 6.02: Add more font sizes. See http://bugs.pcpin.com/view.php?id=224
      $queries[]="UPDATE `".PCPIN_DB_PREFIX."config` SET `_conf_choices` = '9=9|10=10|11=11|12=12|13=13|14=14|15=15|16=16|17=17|18=18|19=19|20=20' WHERE `_conf_name` = 'default_font_size' LIMIT 1";
      $queries[]="UPDATE `".PCPIN_DB_PREFIX."config` SET `_conf_choices` = '9=9|10=10|11=11|12=12|13=13|14=14|15=15|16=16|17=17|18=18|19=19|20=20' WHERE `_conf_name` = 'font_sizes' LIMIT 1";
    case 6.02:
    case 6.03:
    case 6.04:
    case 6.05:
    case 6.06:
    case 6.07:
      // PCPIN Chat 6.10

      // Save failed login attempts. See http://bugs.pcpin.com/view.php?id=297
      $queries[]="DROP TABLE IF EXISTS `".PCPIN_DB_PREFIX."failed_login`";
      $queries[]="CREATE TABLE IF NOT EXISTS `".PCPIN_DB_PREFIX."failed_login` ( `ip` varchar(15) NOT NULL default '', `count` int(11) default 0 NOT NULL, PRIMARY KEY  (`ip`) ) TYPE=MyISAM";
      $queries[]="INSERT INTO `".PCPIN_DB_PREFIX."config` ( `_conf_group` , `_conf_subgroup` , `_conf_name` , `_conf_value` , `_conf_type` , `_conf_choices` , `_conf_description` ) VALUES ( 'security', '{LNG_LOG_IN}', 'ip_failed_login_limit', '10', 'int_range', '0|*', '{LNG__CONF_IP_FAILED_LOGIN_LIMIT}' )";
      $queries[]="INSERT INTO `".PCPIN_DB_PREFIX."config` ( `_conf_group` , `_conf_subgroup` , `_conf_name` , `_conf_value` , `_conf_type` , `_conf_choices` , `_conf_description` ) VALUES ( 'security', '{LNG_LOG_IN}', 'ip_failed_login_ban', '3', 'int_range', '1|*', '{LNG__CONF_IP_FAILED_LOGIN_BAN}' )";
      $queries[]="INSERT INTO `".PCPIN_DB_PREFIX."language_expression` ( `language_id`, `code`, `value`, `multi_row` ) SELECT DISTINCT `".PCPIN_DB_PREFIX."language_expression`.`language_id` AS `language_id`, '_conf_ip_failed_login_limit' AS `code`, 0x416674657220686f77206d616e79206661696c6564206c6f6720696e20617474656d7074732062616e20736f7572636520495020616464726573733f0d0a303a20446f206e6f742062616e AS `value`, 'y' AS `multi_row` FROM `".PCPIN_DB_PREFIX."language_expression`";
      $queries[]="INSERT INTO `".PCPIN_DB_PREFIX."language_expression` ( `language_id`, `code`, `value`, `multi_row` ) SELECT DISTINCT `".PCPIN_DB_PREFIX."language_expression`.`language_id` AS `language_id`, '_conf_ip_failed_login_ban' AS `code`, 0x466f7220686f77206d616e7920686f7572732062616e2049502061646472657373657320616674657220746f6f206d616e79206661696c6564206c6f67696e20617474656d7074733f AS `value`, 'n' AS `multi_row` FROM `".PCPIN_DB_PREFIX."language_expression`";
      $queries[]="INSERT INTO `".PCPIN_DB_PREFIX."language_expression` ( `language_id`, `code`, `value`, `multi_row` ) SELECT DISTINCT `".PCPIN_DB_PREFIX."language_expression`.`language_id` AS `language_id`, 'too_many_failed_logins' AS `code`, 0x546f6f206d616e79206661696c6564206c6f67696e20617474656d707473 AS `value`, 'n' AS `multi_row` FROM `".PCPIN_DB_PREFIX."language_expression`";
      $queries[]="ALTER TABLE `".PCPIN_DB_PREFIX."language_expression` ORDER BY `language_id` ASC, `code` ASC";
      $queries[]="ALTER TABLE `".PCPIN_DB_PREFIX."user`  ADD `language_id` INT NOT NULL DEFAULT 0";

      // Force logout when user close browser window. See http://bugs.pcpin.com/view.php?id=314
      $queries[]="ALTER TABLE `".PCPIN_DB_PREFIX."session` ADD `_s_page_unloaded` ENUM( 'n', 'y' ) DEFAULT 'n' NOT NULL";
      $queries[]="ALTER TABLE `".PCPIN_DB_PREFIX."session` ADD INDEX ( `_s_page_unloaded` )";
      $queries[]="ALTER TABLE `".PCPIN_DB_PREFIX."session` ADD INDEX ( `_s_backend` )";
      $queries[]="UPDATE `".PCPIN_DB_PREFIX."config` SET `_conf_choices` = '10|120' WHERE `_conf_name` = 'session_timeout' LIMIT 1";
      $queries[]="UPDATE `".PCPIN_DB_PREFIX."config` SET `_conf_value` = '30' WHERE `_conf_name` = 'session_timeout' AND `_conf_value` > '30' LIMIT 1";
      $queries[]="UPDATE `".PCPIN_DB_PREFIX."config` SET `_conf_choices` = '1|20' WHERE `_conf_name` = 'updater_interval' LIMIT 1";
      $queries[]="UPDATE `".PCPIN_DB_PREFIX."config` SET `_conf_value` = '3' WHERE `_conf_name` = 'updater_interval' AND `_conf_value` > '3' LIMIT 1";

      // Event sounds. See http://bugs.pcpin.com/view.php?id=329
      $queries[]="INSERT INTO `".PCPIN_DB_PREFIX."language_expression` ( `language_id`, `code`, `value`, `multi_row` ) SELECT DISTINCT `".PCPIN_DB_PREFIX."language_expression`.`language_id` AS `language_id`, '_conf_allow_sounds' AS `code`, 0x416c6c6f7720736f756e6420656666656374733f AS `value`, 'n' AS `multi_row` FROM `".PCPIN_DB_PREFIX."language_expression`";
      $queries[]="INSERT INTO `".PCPIN_DB_PREFIX."language_expression` ( `language_id`, `code`, `value`, `multi_row` ) SELECT DISTINCT `".PCPIN_DB_PREFIX."language_expression`.`language_id` AS `language_id`, 'sounds' AS `code`, 0x536f756e6473 AS `value`, 'n' AS `multi_row` FROM `".PCPIN_DB_PREFIX."language_expression`";
      $queries[]="INSERT INTO `".PCPIN_DB_PREFIX."config` ( `_conf_group` , `_conf_subgroup` , `_conf_name` , `_conf_value` , `_conf_type` , `_conf_choices` , `_conf_description` ) VALUES ( 'chat', '{LNG_SOUNDS}', 'allow_sounds', '0', 'boolean_choice', '1={LNG_YES}|0={LNG_NO}', '{LNG__CONF_ALLOW_SOUNDS}' )";

      // 0000361: Performance optimisation
      // http://bugs.pcpin.com/view.php?id=361
      $queries[]="ALTER TABLE `".PCPIN_DB_PREFIX."language_expression` DROP PRIMARY KEY";
      $queries[]="ALTER TABLE `".PCPIN_DB_PREFIX."language_expression` ADD INDEX ( `language_id` )";
      $queries[]="ALTER TABLE `".PCPIN_DB_PREFIX."config` ORDER BY `_conf_group` ASC, `_conf_subgroup` ASC, `_conf_id` ASC";
      $queries[]="CREATE TABLE `".PCPIN_DB_PREFIX."cache` ( `id` CHAR( 255 ) NOT NULL , `contents` LONGBLOB NOT NULL , PRIMARY KEY ( `id` ) )";
    break;

    case 6.10:
    case 6.11:
      // PCPIN Chat *.* ==> PCPIN Chat 6.20

      // 0000367: Wrong encoded characters in database tables
      // http://bugs.pcpin.com/view.php?id=367
      $queries[]="ALTER TABLE `".PCPIN_DB_PREFIX."attachment` DEFAULT CHARACTER SET utf8";
      $queries[]="ALTER TABLE `".PCPIN_DB_PREFIX."avatar` DEFAULT CHARACTER SET utf8";
      $queries[]="ALTER TABLE `".PCPIN_DB_PREFIX."badword` DEFAULT CHARACTER SET utf8";
      $queries[]="ALTER TABLE `".PCPIN_DB_PREFIX."banner` DEFAULT CHARACTER SET utf8";
      $queries[]="ALTER TABLE `".PCPIN_DB_PREFIX."binaryfile` DEFAULT CHARACTER SET utf8";
      $queries[]="ALTER TABLE `".PCPIN_DB_PREFIX."cache` DEFAULT CHARACTER SET utf8";
      $queries[]="ALTER TABLE `".PCPIN_DB_PREFIX."category` DEFAULT CHARACTER SET utf8";
      $queries[]="ALTER TABLE `".PCPIN_DB_PREFIX."config` DEFAULT CHARACTER SET utf8";
      $queries[]="ALTER TABLE `".PCPIN_DB_PREFIX."disallowed_name` DEFAULT CHARACTER SET utf8";
      $queries[]="ALTER TABLE `".PCPIN_DB_PREFIX."failed_login` DEFAULT CHARACTER SET utf8";
      $queries[]="ALTER TABLE `".PCPIN_DB_PREFIX."invitation` DEFAULT CHARACTER SET utf8";
      $queries[]="ALTER TABLE `".PCPIN_DB_PREFIX."ipfilter` DEFAULT CHARACTER SET utf8";
      $queries[]="ALTER TABLE `".PCPIN_DB_PREFIX."language` DEFAULT CHARACTER SET utf8";
      $queries[]="ALTER TABLE `".PCPIN_DB_PREFIX."language_expression` DEFAULT CHARACTER SET utf8";
      $queries[]="ALTER TABLE `".PCPIN_DB_PREFIX."message` DEFAULT CHARACTER SET utf8";
      $queries[]="ALTER TABLE `".PCPIN_DB_PREFIX."message_log` DEFAULT CHARACTER SET utf8";
      $queries[]="ALTER TABLE `".PCPIN_DB_PREFIX."message_log_attachment` DEFAULT CHARACTER SET utf8";
      $queries[]="ALTER TABLE `".PCPIN_DB_PREFIX."nickname` DEFAULT CHARACTER SET utf8";
      $queries[]="ALTER TABLE `".PCPIN_DB_PREFIX."room` DEFAULT CHARACTER SET utf8";
      $queries[]="ALTER TABLE `".PCPIN_DB_PREFIX."session` DEFAULT CHARACTER SET utf8";
      $queries[]="ALTER TABLE `".PCPIN_DB_PREFIX."smilie` DEFAULT CHARACTER SET utf8";
      $queries[]="ALTER TABLE `".PCPIN_DB_PREFIX."tmpdata` DEFAULT CHARACTER SET utf8";
      $queries[]="ALTER TABLE `".PCPIN_DB_PREFIX."user` DEFAULT CHARACTER SET utf8";
      $queries[]="ALTER TABLE `".PCPIN_DB_PREFIX."userdata` DEFAULT CHARACTER SET utf8";
      $queries[]="ALTER TABLE `".PCPIN_DB_PREFIX."version` DEFAULT CHARACTER SET utf8";

      // 0000369: A possibility to turn sounds off/on for users
      // http://bugs.pcpin.com/view.php?id=369
      $queries[]="ALTER TABLE `".PCPIN_DB_PREFIX."user` ADD `allow_sounds` ENUM( 'y', 'n' ) DEFAULT 'y' NOT NULL";

      // 0000374: "Optimize Database" menu in Administrator area
      // http://bugs.pcpin.com/view.php?id=374
      $queries[]="INSERT INTO `".PCPIN_DB_PREFIX."language_expression` ( `language_id`, `code`, `value`, `multi_row` ) SELECT DISTINCT `".PCPIN_DB_PREFIX."language_expression`.`language_id` AS `language_id`, 'optimize_database' AS `code`, 0x4f7074696d697a65206461746162617365 AS `value`, 'n' AS `multi_row` FROM `".PCPIN_DB_PREFIX."language_expression`";

      // 0000382: Store last used room selection view setting
      // http://bugs.pcpin.com/view.php?id=382
      $queries[]="ALTER TABLE `".PCPIN_DB_PREFIX."user` ADD `room_selection_view` ENUM( 's', 'a' ) DEFAULT 's' NOT NULL";
      $queries[]="UPDATE `".PCPIN_DB_PREFIX."config` SET `_conf_type` = 'string_choice', `_conf_choices` = 'a={LNG_ADVANCED_VIEW}|s={LNG_SIMPLIFIED_VIEW}' WHERE `_conf_id` = 46 LIMIT 1";
      $queries[]="UPDATE `".PCPIN_DB_PREFIX."config` SET `_conf_value` = IF( `_conf_value` = '0', 'a', 's' ) WHERE `_conf_id` = 46 LIMIT 1";

      // 0000327: More user management features in admin area
      // http://bugs.pcpin.com/view.php?id=327
      $queries[]="INSERT INTO `".PCPIN_DB_PREFIX."language_expression` ( `language_id`, `code`, `value`, `multi_row` ) SELECT DISTINCT `".PCPIN_DB_PREFIX."language_expression`.`language_id` AS `language_id`, 'add_new_user' AS `code`, 0x416464206e65772075736572 AS `value`, 'n' AS `multi_row` FROM `".PCPIN_DB_PREFIX."language_expression`";
      $queries[]="INSERT INTO `".PCPIN_DB_PREFIX."language_expression` ( `language_id`, `code`, `value`, `multi_row` ) SELECT DISTINCT `".PCPIN_DB_PREFIX."language_expression`.`language_id` AS `language_id`, 'new_user_added' AS `code`, 0x4e6577207573657220686173206265656e2063726561746564207375636365737366756c6c79 AS `value`, 'n' AS `multi_row` FROM `".PCPIN_DB_PREFIX."language_expression`";
      $queries[]="INSERT INTO `".PCPIN_DB_PREFIX."language_expression` ( `language_id`, `code`, `value`, `multi_row` ) SELECT DISTINCT `".PCPIN_DB_PREFIX."language_expression`.`language_id` AS `language_id`, '_conf_allow_user_registration' AS `code`, 0x416c6c6f77206163636f756e7420726567697374726174696f6e3f AS `value`, 'n' AS `multi_row` FROM `".PCPIN_DB_PREFIX."language_expression`";
      $queries[]="INSERT INTO `".PCPIN_DB_PREFIX."config` ( `_conf_id` , `_conf_group` , `_conf_subgroup` , `_conf_name` , `_conf_value` , `_conf_type` , `_conf_choices` , `_conf_description` ) VALUES ( '', 'account', '{LNG_USERS}', 'allow_user_registration', '1', 'boolean_choice', '1={LNG_YES}|0={LNG_NO}', '{LNG__CONF_ALLOW_USER_REGISTRATION}\r\n{LNG_SETTING_IGNORED_IN_SLAVE_MODE}' )";
      $queries[]="UPDATE `".PCPIN_DB_PREFIX."config` SET `_conf_type` = 'int_choice', `_conf_choices` = '0={LNG_NO}|1={LNG_YES}: {LNG_ACTIVATION_EMAIL}|2={LNG_YES}: {LNG_ACTIVATION_BY_ADMIN}' WHERE `_conf_name` = 'activate_new_accounts' LIMIT 1";
      $queries[]="INSERT INTO `".PCPIN_DB_PREFIX."language_expression` ( `language_id`, `code`, `value`, `multi_row` ) SELECT DISTINCT `".PCPIN_DB_PREFIX."language_expression`.`language_id` AS `language_id`, 'activation_email' AS `code`, 0x41637469766174696f6e20656d61696c AS `value`, 'n' AS `multi_row` FROM `".PCPIN_DB_PREFIX."language_expression`";
      $queries[]="INSERT INTO `".PCPIN_DB_PREFIX."language_expression` ( `language_id`, `code`, `value`, `multi_row` ) SELECT DISTINCT `".PCPIN_DB_PREFIX."language_expression`.`language_id` AS `language_id`, 'activation_by_admin' AS `code`, 0x41637469766174696f6e2062792041646d696e AS `value`, 'n' AS `multi_row` FROM `".PCPIN_DB_PREFIX."language_expression`";
      $queries[]="INSERT INTO `".PCPIN_DB_PREFIX."language_expression` ( `language_id`, `code`, `value`, `multi_row` ) SELECT DISTINCT `".PCPIN_DB_PREFIX."language_expression`.`language_id` AS `language_id`, 'email_new_user_activation_notification' AS `code`, 0x48656c6c6f2c0d0a0d0a54686520666f6c6c6f77696e67206163636f756e74206174205b434841545f4e414d455d20686173206265656e206372656174656420616e64206e6565647320746f206265206163746976617465643a0d0a0d0a557365726e616d653a205b555345524e414d455d0d0a452d4d61696c20616464726573733a205b454d41494c5f414444524553535d0d0a52656d6f746520495020616464726573733a205b52454d4f54455f49505d0d0a0d0a0d0a2d2d2d2d2d2d2d2d2d2d2d2d2d2d2d2d2d2d2d2d2d2d2d2d2d2d2d0d0a5468616e6b20596f752c0d0a5b53454e4445525d AS `value`, 'y' AS `multi_row` FROM `".PCPIN_DB_PREFIX."language_expression`";
      $queries[]="INSERT INTO `".PCPIN_DB_PREFIX."language_expression` ( `language_id`, `code`, `value`, `multi_row` ) SELECT DISTINCT `".PCPIN_DB_PREFIX."language_expression`.`language_id` AS `language_id`, 'account_will_be_activated_by_admin' AS `code`, 0x596f7572206163636f756e7420686173206265656e206372656174656420616e642073656e7420746f207468652041646d696e6973747261746f7220666f722061637469766174696f6e2e AS `value`, 'n' AS `multi_row` FROM `".PCPIN_DB_PREFIX."language_expression`";

      // 0000333: Automatically display enlarged thumbnail of user's profile image in the userlist on mouseover
      // http://bugs.pcpin.com/view.php?id=333
      $queries[]="ALTER TABLE `".PCPIN_DB_PREFIX."tmpdata` CHANGE `filename` `filename` char(255) default '' NOT NULL";

      // 0000363: Typo in German language pack
      // http://bugs.pcpin.com/view.php?id=363
      $queries[]="UPDATE `".PCPIN_DB_PREFIX."language_expression` AS `le` LEFT JOIN `".PCPIN_DB_PREFIX."language` AS `la` ON `la`.`id` = `le`.`language_id` SET `le`.`value` = 0x47442d556e7465727374c3bc747a756e6720616b746976696572656e3f WHERE `le`.`code` = '_conf_allow_gd' AND `la`.`iso_name` = 'de'";

      // 0000393: Typo in German language pack
      // http://bugs.pcpin.com/view.php?id=393
      $queries[]="UPDATE `".PCPIN_DB_PREFIX."language_expression` AS `le` LEFT JOIN `".PCPIN_DB_PREFIX."language` AS `la` ON `la`.`id` = `le`.`language_id` SET `le`.`value` = 'Sie sind bereits eingeloggt' WHERE `le`.`code` = 'you_already_logged_in' AND `la`.`iso_name` = 'de'";
      $queries[]="UPDATE `".PCPIN_DB_PREFIX."language_expression` AS `le` LEFT JOIN `".PCPIN_DB_PREFIX."language` AS `la` ON `la`.`id` = `le`.`language_id` SET `le`.`value` = 'Benutzer [USER] ist zur Zeit nicht eingeloggt' WHERE `le`.`code` = 'user_is_not_logged_in' AND `la`.`iso_name` = 'de'";
      $queries[]="UPDATE `".PCPIN_DB_PREFIX."language_expression` AS `le` LEFT JOIN `".PCPIN_DB_PREFIX."language` AS `la` ON `la`.`id` = `le`.`language_id` SET `le`.`value` = 'Benutzer [USER] ist zur Zeit eingeloggt' WHERE `le`.`code` = 'user_is_logged_in' AND `la`.`iso_name` = 'de'";
      $queries[]="UPDATE `".PCPIN_DB_PREFIX."language_expression` AS `le` LEFT JOIN `".PCPIN_DB_PREFIX."language` AS `la` ON `la`.`id` = `le`.`language_id` SET `le`.`value` = '/exitroom\nDen Raum verlassen, aber eingeloggt bleiben.' WHERE `le`.`code` = 'cmd_help_exitroom' AND `la`.`iso_name` = 'de'";

      // Customizable user profile fields
      // http://bugs.pcpin.com/view.php?id=325
      $queries[]="CREATE TABLE `".PCPIN_DB_PREFIX."userdata_field` ( `id` int(10) unsigned NOT NULL auto_increment, `name` varchar(255) NOT NULL default '', `default_value` text NOT NULL, `custom` enum('y','n') default 'y', `type` enum('string','text','url','email','choice','multichoice') NOT NULL default 'text', `choices` text NOT NULL, `visibility` enum('public','registered','moderator','admin') NOT NULL default 'public', `writeable` enum('user','admin') NOT NULL default 'user', `order` int(10) unsigned NOT NULL, `disabled` enum('n','y') NOT NULL default 'n', PRIMARY KEY  (`id`), KEY `name` (`name`), KEY `visibility` (`visibility`), KEY `order` (`order`), KEY `disabled` (`disabled`) ) TYPE=MyISAM DEFAULT CHARSET=utf8 PACK_KEYS=0";
      $queries[]="INSERT INTO `".PCPIN_DB_PREFIX."userdata_field` (`id`, `name`, `default_value`, `custom`, `type`, `choices`, `visibility`, `order`) VALUES ('1', 'homepage', '', 'n', 'url', '', 'public', '0'), ('2', 'gender', '-', 'n', 'choice', '-\nm\nf', 'public', '1'), ('3', 'age', '', 'n', 'string', '', 'public', '2'), ('4', 'messenger_icq', '', 'n', 'string', '', 'public', '3'), ('5', 'messenger_msn', '', 'n', 'string', '', 'public', '4'), ('6', 'messenger_aim', '', 'n', 'string', '', 'public', '5'), ('7', 'messenger_yim', '', 'n', 'string', '', 'public', '6'), ('8', 'location', '', 'n', 'string', '', 'public', '7'), ('9', 'occupation', '', 'n', 'string', '', 'public', '8'), ('10', 'interests', '', 'n', 'text', '', 'public', '9')";
      $queries[]="CREATE TABLE `".PCPIN_DB_PREFIX."userdata_tmp` ( `user_id` INT NOT NULL DEFAULT '0' , `field_id` INT UNSIGNED DEFAULT '0' NOT NULL , `field_value` TEXT NOT NULL , INDEX ( `user_id` ) , INDEX ( `field_id` ) )";
      $queries[]="INSERT INTO `".PCPIN_DB_PREFIX."userdata_tmp` ( `user_id`, `field_id`, `field_value` ) SELECT `user_id`, '1', `homepage` FROM `".PCPIN_DB_PREFIX."userdata`";
      $queries[]="INSERT INTO `".PCPIN_DB_PREFIX."userdata_tmp` ( `user_id`, `field_id`, `field_value` ) SELECT `user_id`, '2', `gender` FROM `".PCPIN_DB_PREFIX."userdata`";
      $queries[]="INSERT INTO `".PCPIN_DB_PREFIX."userdata_tmp` ( `user_id`, `field_id`, `field_value` ) SELECT `user_id`, '3', `age` FROM `".PCPIN_DB_PREFIX."userdata`";
      $queries[]="INSERT INTO `".PCPIN_DB_PREFIX."userdata_tmp` ( `user_id`, `field_id`, `field_value` ) SELECT `user_id`, '4', `icq` FROM `".PCPIN_DB_PREFIX."userdata`";
      $queries[]="INSERT INTO `".PCPIN_DB_PREFIX."userdata_tmp` ( `user_id`, `field_id`, `field_value` ) SELECT `user_id`, '5', `msn` FROM `".PCPIN_DB_PREFIX."userdata`";
      $queries[]="INSERT INTO `".PCPIN_DB_PREFIX."userdata_tmp` ( `user_id`, `field_id`, `field_value` ) SELECT `user_id`, '6', `aim` FROM `".PCPIN_DB_PREFIX."userdata`";
      $queries[]="INSERT INTO `".PCPIN_DB_PREFIX."userdata_tmp` ( `user_id`, `field_id`, `field_value` ) SELECT `user_id`, '7', `yim` FROM `".PCPIN_DB_PREFIX."userdata`";
      $queries[]="INSERT INTO `".PCPIN_DB_PREFIX."userdata_tmp` ( `user_id`, `field_id`, `field_value` ) SELECT `user_id`, '8', `location` FROM `".PCPIN_DB_PREFIX."userdata`";
      $queries[]="INSERT INTO `".PCPIN_DB_PREFIX."userdata_tmp` ( `user_id`, `field_id`, `field_value` ) SELECT `user_id`, '9', `occupation` FROM `".PCPIN_DB_PREFIX."userdata`";
      $queries[]="INSERT INTO `".PCPIN_DB_PREFIX."userdata_tmp` ( `user_id`, `field_id`, `field_value` ) SELECT `user_id`, '10', `interests` FROM `".PCPIN_DB_PREFIX."userdata`";
      $queries[]="DROP TABLE `".PCPIN_DB_PREFIX."userdata`";
      $queries[]="ALTER TABLE `".PCPIN_DB_PREFIX."userdata_tmp` RENAME `".PCPIN_DB_PREFIX."userdata`";
      $queries[]="INSERT INTO `".PCPIN_DB_PREFIX."language_expression` ( `language_id`, `code`, `value`, `multi_row` ) SELECT DISTINCT `".PCPIN_DB_PREFIX."language_expression`.`language_id` AS `language_id`, 'ignore_list' AS `code`, 0x49676e6f7265206c697374 AS `value`, 'n' AS `multi_row` FROM `".PCPIN_DB_PREFIX."language_expression`";
      $queries[]="DELETE FROM `".PCPIN_DB_PREFIX."language_expression` WHERE `code` = 'select_new_level_or_cancel'";
      $queries[]="DELETE FROM `".PCPIN_DB_PREFIX."language_expression` WHERE `code` = 'delete_yourself_error'";
      $queries[]="UPDATE `".PCPIN_DB_PREFIX."config` SET `_conf_description` = REPLACE( `_conf_description`, '\r\n{LNG_SETTING_IGNORED_IN_SLAVE_MODE}', '' )";
      $queries[]="DELETE FROM `".PCPIN_DB_PREFIX."language_expression` WHERE `code` = 'setting_ignored_in_slave_mode'";
      $queries[]="INSERT INTO `".PCPIN_DB_PREFIX."language_expression` ( `language_id`, `code`, `value`, `multi_row` ) SELECT DISTINCT `".PCPIN_DB_PREFIX."language_expression`.`language_id` AS `language_id`, 'default_value' AS `code`, 0x44656661756c742076616c7565 AS `value`, 'n' AS `multi_row` FROM `".PCPIN_DB_PREFIX."language_expression`";
      $queries[]="INSERT INTO `".PCPIN_DB_PREFIX."language_expression` ( `language_id`, `code`, `value`, `multi_row` ) SELECT DISTINCT `".PCPIN_DB_PREFIX."language_expression`.`language_id` AS `language_id`, 'custom_profile_fields' AS `code`, 0x437573746f6d2070726f66696c65206669656c6473 AS `value`, 'n' AS `multi_row` FROM `".PCPIN_DB_PREFIX."language_expression`";
      $queries[]="INSERT INTO `".PCPIN_DB_PREFIX."language_expression` ( `language_id`, `code`, `value`, `multi_row` ) SELECT DISTINCT `".PCPIN_DB_PREFIX."language_expression`.`language_id` AS `language_id`, 'type' AS `code`, 0x54797065 AS `value`, 'n' AS `multi_row` FROM `".PCPIN_DB_PREFIX."language_expression`";
      $queries[]="INSERT INTO `".PCPIN_DB_PREFIX."language_expression` ( `language_id`, `code`, `value`, `multi_row` ) SELECT DISTINCT `".PCPIN_DB_PREFIX."language_expression`.`language_id` AS `language_id`, 'writeable' AS `code`, 0x577269746561626c65 AS `value`, 'n' AS `multi_row` FROM `".PCPIN_DB_PREFIX."language_expression`";
      $queries[]="INSERT INTO `".PCPIN_DB_PREFIX."language_expression` ( `language_id`, `code`, `value`, `multi_row` ) SELECT DISTINCT `".PCPIN_DB_PREFIX."language_expression`.`language_id` AS `language_id`, 'visibility' AS `code`, 0x5669736962696c697479 AS `value`, 'n' AS `multi_row` FROM `".PCPIN_DB_PREFIX."language_expression`";
      $queries[]="INSERT INTO `".PCPIN_DB_PREFIX."language_expression` ( `language_id`, `code`, `value`, `multi_row` ) SELECT DISTINCT `".PCPIN_DB_PREFIX."language_expression`.`language_id` AS `language_id`, 'choices' AS `code`, 0x43686f69636573 AS `value`, 'n' AS `multi_row` FROM `".PCPIN_DB_PREFIX."language_expression`";
      $queries[]="INSERT INTO `".PCPIN_DB_PREFIX."language_expression` ( `language_id`, `code`, `value`, `multi_row` ) SELECT DISTINCT `".PCPIN_DB_PREFIX."language_expression`.`language_id` AS `language_id`, 'create_new_field' AS `code`, 0x437265617465206e6577206669656c64 AS `value`, 'n' AS `multi_row` FROM `".PCPIN_DB_PREFIX."language_expression`";
      $queries[]="INSERT INTO `".PCPIN_DB_PREFIX."language_expression` ( `language_id`, `code`, `value`, `multi_row` ) SELECT DISTINCT `".PCPIN_DB_PREFIX."language_expression`.`language_id` AS `language_id`, 'simple_choice' AS `code`, 0x53696d706c652063686f696365 AS `value`, 'n' AS `multi_row` FROM `".PCPIN_DB_PREFIX."language_expression`";
      $queries[]="INSERT INTO `".PCPIN_DB_PREFIX."language_expression` ( `language_id`, `code`, `value`, `multi_row` ) SELECT DISTINCT `".PCPIN_DB_PREFIX."language_expression`.`language_id` AS `language_id`, 'textarea' AS `code`, 0x5465787461726561 AS `value`, 'n' AS `multi_row` FROM `".PCPIN_DB_PREFIX."language_expression`";
      $queries[]="INSERT INTO `".PCPIN_DB_PREFIX."language_expression` ( `language_id`, `code`, `value`, `multi_row` ) SELECT DISTINCT `".PCPIN_DB_PREFIX."language_expression`.`language_id` AS `language_id`, 'single_text_field' AS `code`, 0x53696e676c652074657874206669656c64 AS `value`, 'n' AS `multi_row` FROM `".PCPIN_DB_PREFIX."language_expression`";
      $queries[]="INSERT INTO `".PCPIN_DB_PREFIX."language_expression` ( `language_id`, `code`, `value`, `multi_row` ) SELECT DISTINCT `".PCPIN_DB_PREFIX."language_expression`.`language_id` AS `language_id`, 'multiple_choice' AS `code`, 0x4d756c7469706c652063686f696365 AS `value`, 'n' AS `multi_row` FROM `".PCPIN_DB_PREFIX."language_expression`";
      $queries[]="INSERT INTO `".PCPIN_DB_PREFIX."language_expression` ( `language_id`, `code`, `value`, `multi_row` ) SELECT DISTINCT `".PCPIN_DB_PREFIX."language_expression`.`language_id` AS `language_id`, 'profile_owner' AS `code`, 0x50726f66696c65206f776e6572 AS `value`, 'n' AS `multi_row` FROM `".PCPIN_DB_PREFIX."language_expression`";
      $queries[]="INSERT INTO `".PCPIN_DB_PREFIX."language_expression` ( `language_id`, `code`, `value`, `multi_row` ) SELECT DISTINCT `".PCPIN_DB_PREFIX."language_expression`.`language_id` AS `language_id`, 'sure_delete_field' AS `code`, 0x41726520796f75207375726520796f752077616e7420746f2064656c6574652074686973206669656c643f AS `value`, 'n' AS `multi_row` FROM `".PCPIN_DB_PREFIX."language_expression`";
      $queries[]="INSERT INTO `".PCPIN_DB_PREFIX."language_expression` ( `language_id`, `code`, `value`, `multi_row` ) SELECT DISTINCT `".PCPIN_DB_PREFIX."language_expression`.`language_id` AS `language_id`, 'field_deleted' AS `code`, 0x4669656c642064656c65746564 AS `value`, 'n' AS `multi_row` FROM `".PCPIN_DB_PREFIX."language_expression`";
      $queries[]="INSERT INTO `".PCPIN_DB_PREFIX."language_expression` ( `language_id`, `code`, `value`, `multi_row` ) SELECT DISTINCT `".PCPIN_DB_PREFIX."language_expression`.`language_id` AS `language_id`, 'field_choices_description' AS `code`, 0x456e74657220796f7572206f7074696f6e7320686572652c206576657279206f7074696f6e20696e206f6e65206c696e65 AS `value`, 'n' AS `multi_row` FROM `".PCPIN_DB_PREFIX."language_expression`";
      $queries[]="INSERT INTO `".PCPIN_DB_PREFIX."language_expression` ( `language_id`, `code`, `value`, `multi_row` ) SELECT DISTINCT `".PCPIN_DB_PREFIX."language_expression`.`language_id` AS `language_id`, 'no_options_specified' AS `code`, 0x506c656173652073706563696679206174206c65617374206f6e65206f7074696f6e AS `value`, 'n' AS `multi_row` FROM `".PCPIN_DB_PREFIX."language_expression`";
      $queries[]="INSERT INTO `".PCPIN_DB_PREFIX."language_expression` ( `language_id`, `code`, `value`, `multi_row` ) SELECT DISTINCT `".PCPIN_DB_PREFIX."language_expression`.`language_id` AS `language_id`, 'field_created' AS `code`, 0x4669656c642063726561746564 AS `value`, 'n' AS `multi_row` FROM `".PCPIN_DB_PREFIX."language_expression`";
      $queries[]="INSERT INTO `".PCPIN_DB_PREFIX."language_expression` ( `language_id`, `code`, `value`, `multi_row` ) SELECT DISTINCT `".PCPIN_DB_PREFIX."language_expression`.`language_id` AS `language_id`, 'edit_field' AS `code`, 0x45646974206669656c64 AS `value`, 'n' AS `multi_row` FROM `".PCPIN_DB_PREFIX."language_expression`";

      // 0000409: Store IP address in message log
      // http://bugs.pcpin.com/view.php?id=409
      $queries[]="ALTER TABLE `".PCPIN_DB_PREFIX."message_log` ADD `author_ip` VARCHAR( 15 ) NOT NULL default '' AFTER `author_id`";
      $queries[]="ALTER TABLE `".PCPIN_DB_PREFIX."message_log` ADD INDEX ( `author_ip` )";

      // 0000406: Database performance optimisation
      // http://bugs.pcpin.com/view.php?id=406
      $queries[]="ALTER TABLE `".PCPIN_DB_PREFIX."attachment` CHANGE `filename` `filename` VARCHAR( 255 ) CHARACTER SET utf8 default '' NOT NULL";
      $queries[]="ALTER TABLE `".PCPIN_DB_PREFIX."badword` CHANGE `word` `word` VARCHAR( 255 ) CHARACTER SET utf8 default '' NOT NULL , CHANGE `replacement` `replacement` VARCHAR( 255 ) CHARACTER SET utf8 default '' NOT NULL";
      $queries[]="ALTER TABLE `".PCPIN_DB_PREFIX."banner` CHANGE `name` `name` VARCHAR( 255 ) CHARACTER SET utf8 default '' NOT NULL";
      $queries[]="ALTER TABLE `".PCPIN_DB_PREFIX."binaryfile` CHANGE `mime_type` `mime_type` VARCHAR( 255 ) CHARACTER SET utf8 default '' NOT NULL , CHANGE `protected` `protected` VARCHAR( 255 ) CHARACTER SET utf8 default '' NOT NULL";
      $queries[]="ALTER TABLE `".PCPIN_DB_PREFIX."category` CHANGE `name` `name` VARCHAR( 255 ) CHARACTER SET utf8 default '' NOT NULL";
      $queries[]="ALTER TABLE `".PCPIN_DB_PREFIX."config` CHANGE `_conf_subgroup` `_conf_subgroup` VARCHAR( 255 ) CHARACTER SET utf8 default '' NOT NULL , CHANGE `_conf_name` `_conf_name` VARCHAR( 255 ) CHARACTER SET utf8 default '' NOT NULL , CHANGE `_conf_type` `_conf_type` VARCHAR( 255 ) CHARACTER SET utf8 default '' NOT NULL";
      $queries[]="ALTER TABLE `".PCPIN_DB_PREFIX."disallowed_name` CHANGE `name` `name` VARCHAR( 255 ) CHARACTER SET utf8 default '' NOT NULL";
      $queries[]="ALTER TABLE `".PCPIN_DB_PREFIX."failed_login` CHANGE `ip` `ip` VARCHAR( 15 ) NOT NULL";
      $queries[]="ALTER TABLE `".PCPIN_DB_PREFIX."invitation` CHANGE `author_nickname` `author_nickname` VARCHAR( 255 ) CHARACTER SET utf8 default '' NOT NULL , CHANGE `room_name` `room_name` VARCHAR( 255 ) CHARACTER SET utf8 default '' NOT NULL";
      $queries[]="ALTER TABLE `".PCPIN_DB_PREFIX."language` CHANGE `name` `name` VARCHAR( 255 ) CHARACTER SET utf8 default '' NOT NULL , CHANGE `local_name` `local_name` VARCHAR( 255 ) CHARACTER SET utf8 default '' NOT NULL";
      $queries[]="ALTER TABLE `".PCPIN_DB_PREFIX."language_expression` CHANGE `code` `code` VARCHAR( 255 ) CHARACTER SET utf8 default '' NOT NULL";
      $queries[]="ALTER TABLE `".PCPIN_DB_PREFIX."message` CHANGE `author_nickname` `author_nickname` VARCHAR( 255 ) CHARACTER SET utf8 default '' NOT NULL , CHANGE `css_properties` `css_properties` VARCHAR( 255 ) CHARACTER SET utf8 default '' NOT NULL";
      $queries[]="ALTER TABLE `".PCPIN_DB_PREFIX."message_log` CHANGE `category_name` `category_name` VARCHAR( 255 ) CHARACTER SET utf8 default '' NOT NULL , CHANGE `room_name` `room_name` VARCHAR( 255 ) CHARACTER SET utf8 default '' NOT NULL , CHANGE `target_category_name` `target_category_name` VARCHAR( 255 ) CHARACTER SET utf8 default '' NOT NULL , CHANGE `target_room_name` `target_room_name` VARCHAR( 255 ) CHARACTER SET utf8 default '' NOT NULL , CHANGE `author_ip` `author_ip` VARCHAR( 15 ) CHARACTER SET utf8 default '' NOT NULL , CHANGE `author_nickname` `author_nickname` VARCHAR( 255 ) CHARACTER SET utf8 default '' NOT NULL , CHANGE `target_user_nickname` `target_user_nickname` VARCHAR( 255 ) CHARACTER SET utf8 default '' NOT NULL , CHANGE `css_properties` `css_properties` VARCHAR( 255 ) CHARACTER SET utf8 default '' NOT NULL";
      $queries[]="ALTER TABLE `".PCPIN_DB_PREFIX."message_log_attachment` CHANGE `filename` `filename` VARCHAR( 255 ) CHARACTER SET utf8 default '' NOT NULL , CHANGE `mime_type` `mime_type` VARCHAR( 255 ) CHARACTER SET utf8 default '' NOT NULL";
      $queries[]="ALTER TABLE `".PCPIN_DB_PREFIX."nickname` CHANGE `nickname` `nickname` VARCHAR( 255 ) CHARACTER SET utf8 default '' NOT NULL , CHANGE `nickname_plain` `nickname_plain` VARCHAR( 255 ) CHARACTER SET utf8 default '' NOT NULL";
      $queries[]="ALTER TABLE `".PCPIN_DB_PREFIX."room` CHANGE `name` `name` VARCHAR( 255 ) CHARACTER SET utf8 default '' NOT NULL , CHANGE `description` `description` VARCHAR( 255 ) CHARACTER SET utf8 default '' NOT NULL";
      $queries[]="ALTER TABLE `".PCPIN_DB_PREFIX."session` CHANGE `_s_security_code` `_s_security_code` VARCHAR( 255 ) CHARACTER SET utf8 default '' NOT NULL , CHANGE `_s_client_agent_name` `_s_client_agent_name` VARCHAR( 255 ) CHARACTER SET utf8 default '' NOT NULL , CHANGE `_s_client_agent_version` `_s_client_agent_version` VARCHAR( 255 ) CHARACTER SET utf8 default '' NOT NULL , CHANGE `_s_client_os` `_s_client_os` VARCHAR( 255 ) CHARACTER SET utf8 default '' NOT NULL , CHANGE `_s_online_status_message` `_s_online_status_message` VARCHAR( 255 ) CHARACTER SET utf8 default '' NOT NULL";
      $queries[]="ALTER TABLE `".PCPIN_DB_PREFIX."smilie` CHANGE `code` `code` VARCHAR( 32 ) CHARACTER SET utf8 default '' NOT NULL , CHANGE `description` `description` VARCHAR( 255 ) CHARACTER SET utf8 default '' NOT NULL";
      $queries[]="ALTER TABLE `".PCPIN_DB_PREFIX."tmpdata` CHANGE `filename` `filename` VARCHAR( 255 ) CHARACTER SET utf8 default '' NOT NULL";
      $queries[]="ALTER TABLE `".PCPIN_DB_PREFIX."user` CHANGE `login` `login` VARCHAR( 30 ) CHARACTER SET utf8 default '' NOT NULL , CHANGE `email` `email` VARCHAR( 255 ) CHARACTER SET utf8 default '' NOT NULL , CHANGE `email_new` `email_new` VARCHAR( 255 ) CHARACTER SET utf8 default '' NOT NULL , CHANGE `email_new_activation_code` `email_new_activation_code` VARCHAR( 32 ) CHARACTER SET utf8 default '' NOT NULL , CHANGE `activation_code` `activation_code` VARCHAR( 255 ) CHARACTER SET utf8 default '' NOT NULL , CHANGE `date_format` `date_format` VARCHAR( 255 ) CHARACTER SET utf8 default '' NOT NULL , CHANGE `banned_by_username` `banned_by_username` VARCHAR( 255 ) CHARACTER SET utf8 default '' NOT NULL , CHANGE `ban_reason` `ban_reason` VARCHAR( 255 ) CHARACTER SET utf8 default '' NOT NULL , CHANGE `global_muted_by_username` `global_muted_by_username` VARCHAR( 255 ) CHARACTER SET utf8 default '' NOT NULL , CHANGE `global_muted_reason` `global_muted_reason` VARCHAR( 255 ) CHARACTER SET utf8 default '' NOT NULL";
      $queries[]="ALTER TABLE `".PCPIN_DB_PREFIX."version` CHANGE `new_version_url` `new_version_url` VARCHAR( 255 ) CHARACTER SET utf8 default '' NOT NULL";

      // 0000316: Flood protection
      // http://bugs.pcpin.com/view.php?id=316
      $queries[]="INSERT INTO `".PCPIN_DB_PREFIX."language_expression` ( `language_id`, `code`, `value`, `multi_row` ) SELECT DISTINCT `".PCPIN_DB_PREFIX."language_expression`.`language_id` AS `language_id`, 'flood_protection_max_messages' AS `code`, 0x5468697320636861742068617320736f6d65206b696e64206f6620666c6f6f642070726f74656374696f6e2077686963682064657465637473206966207468652073616d652070687261736520697320706f7374656420616761696e20616e6420616761696e206279207468652073616d6520757365722e20506c6561736520656e74657220686f77206d616e79206f6620746869732073616d6520706872617365732062792061207573657220796f752077616e7420746f20616c6c6f77206265666f72652068652067657473206d757465642e AS `value`, 'n' AS `multi_row` FROM `".PCPIN_DB_PREFIX."language_expression`";
      $queries[]="INSERT INTO `".PCPIN_DB_PREFIX."language_expression` ( `language_id`, `code`, `value`, `multi_row` ) SELECT DISTINCT `".PCPIN_DB_PREFIX."language_expression`.`language_id` AS `language_id`, 'flood_protection_mute_time' AS `code`, 0x466f7220686f77206d616e79207365636f6e6473206d75746520757365727320666f7220666c6f6f64696e673f AS `value`, 'n' AS `multi_row` FROM `".PCPIN_DB_PREFIX."language_expression`";
      $queries[]="INSERT INTO `".PCPIN_DB_PREFIX."language_expression` ( `language_id`, `code`, `value`, `multi_row` ) SELECT DISTINCT `".PCPIN_DB_PREFIX."language_expression`.`language_id` AS `language_id`, 'flood_protection_message_delay' AS `code`, 0x4d696e696d756d2074696d6520706572696f6420696e207365636f6e6473206265747765656e2074776f206d657373616765732066726f6d207468652073616d6520757365722e AS `value`, 'n' AS `multi_row` FROM `".PCPIN_DB_PREFIX."language_expression`";
      $queries[]="INSERT INTO `".PCPIN_DB_PREFIX."language_expression` ( `language_id`, `code`, `value`, `multi_row` ) SELECT DISTINCT `".PCPIN_DB_PREFIX."language_expression`.`language_id` AS `language_id`, 'flooding' AS `code`, 0x466c6f6f64696e67 AS `value`, 'n' AS `multi_row` FROM `".PCPIN_DB_PREFIX."language_expression`";
      $queries[]="INSERT INTO `".PCPIN_DB_PREFIX."config` ( `_conf_id` , `_conf_group` , `_conf_subgroup` , `_conf_name` , `_conf_value` , `_conf_type` , `_conf_choices` , `_conf_description` )  VALUES ( '', 'chat', '{LNG_MESSAGES}', 'flood_protection_max_messages', '5', 'int_range', '3|*', '{LNG_FLOOD_PROTECTION_MAX_MESSAGES}' )";
      $queries[]="INSERT INTO `".PCPIN_DB_PREFIX."config` ( `_conf_id` , `_conf_group` , `_conf_subgroup` , `_conf_name` , `_conf_value` , `_conf_type` , `_conf_choices` , `_conf_description` )  VALUES ( '', 'chat', '{LNG_MESSAGES}', 'flood_protection_mute_time', '60', 'int_range', '1|*', '{LNG_FLOOD_PROTECTION_MUTE_TIME}' )";
      $queries[]="INSERT INTO `".PCPIN_DB_PREFIX."config` ( `_conf_id` , `_conf_group` , `_conf_subgroup` , `_conf_name` , `_conf_value` , `_conf_type` , `_conf_choices` , `_conf_description` )  VALUES ( '', 'chat', '{LNG_MESSAGES}', 'flood_protection_message_delay', '1', 'int_range', '0|*', '{LNG_FLOOD_PROTECTION_MESSAGE_DELAY}' )";
      $queries[]="ALTER TABLE `".PCPIN_DB_PREFIX."session` ADD `_s_last_sent_message_time` DATETIME DEFAULT '0000-00-00 00:00:00' NOT NULL AFTER `_s_last_message_id`, ADD `_s_last_sent_message_hash` CHAR(32) default '' NOT NULL AFTER `_s_last_sent_message_time`, ADD `_s_last_sent_message_repeats_count` int(10) unsigned NOT NULL default '0' AFTER `_s_last_sent_message_hash`";

      // Translate some new language expressions
      $queries[]="UPDATE `".PCPIN_DB_PREFIX."language` AS `la` LEFT JOIN `".PCPIN_DB_PREFIX."language_expression` AS `le` ON `le`.`language_id` = `la`.`id` SET `le`.`value` = 0x536f756e6473 WHERE `le`.`code` = 'sounds' AND `la`.`iso_name` = 'de'";
      $queries[]="UPDATE `".PCPIN_DB_PREFIX."language` AS `la` LEFT JOIN `".PCPIN_DB_PREFIX."language_expression` AS `le` ON `le`.`language_id` = `la`.`id` SET `le`.`value` = 0x416c6c6f7720736f756e6420656666656374733f WHERE `le`.`code` = '_conf_allow_sounds' AND `la`.`iso_name` = 'de'";
      $queries[]="UPDATE `".PCPIN_DB_PREFIX."language` AS `la` LEFT JOIN `".PCPIN_DB_PREFIX."language_expression` AS `le` ON `le`.`language_id` = `la`.`id` SET `le`.`value` = 0x53696368746261726b656974 WHERE `le`.`code` = 'visibility' AND `la`.`iso_name` = 'de'";
      $queries[]="UPDATE `".PCPIN_DB_PREFIX."language` AS `la` LEFT JOIN `".PCPIN_DB_PREFIX."language_expression` AS `le` ON `le`.`language_id` = `la`.`id` SET `le`.`value` = 0x547970 WHERE `le`.`code` = 'type' AND `la`.`iso_name` = 'de'";
      $queries[]="UPDATE `".PCPIN_DB_PREFIX."language` AS `la` LEFT JOIN `".PCPIN_DB_PREFIX."language_expression` AS `le` ON `le`.`language_id` = `la`.`id` SET `le`.`value` = 0x42656e75747a6572646566696e69657274652050726f66696c66656c646572 WHERE `le`.`code` = 'custom_profile_fields' AND `la`.`iso_name` = 'de'";
      $queries[]="UPDATE `".PCPIN_DB_PREFIX."language` AS `la` LEFT JOIN `".PCPIN_DB_PREFIX."language_expression` AS `le` ON `le`.`language_id` = `la`.`id` SET `le`.`value` = 0x44656661756c7477657274 WHERE `le`.`code` = 'default_value' AND `la`.`iso_name` = 'de'";
      $queries[]="UPDATE `".PCPIN_DB_PREFIX."language` AS `la` LEFT JOIN `".PCPIN_DB_PREFIX."language_expression` AS `le` ON `le`.`language_id` = `la`.`id` SET `le`.`value` = 0x426573636872656962626172 WHERE `le`.`code` = 'writeable' AND `la`.`iso_name` = 'de'";
      $queries[]="UPDATE `".PCPIN_DB_PREFIX."language` AS `la` LEFT JOIN `".PCPIN_DB_PREFIX."language_expression` AS `le` ON `le`.`language_id` = `la`.`id` SET `le`.`value` = 0x446174656e62616e6b206f7074696d696572656e WHERE `le`.`code` = 'optimize_database' AND `la`.`iso_name` = 'de'";
      $queries[]="UPDATE `".PCPIN_DB_PREFIX."language` AS `la` LEFT JOIN `".PCPIN_DB_PREFIX."language_expression` AS `le` ON `le`.`language_id` = `la`.`id` SET `le`.`value` = 0x4e6575656e2042656e75747a65722068696e7a7566c3bc67656e WHERE `le`.`code` = 'add_new_user' AND `la`.`iso_name` = 'de'";
      $queries[]="UPDATE `".PCPIN_DB_PREFIX."language` AS `la` LEFT JOIN `".PCPIN_DB_PREFIX."language_expression` AS `le` ON `le`.`language_id` = `la`.`id` SET `le`.`value` = 0x42656e75747a6572207775726465206572666f6c6772656963682068696e7a75676566c3bc6774 WHERE `le`.`code` = 'new_user_added' AND `la`.`iso_name` = 'de'";
      $queries[]="UPDATE `".PCPIN_DB_PREFIX."language` AS `la` LEFT JOIN `".PCPIN_DB_PREFIX."language_expression` AS `le` ON `le`.`language_id` = `la`.`id` SET `le`.`value` = 0x42656e75747a65722d52656769737472696572756e6720616b746976696572656e3f WHERE `le`.`code` = '_conf_allow_user_registration' AND `la`.`iso_name` = 'de'";
      $queries[]="UPDATE `".PCPIN_DB_PREFIX."language` AS `la` LEFT JOIN `".PCPIN_DB_PREFIX."language_expression` AS `le` ON `le`.`language_id` = `la`.`id` SET `le`.`value` = 0x416b746976696572756e6773656d61696c WHERE `le`.`code` = 'activation_email' AND `la`.`iso_name` = 'de'";
      $queries[]="UPDATE `".PCPIN_DB_PREFIX."language` AS `la` LEFT JOIN `".PCPIN_DB_PREFIX."language_expression` AS `le` ON `le`.`language_id` = `la`.`id` SET `le`.`value` = 0x416b746976696572756e672064757263682041646d696e6973747261746f72 WHERE `le`.`code` = 'activation_by_admin' AND `la`.`iso_name` = 'de'";
      $queries[]="UPDATE `".PCPIN_DB_PREFIX."language` AS `la` LEFT JOIN `".PCPIN_DB_PREFIX."language_expression` AS `le` ON `le`.`language_id` = `la`.`id` SET `le`.`value` = 0x48616c6c6f2c0d0a0d0a44657220666f6c67656e64652042656e75747a65726b6f6e746f20626569205b434841545f4e414d455d20777572646520616e67656d656c64657420756e64206d757373206d616e75656c6c20616b746976696572742077657264656e3a0d0a0d0a42656e75747a65726e616d653a205b555345524e414d455d0d0a456d61696c20416472657373653a20205b454d41494c5f414444524553535d0d0a495020416472657373653a2020202020205b52454d4f54455f49505d0d0a0d0a0d0a2d2d2d2d2d2d2d2d2d2d2d2d2d2d2d2d2d2d2d2d2d2d2d2d2d2d2d0d0a64616e6b652c0d0a5b53454e4445525d WHERE `le`.`code` = 'email_new_user_activation_notification' AND `la`.`iso_name` = 'de'";
      $queries[]="UPDATE `".PCPIN_DB_PREFIX."language` AS `la` LEFT JOIN `".PCPIN_DB_PREFIX."language_expression` AS `le` ON `le`.`language_id` = `la`.`id` SET `le`.`value` = 0x4968722042656e75747a65726b6f6e746f207775726465206572666f6c6772656963682065727374656c6c7420756e6420616e2041646d696e6973747261746f72207a757220416b746976696572756e6720676573656e6465742e WHERE `le`.`code` = 'account_will_be_activated_by_admin' AND `la`.`iso_name` = 'de'";
      $queries[]="UPDATE `".PCPIN_DB_PREFIX."language` AS `la` LEFT JOIN `".PCPIN_DB_PREFIX."language_expression` AS `le` ON `le`.`language_id` = `la`.`id` SET `le`.`value` = 0x49676e6f7269657274652042656e75747a6572 WHERE `le`.`code` = 'ignore_list' AND `la`.`iso_name` = 'de'";
      $queries[]="UPDATE `".PCPIN_DB_PREFIX."language` AS `la` LEFT JOIN `".PCPIN_DB_PREFIX."language_expression` AS `le` ON `le`.`language_id` = `la`.`id` SET `le`.`value` = 0x4175737761686c WHERE `le`.`code` = 'choices' AND `la`.`iso_name` = 'de'";
      $queries[]="UPDATE `".PCPIN_DB_PREFIX."language` AS `la` LEFT JOIN `".PCPIN_DB_PREFIX."language_expression` AS `le` ON `le`.`language_id` = `la`.`id` SET `le`.`value` = 0x4e657565732046656c642068696e7a7566c3bc67656e WHERE `le`.`code` = 'create_new_field' AND `la`.`iso_name` = 'de'";
      $queries[]="UPDATE `".PCPIN_DB_PREFIX."language` AS `la` LEFT JOIN `".PCPIN_DB_PREFIX."language_expression` AS `le` ON `le`.`language_id` = `la`.`id` SET `le`.`value` = 0x45696e6661636865204175737761686c WHERE `le`.`code` = 'simple_choice' AND `la`.`iso_name` = 'de'";
      $queries[]="UPDATE `".PCPIN_DB_PREFIX."language` AS `la` LEFT JOIN `".PCPIN_DB_PREFIX."language_expression` AS `le` ON `le`.`language_id` = `la`.`id` SET `le`.`value` = 0x4d6568727a65696c69676573205465787466656c64 WHERE `le`.`code` = 'textarea' AND `la`.`iso_name` = 'de'";
      $queries[]="UPDATE `".PCPIN_DB_PREFIX."language` AS `la` LEFT JOIN `".PCPIN_DB_PREFIX."language_expression` AS `le` ON `le`.`language_id` = `la`.`id` SET `le`.`value` = 0x45696e7a65696c69676573205465787466656c64 WHERE `le`.`code` = 'single_text_field' AND `la`.`iso_name` = 'de'";
      $queries[]="UPDATE `".PCPIN_DB_PREFIX."language` AS `la` LEFT JOIN `".PCPIN_DB_PREFIX."language_expression` AS `le` ON `le`.`language_id` = `la`.`id` SET `le`.`value` = 0x4d6568726661636865204175737761686c WHERE `le`.`code` = 'multiple_choice' AND `la`.`iso_name` = 'de'";
      $queries[]="UPDATE `".PCPIN_DB_PREFIX."language` AS `la` LEFT JOIN `".PCPIN_DB_PREFIX."language_expression` AS `le` ON `le`.`language_id` = `la`.`id` SET `le`.`value` = 0x50726f66696c62657369747a6572 WHERE `le`.`code` = 'profile_owner' AND `la`.`iso_name` = 'de'";
      $queries[]="UPDATE `".PCPIN_DB_PREFIX."language` AS `la` LEFT JOIN `".PCPIN_DB_PREFIX."language_expression` AS `le` ON `le`.`language_id` = `la`.`id` SET `le`.`value` = 0x576f6c6c656e20536965207769726b6c696368206469657365732046656c64206cc3b6736368656e3f WHERE `le`.`code` = 'sure_delete_field' AND `la`.`iso_name` = 'de'";
      $queries[]="UPDATE `".PCPIN_DB_PREFIX."language` AS `la` LEFT JOIN `".PCPIN_DB_PREFIX."language_expression` AS `le` ON `le`.`language_id` = `la`.`id` SET `le`.`value` = 0x4461732046656c64207775726465206572666f6c6772656963682067656cc3b673636874 WHERE `le`.`code` = 'field_deleted' AND `la`.`iso_name` = 'de'";
      $queries[]="UPDATE `".PCPIN_DB_PREFIX."language` AS `la` LEFT JOIN `".PCPIN_DB_PREFIX."language_expression` AS `le` ON `le`.`language_id` = `la`.`id` SET `le`.`value` = 0x476562656e20536965206869657220646965204f7074696f6e656e20616e3b206a656465204f7074696f6e20696e2065696e6572206e6575656e205a65696c65 WHERE `le`.`code` = 'field_choices_description' AND `la`.`iso_name` = 'de'";
      $queries[]="UPDATE `".PCPIN_DB_PREFIX."language` AS `la` LEFT JOIN `".PCPIN_DB_PREFIX."language_expression` AS `le` ON `le`.`language_id` = `la`.`id` SET `le`.`value` = 0x476562656e20536965206269747465206d696e6465737465732065696e65204175737761686c6f7074696f6e2065696e WHERE `le`.`code` = 'no_options_specified' AND `la`.`iso_name` = 'de'";
      $queries[]="UPDATE `".PCPIN_DB_PREFIX."language` AS `la` LEFT JOIN `".PCPIN_DB_PREFIX."language_expression` AS `le` ON `le`.`language_id` = `la`.`id` SET `le`.`value` = 0x4e657565732046656c64207775726465206572666f6c6772656963682068696e7a75676566c3bc6774 WHERE `le`.`code` = 'field_created' AND `la`.`iso_name` = 'de'";
      $queries[]="UPDATE `".PCPIN_DB_PREFIX."language` AS `la` LEFT JOIN `".PCPIN_DB_PREFIX."language_expression` AS `le` ON `le`.`language_id` = `la`.`id` SET `le`.`value` = 0x46656c642065646974696572656e WHERE `le`.`code` = 'edit_field' AND `la`.`iso_name` = 'de'";
      $queries[]="UPDATE `".PCPIN_DB_PREFIX."language` AS `la` LEFT JOIN `".PCPIN_DB_PREFIX."language_expression` AS `le` ON `le`.`language_id` = `la`.`id` SET `le`.`value` = 0x4469657365722043686174206861742065696e652041727420466c6f6f64696e672d53636875747a2c2064657220656e746465636b742c2077656e6e2064696573656c6265204e616368726963687420696d6d65722077696564657220766f6e2064656d73656c62656e2042656e75747a65722076657273656e64657420776972642e20576965207669656c6520576965646572686f6c756e67656e20646572204e616368726963687420776f6c6c656e20736965207a756c617373656e206265766f72206465722042656e75747a6572207374696c6c67657365747a7420776972643f WHERE `le`.`code` = 'flood_protection_max_messages' AND `la`.`iso_name` = 'de'";
      $queries[]="UPDATE `".PCPIN_DB_PREFIX."language` AS `la` LEFT JOIN `".PCPIN_DB_PREFIX."language_expression` AS `le` ON `le`.`language_id` = `la`.`id` SET `le`.`value` = 0x46c3bc7220776965207669656c2053656b756e64656e20736f6c6c656e2042656e75747a65722066c3bc7220466c6f6f64696e67207374696c6c67657365747a7420776564656e3f WHERE `le`.`code` = 'flood_protection_mute_time' AND `la`.`iso_name` = 'de'";
      $queries[]="UPDATE `".PCPIN_DB_PREFIX."language` AS `la` LEFT JOIN `".PCPIN_DB_PREFIX."language_expression` AS `le` ON `le`.`language_id` = `la`.`id` SET `le`.`value` = 0x4d696e696d616c6572205a65697461627374616e6420696e2053656b756e64656e207a7769736368656e207a776569204e6163687269636874656e20766f6e2064656d73656c62656e2042656e75747a65722e WHERE `le`.`code` = 'flood_protection_message_delay' AND `la`.`iso_name` = 'de'";
      $queries[]="UPDATE `".PCPIN_DB_PREFIX."language` AS `la` LEFT JOIN `".PCPIN_DB_PREFIX."language_expression` AS `le` ON `le`.`language_id` = `la`.`id` SET `le`.`value` = 0x466c6f6f64696e67 WHERE `le`.`code` = 'flooding' AND `la`.`iso_name` = 'de'";
      $queries[]="UPDATE `".PCPIN_DB_PREFIX."language` AS `la` LEFT JOIN `".PCPIN_DB_PREFIX."language_expression` AS `le` ON `le`.`language_id` = `la`.`id` SET `le`.`value` = 0xd094d0bed0bfd0bed0bbd0bdd0b8d182d0b5d0bbd18cd0bdd18bd0b520d0bfd0bed0bbd18f20d0b220d0bfd180d0bed184d0b8d0bbd0b5 WHERE `le`.`code` = 'custom_profile_fields' AND `la`.`iso_name` = 'ru'";
      $queries[]="UPDATE `".PCPIN_DB_PREFIX."language` AS `la` LEFT JOIN `".PCPIN_DB_PREFIX."language_expression` AS `le` ON `le`.`language_id` = `la`.`id` SET `le`.`value` = 0xd092d0bed0b7d0bcd0bed0b6d0bdd0bed181d182d18c20d0b8d0b7d0bcd0b5d0bdd0b5d0bdd0b8d18f WHERE `le`.`code` = 'writeable' AND `la`.`iso_name` = 'ru'";
      $queries[]="UPDATE `".PCPIN_DB_PREFIX."language` AS `la` LEFT JOIN `".PCPIN_DB_PREFIX."language_expression` AS `le` ON `le`.`language_id` = `la`.`id` SET `le`.`value` = 0xd097d0bdd0b0d187d0b5d0bdd0b8d0b520d0bfd0be20d183d0bcd0bed0bbd187d0b0d0bdd0b8d18e WHERE `le`.`code` = 'default_value' AND `la`.`iso_name` = 'ru'";
      $queries[]="UPDATE `".PCPIN_DB_PREFIX."language` AS `la` LEFT JOIN `".PCPIN_DB_PREFIX."language_expression` AS `le` ON `le`.`language_id` = `la`.`id` SET `le`.`value` = 0xd092d0b8d0b4d0b8d0bcd0bed181d182d18c WHERE `le`.`code` = 'visibility' AND `la`.`iso_name` = 'ru'";
      $queries[]="UPDATE `".PCPIN_DB_PREFIX."language` AS `la` LEFT JOIN `".PCPIN_DB_PREFIX."language_expression` AS `le` ON `le`.`language_id` = `la`.`id` SET `le`.`value` = 0xd090d0bad182d0b8d0b2d0b8d180d0bed0b2d0b0d182d18c20d0b7d0b2d183d0bad0bed0b2d18bd0b520d18dd184d184d0b5d0bad182d18b3f WHERE `le`.`code` = '_conf_allow_sounds' AND `la`.`iso_name` = 'ru'";
      $queries[]="UPDATE `".PCPIN_DB_PREFIX."language` AS `la` LEFT JOIN `".PCPIN_DB_PREFIX."language_expression` AS `le` ON `le`.`language_id` = `la`.`id` SET `le`.`value` = 0xd097d0b2d183d0bad0bed0b2d18bd0b520d18dd184d184d0b5d0bad182d18b WHERE `le`.`code` = 'sounds' AND `la`.`iso_name` = 'ru'";
      $queries[]="UPDATE `".PCPIN_DB_PREFIX."language` AS `la` LEFT JOIN `".PCPIN_DB_PREFIX."language_expression` AS `le` ON `le`.`language_id` = `la`.`id` SET `le`.`value` = 0xd0a2d0b8d0bf WHERE `le`.`code` = 'type' AND `la`.`iso_name` = 'ru'";
      $queries[]="UPDATE `".PCPIN_DB_PREFIX."language` AS `la` LEFT JOIN `".PCPIN_DB_PREFIX."language_expression` AS `le` ON `le`.`language_id` = `la`.`id` SET `le`.`value` = 0xd09ed0bfd182d0b8d0bcd0b8d180d0bed0b2d0b0d182d18c20d0b1d0b0d0b7d18320d0b4d0b0d0bdd0bdd18bd185 WHERE `le`.`code` = 'optimize_database' AND `la`.`iso_name` = 'ru'";
      $queries[]="UPDATE `".PCPIN_DB_PREFIX."language` AS `la` LEFT JOIN `".PCPIN_DB_PREFIX."language_expression` AS `le` ON `le`.`language_id` = `la`.`id` SET `le`.`value` = 0xd094d0bed0b1d0b0d0b2d0b8d182d18c20d0bdd0bed0b2d0bed0b3d0be20d0bfd0bed0bbd18cd0b7d0bed0b2d0b0d182d0b5d0bbd18f WHERE `le`.`code` = 'add_new_user' AND `la`.`iso_name` = 'ru'";
      $queries[]="UPDATE `".PCPIN_DB_PREFIX."language` AS `la` LEFT JOIN `".PCPIN_DB_PREFIX."language_expression` AS `le` ON `le`.`language_id` = `la`.`id` SET `le`.`value` = 0xd09dd0bed0b2d18bd0b920d0b0d0bad0bad0b0d183d0bdd18220d0b4d0bed0b1d0b0d0b2d0bbd0b5d0bd WHERE `le`.`code` = 'new_user_added' AND `la`.`iso_name` = 'ru'";
      $queries[]="UPDATE `".PCPIN_DB_PREFIX."language` AS `la` LEFT JOIN `".PCPIN_DB_PREFIX."language_expression` AS `le` ON `le`.`language_id` = `la`.`id` SET `le`.`value` = 0xd0a0d0b0d0b7d180d0b5d188d0b8d182d18c20d180d0b5d0b3d0b8d181d182d180d0b0d186d0b8d18e20d0bdd0bed0b2d18bd18520d0b0d0bad0bad0b0d183d0bdd182d0bed0b23f WHERE `le`.`code` = '_conf_allow_user_registration' AND `la`.`iso_name` = 'ru'";
      $queries[]="UPDATE `".PCPIN_DB_PREFIX."language` AS `la` LEFT JOIN `".PCPIN_DB_PREFIX."language_expression` AS `le` ON `le`.`language_id` = `la`.`id` SET `le`.`value` = 0xd090d0bad182d0b8d0b2d0b0d186d0b8d18f20d0bfd0be20452d4d61696c WHERE `le`.`code` = 'activation_email' AND `la`.`iso_name` = 'ru'";
      $queries[]="UPDATE `".PCPIN_DB_PREFIX."language` AS `la` LEFT JOIN `".PCPIN_DB_PREFIX."language_expression` AS `le` ON `le`.`language_id` = `la`.`id` SET `le`.`value` = 0xd090d0bad182d0b8d0b2d0b0d186d0b8d18f20d090d0b4d0bcd0b8d0bdd0b8d181d182d180d0b0d182d0bed180d0bed0bc2e WHERE `le`.`code` = 'activation_by_admin' AND `la`.`iso_name` = 'ru'";
      $queries[]="UPDATE `".PCPIN_DB_PREFIX."language` AS `la` LEFT JOIN `".PCPIN_DB_PREFIX."language_expression` AS `le` ON `le`.`language_id` = `la`.`id` SET `le`.`value` = 0xd097d0b4d180d0b0d0b2d181d182d0b2d183d0b9d182d0b52c0d0a0d0ad0a1d0bbd0b5d0b4d183d18ed189d0b8d0b920d0b0d0bad0bad0b0d183d0bdd18220d0b220d187d0b0d182d0b520225b434841545f4e414d455d2220d0bed0b6d0b8d0b4d0b0d0b5d18220d0b0d0bad182d0b8d0b2d0b0d186d0b8d0b83a0d0a0d0ad098d0bcd18f20d0bfd0bed0bbd18cd0b7d0bed0b2d0b0d182d0b5d0bbd18f3a205b555345524e414d455d0d0a452d4d61696c20d0b0d0b4d180d0b5d1813a205b454d41494c5f414444524553535d0d0a49502dd0b0d0b4d180d0b5d1813a205b52454d4f54455f49505d0d0a0d0a0d0a2d2d2d2d2d2d2d2d2d2d2d2d2d2d2d2d2d2d2d2d2d2d2d2d2d2d2d0d0ad0a1d0bfd0b0d181d0b8d0b1d0be2c0d0a5b53454e4445525d WHERE `le`.`code` = 'email_new_user_activation_notification' AND `la`.`iso_name` = 'ru'";
      $queries[]="UPDATE `".PCPIN_DB_PREFIX."language` AS `la` LEFT JOIN `".PCPIN_DB_PREFIX."language_expression` AS `le` ON `le`.`language_id` = `la`.`id` SET `le`.`value` = 0xd092d0b0d18820d0b0d0bad0bad0b0d183d0bdd18220d0b1d18bd0bb20d181d0bed0b7d0b4d0b0d0bd20d0b820d0bfd0bed181d0bbd0b0d0bd20d090d0b4d0bcd0b8d0bdd0b8d181d182d180d0b0d182d0bed180d18320d0b4d0bbd18f20d0b0d0bad182d0b8d0b2d0b0d186d0b8d0b82e WHERE `le`.`code` = 'account_will_be_activated_by_admin' AND `la`.`iso_name` = 'ru'";
      $queries[]="UPDATE `".PCPIN_DB_PREFIX."language` AS `la` LEFT JOIN `".PCPIN_DB_PREFIX."language_expression` AS `le` ON `le`.`language_id` = `la`.`id` SET `le`.`value` = 0xd098d0b3d0bdd0bed180d0b8d180d183d0b5d0bcd18bd0b520d0bfd0bed0bbd18cd0b7d0bed0b2d0b0d182d0b5d0bbd0b8 WHERE `le`.`code` = 'ignore_list' AND `la`.`iso_name` = 'ru'";
      $queries[]="UPDATE `".PCPIN_DB_PREFIX."language` AS `la` LEFT JOIN `".PCPIN_DB_PREFIX."language_expression` AS `le` ON `le`.`language_id` = `la`.`id` SET `le`.`value` = 0xd092d0b0d180d0b8d0b0d0bdd182d18b20d0b2d18bd0b1d0bed180d0b0 WHERE `le`.`code` = 'choices' AND `la`.`iso_name` = 'ru'";
      $queries[]="UPDATE `".PCPIN_DB_PREFIX."language` AS `la` LEFT JOIN `".PCPIN_DB_PREFIX."language_expression` AS `le` ON `le`.`language_id` = `la`.`id` SET `le`.`value` = 0xd094d0bed0b1d0b0d0b2d0b8d182d18c20d0bdd0bed0b2d0bed0b520d0bfd0bed0bbd0b5 WHERE `le`.`code` = 'create_new_field' AND `la`.`iso_name` = 'ru'";
      $queries[]="UPDATE `".PCPIN_DB_PREFIX."language` AS `la` LEFT JOIN `".PCPIN_DB_PREFIX."language_expression` AS `le` ON `le`.`language_id` = `la`.`id` SET `le`.`value` = 0xd09fd180d0bed181d182d0bed0b920d0b2d18bd0b1d0bed180 WHERE `le`.`code` = 'simple_choice' AND `la`.`iso_name` = 'ru'";
      $queries[]="UPDATE `".PCPIN_DB_PREFIX."language` AS `la` LEFT JOIN `".PCPIN_DB_PREFIX."language_expression` AS `le` ON `le`.`language_id` = `la`.`id` SET `le`.`value` = 0xd0a2d0b5d0bad181d182 WHERE `le`.`code` = 'textarea' AND `la`.`iso_name` = 'ru'";
      $queries[]="UPDATE `".PCPIN_DB_PREFIX."language` AS `la` LEFT JOIN `".PCPIN_DB_PREFIX."language_expression` AS `le` ON `le`.`language_id` = `la`.`id` SET `le`.`value` = 0xd0a2d0b5d0bad181d1822028d0bed0b4d0bdd0b020d181d182d180d0bed0bad0b029 WHERE `le`.`code` = 'single_text_field' AND `la`.`iso_name` = 'ru'";
      $queries[]="UPDATE `".PCPIN_DB_PREFIX."language` AS `la` LEFT JOIN `".PCPIN_DB_PREFIX."language_expression` AS `le` ON `le`.`language_id` = `la`.`id` SET `le`.`value` = 0xd09cd0bdd0bed0b3d0bed0bad180d0b0d182d0bdd18bd0b920d0b2d18bd0b1d0bed180 WHERE `le`.`code` = 'multiple_choice' AND `la`.`iso_name` = 'ru'";
      $queries[]="UPDATE `".PCPIN_DB_PREFIX."language` AS `la` LEFT JOIN `".PCPIN_DB_PREFIX."language_expression` AS `le` ON `le`.`language_id` = `la`.`id` SET `le`.`value` = 0xd092d0bbd0b0d0b4d0b5d0bbd0b5d18620d0bfd180d0bed184d0b8d0bbd18f WHERE `le`.`code` = 'profile_owner' AND `la`.`iso_name` = 'ru'";
      $queries[]="UPDATE `".PCPIN_DB_PREFIX."language` AS `la` LEFT JOIN `".PCPIN_DB_PREFIX."language_expression` AS `le` ON `le`.`language_id` = `la`.`id` SET `le`.`value` = 0xd092d18b20d0b4d0b5d0b9d181d182d0b2d0b8d182d0b5d0bbd18cd0bdd0be20d185d0bed182d0b8d182d0b520d183d0b4d0b0d0bbd0b8d182d18c20d18dd182d0be20d0bfd0bed0bbd0b53f WHERE `le`.`code` = 'sure_delete_field' AND `la`.`iso_name` = 'ru'";
      $queries[]="UPDATE `".PCPIN_DB_PREFIX."language` AS `la` LEFT JOIN `".PCPIN_DB_PREFIX."language_expression` AS `le` ON `le`.`language_id` = `la`.`id` SET `le`.`value` = 0xd09fd0bed0bbd0b520d183d0b4d0b0d0bbd0b5d0bdd0be WHERE `le`.`code` = 'field_deleted' AND `la`.`iso_name` = 'ru'";
      $queries[]="UPDATE `".PCPIN_DB_PREFIX."language` AS `la` LEFT JOIN `".PCPIN_DB_PREFIX."language_expression` AS `le` ON `le`.`language_id` = `la`.`id` SET `le`.`value` = 0xd092d0b2d0b5d0b4d0b8d182d0b520d0b2d0b0d180d0b8d0b0d0bdd182d18b20d0b2d18bd0b1d0bed180d0b02c20d0bfd0be20d0bed0b4d0bdd0bed0bcd18320d0b2d0b0d180d0b8d0b0d0bdd182d18320d0bdd0b020d181d182d180d0bed0bad183 WHERE `le`.`code` = 'field_choices_description' AND `la`.`iso_name` = 'ru'";
      $queries[]="UPDATE `".PCPIN_DB_PREFIX."language` AS `la` LEFT JOIN `".PCPIN_DB_PREFIX."language_expression` AS `le` ON `le`.`language_id` = `la`.`id` SET `le`.`value` = 0xd092d18b20d0bdd0b520d0bed0bfd180d0b5d0b4d0b5d0bbd0b8d0bbd0b820d0bdd0b820d0bed0b4d0bdd0bed0b3d0be20d0b2d0b0d180d0b8d0b0d0bdd182d0b020d0b2d18bd0b1d0bed180d0b0 WHERE `le`.`code` = 'no_options_specified' AND `la`.`iso_name` = 'ru'";
      $queries[]="UPDATE `".PCPIN_DB_PREFIX."language` AS `la` LEFT JOIN `".PCPIN_DB_PREFIX."language_expression` AS `le` ON `le`.`language_id` = `la`.`id` SET `le`.`value` = 0xd09fd0bed0bbd0b520d0b4d0bed0b1d0b0d0b2d0bbd0b5d0bdd0be WHERE `le`.`code` = 'field_created' AND `la`.`iso_name` = 'ru'";
      $queries[]="UPDATE `".PCPIN_DB_PREFIX."language` AS `la` LEFT JOIN `".PCPIN_DB_PREFIX."language_expression` AS `le` ON `le`.`language_id` = `la`.`id` SET `le`.`value` = 0xd098d0b7d0bcd0b5d0bdd0b8d182d18c20d0bfd0bed0bbd0b5 WHERE `le`.`code` = 'edit_field' AND `la`.`iso_name` = 'ru'";
      $queries[]="UPDATE `".PCPIN_DB_PREFIX."language` AS `la` LEFT JOIN `".PCPIN_DB_PREFIX."language_expression` AS `le` ON `le`.`language_id` = `la`.`id` SET `le`.`value` = 0xd0add182d0bed18220d187d0b0d18220d0b8d0bcd0b5d0b5d18220d0bdd0b5d0bad0bed182d0bed180d183d18e20d0b7d0b0d189d0b8d182d18320d0bed18220d184d0bbd183d0b4d0b02c20d0bad0bed182d0bed180d0b0d18f20d181d180d0b0d0b1d0b0d182d18bd0b2d0b0d0b5d1822c20d0bad0bed0b3d0b4d0b020d0bfd0bed0bbd18cd0b7d0bed0b2d0b0d182d0b5d0bbd18c20d0bed182d0bfd180d0b0d0b2d0bbd18fd0b5d18220d0bed0b4d0b8d0bdd0b0d0bad0bed0b2d18bd0b520d181d0bed0bed0b1d189d0b5d0bdd0b8d18f20d0bdd0b5d181d0bad0bed0bbd18cd0bad0be20d180d0b0d0b720d0bfd0bed0b4d180d18fd0b42e20d0a1d0bad0bed0bbd18cd0bad0be20d182d0b0d0bad0b8d18520d0bfd0bed0b2d182d0bed180d18fd18ed189d0b8d185d181d18f20d181d0bed0bed0b1d189d0b5d0bdd0b8d0b920d0b2d18b20d185d0bed182d0b8d182d0b520d0bfd0bed0b7d0b2d0bed0bbd0b8d182d18c20d0bfd180d0b5d0b6d0b4d0b52c20d187d0b5d0bc20d0bfd0bed0bbd18cd0b7d0bed0b2d0b0d182d0b5d0bbd18c20d0b1d183d0b4d0b5d18220d0b0d0b2d182d0bed0bcd0b0d182d0b8d187d0b5d181d0bad0b820d0b7d0b0d0b3d0bbd183d188d0b5d0bd3f WHERE `le`.`code` = 'flood_protection_max_messages' AND `la`.`iso_name` = 'ru'";
      $queries[]="UPDATE `".PCPIN_DB_PREFIX."language` AS `la` LEFT JOIN `".PCPIN_DB_PREFIX."language_expression` AS `le` ON `le`.`language_id` = `la`.`id` SET `le`.`value` = 0xd09dd0b020d181d0bad0bed0bbd18cd0bad0be20d181d0b5d0bad183d0bdd0b420d0b7d0b0d0b3d0bbd183d188d0b0d182d18c20d184d0bbd183d0b4d18fd189d0b8d18520d0bfd0bed0bbd18cd0b7d0bed0b2d0b0d182d0b5d0bbd0b5d0b93f WHERE `le`.`code` = 'flood_protection_mute_time' AND `la`.`iso_name` = 'ru'";
      $queries[]="UPDATE `".PCPIN_DB_PREFIX."language` AS `la` LEFT JOIN `".PCPIN_DB_PREFIX."language_expression` AS `le` ON `le`.`language_id` = `la`.`id` SET `le`.`value` = 0xd09cd0b8d0bdd0b8d0bcd0b0d0bbd18cd0bdd18bd0b920d0bfd0b5d180d0b8d0bed0b420d0b2d180d0b5d0bcd0b5d0bdd0b820d0b220d181d0b5d0bad183d0bdd0b4d0b0d18520d0bcd0b5d0b6d0b4d18320d0b4d0b2d183d0bcd18f20d181d0bed0bed0b1d189d0b5d0bdd0b8d18fd0bcd0b820d0bed18220d182d0bed0b3d0be20d0b6d0b520d181d0b0d0bcd0bed0b3d0be20d0bfd0bed0bbd18cd0b7d0bed0b2d0b0d182d0b5d0bbd18f2e WHERE `le`.`code` = 'flood_protection_message_delay' AND `la`.`iso_name` = 'ru'";
      $queries[]="UPDATE `".PCPIN_DB_PREFIX."language` AS `la` LEFT JOIN `".PCPIN_DB_PREFIX."language_expression` AS `le` ON `le`.`language_id` = `la`.`id` SET `le`.`value` = 0xd0a4d0bbd183d0b4 WHERE `le`.`code` = 'flooding' AND `la`.`iso_name` = 'ru'";

    case 6.20:
    case 6.21:
      $queries[]="INSERT INTO `".PCPIN_DB_PREFIX."config` ( `_conf_id` , `_conf_group` , `_conf_subgroup` , `_conf_name` , `_conf_value` , `_conf_type` , `_conf_choices` , `_conf_description` )  VALUES ( '88', 'account', '{LNG_USERS}', 'allow_account_unsubscribe', '0', 'boolean_choice', '1={LNG_YES}|0={LNG_NO}', '{LNG__CONF_ALLOW_ACCOUNT_UNSUBSCRIBE}' )";
      $queries[]="INSERT INTO `".PCPIN_DB_PREFIX."language_expression` ( `language_id`, `code`, `value`, `multi_row` ) SELECT DISTINCT `".PCPIN_DB_PREFIX."language_expression`.`language_id` AS `language_id`, '_conf_allow_account_unsubscribe' AS `code`, 0x416c6c6f7720757365727320746f2064656c657465206f776e206163636f756e743f AS `value`, 'n' AS `multi_row` FROM `".PCPIN_DB_PREFIX."language_expression`";
      $queries[]="INSERT INTO `".PCPIN_DB_PREFIX."language_expression` ( `language_id`, `code`, `value`, `multi_row` ) SELECT DISTINCT `".PCPIN_DB_PREFIX."language_expression`.`language_id` AS `language_id`, 'delete_my_account' AS `code`, 0x44656c657465206d79206163636f756e74 AS `value`, 'n' AS `multi_row` FROM `".PCPIN_DB_PREFIX."language_expression`";
      $queries[]="INSERT INTO `".PCPIN_DB_PREFIX."language_expression` ( `language_id`, `code`, `value`, `multi_row` ) SELECT DISTINCT `".PCPIN_DB_PREFIX."language_expression`.`language_id` AS `language_id`, 'delete_my_account_confirmation' AS `code`, 0x5761726e696e672120596f75206172652061626f757420746f2064656c65746520796f7572206f776e2075736572206163636f756e742e20546869732063616e277420626520756e646f6e652e0d0a41726520796f75207375726520796f752077616e7420746f2064656c65746520796f7572206163636f756e743f AS `value`, 'n' AS `multi_row` FROM `".PCPIN_DB_PREFIX."language_expression`";
      $queries[]="UPDATE `".PCPIN_DB_PREFIX."language` AS `la` LEFT JOIN `".PCPIN_DB_PREFIX."language_expression` AS `le` ON `le`.`language_id` = `la`.`id` SET `le`.`value` = 0x44656e2042656e75747a65726e2065726c617562656e2c20656967656e65732042656e75747a65726b6f6e746f207a75206cc3b6736368656e3f WHERE `le`.`code` = '_conf_allow_account_unsubscribe' AND `la`.`iso_name` = 'de'";
      $queries[]="UPDATE `".PCPIN_DB_PREFIX."language` AS `la` LEFT JOIN `".PCPIN_DB_PREFIX."language_expression` AS `le` ON `le`.`language_id` = `la`.`id` SET `le`.`value` = 0x4d65696e2042656e75747a65726b6f6e746f206cc3b6736368656e WHERE `le`.`code` = 'delete_my_account' AND `la`.`iso_name` = 'de'";
      $queries[]="UPDATE `".PCPIN_DB_PREFIX."language` AS `la` LEFT JOIN `".PCPIN_DB_PREFIX."language_expression` AS `le` ON `le`.`language_id` = `la`.`id` SET `le`.`value` = 0x41636874756e6721205369652073696e6420696d20426567726966662c204968722042656e75747a65726b6f6e746f207a75206cc3b6736368656e2e20446965736520416b74696f6e206b616e6e206e696368742072c3bc636b67c3a46e6769672067656d616368742077657264656e2e0d0a576f6c6c656e20536965204968722042656e75747a65726b6f6e746f207769726b6c696368206cc3b6736368656e3f WHERE `le`.`code` = 'delete_my_account_confirmation' AND `la`.`iso_name` = 'de'";

    break;

    case 6.22:
      $queries[]="ALTER TABLE `".PCPIN_DB_PREFIX."ipfilter` ADD `type` ENUM( 'IPv4', 'IPv6' ) CHARACTER SET ascii COLLATE ascii_bin DEFAULT 'IPv4' NOT NULL, ADD INDEX ( `type` ), CHANGE `address` `address` VARCHAR( 45 ) CHARACTER SET ascii COLLATE ascii_general_ci NOT NULL DEFAULT ''";
      $queries[]="ALTER TABLE `".PCPIN_DB_PREFIX."session` CHANGE `_s_ip` `_s_ip` VARCHAR( 45 ) CHARACTER SET ascii COLLATE ascii_general_ci NOT NULL DEFAULT ''";
      $queries[]="UPDATE `".PCPIN_DB_PREFIX."ipfilter` SET `description` = 'This rule allows all IPv4 addresses' WHERE `description` = 'This rule allows all IP addresses' LIMIT 1";
      $queries[]="INSERT INTO `".PCPIN_DB_PREFIX."ipfilter` ( `address`, `added_on`, `expires`, `description`, `action`, `type` ) VALUES ( '*', NOW(), '0000-00-00 00:00:00', 'This rule allows all IPv6 addresses', 'a', 'IPv6' )";
    break;

  }

  // All versions

  // 0000431: Invalid online time counter by manually created user accounts
  // http://bugs.pcpin.com/view.php?id=431
  $queries[]="UPDATE `".PCPIN_DB_PREFIX."user` AS `us` SET `us`.`time_online` = 0 WHERE `us`.`last_login` = '0000-00-00 00:00:00'";

  // Clear database table data cache
  $queries[]="TRUNCATE TABLE `".PCPIN_DB_PREFIX."cache`";

  // Store new version number
  $queries[]='DELETE FROM `'.PCPIN_DB_PREFIX.'version`';
  $queries[]='INSERT INTO `'.PCPIN_DB_PREFIX.'version` ( `version`, `version_check_key`, `last_version_check` ) VALUES ( "'.mysql_real_escape_string(PCPIN_UPGRADE_NEW_VERSION).'", "-", "0000-00-00 00:00:00" )';

  return $queries;
}

?>