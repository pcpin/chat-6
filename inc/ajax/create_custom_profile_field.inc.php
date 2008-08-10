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

$status=-1;
$messages=array();

if (!isset($name)) $name='';
if (!isset($default_value)) $default_value='';
if (!isset($type)) $type='';
if (!isset($choices)) $choices='';
if (!isset($visibility)) $visibility='';
if (!isset($writeable)) $writeable='';
if (!isset($disabled)) $disabled='';

if (is_object($session) && !empty($current_user->id) && $session->_s_user_id==$current_user->id && $current_user->is_admin==='y') {
  $status=1;
  if ($type!='') {
    $name=trim($name);
    if ($name=='') {
      $messages[]=$l->g('name_empty_error');
    }
    if ($type=='choice' || $type=='multichoice') {
      $choices=str_replace("\r", "\n", trim($choices));
      do {
        $choices=str_replace("\n\n", "\n", $choices);
      } while (false!==strpos($choices, "\n\n"));
      if ($choices=='') {
        $messages[]=$l->g('no_options_specified');
      }
    }
    if (empty($messages)) {
      if ($userdata_field->addNewField($name,
                                       $default_value,
                                       $type,
                                       $choices,
                                       $visibility,
                                       $writeable,
                                       $disabled
                                       )) {
        $status=0;
        $messages[]=$l->g('field_created');
      }
    }
  }
  if (!empty($status) && empty($messages)) {
    $messages[]=$l->g('error');
  }
  $xmlwriter->setHeaderStatus($status);
  $xmlwriter->setHeaderMessage(implode("\n", $messages));
}

?>