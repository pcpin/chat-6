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

_pcpin_loadClass('category'); $category=new PCPIN_Category($session);

$parent_id=0; // todo
if (!isset($name) || !is_scalar($name)) $name='';
if (!isset($description) || !is_scalar($description)) $description='';
if (!isset($creatable_rooms) || !is_scalar($creatable_rooms)) $creatable_rooms='n';

$errortext=array();

if (!empty($current_user->id) && $current_user->is_admin==='y' && $session->_s_user_id==$current_user->id) {
  $errortext=array();
  $name=trim($name);
  $description=trim($description);
  $creatable_rooms=trim($creatable_rooms);
  if ($name=='') {
    $errortext[]=$l->g('category_name_empty');
  } elseif ($category->_db_getList('parent_id = '.$parent_id, 'name LIKE '.$name, 1)) {
    $errortext[]=str_replace('[NAME]', $name, $l->g('category_name_exists'));
  }

  if (!empty($errortext)) {
    $status=1;
    $message=implode("\n", $errortext);
  } else {
    $status=0;
    $message=str_replace('[NAME]', $name, $l->g('category_created'));
    $category->addCategory($parent_id, $name, $description, $creatable_rooms);
  }
}

echo '<?xml version="1.0" encoding="UTF-8"?>
<pcpin_xml>
<message>'.htmlspecialchars($message).'</message>
<status>'.htmlspecialchars($status).'</status>
</pcpin_xml>';
die();
?>