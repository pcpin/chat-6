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

if (!isset($room_id) || !pcpin_ctype_digit($room_id)) $room_id=0;

if (!empty($current_user->id) && $current_user->is_admin==='y' && $session->_s_user_id==$current_user->id) {
  $status=1;
  $message=$l->g('error');
  if (!empty($room_id) && $room->_db_getList('name', 'id = '.$room_id)) {
    // Room exists
    $status=0;
    $room_name=$room->_db_list[0]['name'];
    $message=str_replace('[NAME]', $room_name, $l->g('room_deleted'));
    // Delete room
    $room->deleteRoom($room_id);
  }
}

echo '<?xml version="1.0" encoding="UTF-8"?>
<pcpin_xml>
<message>'.htmlspecialchars($message).'</message>
<status>'.htmlspecialchars($status).'</status>
</pcpin_xml>';
die();
?>