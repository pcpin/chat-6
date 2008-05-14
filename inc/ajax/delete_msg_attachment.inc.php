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
 * Delete temporary message attachment
 * @param   int    $binaryfile_id   Binaryfile ID of the attachment
 */

if (!isset($binaryfile_id) || !pcpin_ctype_digit($binaryfile_id)) $binaryfile_id=0;

_pcpin_loadClass('tmpdata'); $tmpdata=new PCPIN_TmpData($session);

if (!empty($current_user->id)) {
  $xmlwriter->setHeaderStatus(0);
  $xmlwriter->setHeaderMessage('OK');
  if (!empty($binaryfile_id)) {
    $tmpdata->deleteUserRecords($current_user->id, 3, $binaryfile_id);
  }
}
?>