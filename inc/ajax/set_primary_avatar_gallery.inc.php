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

if (empty($avatar_id) || !pcpin_ctype_digit($avatar_id)) {
  $avatar_id=0;
}

_pcpin_loadClass('avatar'); $avatar=new PCPIN_Avatar($session);

if (!empty($current_user->id) && $current_user->is_admin==='y' && !empty($avatar_id) && $avatar->_db_getList('primary', 'id = '.$avatar_id, 'user_id = 0', 1)) {
  // Avatar exists
  $xmlwriter->setHeaderMessage('OK');
  $xmlwriter->setHeaderStatus(0);
  if ($avatar->_db_list[0]['primary']!='y') {
    $avatar->setDefaultAvatarGallery($avatar_id);
  }
  $avatar->_db_freeList();
}
?>