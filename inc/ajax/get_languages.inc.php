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
* Get available languages list
*/

if (!empty($all_languages) && $current_user->is_admin!=='y') {
  unset($all_languages);
}
if (!empty($get_iso_names) && $current_user->is_admin!=='y') {
  unset($get_iso_names);
}

$languages_xml='';
$language_names_xml='';

if (is_object($session)) {
  $message='OK';
  $status=0;
  $languages=$l->getLanguages(!empty($all_languages));
  foreach ($languages as $language_data) {
    $languages_xml.='  <language>
    <id>'.htmlspecialchars($language_data['id']).'</id>
    <iso_name>'.htmlspecialchars($language_data['iso_name']).'</iso_name>
    <name>'.htmlspecialchars($language_data['name']).'</name>
    <local_name>'.htmlspecialchars($language_data['local_name']).'</local_name>
    <active>'.htmlspecialchars($language_data['active']).'</active>
  </language>
';
  }
  if (!empty($get_iso_names)) {
    // Get language names
    $consts=get_defined_constants();
    foreach ($consts as $const=>$data) {
      if (0===strpos($const, 'PCPIN_ISO_LNG_')) {
        $language_names_xml.='  <language_name>
    <iso_name>'.htmlspecialchars(substr($data, 0, 2)).'</iso_name>
    <name>'.htmlspecialchars(substr($data, 3)).'</name>
  </language_name>
';
      }
    }
  }
  unset($const);
  unset($consts);
}


echo '<?xml version="1.0" encoding="UTF-8"?>
<pcpin_xml>
  <message>'.htmlspecialchars($message).'</message>
  <status>'.htmlspecialchars($status).'</status>
'.$languages_xml
.$language_names_xml
.'</pcpin_xml>';
die();
?>