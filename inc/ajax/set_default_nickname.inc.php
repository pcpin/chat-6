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

_pcpin_loadClass('nickname'); $nickname=new PCPIN_Nickname($session);

if (empty($profile_user_id) || $profile_user_id!=$current_user->id && $current_user->is_admin!=='y') {
  $profile_user_id=$current_user->id;
}
if (empty($nickname_id) || !is_scalar($nickname_id)) {
  $nickname_id=0;
}

if (!empty($nickname_id) && $nickname->_db_getList('id,default', 'id = '.$nickname_id, 'user_id = '.$profile_user_id, 1)) {
  // Nickname exists
  $xmlwriter->setHeaderMessage('OK');
  $xmlwriter->setHeaderStatus(0);
  if ($nickname->_db_list[0]['default']!='y') {
    $nickname->setDefault($nickname_id, $profile_user_id);
  }
  $nickname->_db_freeList();
}
?>