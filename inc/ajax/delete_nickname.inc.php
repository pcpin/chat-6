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

_pcpin_loadClass('nickname'); $nickname=new PCPIN_Nickname($session);
_pcpin_loadClass('message'); $msg=new PCPIN_Message($session);

if (empty($profile_user_id) || $current_user->is_admin!=='y') {
  $profile_user_id=$current_user->id;
}

$nicknames_xml='';
if (!empty($profile_user_id) && !empty($nickname_id)) {
  // Delete nickname
  $nickname->deleteNickname($profile_user_id, $nickname_id);
  $message='OK';
  $status=$l->g('nickname_deleted');
  // Get nicknames list
  $nicknames=$nickname->getNicknames($profile_user_id);
  foreach ($nicknames as $nickname_data) {
    $nicknames_xml.='
  <nickname>
    <id>'.htmlspecialchars($nickname_data['id']).'</id>
    <nickname>'.htmlspecialchars($nickname_data['nickname']).'</nickname>
    <nickname_plain>'.htmlspecialchars($nickname_data['nickname_plain']).'</nickname_plain>
    <default>'.htmlspecialchars($nickname_data['default']).'</default>
  </nickname>';
  }
}

echo '<?xml version="1.0" encoding="UTF-8"?>
<pcpin_xml>
<message>'.htmlspecialchars($message).'</message>
<status>'.htmlspecialchars($status).'</status>
'.$nicknames_xml.'
</pcpin_xml>';
die();
?>