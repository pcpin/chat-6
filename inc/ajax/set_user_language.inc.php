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

if (empty($set_language_id) || !is_scalar($set_language_id)) {
  $set_language_id=$session->_s_language_id;
}

if (empty($profile_user_id) || $profile_user_id!=$current_user->id && $current_user->is_admin!=='y') {
  $profile_user_id=$current_user->id;
}

if ($profile_user_id!=$current_user->id) {
  $profile_user=new PCPIN_User($session);
  $profile_user->_db_loadObj($profile_user_id);
} else {
  $profile_user=&$current_user;
}

if (is_object($session) && !empty($profile_user->id)) {
  $xmlwriter->setHeaderMessage('OK');
  $xmlwriter->setHeaderStatus(0);
  if (!empty($session->_conf_all['allow_language_selection'])) {
    _pcpin_loadClass('language'); $profile_language=new PCPIN_Language($session);
    if ($set_language_id==$profile_language->checkLanguage($set_language_id)) {
      $profile_user->language_id=$set_language_id;
      $profile_user->_db_updateObj($profile_user->id, 'id');
      if ($profile_user_id==$session->_s_user_id) {
        $session->_s_updateSession($session->_s_id, true, true, $set_language_id);
      }
    }
  }
}
?>