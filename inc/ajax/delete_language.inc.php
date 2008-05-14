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
 * Delete banner
 */

if (!isset($language_id) || !is_scalar($language_id)) $language_id=0;

// Get client session
if (is_object($session) && !empty($current_user->id) && $current_user->is_admin==='y') {
  // Check language availability
  $languages=$l->getLanguages(true);
  $language_found=false;
  $active_language_needed=false;
  $active_language_found=false;
  foreach ($languages as $language_data) {
    if ($language_data['id']==$language_id) {
      $language_found=true;
      $active_language_needed=$language_data['active']=='y';
    } elseif ($language_data['active']=='y') {
      $active_language_found=true;
    }
    if ($language_found && $active_language_needed && $active_language_found) {
      break;
    }
  }
  if ($language_found) {
    if ($active_language_needed && !$active_language_found) {
      // Selected language is last active language and cannot be deleted
      $xmlwriter->setHeaderMessage($l->g('delete_language_last_active_error'));
      $xmlwriter->setHeaderStatus(1);
    } else {
      // Delete language
      if ($l->deleteLanguage($language_id)) {
        $xmlwriter->setHeaderMessage($l->g('language_deleted'));
        $xmlwriter->setHeaderStatus(0);
        if ($session->_conf_all['default_language']==$language_id) {
          // Set new default language
          // Trying to set English
          if ($l->_db_getList('id', 'iso_name = en', 'active = y', 1)) {
            // English exists and active
            $session->_conf_updateSettings(array('default_language'=>$l->_db_list[0]['id']));
            $l->_db_freeList();
          } else {
            // Set first available language
            $l->_db_getList('id', 'active = y', 'id ASC', 1);
            $session->_conf_updateSettings(array('default_language'=>$l->_db_list[0]['id']));
            $l->_db_freeList();
          }
        }
        // Check default language
      } else {
        $xmlwriter->setHeaderMessage($l->g('error'));
        $xmlwriter->setHeaderStatus(1);
      }
    }
  } else {
    $xmlwriter->setHeaderMessage($l->g('error'));
    $xmlwriter->setHeaderStatus(1);
  }
}
?>