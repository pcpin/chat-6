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

_pcpin_loadClass('smilie'); $smilie=new PCPIN_Smilie($session);
_pcpin_loadClass('tmpdata'); $tmpdata=new PCPIN_TmpData($session);
_pcpin_loadClass('binaryfile'); $binaryfile=new PCPIN_BinaryFile($session);

$errortext=array();

if (!empty($current_user->id) && $current_user->is_admin==='y') {

  // Delete smilie
  if (!empty($smilie_id) && pcpin_ctype_digit($smilie_id) && $smilie->deleteSmilie($smilie_id)) {
    $xmlwriter->setHeaderStatus(0);
    $xmlwriter->setHeaderMessage($l->g('smilie_deleted'));
  } else {
    $xmlwriter->setHeaderStatus(1);
    $xmlwriter->setHeaderMessage($l->g('error'));
  }
}
?>