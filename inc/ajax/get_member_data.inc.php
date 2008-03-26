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

/**
 * Get member data
 */
_pcpin_loadClass('room'); $room=new PCPIN_Room($session);
_pcpin_loadClass('category'); $category=new PCPIN_Category($session);

if (!isset($profile_user_id)) $profile_user_id=0;

$member_xml='';
$moderated_rooms='';
$moderated_categories='';

// Get client session
if (is_object($session) && !empty($profile_user_id) && !empty($current_user->id) && $current_user->is_admin==='y') {
  if ($current_user->_db_getList('id = '.$profile_user_id, 1)) {
    $message='OK';
    $status=0;
    $member=$current_user->_db_list[0];
    $current_user->_db_freeList();
    $room_ids=array();
    // Get moderated categories
    if (!empty($member['moderated_categories']) && $category->_db_getList('name', 'id IN '.$member['moderated_categories'], 'name ASC')) {
      foreach ($category->_db_list as $category_data) {
        $moderated_categories.='      <moderated_category>'.htmlspecialchars($category_data['name']).'</moderated_category>'."\n";
      }
      $category->_db_freeList();
      if ($room->_db_getList('id', 'category_id IN '.$member['moderated_categories'])) {
        foreach ($room->_db_list as $room_data) {
          $room_ids[]=$room_data['id'];
        }
        $room->_db_freeList();
      }
    }
    // Get moderated rooms
    if (!empty($member['moderated_rooms']) && $room->_db_getList('id', 'id IN '.$member['moderated_rooms'])) {
      foreach ($room->_db_list as $room_data) {
        $room_ids[]=$room_data['id'];
      }
      $room->_db_freeList();
    }
    $room_ids=array_unique($room_ids);
    if (!empty($room_ids) && $room->_db_getList('name', 'id IN '.implode(',', $room_ids), 'name ASC')) {
      foreach ($room->_db_list as $room_data) {
        $moderated_rooms.='      <moderated_room>'.htmlspecialchars($room_data['name']).'</moderated_room>'."\n";
      }
      $room->_db_freeList();
    }
    $member_xml='  <member_data>
    <login>'.htmlspecialchars($member['login']).'</login>
    <is_registered>'.htmlspecialchars($member['is_guest']==='n'? 1 : 0).'</is_registered>
    <is_admin>'.htmlspecialchars($member['is_admin']==='y'? 1 : 0).'</is_admin>
    <activated>'.htmlspecialchars($member['activated']==='y'? 1 : 0).'</activated>
'.$moderated_categories.$moderated_rooms.'
  </member_data>
';

  } else {
    $message=$l->g('user_not_found');
    $status=1;
  }

}

echo '<?xml version="1.0" encoding="UTF-8"?>
<pcpin_xml>
  <message>'.htmlspecialchars($message).'</message>
  <status>'.htmlspecialchars($status).'</status>
'.$member_xml
.'</pcpin_xml>';
die();
?>