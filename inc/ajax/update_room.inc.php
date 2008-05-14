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
_pcpin_loadClass('binaryfile'); $binaryfile=new PCPIN_BinaryFile($session);

if (!isset($room_id) || !pcpin_ctype_digit($room_id)) $room_id=0;
if (!isset($action) || !is_scalar($action)) $action='';
if (!isset($dir) || !pcpin_ctype_digit($dir)) $dir=0;
if (!isset($category_id) || !pcpin_ctype_digit($category_id)) $category_id=0;
if (!isset($name) || !is_scalar($name)) $name='';
if (!isset($description) || !is_scalar($description)) $description='';
if (!isset($default_message_color) || !is_scalar($default_message_color)) $default_message_color=$session->_conf_all['default_message_color'];
if (!isset($password_protect) || !pcpin_ctype_digit($password_protect)) $password_protect=0;
if (!isset($change_password) || !pcpin_ctype_digit($change_password)) $change_password=0;
if (!isset($password) || !is_scalar($password)) $password='';
if (!isset($image) || !pcpin_ctype_digit($image)) $image=0;


if (!empty($current_user->id) && $current_user->is_admin==='y' && $session->_s_user_id==$current_user->id) {
  $xmlwriter->setHeaderStatus(1);
  $xmlwriter->setHeaderMessage($l->g('error'));
  if (!empty($room_id) && $room->_db_getList('id = '.$room_id)) {
    // Room exists
    $xmlwriter->setHeaderStatus(0);
    $xmlwriter->setHeaderMessage('OK');
    $room_data=$room->_db_list[0];
    $room->_db_freelist();
    switch ($action) {

      case 'change_listpos':
        if (empty($dir)) {
          // Move up
          if ($room->_db_getList('id,listpos',
                                 'category_id = '.$room_data['category_id'],
                                 'listpos < '.$room_data['listpos'],
                                 'listpos DESC',
                                 1)) {
            $higher_room_id=$room->_db_list[0]['id'];
            $higher_room_listpos=$room->_db_list[0]['listpos'];
            // Update room
            $room->updateRoom($room_id, false, true, null, null, null, null, null, null, null, null, null, $higher_room_listpos);
            // Update higher room
            $room->updateRoom($higher_room_id, false, true, null, null, null, null, null, null, null, null, null, $room_data['listpos']);
          }
        } else {
          // Move down
          if ($room->_db_getList('id,listpos',
                                 'category_id = '.$room_data['category_id'],
                                 'listpos > '.$room_data['listpos'],
                                 'listpos ASC',
                                 1)) {
            $lower_room_id=$room->_db_list[0]['id'];
            $lower_room_listpos=$room->_db_list[0]['listpos'];
            // Update room
            $room->updateRoom($room_id, false, true, null, null, null, null, null, null, null, null, null, $lower_room_listpos);
            // Update lower room
            $room->updateRoom($lower_room_id, false, true, null, null, null, null, null, null, null, null, null, $room_data['listpos']);
          }
        }
      break;

      case 'change_data':
        $errortext=array();
        $name=trim($name);
        $description=trim($description);
        if (empty($category_id) || !$category->_db_getList('id', 'id = '.$category_id, 1)) {
          $errortext[]=$l->g('select_category');
        } elseif ($name=='') {
          $errortext[]=$l->g('room_name_empty');
        } elseif ($room->_db_getList('id != '.$room_id, 'category_id = '.$category_id, 'name LIKE '.$name, 1)) {
          $errortext[]=str_replace('[NAME]', $name, $l->g('room_already_exists_in_category'));
        } elseif (!empty($password_protect) && !empty($change_password) && _pcpin_strlen($password)<3) {
          $errortext[]=$l->g('password_too_short');
        }

        if (!empty($errortext)) {
          $xmlwriter->setHeaderStatus(1);
          $xmlwriter->setHeaderMessage(implode("\n", $errortext));
        } else {
          // Check image
          if (!empty($image)) {
            if ($tmpdata->_db_getList('binaryfile_id', 'user_id = '.$session->_s_user_id, 'type = 1', 1)) {
              // New image uploaded
              $binaryfile_id=$tmpdata->_db_list[0]['binaryfile_id'];
              $tmpdata->_db_freeList();
            } elseif ($room_data['background_image']==$image) {
              $binaryfile_id=$image;
            } else {
              $binaryfile_id=0;
            }
          } else {
            $binaryfile_id=0;
          }
          $tmpdata->deleteUserRecords($session->_s_user_id, 1, 0, true);
          $xmlwriter->setHeaderStatus(0);
          $xmlwriter->setHeaderMessage($l->g('changes_saved'));
          $room_password=null;
          if (!empty($password_protect) && !empty($change_password)) {
            $room_password=md5(base64_decode($password));
          } elseif (empty($password_protect)) {
            $room_password='';
          }
          if (empty($binaryfile_id) && !empty($room_data['background_image'])) {
            // Delete old image
            $binaryfile->deleteBinaryFile($room_data['background_image']);
          }
          $room->updateRoom($room_id, false, true,
                            null,
                            $name,
                            $category_id,
                            $description,
                            null,
                            $default_message_color,
                            $room_password,
                            $binaryfile_id
                            );
        }

      break;

    }
  }
}
?>