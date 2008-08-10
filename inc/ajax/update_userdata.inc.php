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
 * Update user data. Following variables will be used (if set)
 * @param   array     $fields       Array with field id as KEY and field value as VAL
 */
if (empty($profile_user_id) || $profile_user_id!=$current_user->id && $current_user->is_admin!=='y') {
  $profile_user_id=$current_user->id;
}

if (!empty($profile_user_id) && $current_user->_db_getList('id', 'id =# '.$profile_user_id, 1) && !empty($custom_fields) && is_array($custom_fields)) {
  $current_user->_db_freeList();
  _pcpin_loadClass('userdata'); $userdata=new PCPIN_UserData($session);
  // Get current userdata
  $userdata_current=$userdata->getUserData($profile_user_id);
  $new_fields=array();
  foreach ($userdata_current as $val) {
    if (($val['writeable']=='user' || $current_user->is_admin==='y') && array_key_exists($val['id'], $custom_fields)) {
      if ($val['type']=='multichoice') {
        // Check values for multichoice field
        $choices_allowed="\n".$val['choices']."\n";
        $choices_new=$custom_fields[$val['id']]!=''? explode("\n", $custom_fields[$val['id']]) : array();
        $choices_checked=array();
        foreach ($choices_new as $choice) {
          if (false!==strpos($choices_allowed, "\n".$choice."\n")) {
            $choices_checked[]=$choice;
          }
        }
        $custom_fields[$val['id']]=!empty($choices_checked)? implode("\n", $choices_checked) : '';
      } elseif ($val['type']=='choice') {
        // Check value for choice field
        if (false===strpos("\n".$val['choices']."\n", "\n".$custom_fields[$val['id']]."\n")) {
          $custom_fields[$val['id']]='';
        }
      } else {
        $custom_fields[$val['id']]=trim($custom_fields[$val['id']]);
      }
      $new_fields[$val['id']]=$custom_fields[$val['id']];
    } else {
      $new_fields[$val['id']]=$val['field_value'];
    }
  }
  if (!empty($new_fields)) {
    // Delete old userdata fields
    $userdata->deleteUserData($profile_user_id);
    // Insert new fields
    $userdata->addNewUserData($profile_user_id, $new_fields);
  }
  $xmlwriter->setHeaderMessage($l->g('changes_saved'));
  $xmlwriter->setHeaderStatus(0);
} else {
  $xmlwriter->setHeaderMessage($l->g('error'));
  $xmlwriter->setHeaderStatus(1);
}
?>