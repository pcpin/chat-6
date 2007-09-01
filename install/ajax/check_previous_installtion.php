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


$status=0;
$message='';
$version='';

if (!isset($host)) $host='';
if (!isset($user)) $user='';
if (!isset($password)) $password='';
if (!isset($database)) $database='';
if (!isset($prefix)) $prefix='';

$conn=@mysql_connect($host, $user, $password);

if (!empty($conn)) {
  if (@mysql_select_db($database, $conn)) {
    $message='OK';
    $status=0;
    mysql_query('SET NAMES "utf8"', $conn);
    mysql_query('SET SESSION sql_mode=""', $conn);
    // Check for previous installation
    if ($result=mysql_query('SELECT `version` FROM `'.$prefix.'version` ORDER BY `version` DESC LIMIT 1', $conn)) {
      if ($data=mysql_fetch_array($result, MYSQL_ASSOC)) {
        $version=$data['version'];
      }
    }
  } else {
    $status=1;
    $message="MySQL server connected, but the database could not be selected:\n\n".mysql_error();
  }
} else {
  $status=1;
  $message="Could not connect to MySQL server:\n\n".mysql_error();
}


echo '<?xml version="1.0" encoding="UTF-8"?>
<pcpin_xml>
<message>'.htmlspecialchars($message).'</message>
<status>'.htmlspecialchars($status).'</status>
<version>'.htmlspecialchars($version).'</version>
</pcpin_xml>';
die();
?>