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
if (!isset($level)) $level='';


// Get client session
if (is_object($session) && !empty($profile_user_id) && !empty($current_user->id) && $current_user->is_admin==='y') {
  if ($current_user->_db_getList('id', 'id = '.$profile_user_id, 1) && ($level==='g' || $level==='r' || $level==='a')) {
    $current_user->_db_freeList();
    $is_admin=$level==='a'? 'y' : 'n';
    $is_guest=($level!=='a' && $level!=='r')? 'y' : 'n';
    $current_user->_db_updateRow($profile_user_id, 'id', array('is_admin'=>$is_admin, 'is_guest'=>$is_guest));
    $message=$l->g('changes_saved');
    $status=0;
  } else {
    $message=$l->g('error');
    $status=1;
  }

}

echo '<?xml version="1.0" encoding="UTF-8"?>
<pcpin_xml>
  <message>'.htmlspecialchars($message).'</message>
  <status>'.htmlspecialchars($status).'</status>
</pcpin_xml>';
die();
?>