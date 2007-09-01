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
if (!isset($data_objects)) $data_objects='';

$conn=@mysql_connect($host, $user, $password);

if (!empty($conn) && @mysql_select_db($database, $conn)) {
  mysql_query('SET NAMES "utf8"', $conn);
  mysql_query('SET SESSION sql_mode=""', $conn);

  if ($result=mysql_query('SELECT `data` FROM `'.$prefix.'_chat_installdata` WHERE `id` = "from_version" LIMIT 1', $conn)) {
    if ($data=mysql_fetch_array($result, MYSQL_ASSOC)) {
      $version=$data['data'];
      if ($version<6) {

        // PCPIN Chat 5.xx
        restoreUsers5();
        restoreSmilies5();
        restoreSettings5();
        restoreRooms5();
        restoreBadWords5();
        restoreIPFilter5();

        $status=0;
        $message='OK';
        $short_message='Done';
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

function restoreUsers5() {
  global $conn;
  global $prefix;
  if ($result=mysql_query('SELECT `data` FROM `'.$prefix.'_chat_installdata` WHERE `id` = "user"', $conn)) {
    while ($data=mysql_fetch_array($result, MYSQL_ASSOC)) {
      if ($user=@unserialize($data['data'])) {
        // Create user
        mysql_query('INSERT INTO `'.$prefix.'user` ( `id`,
                                                     `login`,
                                                     `password`,
                                                     `password_new`,
                                                     `email`,
                                                     `hide_email`,
                                                     `previous_login`,
                                                     `last_login`,
                                                     `joined`,
                                                     `activated`,
                                                     `is_admin`,
                                                     `banned_permanently`,
                                                     `outgoing_message_color`
                                                    ) VALUES (
                                                     "'.mysql_real_escape_string($user['id'], $conn).'",
                                                     "'.mysql_real_escape_string($user['login'], $conn).'",
                                                     "'.mysql_real_escape_string($user['password'], $conn).'",
                                                     "'.mysql_real_escape_string(md5(mt_rand(-time(), time()).microtime()), $conn).'",
                                                     "'.mysql_real_escape_string($user['email'], $conn).'",
                                                     "'.mysql_real_escape_string($user['hide_email'], $conn).'",
                                                     "'.mysql_real_escape_string(date('Y-m-d H:i:s', $user['last_login']), $conn).'",
                                                     "'.mysql_real_escape_string(date('Y-m-d H:i:s', $user['last_login']), $conn).'",
                                                     "'.mysql_real_escape_string(date('Y-m-d H:i:s', $user['joined']), $conn).'",
                                                     "y",
                                                     "'.(($user['level'] &4095)? 'y' : 'n').'",
                                                     "'.(!empty($user['banned_permanently'])? 'y' : 'n').'",
                                                     "'.mysql_real_escape_string(substr($user['color'], -6), $conn).'"
                                                    )', $conn);
        // Create userdata
        mysql_query('INSERT INTO `'.$prefix.'userdata` ( `user_id`,
                                                         `gender`,
                                                         `age`,
                                                         `location`,
                                                         `interests`
                                                       ) VALUES (
                                                         "'.mysql_real_escape_string($user['id'], $conn).'",
                                                         "'.(($user['sex']=='m' || $user['sex']=='f')? $user['sex'] : '-').'",
                                                         "'.mysql_real_escape_string($user['age']>0? $user['age'] : '', $conn).'",
                                                         "'.mysql_real_escape_string($user['location'], $conn).'",
                                                         "'.mysql_real_escape_string($user['about'], $conn).'"
                                                       )', $conn);
        // Create nickname
        mysql_query('INSERT INTO `'.$prefix.'nickname` ( `user_id`,
                                                         `nickname`,
                                                         `nickname_plain`,
                                                         `default`
                                                       ) VALUES (
                                                         "'.mysql_real_escape_string($user['id'], $conn).'",
                                                         "'.mysql_real_escape_string($user['login'], $conn).'",
                                                         "'.mysql_real_escape_string($user['login'], $conn).'",
                                                         "y"
                                                       )', $conn);
        // Avatar?
        if (   !empty($user['avatar_image'])
            && file_exists($user['avatar_image'])
            && is_readable($user['avatar_image'])) {
          $img_data=null;
          if ($img_data=getimagesize($user['avatar_image'])) {
            $width=$img_data[0];
            $height=$img_data[1];
            $img_body_length=filesize($user['avatar_image']);
            if ($width>0 && $height>0 && $img_body_length>0 && $img_body=file_get_contents($user['avatar_image'])) {
              mysql_query('INSERT INTO `'.$prefix.'binaryfile` ( `body`,
                                                                 `size`,
                                                                 `mime_type`,
                                                                 `last_modified`,
                                                                 `width`,
                                                                 `height`,
                                                                 `protected`
                                                                ) VALUES (
                                                                  "'.mysql_real_escape_string($img_body, $conn).'",
                                                                  "'.mysql_real_escape_string($img_body_length, $conn).'",
                                                                  "'.mysql_real_escape_string($img_data['mime'], $conn).'",
                                                                  NOW(),
                                                                  "'.mysql_real_escape_string($width, $conn).'",
                                                                  "'.mysql_real_escape_string($height, $conn).'",
                                                                  "log"
                                                                 )', $conn);
              $result2=mysql_query('SELECT LAST_INSERT_ID()', $conn);
              if ($data2=mysql_fetch_array($result2, MYSQL_NUM)) {
                if (!empty($data2[0])) {
                  mysql_query('INSERT INTO `'.$prefix.'avatar` ( `user_id`,
                                                                 `primary`,
                                                                 `binaryfile_id`
                                                                ) VALUES (
                                                                 "'.mysql_real_escape_string($user['id'], $conn).'",
                                                                 "y",
                                                                 "'.mysql_real_escape_string($data2[0], $conn).'"
                                                                )', $conn);
                }
              }
              mysql_free_result($result2);
            }
          }
        }
      }
    }
    unset($data);
    mysql_free_result($result);
  }
}

function restoreSmilies5() {
  global $conn;
  global $prefix;
  if ($result=mysql_query('SELECT `data` FROM `'.$prefix.'_chat_installdata` WHERE `id` = "smilie"', $conn)) {
    // Deleting existing smilies
    mysql_query('DELETE `sm`, `bf`
                   FROM `'.$prefix.'smilie` `sm`
                        LEFT JOIN `'.$prefix.'binaryfile` `bf` ON `bf`.`id` = `sm`.`binaryfile_id`');
    while ($data=mysql_fetch_array($result, MYSQL_ASSOC)) {
      if ($smilie=@unserialize($data['data'])) {
        if (   !empty($smilie['smilie_image'])
            && file_exists($smilie['smilie_image'])
            && is_readable($smilie['smilie_image'])) {
          $img_data=null;
          if ($img_data=getimagesize($smilie['smilie_image'])) {
            $width=$img_data[0];
            $height=$img_data[1];
            $img_body_length=filesize($smilie['smilie_image']);
            if ($width>0 && $height>0 && $img_body_length>0 && $img_body=file_get_contents($smilie['smilie_image'])) {
              mysql_query('INSERT INTO `'.$prefix.'binaryfile` ( `body`,
                                                                 `size`,
                                                                 `mime_type`,
                                                                 `last_modified`,
                                                                 `width`,
                                                                 `height`,
                                                                 `protected`
                                                                ) VALUES (
                                                                  "'.mysql_real_escape_string($img_body, $conn).'",
                                                                  "'.mysql_real_escape_string($img_body_length, $conn).'",
                                                                  "'.mysql_real_escape_string($img_data['mime'], $conn).'",
                                                                  NOW(),
                                                                  "'.mysql_real_escape_string($width, $conn).'",
                                                                  "'.mysql_real_escape_string($height, $conn).'",
                                                                  ""
                                                                 )', $conn);
              $result2=mysql_query('SELECT LAST_INSERT_ID()', $conn);
              if ($data2=mysql_fetch_array($result2, MYSQL_NUM)) {
                if (!empty($data2[0])) {
                  mysql_query('INSERT INTO `'.$prefix.'smilie` ( `binaryfile_id`,
                                                                 `code`,
                                                                 `description`
                                                                ) VALUES (
                                                                 "'.mysql_real_escape_string($data2[0], $conn).'",
                                                                 "'.mysql_real_escape_string($smilie['text'], $conn).'",
                                                                 "'.mysql_real_escape_string($smilie['text'], $conn).'"
                                                                )', $conn);
                }
              }
              mysql_free_result($result2);
            }
          }
        }
      }
    }
    unset($data);
    mysql_free_result($result);
  }
}

function restoreSettings5() {
  global $conn;
  global $prefix;
  if ($result=mysql_query('SELECT `data` FROM `'.$prefix.'_chat_installdata` WHERE `id` = "setting"', $conn)) {
    while ($data=mysql_fetch_array($result, MYSQL_ASSOC)) {
      if ($conf=@unserialize($data['data'])) {
        $update_array=array();
        switch ($conf['name']) {

          case 'title':
            $update_array=array('name'=>'chat_name', 'value'=>$conf['value']);
          break;

          case 'sender_name':
            $update_array=array('name'=>'chat_email_sender_name', 'value'=>$conf['value']);
          break;

          case 'sender_email':
            $update_array=array('name'=>'chat_email_sender_address', 'value'=>$conf['value']);
          break;

          case 'exit_url':
            $update_array=array('name'=>'exit_url', 'value'=>$conf['value']);
          break;

          case 'input_maxsize':
            $update_array=array('name'=>'message_length_max', 'value'=>$conf['value']);
          break;

          case 'main_refresh':
            if ($conf['value']>4) {
              $update_array=array('name'=>'updater_interval', 'value'=>$conf['value']);
            }
          break;

          case 'userroom_life':
            $update_array=array('name'=>'empty_userroom_lifetime', 'value'=>$conf['value']);
          break;

          case 'enable_userphotos':
            if (empty($conf['value'])) {
              $update_array=array('name'=>'avatars_max_count', 'value'=>'0');
            }
          break;

          case 'max_photo_size':
            $update_array=array('name'=>'avatar_max_filesize', 'value'=>$conf['value']);
          break;

          case 'delete_inactive':
            $update_array=array('name'=>'account_pruning', 'value'=>$conf['value']);
          break;

          case 'date_format':
            $update_array=array('name'=>'date_format', 'value'=>$conf['value']);
          break;

          case 'smiliesInRow':
            $update_array=array('name'=>'smilies_per_row', 'value'=>$conf['value']);
          break;

          case 'allow_guests':
            $update_array=array('name'=>'allow_guests', 'value'=>$conf['value']);
          break;

          case 'guest_color':
            $update_array=array('name'=>'default_nickname_color', 'value'=>$conf['value']);
          break;

          case 'max_roomimage_size':
            $update_array=array('name'=>'room_img_max_filesize', 'value'=>$conf['value']);
          break;

          case 'login_length_min':
            if ($conf['value']>=3 && $conf['value']<=30) {
              $update_array=array('name'=>'login_length_min', 'value'=>$conf['value']);
            }
          break;

          case 'login_length_max':
            if ($conf['value']>=3 && $conf['value']<=30) {
              $update_array=array('name'=>'login_length_max', 'value'=>$conf['value']);
            }
          break;

          case 'require_activation':
            $update_array=array('name'=>'activate_new_accounts', 'value'=>$conf['value']);
          break;

          case 'delete_notactivated':
            $update_array=array('name'=>'new_account_activation_timeout', 'value'=>$conf['value']);
          break;

          case 'email_validation_level':
            $update_array=array('name'=>'new_account_activation_timeout', 'value'=>$conf['value']);
          break;


        }
        if (!empty($update_array)) {
          mysql_query('UPDATE `'.$prefix.'config` SET `_conf_value` = "'.mysql_real_escape_string($update_array['value'], $conn).'" WHERE `_conf_name` = "'.mysql_real_escape_string($update_array['name'], $conn).'" LIMIT 1');
        }
      }
    }
    unset($data);
    mysql_free_result($result);
  }
}

function restoreRooms5() {
  global $conn;
  global $prefix;
  if ($result=mysql_query('SELECT `data` FROM `'.$prefix.'_chat_installdata` WHERE `id` = "room"', $conn)) {
    // Deleting existing rooms and categories
    mysql_query('DELETE FROM `'.$prefix.'category`');
    mysql_query('DELETE `ro`, `bf`
                   FROM `'.$prefix.'room` `ro`
                        LEFT JOIN `'.$prefix.'binaryfile` `bf` ON `bf`.`id` = `ro`.`background_image`');
    // Create default category
    mysql_query('INSERT INTO `'.$prefix.'category` ( `id`,
                                                     `parent_id`,
                                                     `name`,
                                                     `description`,
                                                     `creatable_rooms`,
                                                     `listpos`
                                                    ) VALUES (
                                                     "1",
                                                     "0",
                                                     "Default category",
                                                     "Default category",
                                                     "g",
                                                     "0"
                                                    )');
    // Get default message color
    $result2=mysql_query('SELECT `_conf_value` FROM `'.$prefix.'config` WHERE `_conf_name` = "default_message_color" LIMIT 1');
    if ($data=mysql_fetch_array($result2, MYSQL_NUM)) {
      mysql_free_result($result2);
      $room_default_message_color=$data[0];
    }
    if ($room_default_message_color=='') {
      $room_default_message_color='ff0000';
    }
    $room_listpos=0;
    while ($data=mysql_fetch_array($result, MYSQL_ASSOC)) {
      if ($room=@unserialize($data['data'])) {
        mysql_query('INSERT INTO `'.$prefix.'room` ( `category_id`,
                                                     `name`,
                                                     `description`,
                                                     `password`,
                                                     `default_message_color`,
                                                     `listpos`
                                                    ) VALUES (
                                                     "1",
                                                     "'.mysql_real_escape_string($room['name'], $conn).'",
                                                     "'.mysql_real_escape_string($room['name'], $conn).'",
                                                     "'.mysql_real_escape_string($room['password']!=''? md5($room['password']) : '', $conn).'",
                                                     "'.mysql_real_escape_string($room_default_message_color, $conn).'",
                                                     "'.mysql_real_escape_string($room_listpos++, $conn).'"
                                                    )', $conn);
        $result2=mysql_query('SELECT LAST_INSERT_ID()', $conn);
        if ($data2=mysql_fetch_array($result2, MYSQL_NUM)) {
          mysql_free_result($result2);
          if (!empty($data2[0])) {
            $room_id=$data2[0];
            // Check background image
            if (   !empty($room['background_image'])
                && file_exists($room['background_image'])
                && is_readable($room['background_image'])) {
              $img_data=null;
              if ($img_data=getimagesize($room['background_image'])) {
                $width=$img_data[0];
                $height=$img_data[1];
                $img_body_length=filesize($room['background_image']);
                if ($width>0 && $height>0 && $img_body_length>0 && $img_body=file_get_contents($room['background_image'])) {
                  mysql_query('INSERT INTO `'.$prefix.'binaryfile` ( `body`,
                                                                     `size`,
                                                                     `mime_type`,
                                                                     `last_modified`,
                                                                     `width`,
                                                                     `height`,
                                                                     `protected`
                                                                    ) VALUES (
                                                                      "'.mysql_real_escape_string($img_body, $conn).'",
                                                                      "'.mysql_real_escape_string($img_body_length, $conn).'",
                                                                      "'.mysql_real_escape_string($img_data['mime'], $conn).'",
                                                                      NOW(),
                                                                      "'.mysql_real_escape_string($width, $conn).'",
                                                                      "'.mysql_real_escape_string($height, $conn).'",
                                                                      ""
                                                                     )', $conn);
                  $result2=mysql_query('SELECT LAST_INSERT_ID()', $conn);
                  if ($data2=mysql_fetch_array($result2, MYSQL_NUM)) {
                    mysql_free_result($result2);
                    if (!empty($data2[0])) {
                      mysql_query('UPDATE `'.$prefix.'room` SET `background_image` = "'.mysql_real_escape_string($data2[0], $conn).'" WHERE `id` = "'.mysql_real_escape_string($room_id, $conn).'" LIMIT 1');
                    }
                  }
                }
              }
            }
          }
        }
      }
    }
    unset($data);
    mysql_free_result($result);
  }
}

function restoreBadWords5() {
  global $conn;
  global $prefix;
  if ($result=mysql_query('SELECT `data` FROM `'.$prefix.'_chat_installdata` WHERE `id` = "bad_word"', $conn)) {
    // Deleting existing bad words
    mysql_query('DELETE FROM `'.$prefix.'badword`');
    while ($data=mysql_fetch_array($result, MYSQL_ASSOC)) {
      if ($badword=@unserialize($data['data'])) {
        mysql_query('INSERT INTO `'.$prefix.'badword` ( `word`,
                                                        `replacement`
                                                      ) VALUES (
                                                        "'.mysql_real_escape_string($badword['word'], $conn).'",
                                                        "'.mysql_real_escape_string($badword['replacement'], $conn).'"
                                                      )', $conn);
      }
    }
    unset($data);
    mysql_free_result($result);
  }
}

function restoreIPFilter5() {
  global $conn;
  global $prefix;
  if ($result=mysql_query('SELECT `data` FROM `'.$prefix.'_chat_installdata` WHERE `id` = "ip_ban"', $conn)) {
    while ($data=mysql_fetch_array($result, MYSQL_ASSOC)) {
      if ($ip_ban=@unserialize($data['data'])) {
        mysql_query('INSERT INTO `'.$prefix.'ipfilter` ( `address`,
                                                         `added_on`,
                                                         `description`,
                                                         `action`
                                                       ) VALUES (
                                                         "'.mysql_real_escape_string($ip_ban['ip'], $conn).'",
                                                         "'.mysql_real_escape_string(date('Y-m-d H:i:s', $ip_ban['bandate']), $conn).'",
                                                         "Ban",
                                                         "d"
                                                       )', $conn);
      }
    }
    unset($data);
    mysql_free_result($result);
  }
}






?>