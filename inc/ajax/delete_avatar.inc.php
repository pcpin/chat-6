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
 * Delete avatar
 * @param   int   $avatar_id      Avatar ID
 */

$message=$l->g('access_denied');
$status='-1'; // -1: Session is invalid

if (empty($avatar_id) || !pcpin_ctype_digit($avatar_id)) {
  $avatar_id=0;
}

if (empty($profile_user_id) || $current_user->is_admin!=='y') {
  $profile_user_id=$current_user->id;
}

_pcpin_loadClass('avatar'); $avatar=new PCPIN_Avatar($session);
_pcpin_loadClass('message'); $msg=new PCPIN_Message($session);

if (!empty($profile_user_id) && !empty($avatar_id)) {
  // Delete avatar
  $avatar->deleteAvatar($profile_user_id, $avatar_id);
  $message=$l->g('avatar_deleted');
  $status=0;
  $msg->addMessage(1010, 'n', 0, '', $session->_s_room_id, 0, $profile_user_id);
}

echo '<?xml version="1.0" encoding="UTF-8"?>
<pcpin_xml>
<message>'.htmlspecialchars($message).'</message>
<status>'.htmlspecialchars($status).'</status>
</pcpin_xml>';
die();
?>