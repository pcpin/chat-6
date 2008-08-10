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

$nicknames_xml=array();
if (!empty($current_user->id)) {
  $xmlwriter->setHeaderMessage('OK');
  $xmlwriter->setHeaderStatus(0);
  $nicknames=$nickname->getNicknames($profile_user_id);
  foreach ($nicknames as $nickname_data) {
    $nicknames_xml[]=array('id'=>$nickname_data['id'],
                           'nickname'=>$nickname_data['nickname'],
                           'nickname_plain'=>$nickname_data['nickname_plain'],
                           'default'=>$nickname_data['default']
                           );
  }
}
$xmlwriter->setData(array('nickname'=>$nicknames_xml));
?>