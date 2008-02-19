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
$counter=0;

if (!isset($host)) $host='';
if (!isset($user)) $user='';
if (!isset($password)) $password='';
if (!isset($database)) $database='';
if (!isset($prefix)) $prefix='';
if (!isset($iso_name)) $iso_name='';
if (!isset($lng_name)) $lng_name='';
if (!isset($filename)) $filename='';

$conn=@mysql_connect($host, $user, $password);

error_reporting(E_ALL); ini_set('display_errors', 'on');
function _pcpin_loadClass($class) { require_once('../../class/'.$class.'.class.php'); }
if (!empty($conn) && @mysql_select_db($database, $conn)) {
  $status=1;
  $message='Failed to install language '.$lng_name;
  $short_message='Error';
  require_once('../../config/config.inc.php');
  _pcpin_loadClass('common');
  _pcpin_loadClass('db');
  class PCPIN_Config extends PCPIN_DB { function PCPIN_Config(&$caller) { $this->_db_pass_vars($caller, $this); $this->_db_pass_vars($this, $caller); } }
  class PCPIN_Session extends PCPIN_Config { function PCPIN_Session(&$config) { $this->_db_pass_vars($config, $this, true); } function _s_init(&$session, &$child) { $this->_db_pass_vars($session, $child); } }
  $__pcpin_init_class=new stdClass();
  $__pcpin_init_class->_cache=array(); // Cahced data (to be used by all child objects)
  new PCPIN_DB($__pcpin_init_class, array('server'=>$host, 'user'=>$user, 'password'=>$password, 'database'=>$database, 'tbl_prefix'=>$prefix));
  new PCPIN_Config($__pcpin_init_class);
  _pcpin_loadClass('language'); $lng=new PCPIN_Language($__pcpin_init_class);
  $language_id=0;
  if (   substr($filename, -4)=='.bin'
      && is_readable('../languages/'.$filename)
      && $raw=file_get_contents('../languages/'.$filename)
      ) {
    if (0===$lng->importLanguage($raw, $language_id)) {
      $lng->_db_updateRow($language_id, 'id', array('active'=>'y'));
      $status=0;
      $message='OK';
      $short_message='Done';
    }
    unset($raw);
  }
}

echo '<?xml version="1.0" encoding="UTF-8"?>
<pcpin_xml>
<status>'.htmlspecialchars($status).'</status>
<message>'.htmlspecialchars($message).'</message>
<short_message>'.htmlspecialchars($short_message).'</short_message>
</pcpin_xml>';
die();

?>