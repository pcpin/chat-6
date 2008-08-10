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

_pcpin_loadClass('avatar'); $avatar=new PCPIN_Avatar($session);

if (empty($profile_user_id) || $profile_user_id!=$current_user->id && $current_user->is_admin!=='y') {
  $profile_user_id=$current_user->id;
}
if (empty($avatar_id) || !is_scalar($avatar_id)) {
  $avatar_id=0;
}
if (!empty($profile_user_id) && !empty($avatar_id) && $avatar->_db_getList('id,binaryfile_id', 'id = '.$avatar_id, 'user_id = 0', 1)) {
  $binaryfile_id=$avatar->_db_list[0]['binaryfile_id'];
  // Check avatars limit
  $avatar->_db_getList('COUNT', 'user_id = '.$profile_user_id);
  if ($avatar->_db_list_count>=$session->_conf_all['avatars_max_count']) {
    // Limit reached
    $xmlwriter->setHeaderMessage(str_replace('[NUMBER]', $session->_conf_all['avatars_max_count'], $l->g('avatars_limit_reached')));
    $xmlwriter->setHeaderStatus(1);
  } elseif ($avatar->_db_getList('id', 'user_id = '.$profile_user_id, 'binaryfile_id = '.$binaryfile_id, 1)) {
    // Selected avatar already exists
    $xmlwriter->setHeaderMessage($l->g('avatar_already_exists'));
    $xmlwriter->setHeaderStatus(1);
  } else {
    // Set avatar
    if ($avatar->setAvatarFromGallery($profile_user_id, $avatar_id)) {
      $xmlwriter->setHeaderMessage('OK');
      $xmlwriter->setHeaderStatus(0);
    } else {
      $xmlwriter->setHeaderMessage($l->g('error'));
      $xmlwriter->setHeaderStatus(1);
    }
  }
}
?>