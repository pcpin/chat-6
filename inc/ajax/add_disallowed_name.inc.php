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

_pcpin_loadClass('disallowed_name'); $disallowed_name=new PCPIN_Disallowed_Name($session);

$errortext=array();
if (!isset($name)) $name='';

if (!empty($current_user->id) && $current_user->is_admin==='y') {

  if ($name=='') {
    $errortext[]=$l->g('name_empty_error');
  }

  if ($disallowed_name->_db_getList('name = '.$name, 1)) {
    $errortext[]=str_replace('[NAME]', $name, $l->g('name_already_exists_error'));
    $disallowed_name->_db_freeList();
  }

  if (empty($errortext)) {
    // Save word
    if ($disallowed_name->addName($name)) {
      $xmlwriter->setHeaderStatus(0);
      $xmlwriter->setHeaderMessage(str_replace('[NAME]', $name, $l->g('name_added_to_filter')));
    } else {
      $xmlwriter->setHeaderStatus(1);
      $xmlwriter->setHeaderMessage($l->g('error'));
    }
  } else {
    $xmlwriter->setHeaderMessage(implode("\n", $errortext));
  }
}
?>