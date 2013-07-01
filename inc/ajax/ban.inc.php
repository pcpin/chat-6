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

if (!empty($current_user->id) && $current_user->is_admin==='y' && $session->_s_user_id==$current_user->id) {
  $xmlwriter->setHeaderStatus(1);
  if (!empty($target_user_id) && $current_user->_db_getList('is_admin', 'id = '.$target_user_id, 1)) {
    // User exists
    // Check permissions
    if ($allowed=$current_user->_db_list[0]['is_admin']!='y') {
      // Action permitted
      $xmlwriter->setHeaderStatus(0);
      $xmlwriter->setHeaderMessage('OK');
      if ($session->_db_getList('_s_id,_s_room_id,_s_ip', '_s_user_id = '.$target_user_id, 1)) {
        // User is online
        $tgt_session_id=$session->_db_list[0]['_s_id'];
        $tgt_session_ip=$session->_db_list[0]['_s_ip'];
        if (!empty($ip_ban) && $tgt_session_ip==PCPIN_CLIENT_IP) {
          // Own IP address cannot be banned
          unset($ip_ban);
        }
        // Add new message
        if (empty($ip_ban)) {
          $msg->addMessage(10105, 'n', $current_user->id, $current_nickname, $session->_db_list[0]['_s_room_id'], 0, $target_user_id.'/'.$current_user->id.'/'.$duration.'/'.$reason, date('Y-m-d H:i:s'), 0, '');
        } else {
          $msg->addMessage(10106, 'n', $current_user->id, $current_nickname, $session->_db_list[0]['_s_room_id'], 0, $target_user_id.'/'.$current_user->id.'/'.$duration.'/'.$reason, date('Y-m-d H:i:s'), 0, '');
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
        if (!empty($ip_ban)) {
          // IP ban
          _pcpin_loadClass('ipfilter'); $ipfilter=new PCPIN_IPFilter($session);
          $ipfilter->addAddress(false === strpos($tgt_session_ip, ':')? 'IPv4' : 'IPv6', $tgt_session_ip, !empty($duration)? date('Y-m-d H:i:s', time()+$duration*60) : '', $reason, 'd');
        }
      }
      // Ban user
      $current_user->banUnban($target_user_id, 1, $duration, $reason, $current_user->id, $current_nickname);
    }
  }
}
?>