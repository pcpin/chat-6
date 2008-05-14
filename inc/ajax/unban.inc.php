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


if (!empty($current_user->id) && $current_user->is_admin==='y' && $session->_s_user_id==$current_user->id) {
  $xmlwriter->setHeaderStatus(1);
  if (!empty($target_user_id) && $current_user->_db_getList('banned_permanently,banned_until', 'id = '.$target_user_id, 1)) {
    // User exists
    $xmlwriter->setHeaderStatus(0);
    $xmlwriter->setHeaderMessage('OK');
    if ($current_user->_db_list[0]['banned_permanently']=='y' || $current_user->_db_list[0]['banned_until']>date('Y-m-d H:i:s')) {
      // Add new message
      $msg->addMessage(10107, 'n', $current_user->id, $current_nickname, 0, 0, $target_user_id.'/'.$current_user->id, date('Y-m-d H:i:s'), 0, '');
      // Unban user
      $current_user->banUnban($target_user_id, 0);
    }
  }
}
?>