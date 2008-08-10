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


_pcpin_loadClass('userdata_field'); $userdata_field=new PCPIN_UserData_Field($session);

if (!isset($field_id)) $field_id=0;

$status=-1;
$messages=array();

if (is_object($session) && !empty($current_user->id) && $session->_s_user_id==$current_user->id && $current_user->is_admin==='y') {
  $status=1;
  if (!empty($field_id)) {
    if (isset($name)) {
      $name=trim($name);
      if ($name=='') {
        $messages[]=$l->g('name_empty_error');
      }
    }
    if (isset($type) && ($type=='choice' || $type=='multichoice')) {
      if (!isset($choices)) {
        $messages[]=$l->g('no_options_specified');
      } else {
        $choices=str_replace("\r", "\n", trim($choices));
        do {
          $choices=str_replace("\n\n", "\n", $choices);
        } while (false!==strpos($choices, "\n\n"));
        if ($choices=='') {
          $messages[]=$l->g('no_options_specified');
        }
      }
    }
    if (empty($messages)) {
      $userdata_field->updateField($field_id,
                                   isset($name)? $name : null,
                                   isset($default_value)? $default_value : null,
                                   isset($type)? $type : null,
                                   isset($choices)? $choices : null,
                                   isset($visibility)? $visibility : null,
                                   isset($writeable)? $writeable : null,
                                   isset($disabled)? $disabled : null
                                   );
      if (isset($order)) {
        $userdata_field->updateFieldOrder($field_id, !empty($order));
      }
      $status=0;
      $messages[]=$l->g('changes_saved');
    }
  }
  if (!empty($status) && empty($messages)) {
    $messages[]=$l->g('error');
  }
  $xmlwriter->setHeaderStatus($status);
  $xmlwriter->setHeaderMessage(implode("\n", $messages));
}
?>