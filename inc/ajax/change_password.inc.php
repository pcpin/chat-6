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

if (empty($profile_user_id) || $profile_user_id!=$current_user->id && $current_user->is_admin!=='y') {
  $profile_user_id=$current_user->id;
}
if ($profile_user_id!=$current_user->id) {
  $profile_user=new PCPIN_User($session);
  $profile_user->_db_loadObj($profile_user_id);
} else {
  $profile_user=&$current_user;
}

if (!empty($profile_user_id) && isset($password) && is_scalar($password) && false!==$password=base64_decode($password)) {
  $profile_user->password=md5($password);
  if ($profile_user->_db_updateObj($profile_user->id)) {
    $xmlwriter->setHeaderMessage($l->g('password_changed'));
    $xmlwriter->setHeaderStatus(0);
  } else {
    $xmlwriter->setHeaderMessage($l->g('error'));
    $xmlwriter->setHeaderStatus(1);
  }
}
?>