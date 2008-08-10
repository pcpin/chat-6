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

_pcpin_loadClass('nickname'); $nickname=new PCPIN_Nickname($session);
_pcpin_loadClass('message'); $msg=new PCPIN_Message($session);
_pcpin_loadClass('badword'); $badword=new PCPIN_Badword($session);
_pcpin_loadClass('disallowed_name'); $disallowed_name=new PCPIN_Disallowed_Name($session);

$nicknames_xml=array();
$new_nickname_id=0;

if (empty($profile_user_id) || $profile_user_id!=$current_user->id && $current_user->is_admin!=='y') {
  $profile_user_id=$current_user->id;
}
if (!isset($nickname_id) || !is_scalar($nickname_id)) $nickname_id=0;
if (!isset($new_nickname) || !is_scalar($new_nickname)) $new_nickname='';

if (!empty($nickname_id) && !$nickname->_db_getList('id', 'id = '.$nickname_id, 'user_id = '.$profile_user_id, 1)) {
  // Nickname does not exists or not belongs to user
  $nickname_id=0;
}

if (!empty($profile_user_id) && !empty($nickname_id)) {
  $new_nickname=trim($new_nickname);
  if (!empty($nickname_id) && $new_nickname!='') {
    $new_nickname=$nickname->optimizeColored('^'.$session->_conf_all['default_nickname_color'].$new_nickname);
    $new_nickname_plain=$nickname->coloredToPlain($new_nickname, false);
    // Check nickname
    if ($new_nickname_plain=='') {
      // Nickname is empty
      $xmlwriter->setHeaderStatus(1);
      $xmlwriter->setHeaderMessage($l->g('nickname_empty_error'));
    } elseif(_pcpin_strlen($new_nickname_plain)<$session->_conf_all['nickname_length_min']) {
      // Nickname is too short
      $xmlwriter->setHeaderStatus(1);
      $xmlwriter->setHeaderMessage(str_replace('[LENGTH]', $session->_conf_all['nickname_length_min'], $l->g('nickname_too_short_error')));
    } elseif(_pcpin_strlen($new_nickname_plain)>$session->_conf_all['nickname_length_max']) {
      // Nickname is too long
      $xmlwriter->setHeaderStatus(1);
      $xmlwriter->setHeaderMessage(str_replace('[LENGTH]', $session->_conf_all['nickname_length_max'], $l->g('nickname_too_long')));
    } elseif ($nickname->_db_getList('id', 'id != '.$nickname_id, 'nickname_plain LIKE '.$new_nickname_plain, 1)) {
      // Nickname already exists
      $xmlwriter->setHeaderStatus(1);
      $xmlwriter->setHeaderMessage(str_replace('[NICKNAME]', $new_nickname_plain, $l->g('nickname_not_available')));
      $nickname->_db_freeList();
    } elseif (   false===$badword->checkString($new_nickname_plain) // "Bad words" filter
              || false===$disallowed_name->checkString($new_nickname_plain) && $current_user->is_admin!=='y' // "Disallowed names" filter
              ) {
      // Nickname is not allowed
      $xmlwriter->setHeaderStatus(1);
      $xmlwriter->setHeaderMessage(str_replace('[NICKNAME]', $new_nickname_plain, $l->g('nickname_not_available')));
    } else {
      // Nickname is free
      if ($nickname->updateNickname($profile_user_id, $nickname_id, $new_nickname)) {
        // Success
        $xmlwriter->setHeaderStatus(0);
        $xmlwriter->setHeaderMessage($l->g('nickname_updated'));
        // Get nicknames list
        $nicknames=$nickname->getNicknames($profile_user_id);
        foreach ($nicknames as $nickname_data) {
          $nicknames_xml[]=array('id'=>$nickname_data['id'],
                                 'nickname'=>$nickname_data['nickname'],
                                 'nickname_plain'=>$nickname_data['nickname_plain'],
                                 'default'=>$nickname_data['default']
                                 );
        }
      } else {
        // Failed to update nickname
        $xmlwriter->setHeaderStatus(1);
        $xmlwriter->setHeaderMessage($l->g('error'));
      }
    }
  } else {
    // An error
    $xmlwriter->setHeaderStatus(1);
    $xmlwriter->setHeaderMessage($l->g('error'));
  }
}
$xmlwriter->setData(array('nickname'=>$nicknames_xml));
?>