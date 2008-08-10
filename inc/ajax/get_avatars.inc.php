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

if (empty($profile_user_id)) {
  $profile_user_id=$current_user->id;
}

$avatars_xml=array();
if (!empty($profile_user_id)) {
  $xmlwriter->setHeaderMessage('OK');
  $xmlwriter->setHeaderStatus(0);
  $avatars=$avatar->getAvatars($profile_user_id);
  foreach ($avatars as $avatar_data) {
    $avatars_xml[]=array('id'=>$avatar_data['id'],
                         'primary'=>$avatar_data['primary'],
                         'binaryfile_id'=>$avatar_data['binaryfile_id'],
                         'width'=>$avatar_data['width'],
                         'height'=>$avatar_data['height'],
                         );
  }
}
$xmlwriter->setData(array('avatar'=>$avatars_xml));
?>