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

// Send headers
header('Content-Type: text/xml; charset=UTF-8');
header('Expires: '.gmdate('D, d M Y H:i:s').' GMT');
header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
header('Pragma: public');
header('Pragma: no-cache');


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

$conn=@mysql_connect($host, $user, $password);

if (!empty($conn) && @mysql_select_db($database, $conn)) {
  mysql_query('SET NAMES "utf8"', $conn);
  mysql_query('SET SESSION sql_mode=""', $conn);
  if (!empty($do_create) && !empty($admin_username) && !empty($admin_email)) {
    // Create user
    if ($result=mysql_query('INSERT INTO `'.$prefix.'user` ( `login`,
                                                             `password`,
                                                             `password_new`,
                                                             `email`,
                                                             `hide_email`,
                                                             `previous_login`,
                                                             `last_login`,
                                                             `joined`,
                                                             `activated`,
                                                             `is_admin`,
                                                             `outgoing_message_color`
                                                            ) VALUES (
                                                             "'.mysql_real_escape_string($admin_username, $conn).'",
                                                             "'.mysql_real_escape_string(md5($admin_password), $conn).'",
                                                             "'.mysql_real_escape_string(md5(mt_rand(-time(), time()).microtime()), $conn).'",
                                                             "'.mysql_real_escape_string($admin_email, $conn).'",
                                                             "y",
                                                             "'.mysql_real_escape_string(date('Y-m-d H:i:s'), $conn).'",
                                                             "'.mysql_real_escape_string(date('Y-m-d H:i:s'), $conn).'",
                                                             "'.mysql_real_escape_string(date('Y-m-d H:i:s'), $conn).'",
                                                             "y",
                                                             "y",
                                                             "ff0000"
                                                            )', $conn)) {
      $result=mysql_query('SELECT LAST_INSERT_ID()', $conn);
      $data=mysql_fetch_array($result, MYSQL_NUM);
      if (!empty($data[0])) {
        // Create userdata
        mysql_query('INSERT INTO `'.$prefix.'userdata` ( `user_id` ) VALUES ( "'.mysql_real_escape_string($data[0]).'" )', $conn);
        // Create nickname
        mysql_query('INSERT INTO `'.$prefix.'nickname` ( `user_id`,
                                                         `nickname`,
                                                         `nickname_plain`,
                                                         `default`
                                                       ) VALUES (
                                                         "'.mysql_real_escape_string($data[0], $conn).'",
                                                         "^ff0000'.mysql_real_escape_string($admin_username, $conn).'",
                                                         "'.mysql_real_escape_string($admin_username, $conn).'",
                                                         "y"
                                                       )', $conn);
      }
    }
    $short_message='Done';
  } else {
    $short_message='Skipped';
  }
  $status=0;
  $message='OK';
}


echo '<?xml version="1.0" encoding="UTF-8"?>
<pcpin_xml>
<status>'.htmlspecialchars($status).'</status>
<message>'.htmlspecialchars($message).'</message>
<short_message>'.htmlspecialchars($short_message).'</short_message>
</pcpin_xml>';
die();





?>