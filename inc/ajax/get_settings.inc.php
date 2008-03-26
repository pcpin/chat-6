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
$settings_xml='';


// Get client session
if (is_object($session) && !empty($current_user->id) && $session->_s_user_id==$current_user->id && $current_user->is_admin==='y') {

  $message='OK';
  $status=0;
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
      $settings_xml.='    <setting>
      <id>'.htmlspecialchars($conf['_conf_id']).'</id>
      <group>'.htmlspecialchars($conf['_conf_group']).'</group>
      <subgroup>'.htmlspecialchars($conf['_conf_subgroup']).'</subgroup>
      <name>'.htmlspecialchars($conf['_conf_name']).'</name>
      <value>'.htmlspecialchars($conf['_conf_value']).'</value>
      <type>'.htmlspecialchars($conf['_conf_type']).'</type>
      <choices>'.htmlspecialchars($conf['_conf_choices']).'</choices>
      <description>'.htmlspecialchars($conf['_conf_description']).'</description>
    </setting>
';
    }
  }

}


echo '<?xml version="1.0" encoding="UTF-8"?>
<pcpin_xml>
  <message>'.htmlspecialchars($message).'</message>
  <status>'.htmlspecialchars($status).'</status>
  <settings>
'.$settings_xml.'</settings>
</pcpin_xml>';
die();
?>