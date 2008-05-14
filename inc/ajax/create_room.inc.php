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

_pcpin_loadClass('room'); $room=new PCPIN_Room($session);
_pcpin_loadClass('category'); $category=new PCPIN_Category($session);
_pcpin_loadClass('tmpdata'); $tmpdata=new PCPIN_TmpData($session);

$type='p'; //todo
if (!isset($name) || !is_scalar($name)) $name='';
if (!isset($description) || !is_scalar($description)) $description='';
if (!isset($default_message_color) || !is_scalar($default_message_color)) $default_message_color=$session->_conf_all['default_message_color'];
if (!isset($password_protect) || !pcpin_ctype_digit($password_protect)) $password_protect=0;
if (!isset($password) || !is_scalar($password)) $password='';
if (!isset($image) || !pcpin_ctype_digit($image)) $image=0;


if (!empty($current_user->id) && $current_user->is_admin==='y' && $session->_s_user_id==$current_user->id) {
  $errortext=array();
  $name=trim($name);
  $description=trim($description);
  if (empty($category_id) || !$category->_db_getList('id', 'id = '.$category_id, 1)) {
    $errortext[]=$l->g('select_category');
  } elseif ($name=='') {
    $errortext[]=$l->g('room_name_empty');
  } elseif ($room->_db_getList('category_id = '.$category_id, 'name LIKE '.$name, 1)) {
    $errortext[]=str_replace('[NAME]', $name, $l->g('room_already_exists_in_category'));
  } elseif (!empty($password_protect) && !empty($change_password) && _pcpin_strlen($password)<3) {
    $errortext[]=$l->g('password_too_short');
  }

  if (!empty($errortext)) {
    $xmlwriter->setHeaderStatus(1);
    $xmlwriter->setHeaderMessage(implode("\n", $errortext));
  } else {
    // Check image
    if (!empty($image) && $tmpdata->_db_getList('binaryfile_id', 'user_id = '.$session->_s_user_id, 'type = 1', 1)) {
      $binaryfile_id=$tmpdata->_db_list[0]['binaryfile_id'];
      $tmpdata->_db_freeList();
    } else {
      $binaryfile_id=0;
    }
    $tmpdata->deleteUserRecords($session->_s_user_id, 1, 0, true);

    if (!empty($password_protect)) {
      $room_password=md5(base64_decode($password));
    } else {
      $room_password='';
    }
    $room->createRoom($category_id,
                      $type,
                      $name,
                      $description,
                      $default_message_color,
                      $password,
                      $binaryfile_id);
    $xmlwriter->setHeaderStatus(0);
    $xmlwriter->setHeaderMessage(str_replace('[NAME]', $name, $l->g('room_created')));
  }
}
?>