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

$languages=array();
$language_names=array();

if (is_object($session)) {
  $xmlwriter->setHeaderMessage('OK');
  $xmlwriter->setHeaderStatus(0);
  $languages=$l->getLanguages(!empty($all_languages));
  if (!empty($get_iso_names)) {
    // Get language names
    $consts=get_defined_constants();
    foreach ($consts as $const=>$data) {
      if (0===strpos($const, 'PCPIN_ISO_LNG_')) {
        $language_names[]=array('iso_name'=>substr($data, 0, 2),
                                'name'=>substr($data, 3)
                                );
      }
    }
  }
  unset($const);
  unset($consts);
}
$xmlwriter->setData(array('language'=>$languages, 'language_name'=>$language_names));
?>