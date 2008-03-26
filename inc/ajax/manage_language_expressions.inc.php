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

_pcpin_loadClass('language_expression'); $language_expression=new PCPIN_Language_Expression($session);

$expressions_xml='';
$total_count=0;

if (!isset($language_id) || !is_scalar($language_id)) $language_id=0;
if (!isset($start_from) || !is_scalar($start_from)) $start_from=0;
if (!isset($max_results) || !is_scalar($max_results)) $max_results=100;

if (is_object($session) && !empty($current_user->id) && $current_user->is_admin==='y') {
  $message='OK';
  $status=0;
  if (!empty($language_id) && $l->_db_getList('id', 'id = '.$language_id, 1)) {

    if (!empty($update_lng_expr) && is_array($update_lng_expr)) {
      foreach ($update_lng_expr as $code=>$value) {
        $language_expression->updateExpression($language_id, $code, $value);
      }
    }

    if ($language_expression->_db_getList('code,value,multi_row', 'language_id = '.$language_id, 'code ASC', $start_from*1, $max_results*1)) {
      foreach ($language_expression->_db_list as $expr) {
        $expressions_xml.='  <expression>
    <code>'.htmlspecialchars($expr['code']).'</code>
    <value>'.htmlspecialchars($expr['value']).'</value>
    <multi_row>'.htmlspecialchars($expr['multi_row']).'</multi_row>
  </expression>
';
      }
      // Get total count
      $language_expression->_db_getList('COUNT', 'language_id = '.$language_id);
      $total_count=$language_expression->_db_list_count;
      $language_expression->_db_freeList();
    } else {
      $status=1;
      $message=$l->g('error');
    }
  } else {
    $status=1;
    $message=$l->g('error');
  }
}


echo '<?xml version="1.0" encoding="UTF-8"?>
<pcpin_xml>
  <message>'.htmlspecialchars($message).'</message>
  <status>'.htmlspecialchars($status).'</status>
  <expressions_total>'.htmlspecialchars($total_count).'</expressions_total>
'.$expressions_xml
.'</pcpin_xml>';
die();
?>