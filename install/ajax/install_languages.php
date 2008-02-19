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

error_reporting(E_ALL); ini_set('display_errors', 'on');
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
die('OK');


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
  if ($result=mysql_query('SELECT `version` FROM `'.$prefix.'version` ORDER BY `version` DESC LIMIT 1', $conn)) {
    if ($data=mysql_fetch_array($result, MYSQL_ASSOC)) {
      $version=$data['version'];
      if ($version>=5.10) {
        // Create temporary database table
        mysql_query('DROP TABLE IF EXISTS `'.$prefix.'_chat_installdata`', $conn);
        if (mysql_query('CREATE TABLE `'.$prefix.'_chat_installdata` ( `id` VARCHAR( 255 ) NOT NULL , `data` LONGBLOB NOT NULL , INDEX ( `id` ) ) TYPE=MyISAM', $conn)) {
          mysql_query('INSERT INTO `'.$prefix.'_chat_installdata` ( `id`, `data` ) VALUES ( "from_version", "'.mysql_real_escape_string($version, $conn).'" )', $conn);
          $data_objects_array=explode(',', $data_objects);
          if ($version<6) {
            // PCPIN Chat 5.xx
            foreach ($data_objects_array as $data) {
              $data=trim($data);
              if ($data=='users') {
                $counter++;
                secureUsers5();
              } elseif ($data=='smilies') {
                $counter++;
                secureSmilies5();
              } elseif ($data=='settings') {
                $counter++;
                secureSettings5();
              } elseif ($data=='rooms') {
                $counter++;
                secureRooms5();
              } elseif ($data=='bad_words') {
                $counter++;
                secureBadWords5();
              } elseif ($data=='ip_filter') {
                $counter++;
                secureIPFilter5();
              }
            }
          }
          if ($counter>0) {
            $status=0;
            $message='OK';
            $short_message='Done';
          } else {
            $status=0;
            $message='OK';
            $short_message='Skipped';
          }
        } else {
          // Failed to create temporary data table
          $status=1;
          $message='Error';
          $short_message='Failed';
        }
      } else {
        $status=0;
        $message='OK';
        $short_message='Skipped';
      }
    } else {
      $status=0;
      $message='OK';
      $short_message='Skipped';
    }
  } else {
    $status=0;
    $message='OK';
    $short_message='Skipped';
  }

}


echo '<?xml version="1.0" encoding="UTF-8"?>
<pcpin_xml>
<status>'.htmlspecialchars($status).'</status>
<message>'.htmlspecialchars($message).'</message>
<short_message>'.htmlspecialchars($short_message).'</short_message>
</pcpin_xml>';
die();



/******************************************************************************
 * PCPIN Chat 5.xx
 *****************************************************************************/

function secureUsers5() {
  global $conn;
  global $prefix;
  if ($result=mysql_query('SELECT `us`.*,
                                  IF( `ba`.`id` IS NOT NULL, 1, 0 ) AS `banned_permanently`
                             FROM `'.$prefix.'user` `us`
                                  LEFT JOIN `'.$prefix.'ban` `ba` ON `ba`.`user_id` = `us`.`id`
                         ORDER BY `us`.`id` ASC', $conn)) {
    while ($data=mysql_fetch_array($result, MYSQL_ASSOC)) {
      // Get avatar
      if ($data['photo']!='' && $data['photo']!='nophoto.jpg') {
        $filename='../import/images/userphotos/'.$data['photo'];
        if (file_exists($filename) && is_readable($filename)) {
          $data['avatar_image']=$filename;
        }
      }
      unset($data['photo']);
      mysql_query('INSERT INTO `'.$prefix.'_chat_installdata` ( `id`, `data` ) VALUES ( "user", "'.mysql_real_escape_string(serialize($data), $conn).'" )', $conn);
    }
  }
}

function secureSmilies5() {
  global $conn;
  global $prefix;
  if ($result=mysql_query('SELECT `image`, `text` FROM `'.$prefix.'smilie` ORDER BY `id` ASC', $conn)) {
    while ($data=mysql_fetch_array($result, MYSQL_ASSOC)) {
      // Get file
      if ($data['image']!='') {
        $filename='../import/images/smilies/'.$data['image'];
        if (file_exists($filename) && is_readable($filename)) {
          $data['smilie_image']=$filename;
        }
      }
      unset($data['image']);
      mysql_query('INSERT INTO `'.$prefix.'_chat_installdata` ( `id`, `data` ) VALUES ( "smilie", "'.mysql_real_escape_string(serialize($data), $conn).'" )', $conn);
    }
  }
}

function secureSettings5() {
  global $conn;
  global $prefix;
  if ($result=mysql_query('SELECT * FROM `'.$prefix.'configuration` ORDER BY `id` ASC', $conn)) {
    while ($data=mysql_fetch_array($result, MYSQL_ASSOC)) {
      mysql_query('INSERT INTO `'.$prefix.'_chat_installdata` ( `id`, `data` ) VALUES ( "setting", "'.mysql_real_escape_string(serialize($data), $conn).'" )', $conn);
    }
  }
}

function secureRooms5() {
  global $conn;
  global $prefix;
  if ($result=mysql_query('SELECT * FROM `'.$prefix.'room` WHERE `type` = 0 OR `type` = 2 ORDER BY `name` ASC', $conn)) {
    while ($data=mysql_fetch_array($result, MYSQL_ASSOC)) {
      // Get background image
      if ($data['bgimg']!='') {
        $filename='../import/images/rooms/'.$data['bgimg'];
        if (file_exists($filename) && is_readable($filename)) {
          $data['background_image']=$filename;
        }
      }
      unset($data['bgimg']);
      if ($data['type']!=2) {
        $data['password']='';
      }
      mysql_query('INSERT INTO `'.$prefix.'_chat_installdata` ( `id`, `data` ) VALUES ( "room", "'.mysql_real_escape_string(serialize($data), $conn).'" )', $conn);
    }
  }
}

function secureBadWords5() {
  global $conn;
  global $prefix;
  if ($result=mysql_query('SELECT `word`, `replacement` FROM `'.$prefix.'badword` ORDER BY `id` ASC', $conn)) {
    while ($data=mysql_fetch_array($result, MYSQL_ASSOC)) {
      mysql_query('INSERT INTO `'.$prefix.'_chat_installdata` ( `id`, `data` ) VALUES ( "bad_word", "'.mysql_real_escape_string(serialize($data), $conn).'" )', $conn);
    }
  }
}

function secureIPFilter5() {
  global $conn;
  global $prefix;
  if ($result=mysql_query('SELECT `ip`, `bandate` FROM `'.$prefix.'ban` WHERE `ip` != "" ORDER BY `bandate` ASC', $conn)) {
    while ($data=mysql_fetch_array($result, MYSQL_ASSOC)) {
      mysql_query('INSERT INTO `'.$prefix.'_chat_installdata` ( `id`, `data` ) VALUES ( "ip_ban", "'.mysql_real_escape_string(serialize($data), $conn).'" )', $conn);
    }
  }
}






?>