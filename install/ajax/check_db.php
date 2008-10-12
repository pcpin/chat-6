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
    // Check MySQL server version
    $mysql_version='0';
    $result=mysql_query('SELECT VERSION()', $conn);
    if ($data=mysql_fetch_array($result, MYSQL_NUM)) {
      $mysql_version=$data[0];
    }
    $mysql_exists=explode('.', $mysql_version);
    $mysql_needed=explode('.', PCPIN_REQUIRESMYSQL);
    foreach ($mysql_needed as $key=>$val) {
      if (!isset($mysql_exists[$key])) {
        // Installed MySQL version is OK
        break;
      } else {
        if ($val>$mysql_exists[$key]) {
          // MySQL version is too old
          $status=1;
          $message='Installed MySQL server version is "'.$mysql_version.'" (minimum required MySQL version is "'.PCPIN_REQUIRESMYSQL.'")'."\n\n".'PCPIN Chat 6 cannot be installed on this server';
        } elseif ($val<$mysql_exists[$key]) {
          // Installed MySQL version is OK
          break;
        }
      }
    }
    if ($status==0) {
      $status=10;
      $message='Setup was unable to write your database configuration file "db.inc.php".'
              ."\n"
              .'Please download it and save on your server into the directory "./config"';
      // Check db.inc.php
      if (file_exists('../../config/db.inc.php') && is_file('../../config/db.inc.php')) {
        include('../../config/db.inc.php');
        if (defined('PCPIN_DB_DATA_LOADED')) {
          if (   ${$_pcpin_dbcn}['server']===$host
              && ${$_pcpin_dbcn}['user']===$user
              && ${$_pcpin_dbcn}['password']===$password
              && ${$_pcpin_dbcn}['database']===$database
              && ${$_pcpin_dbcn}['tbl_prefix']===$prefix) {
            // File already contains correct data
            $status=0;
            $message='OK';
          }
        }
      }
      // Trying to save database config file
      if ($status==10) {
        if ($src=file_get_contents('../database/db.inc.php_')) {
          $src=str_replace('{{HOST}}', str_replace("'", '\\\'', $host), $src);
          $src=str_replace('{{USER}}', str_replace("'", '\\\'', $user), $src);
          $src=str_replace('{{PASSWORD}}', str_replace("'", '\\\'', $password), $src);
          $src=str_replace('{{DATABASE}}', str_replace("'", '\\\'', $database), $src);
          $src=str_replace('{{PREFIX}}', str_replace("'", '\\\'', $prefix), $src);
          if ($out=fopen('../../config/db.inc.php', 'wb')) {
            if (fwrite($out, $src)) {
              $status=0;
              $message='OK';
            }
            $src='';
            fclose($out);
          }
        } else {
          $status=1;
          $message='FATAL error. Your distribution seems to be broken. Please download it again.';
        }
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