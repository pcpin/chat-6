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

if (!isset($target_user_id) || !is_scalar($target_user_id)) {
  $target_user_id=0;
}
if (!isset($action)) {
  $action=0;
}
if (empty($post_control_message)) {
  $post_control_message=false;
}

if (empty($profile_user_id) || $profile_user_id!=$current_user->id && $current_user->is_admin!=='y') {
  $profile_user_id=$current_user->id;
}

if ($profile_user_id!=$current_user->id) {
  $action_user=new PCPIN_User($session);
  $action_user->_db_loadObj($profile_user_id);
} else {
  $action_user=&$current_user;
}

if (!empty($action_user->id)) {
  $xmlwriter->setHeaderMessage('OK');
  $xmlwriter->setHeaderStatus(0);
  if (!empty($target_user_id) && ($action==1 || $action==0)) {
    $action_user->muteUnmuteLocally($target_user_id, $action);
    if (!empty($post_control_message)) {
      _pcpin_loadClass('message'); $message=new PCPIN_Message($session);
      $message->addMessage(10200, 'n', $current_user->id, $current_nickname, 0, $action_user->id, $action_user->id, '', 1, '');
    }
  }
}
$xmlwriter->setData(array('muted_users'=>$action_user->muted_users));
?>