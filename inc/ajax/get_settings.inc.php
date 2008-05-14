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

if (!isset($group)) $group='';
$settings=array();

// Get client session
if (is_object($session) && !empty($current_user->id) && $session->_s_user_id==$current_user->id && $current_user->is_admin==='y') {
  $xmlwriter->setHeaderMessage('OK');
  $xmlwriter->setHeaderStatus(0);
  if (!empty($session->_conf_all_grouped[$group])) {
    // Create XML
    foreach ($session->_conf_all_grouped[$group] as $conf) {
      // Parse language expressions in "choices" field
      $conf['_conf_choices']=$l->addExpressionsString($conf['_conf_choices']);
      // Parse language expressions in "subgroup" field
      $conf['_conf_subgroup']=$l->addExpressionsString($conf['_conf_subgroup']);
      // Parse language expressions in "description" field
      $conf['_conf_description']=$l->addExpressionsString($conf['_conf_description']);
      if ($conf['_conf_value']===true) {
        $conf['_conf_value']=1;
      } elseif ($conf['_conf_value']===false) {
        $conf['_conf_value']=0;
      }
      $setting=array();
      foreach ($conf as $key=>$val) {
        $setting[substr($key, 6)]=$val;
      }
      $settings[]=$setting;
    }
  }
}
$xmlwriter->setData(array('setting'=>$settings));
?>