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


define('PCPIN_INSTALL_MODE', true);
require_once('../install.php');

$status=-1;
$message='Failed to connect to database. Installation aborted.';
$short_message='FATAL Error';

if (!isset($host)) $host='';
if (!isset($user)) $user='';
if (!isset($password)) $password='';
if (!isset($database)) $database='';
if (!isset($prefix)) $prefix='';
if (!isset($admin_username)) $admin_username='';
if (!isset($admin_password)) $admin_password='';
if (!isset($admin_email)) $admin_email='';
if (!isset($default_language)) $default_language='';

$conn=@mysql_connect($host, $user, $password);

if (!empty($conn) && @mysql_select_db($database, $conn)) {
  mysql_query('SET NAMES "utf8"', $conn);
  mysql_query('SET SESSION sql_mode=""', $conn);
  // Delete temporary data
  mysql_query('DROP TABLE IF EXISTS `'.$prefix.'_chat_installdata`', $conn);
  // Set new base URL
  $port='';
  if (!empty($_SERVER['SERVER_PORT'])) {
    $port=$_SERVER['SERVER_PORT'];
  }
  if ($port=='443') {
    $protocol='https';
    $port='';
  } else {
    $protocol='http';
    if ($port=='80') {
      $port='';
    } else {
      $port=':'.$port;
    }
  }
  $base_dir=!empty($_SERVER['PHP_SELF'])? $_SERVER['PHP_SELF'] : (!empty($_SERVER['SCRIPT_NAME'])? $_SERVER['SCRIPT_NAME'] : $_SERVER['REQUEST_URI']);
  $base_dir=explode('/', trim(str_replace('\\', '/', dirname($base_dir)), '/'));
  array_pop($base_dir);
  array_pop($base_dir);
  $base_dir=implode('/', $base_dir);
  $base_url=$protocol.'://'.$_SERVER['HTTP_HOST'].$port.'/'.$base_dir.'/index.php';
  mysql_query('UPDATE `'.$prefix.'config` SET `_conf_value` = "'.mysql_real_escape_string($base_url, $conn).'" WHERE `_conf_name` = "base_url" LIMIT 1', $conn);
  // Set default language
  mysql_query('UPDATE `'.$prefix.'config` `co`,
                   `'.$prefix.'language` `la`
                  SET `co`.`_conf_value` = `la`.`id`
                WHERE `co`.`_conf_name` = "default_language"
                      AND `la`.`iso_name` = "'.mysql_real_escape_string($default_language, $conn).'"', $conn);
  // Set new version
  mysql_query('DELETE FROM `'.$prefix.'version`', $conn);
  mysql_query('INSERT INTO `'.$prefix.'version` ( `version`,
                                                  `version_check_key`
                                                ) VALUES (
                                                  "'.mysql_real_escape_string(PCPIN_INSTALL_VERSION, $conn).'",
                                                  "'.md5(mt_rand(-time(), time()).microtime().rand(-time(), time())).'"
                                                )', $conn);
  // Sort config table
  mysql_query('ALTER TABLE `'.$prefix.'config` ORDER BY `_conf_group` ASC, `_conf_subgroup` ASC, `_conf_id` ASC', $conn);

  $short_message='Done';
  $status=0;
  $message='OK';
}

$xmlwriter->setHeaderStatus($status);
$xmlwriter->setHeaderMessage($message);
$xmlwriter->setData(array('short_message'=>$short_message));

// Send headers
header('Content-Type: text/xml; charset=UTF-8');
header('Expires: '.gmdate('D, d M Y H:i:s').' GMT');
header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
header('Pragma: no-cache');

// Send XML
echo $xmlwriter->makeXML();

die();
?>