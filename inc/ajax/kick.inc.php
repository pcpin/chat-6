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

if (!empty($current_user->id)) {
  $xmlwriter->setHeaderStatus(1);
  if (!empty($target_user_id) && $current_user->_db_getList('is_admin,moderated_rooms', 'id = '.$target_user_id, 1)) {
    // User exists
    if ($session->_db_getList('_s_ip,_s_id,_s_room_id', '_s_user_id = '.$target_user_id, 1)) {
      // User is online
      $tgt_session_id=$session->_db_list[0]['_s_id'];
      $tgt_session_ip=$session->_db_list[0]['_s_ip'];
      // Check permissions
      $allowed=   $current_user->is_admin==='y'
               || $current_user->_db_list[0]['is_admin']!='y'
                  && !empty($session->_db_list[0]['_s_room_id'])
                  && $current_user->moderated_rooms!=''
                  && false!==strpos(','.$current_user->moderated_rooms.',', ','.$session->_db_list[0]['_s_room_id'].',')
                  && false===strpos(','.$current_user->_db_list[0]['moderated_rooms'].',', ','.$session->_db_list[0]['_s_room_id'].',');
      if (true==$allowed) {
        $xmlwriter->setHeaderStatus(0);
        $xmlwriter->setHeaderMessage('OK');
        // Action permitted
        // Add new message
        $msg->addMessage(10101, 'n', $current_user->id, $current_nickname, $session->_db_list[0]['_s_room_id'], 0, $target_user_id.'/'.$current_user->id.'/'.$reason, date('Y-m-d H:i:s'), 0, '');
        // Ban, if needed
        if (!empty($session->_conf_all['ban_kicked'])) {
          $current_user->banUnban($target_user_id, 1, $session->_conf_all['ban_kicked'], $reason, $current_user->id, $current_nickname);
        }
        // Kick user
        if (!empty($tgt_session_id)) {
          $session->_s_updateSession($tgt_session_id, false, true,
                                     null,
                                     null,
                                     null,
                                     null,
                                     null,
                                     null,
                                     null,
                                     null,
                                     'y'
                                     );
          $session->_s_cleanUp();
        }
      }
    }
  } 
}
?>