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

_pcpin_loadClass('message'); $msg=new PCPIN_Message($session);

if (!isset($target_user_id) || !is_scalar($target_user_id)) {
  $target_user_id=0;
}

if (!isset($reason) || !is_scalar($reason)) {
  $reason='';
} else {
  $reason=trim($reason);
}

if (!isset($duration) || !is_scalar($duration) || !pcpin_ctype_digit($duration)) {
  $duration=0;
}

if (!empty($current_user->id)) {
  $xmlwriter->setHeaderStatus(1);
  if (!empty($target_user_id) && $current_user->_db_getList('is_admin', 'id = '.$target_user_id, 1)) {
    // User exists
    // Check permissions
    $allowed=$current_user->is_admin==='y' && $current_user->_db_list[0]['is_admin']!='y';
    if (true==$allowed) {
      $xmlwriter->setHeaderStatus(0);
      $xmlwriter->setHeaderMessage('OK');
      // Action permitted
      if ($session->_db_getList('_s_room_id', '_s_user_id = '.$target_user_id, 1)) {
        // Add new message
        if (!empty($action)) {
          // User will be muted
          $msg->addMessage(10110, 'n', $current_user->id, $current_nickname, $session->_db_list[0]['_s_room_id'], 0, $target_user_id.'/'.$current_user->id.'/'.$duration.'/'.$reason, date('Y-m-d H:i:s'), 0, '');
        } else {
          // User will be unmuted
          $msg->addMessage(10111, 'n', $current_user->id, $current_nickname, $session->_db_list[0]['_s_room_id'], 0, $target_user_id.'/'.$current_user->id, date('Y-m-d H:i:s'), 0, '');
        }
      }
      // Mute / Unmute user
      $current_user->globalMuteUnmute($target_user_id, !empty($action)? 1 : 0, $duration, $reason, $current_user->id, $current_nickname);
    }
  } 
}
?>