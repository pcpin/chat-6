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

$new_language_id=0;

if (!isset($src_language) || !is_scalar($src_language)) $src_language='';
if (!isset($dst_language) || !is_scalar($dst_language)) $dst_language='';


if (is_object($session) && !empty($current_user->id) && $current_user->is_admin==='y') {

  $src_language=strtolower(trim($src_language));
  $dst_language=strtolower(trim($dst_language));

  // Check language availability
  if (!$l->_db_getList('id', 'iso_name = '.$src_language, 1) || $l->_db_getList('id', 'iso_name = '.$dst_language, 1)) {
    $xmlwriter->setHeaderMessage($l->g('error'));
    $xmlwriter->setHeaderStatus(1);
  } else {
    $lang=new PCPIN_Language($session);
    if ($lang->copyLanguage($src_language, $dst_language)) {
      $new_language_id=$lang->id;
      unset($lang);
      $xmlwriter->setHeaderMessage('OK');
      $xmlwriter->setHeaderStatus(0);
    } else {
      $xmlwriter->setHeaderMessage($l->g('error'));
      $xmlwriter->setHeaderStatus(1);
    }
  }
}
$xmlwriter->setData(array('language_id'=>$new_language_id));
?>