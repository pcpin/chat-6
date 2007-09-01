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
* Get word blacklist
*/

$message=$l->g('access_denied');
$status='-1'; // -1: Session is invalid

_pcpin_loadClass('disallowed_name'); $disallowed_name=new PCPIN_Disallowed_Name($session);

$names_xml='';

if (!empty($current_user->id) && $current_user->is_admin==='y') {
  $message='OK';
  $status=0;
  $names=$disallowed_name->getDisallowedNames();
  foreach ($names as $name_data) {
    $names_xml.='  <name>
    <id>'.htmlspecialchars($name_data['id']).'</id>
    <name>'.htmlspecialchars($name_data['name']).'</name>
  </name>
';
  }
}


echo '<?xml version="1.0" encoding="UTF-8"?>
<pcpin_xml>
  <message>'.htmlspecialchars($message).'</message>
  <status>'.htmlspecialchars($status).'</status>
'.$names_xml
.'</pcpin_xml>';
die();
?>