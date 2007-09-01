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

$message=$l->g('access_denied');
$status='-1'; // -1: Session is invalid

if (!isset($target_user_id) || !is_scalar($target_user_id)) {
  $target_user_id=0;
}

if (!isset($action)) {
  $action=0;
}

if (!empty($current_user->id)) {
  $message='OK';
  $status=0;
  if (!empty($target_user_id) && ($action==1 || $action==0)) {
    $current_user->muteUnmuteLocally($target_user_id, $action);
  }
}

echo '<?xml version="1.0" encoding="UTF-8"?>
<pcpin_xml>
<message>'.htmlspecialchars($message).'</message>
<status>'.htmlspecialchars($status).'</status>
<muted_users>'.htmlspecialchars($current_user->muted_users).'</muted_users>
</pcpin_xml>';
die();
?>