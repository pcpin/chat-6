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

function _pcpin_loadClass($class) { require_once('../../class/'.$class.'.class.php'); }
require_once('../../config/config.inc.php');
_pcpin_loadClass('common');
_pcpin_loadClass('db');
class PCPIN_Config extends PCPIN_DB { function PCPIN_Config(&$caller) { $this->_db_pass_vars($caller, $this); $this->_db_pass_vars($this, $caller); } }
class PCPIN_Session extends PCPIN_Config { function PCPIN_Session(&$config) { $this->_db_pass_vars($config, $this, true); } function _s_init(&$session, &$child) { } }
$__pcpin_init_class=new stdClass();
$__pcpin_init_class->_cache=array(); // Cahced data (to be used by all child objects)
new PCPIN_DB($__pcpin_init_class, array('server'=>$host, 'user'=>$user, 'password'=>$password, 'database'=>$database, 'tbl_prefix'=>$prefix));
new PCPIN_Config($__pcpin_init_class);
_pcpin_loadClass('language'); $lng=new PCPIN_Language($__pcpin_init_class);
$languages=array();
if ($h=opendir('../languages')) {
  while ($file=readdir($h)) {
    if (substr($file, 0, 10)=='pcpin_lng_' && substr($file, -4)=='.bin' && is_readable('../languages/'.$file) && $raw=file_get_contents('../languages/'.$file)) {
      $lng_info=array();
      if ($lng->getLanguageFileInfo($raw, $lng_info)) {
        if (   isset($lng_info['pcpin_version']) && 0===strpos($lng_info['pcpin_version'], 'pcpin_chat_') && (float)PCPIN_INSTALL_VERSION===(float)substr($lng_info['pcpin_version'], 11)
            && isset($lng_info['iso_name']) && _pcpin_strlen($lng_info['iso_name'])==2 && defined('PCPIN_ISO_LNG_'.strtoupper($lng_info['iso_name']))
            && isset($lng_info['expressions_count']) && $lng_info['expressions_count']>0
            ) {
          $languages[]=$lng_info;
        }
      }
      unset($raw);
    }
  }
  closedir($h);
}


// Send headers
header('Content-Type: text/xml; charset=UTF-8');
header('Expires: '.gmdate('D, d M Y H:i:s').' GMT');
header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
header('Pragma: public');
header('Pragma: no-cache');


$status=-1;
$message='Failed to connect to database. Installation aborted.';
$short_message='FATAL Error';
$counter=0;

if (!isset($host)) $host='';
if (!isset($user)) $user='';
if (!isset($password)) $password='';
if (!isset($database)) $database='';
if (!isset($prefix)) $prefix='';
if (!isset($data_objects)) $data_objects='';

$conn=@mysql_connect($host, $user, $password);

if (!empty($conn) && @mysql_select_db($database, $conn)) {
  mysql_query('SET NAMES "utf8"', $conn);
  mysql_query('SET SESSION sql_mode=""', $conn);
  $status=0;
  $message='OK';
}


echo '<?xml version="1.0" encoding="UTF-8"?>
<pcpin_xml>
<status>'.htmlspecialchars($status).'</status>
<message>'.htmlspecialchars($message).'</message>
<languages>';
foreach ($languages as $lng) {
  echo '
  <language>';
  foreach ($lng as $key=>$val) {
    if (is_scalar($val)) {
      echo '
    <'.$key.'>'.htmlspecialchars($val).'</'.$key.'>';
    }
  }
  echo '
  </language>';
}
echo '
</languages>
</pcpin_xml>';
die();


?>