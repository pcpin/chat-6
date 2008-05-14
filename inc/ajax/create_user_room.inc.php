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

$room_id=0;

_pcpin_loadClass('category'); $category=new PCPIN_Category($session);
_pcpin_loadClass('tmpdata'); $tmpdata=new PCPIN_TmpData($session);
_pcpin_loadClass('room'); $room=new PCPIN_Room($session);
_pcpin_loadClass('message'); $msg=new PCPIN_Message($session);
_pcpin_loadClass('binaryfile'); $binaryfile=new PCPIN_BinaryFile($session);

$errortext=array();
if (!isset($name)) $name='';
if (!isset($description)) $description='';
if (!isset($password) || !is_scalar($password)) $password='';

if (!empty($current_user->id) && !empty($category_id) && is_scalar($category_id)) {
  if (!$category->_db_getList('name, creatable_rooms', 'id = '.$category_id, 1)) {
    // Category does not exists
    $xmlwriter->setHeaderStatus(1);
    $errortext[]=$l->g('category_not_exists');
  } elseif ($category->_db_list[0]['creatable_rooms']=='n' || $category->_db_list[0]['creatable_rooms']=='r' && $current_user->is_guest=='y') {
    // New user room cannot be created in this category
    $xmlwriter->setHeaderStatus(1);
    $errortext[]=$l->g('user_room_create_category_error');
  } else {
    $name=trim($name);
    $description=trim($description);
    if ($name=='') {
      $xmlwriter->setHeaderStatus(1);
      $errortext[]=$l->g('room_name_empty');
    } elseif ($room->_db_getList('id', 'category_id = '.$category_id, 'name = '.$name)) {
      // Duplicate room name
      $xmlwriter->setHeaderStatus(1);
      $errortext[]=str_replace('[NAME]', $name, $l->g('room_already_exists_in_category'));
    }
    if (!empty($password_protect)) {
      $password=base64_decode($password);
      if (_pcpin_strlen($password)<3) {
        $xmlwriter->setHeaderStatus(1);
        $errortext[]=$l->g('password_too_short');
      }
    }
  }
  if (empty($errortext)) {
    // Check image
    $background_image=0;
    if (!empty($image) && $tmpdata->_db_getList('id, binaryfile_id', 'user_id = '.$current_user->id, 'type = 1', 1)) {
      // There is an image
      $background_image=$tmpdata->_db_list[0]['binaryfile_id'];
      // Delete temporary data
      $tmpdata->_db_freeList();
      $tmpdata->deleteUserRecords($session->_s_user_id, 1, 0, true);
    }
    if ($room->createRoom($category_id, 'u', $name, $description, $session->_conf_all['default_message_color'], !empty($password_protect)? $password : '', $background_image)) {
      $xmlwriter->setHeaderStatus(0);
      $xmlwriter->setHeaderMessage('OK');
      // Room created
      $room_id=$room->id;
      // Add system message
      $msg->addMessage(1100, 'n', 0, '', 0, 0, '-', date('Y-m-d H:i:s'), 0, '');
    } else {
      $xmlwriter->setHeaderStatus(1);
      $xmlwriter->setHeaderMessage($l->g('error'));
    }
  } else {
    $xmlwriter->setHeaderMessage(implode("\n", $errortext));
  }
}
$xmlwriter->setData(array('room_id'=>$room_id));
?>