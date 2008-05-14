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
 * Update moderator data
 */

_pcpin_loadClass('room'); $room=new PCPIN_Room($session);
_pcpin_loadClass('category'); $category=new PCPIN_Category($session);

if (!isset($moderator_user_id) || !pcpin_ctype_digit($moderator_user_id)) $moderator_user_id=0;
if (!isset($categories) || !is_scalar($categories)) $categories='';
if (!isset($rooms) || !is_scalar($rooms)) $rooms='';

// Get client session
if (is_object($session) && !empty($moderator_user_id) && !empty($current_user->id) && $current_user->is_admin==='y') {
  if ($current_user->_db_getList('id = '.$moderator_user_id, 1)) {
    $xmlwriter->setHeaderMessage('OK');
    $xmlwriter->setHeaderStatus(0);
    $current_user->_db_freeList();
    // Check categories
    $categories_new=array();
    $categories_array=explode(',', $categories);
    foreach ($categories_array as $category_id) {
      $category_id=trim($category_id);
      if (pcpin_ctype_digit($category_id) && $category->_db_getList('id', 'id = '.$category_id, 1)) {
        // Category exists
        $categories_new[]=$category_id;
        $category->_db_freeList();
      }
    }
    $categories_new=array_unique($categories_new);
    sort($categories_new);
    // Check rooms
    $rooms_new=array();
    $rooms_array=explode(',', $rooms);
    foreach ($rooms_array as $room_id) {
      $room_id=trim($room_id);
      if (pcpin_ctype_digit($room_id) && $room->_db_getList('id', 'id = '.$room_id, 1)) {
        // Room exists
        $rooms_new[]=$room_id;
        $room->_db_freeList();
      }
    }
    // Get categories' rooms
    if (!empty($categories_new) && $room->_db_getList('id', 'category_id IN '.implode(',', $categories_new))) {
      foreach ($room->_db_list as $room_data) {
        $rooms_new[]=$room_data['id'];
      }
      $room->_db_freeList();
    }
    $rooms_new=array_unique($rooms_new);
    sort($rooms_new);
    // Save data
    $current_user->_db_updateRow($moderator_user_id, 'id', array('moderated_categories'=>implode(',', $categories_new),
                                                                 'moderated_rooms'=>implode(',', $rooms_new),
                                                                 ));
    $xmlwriter->setHeaderMessage($l->g('changes_saved'));
  } else {
    $xmlwriter->setHeaderMessage($l->g('user_not_found'));
    $xmlwriter->setHeaderStatus(1);
  }

}
?>