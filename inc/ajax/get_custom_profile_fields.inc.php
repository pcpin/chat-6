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

$fields=array();

if (is_object($session) && !empty($current_user->id) && $session->_s_user_id==$current_user->id && $current_user->is_admin==='y') {
  $xmlwriter->setHeaderMessage('OK');
  $xmlwriter->setHeaderStatus(0);
  $fields=$userdata_field->getFields();
}
$xmlwriter->setData(array('field'=>$fields));
?>