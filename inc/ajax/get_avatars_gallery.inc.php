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
 * Get avatars owned by specified user
 * @param   int   $user_id    Optional. User ID. If empty, then $current_user->id will be assumed.
 */

_pcpin_loadClass('avatar'); $avatar=new PCPIN_Avatar($session);

$message=$l->g('access_denied');
$status='-1'; // -1: Session is invalid

$avatars_xml='';
if (!empty($current_user->id) && $current_user->is_admin==='y' && $session->_s_user_id==$current_user->id) {
  $message='OK';
  $status=0;
  $avatars=$avatar->getGalleryAvatars();
  foreach ($avatars as $avatar_data) {
    $avatars_xml.='
  <avatar>
    <id>'.htmlspecialchars($avatar_data['id']).'</id>
    <primary>'.htmlspecialchars($avatar_data['primary']).'</primary>
    <binaryfile_id>'.htmlspecialchars($avatar_data['binaryfile_id']).'</binaryfile_id>
    <width>'.htmlspecialchars($avatar_data['width']).'</width>
    <height>'.htmlspecialchars($avatar_data['height']).'</height>
  </avatar>';
  }
}

echo '<?xml version="1.0" encoding="UTF-8"?>
<pcpin_xml>
<message>'.htmlspecialchars($message).'</message>
<status>'.htmlspecialchars($status).'</status>
'.$avatars_xml.'
</pcpin_xml>';
die();
?>