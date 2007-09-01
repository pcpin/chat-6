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
 * Set new user level
 */
$message=$l->g('access_denied');
$status='-1'; // -1: Session is invalid

if (!isset($profile_user_id)) $profile_user_id=0;

// Get client session
if (is_object($session) && !empty($profile_user_id) && !empty($current_user->id) && $current_user->is_admin==='y') {
  $message=$l->g('account_activated');
  $status=0;
  if ($current_user->_db_getList('activated', 'id = '.$profile_user_id, 1)) {
    if ($current_user->_db_list[0]['activated']==='n') {
      $current_user->activateUser($profile_user_id);
    }
    $current_user->_db_freeList();
  }

}

echo '<?xml version="1.0" encoding="UTF-8"?>
<pcpin_xml>
  <message>'.htmlspecialchars($message).'</message>
  <status>'.htmlspecialchars($status).'</status>
</pcpin_xml>';
die();
?>