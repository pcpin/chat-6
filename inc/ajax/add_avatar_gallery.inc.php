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

_pcpin_loadClass('avatar'); $avatar=new PCPIN_Avatar($session);
_pcpin_loadClass('tmpdata'); $tmpdata=new PCPIN_TmpData($session);
_pcpin_loadClass('binaryfile'); $binaryfile=new PCPIN_BinaryFile($session);

$errortext=array();

if (!empty($current_user->id) && $current_user->is_admin==='y') {

  $tmpdata->_db_getList('id, binaryfile_id', 'user_id = '.$current_user->id, 'type = 4', 1);
  if (empty($tmpdata->_db_list)) {
    $errortext[]=$l->g('smilie_image_empty_error');
  } else {
    $tmpdata_id=$tmpdata->_db_list[0]['id'];
    $binaryfile_id=$tmpdata->_db_list[0]['binaryfile_id'];
    $tmpdata->_db_freeList();
  }

  if (empty($errortext)) {
    // Save avatar
    if ($avatar->addAvatar($binaryfile_id, 0)) {
      // Delete temporary data
      $tmpdata->_db_freeList();
      $tmpdata->deleteUserRecords($session->_s_user_id, 4, 0, true);
      $xmlwriter->setHeaderStatus(0);
      $xmlwriter->setHeaderMessage($l->g('avatar_uploaded'));
    } else {
      $xmlwriter->setHeaderStatus(1);
      $xmlwriter->setHeaderMessage($l->g('error'));
    }
  } else {
    $xmlwriter->setHeaderMessage(implode("\n", $errortext));
  }
}
?>