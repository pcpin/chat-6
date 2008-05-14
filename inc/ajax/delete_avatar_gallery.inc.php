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

if (empty($avatar_id) || !pcpin_ctype_digit($avatar_id)) {
  $avatar_id=0;
}

_pcpin_loadClass('avatar'); $avatar=new PCPIN_Avatar($session);

if (!empty($current_user->id) && $current_user->is_admin==='y' && !empty($avatar_id)) {
  // Check for other gallery avatars
  if ($avatar->_db_getList('id', 'user_id = 0', 'id != '.$avatar_id, 1)) {
    $avatar->_db_freeList();
    // Delete avatar
    if ($avatar->deleteGalleryAvatar($avatar_id)) {
      $xmlwriter->setHeaderMessage($l->g('avatar_deleted'));
      $xmlwriter->setHeaderStatus(0);
    } else {
      $xmlwriter->setHeaderMessage($l->g('error'));
      $xmlwriter->setHeaderStatus(1);
    }
  } else {
    $xmlwriter->setHeaderMessage($l->g('delete_last_avatar_error'));
    $xmlwriter->setHeaderStatus(1);
  }
}
?>