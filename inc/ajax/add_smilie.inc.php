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

_pcpin_loadClass('smilie'); $smilie=new PCPIN_Smilie($session);
_pcpin_loadClass('tmpdata'); $tmpdata=new PCPIN_TmpData($session);
_pcpin_loadClass('binaryfile'); $binaryfile=new PCPIN_BinaryFile($session);

$errortext=array();
if (!isset($code)) $code='';
if (!isset($description)) $description='';

if (!empty($current_user->id) && $current_user->is_admin==='y') {

  if ($code=='') {
    $errortext[]=$l->g('smilie_code_empty_error');
  }

  $tmpdata->_db_getList('id, binaryfile_id', 'user_id = '.$current_user->id, 'type = 2', 1);
  if (empty($tmpdata->_db_list)) {
    $errortext[]=$l->g('smilie_image_empty_error');
  } else {
    $tmpdata_id=$tmpdata->_db_list[0]['id'];
    $binaryfile_id=$tmpdata->_db_list[0]['binaryfile_id'];
    $tmpdata->_db_freeList();
  }

  if ($smilie->_db_getList('code = '.$code, 1)) {
    $errortext[]=$l->g('smilie_code_already_exists_error');
    $smilie->_db_freeList();
  }

  if (empty($errortext)) {
    // Save smilie
    if ($smilie->addSmilie($binaryfile_id, $code, $description)) {
      // Delete temporary data
      $tmpdata->_db_freeList();
      $tmpdata->deleteUserRecords($session->_s_user_id, 2, 0, true);
      $status=0;
      $message=$l->g('smilie_added');
    } else {
      $status=1;
      $message=$l->g('error');
    }
  } else {
    $message=implode("\n", $errortext);
  }
}

echo '<?xml version="1.0" encoding="UTF-8"?>
<pcpin_xml>
<message>'.htmlspecialchars($message).'</message>
<status>'.htmlspecialchars($status).'</status>
</pcpin_xml>';
die();
?>