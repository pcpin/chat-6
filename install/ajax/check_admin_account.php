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

$status=0;
$message='';
$errors=array();

if (!isset($host)) $host='';
if (!isset($user)) $user='';
if (!isset($password)) $password='';
if (!isset($database)) $database='';
if (!isset($prefix)) $prefix='';
if (!isset($admin_username)) $admin_username='';
if (!isset($admin_email)) $admin_email='';

$conn=@mysql_connect($host, $user, $password);

if (!empty($conn)) {
  if (@mysql_select_db($database, $conn)) {
    mysql_query('SET NAMES "utf8"', $conn);
    mysql_query('SET SESSION sql_mode=""', $conn);
    // Check username
    if ($result=mysql_query('SELECT 1 FROM `'.$prefix.'user` WHERE `login` LIKE "'.mysql_real_escape_string($admin_username, $conn).'" LIMIT 1', $conn)) {
      if ($data=mysql_fetch_array($result, MYSQL_ASSOC)) {
        $errors[]='Username "'.$admin_username.'" already taken.';
      }
    }
    // Check email address
    if ($result=mysql_query('SELECT 1 FROM `'.$prefix.'user` WHERE `email` LIKE "'.mysql_real_escape_string($admin_email, $conn).'" LIMIT 1', $conn)) {
      if ($data=mysql_fetch_array($result, MYSQL_ASSOC)) {
        $errors[]='E-Mail address "'.$admin_email.'" already taken.';
      }
    } elseif ($result=mysql_query('SELECT 1 FROM `'.$prefix.'user` WHERE `email_new` LIKE "'.mysql_real_escape_string($admin_email, $conn).'" LIMIT 1', $conn)) {
      if ($data=mysql_fetch_array($result, MYSQL_ASSOC)) {
        $errors[]='E-Mail address "'.$admin_email.'" already taken.';
      }
    }
  } else {
    $errors[]="MySQL server connected, but the database could not be selected:\n\n".mysql_error();
  }
} else {
  $errors[]="Could not connect to MySQL server:\n\n".mysql_error();
}

if (!empty($errors)) {
  $message=implode("\n", $errors);
  $status=1;
} else {
  $message='OK';
  $status=0;
}

$xmlwriter->setHeaderStatus($status);
$xmlwriter->setHeaderMessage($message);

// Send headers
header('Content-Type: text/xml; charset=UTF-8');
header('Expires: '.gmdate('D, d M Y H:i:s').' GMT');
header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
header('Pragma: no-cache');

// Send XML
echo $xmlwriter->makeXML();

die();
?>