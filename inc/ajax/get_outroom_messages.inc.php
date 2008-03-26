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

_pcpin_loadClass('invitation'); $invitation=new PCPIN_Invitation($session);

$invitations_xml='';
if (!empty($current_user->id)) {
  $message='OK';
  $status=0;
  $invitations=$invitation->getNewInvitations($current_user->id, false);
  foreach ($invitations as $invitation_data) {
    if (false===strpos(','.$current_user->muted_users.',', ','.$invitation_data['author_id'].',')) {
      $invitations_xml.='
  <invitation>
    <id>'.htmlspecialchars($invitation_data['id']).'</id>
    <author_id>'.htmlspecialchars($invitation_data['author_id']).'</author_id>
    <author_nickname>'.htmlspecialchars($invitation_data['author_nickname']).'</author_nickname>
    <room_id>'.htmlspecialchars($invitation_data['room_id']).'</room_id>
    <room_name>'.htmlspecialchars($invitation_data['room_name']).'</room_name>
  </invitation>';
    }
  }
}


echo '<?xml version="1.0" encoding="UTF-8"?>
<pcpin_xml>
<message>'.htmlspecialchars($message).'</message>
<status>'.htmlspecialchars($status).'</status>
'.$invitations_xml.'
'.$abuses_xml.'
</pcpin_xml>';
die();
?>