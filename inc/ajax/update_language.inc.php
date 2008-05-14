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
 * Update language
 * @param   int       $language_id        Language ID
 * @param   string    $iso_name           ISO name
 * @param   string    $local_name         Local name
 * @param   string    $active             Active flag
 */

if (!isset($language_id) || !is_scalar($language_id)) $language_id=0;
if (!isset($iso_name)) $iso_name='';
if (!isset($local_name)) $local_name='';
if (!isset($active)) $active='n';

$errortext=array();
if (is_object($session) && !empty($current_user->id) && $current_user->is_admin==='y') {
  $xmlwriter->setHeaderMessage('OK');
  $xmlwriter->setHeaderStatus(0);

  $language_id*=1;

  if ($l->_db_getList('id', 'id = '.$language_id, 1)) {

    $iso_name=strtolower(trim($iso_name));
    $local_name=trim($local_name);
    $active=strtolower(trim($active));

    if (!defined('PCPIN_ISO_LNG_'.strtoupper($iso_name))) {
      // Invalid ISO code
      $errortext[]=$l->g('error');
    } elseif ($l->_db_getList('name', 'iso_name = '.$iso_name, 'id != '.$language_id, 1)) {
      // Language already exists
      $errortext[]=str_replace('[NAME]', $l->_db_list[0]['name'], $l->g('language_already_exists'));
      $l->_db_freeList();
    } elseif ($active!='y' && !$l->_db_getList('id', 'id != '.$language_id, 'active = y', 1)) {
      // Last active language cannot be deactivated
      $errortext[]=$l->g('deactivate_language_last_active_error');
    }
    $l->_db_freeList();

    if (empty($errortext)) {
      // Update language
      $l->_db_updateRow($language_id, 'id', array('iso_name'=>$iso_name,
                                                  'name'=>substr(constant('PCPIN_ISO_LNG_'.strtoupper($iso_name)), 3),
                                                  'local_name'=>$local_name,
                                                  'active'=>$active,
                                                  ));
      if ($active=='n' && $session->_conf_all['default_language']==$language_id) {
        // Default language deactivated. Set new default language.
        if ($iso_name!='en') {
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
        
      }
      $xmlwriter->setHeaderMessage($l->g('language_updated'));
    }
  } else {
    $errortext[]=$l->g('error');
  }
}
if (!empty($errortext)) {
  $xmlwriter->setHeaderStatus(1);
  $xmlwriter->setHeaderMessage(implode("\n", $errortext));
}
?>