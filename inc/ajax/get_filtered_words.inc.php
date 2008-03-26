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
* Get word blacklist
*/

_pcpin_loadClass('badword'); $badword=new PCPIN_Badword($session);

$words_xml='';

if (is_object($session) && !empty($current_user->id)) {
  $message='OK';
  $status=0;
  $words=$badword->getWords();
  foreach ($words as $word_data) {
    $words_xml.='  <word>
    <id>'.htmlspecialchars($word_data['id']).'</id>
    <word>'.htmlspecialchars($word_data['word']).'</word>
    <replacement>'.htmlspecialchars($word_data['replacement']).'</replacement>
  </word>
';
  }
}


echo '<?xml version="1.0" encoding="UTF-8"?>
<pcpin_xml>
  <message>'.htmlspecialchars($message).'</message>
  <status>'.htmlspecialchars($status).'</status>
'.$words_xml
.'</pcpin_xml>';
die();
?>