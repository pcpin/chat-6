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

_pcpin_loadClass('nickname'); $nickname=new PCPIN_Nickname($session);
_pcpin_loadClass('room'); $room=new PCPIN_Room($session);

if (!isset($nickname_id) || !is_scalar($nickname_id)) $nickname_id=0;
if (!isset($room_id) || !is_scalar($room_id)) $room_id=0;
if (!isset($stealth_mode)) $stealth_mode='n';
if (!isset($password)) $password='';

if (!empty($current_user->id)) {
  $enter_allowed=true;
  if (empty($nickname_id)) {
    // No nickname selected. Using default.
    if ($nickname->_db_getList('id', 'user_id = '.$current_user->id, 'default = y', 1)) {
      $nickname_id=$nickname->_db_list[0]['id'];
    }
  }
  if (empty($nickname_id)) {
    // No nickname selected
    $enter_allowed=false;
    $status=100;
    $message=$l->g('error');
  } elseif (!$nickname->_db_getList('id = '.$nickname_id, 'user_id = '.$current_user->id, 1)) {
    // Nickname does not exists ot not belongs to user
    $enter_allowed=false;
    $status=200;
    $message=$l->g('error');
  } elseif (empty($room_id)) {
    // No room selected
    $enter_allowed=false;
    $status=300;
    $message=$l->g('error');
  } elseif (!$room->_db_getList('id, category_id, password', 'id = '.$room_id, 1)) {
    // Room does not exists
    $enter_allowed=false;
    $status=400;
    $message=$l->g('room_not_exists');
  } elseif ($current_user->is_admin!=='y' && $room->_db_list[0]['password']!='' && $room->_db_list[0]['password']!=md5(base64_decode($password)) && false===strpos(','.$current_user->moderated_rooms.',', ','.$room_id.',')) {
    // Invalid password
    $enter_allowed=false;
    $status=600;
    $message=$l->g('invalid_password');
  } else {
    $category_id=$room->_db_list[0]['category_id'];
    if ($nickname->_db_list[0]['default']!='y') {
      // Save nickname as default
      $nickname->setDefault($nickname_id, $session->_s_user_id);
    }
    // Check "stealth" mode
    if ($stealth_mode=='y') {
      if ($current_user->is_admin!=='y' && false===strpos(','.$current_user->moderated_rooms.',', ','.$room_id.',')) {
        $stealth_mode='n';
      }
    } else {
      $stealth_mode='n';
    }
    // Enter room
    if ($room->putUser($session->_s_user_id, $room_id, $stealth_mode=='y', $stealth_mode)) {
      $status=0;
      $message='OK';
    } else {
      $status=500;
      $message=$l->g('error');
    }
  }
}

echo '<?xml version="1.0" encoding="UTF-8"?>
<pcpin_xml>
<message>'.htmlspecialchars($message).'</message>
<status>'.htmlspecialchars($status).'</status>
</pcpin_xml>';
die();
?>