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

if (!isset($user_id)) $user_id=0;


_pcpin_loadClass('nickname'); $nickname=new PCPIN_Nickname($session);
_pcpin_loadClass('invitation'); $invitation=new PCPIN_Invitation($session);

if (!empty($current_user->id)) {
  $xmlwriter->setHeaderMessage($l->g('error'));
  $xmlwriter->setHeaderStatus(1);

  if ($current_user->global_muted_until>date('Y-m-d H:i:s')) {
    $xmlwriter->setHeaderMessage($l->g('you_are_muted_until'));
    $xmlwriter->setHeaderMessage(str_replace('[EXPIRATION_DATE]', $current_user->makeDate(PCPIN_Common::datetimeToTimestamp($current_user->global_muted_until)), $message));
  } elseif ($current_user->global_muted_permanently=='y') {
    $xmlwriter->setHeaderMessage($l->g('you_are_muted_permanently'));
  } else {
    if (   !empty($session->_s_room_id)
        && !empty($user_id)
        && $current_user->_db_getList('id', 'id = '.$user_id, 1)) {
      // User exists
      if ($session->_db_getList('_s_room_id, _s_stealth_mode', '_s_user_id = '.$user_id, 1)) {
        // User is online
        if ($session->_db_list[0]['_s_room_id']==$session->_s_room_id) {
          // User is already in desired room
          if ($session->_db_list[0]['_s_stealth_mode']=='y' && $current_user->is_admin!=='y') {
            // Invited user is in stealth mode, produce a dummy message
            $xmlwriter->setHeaderStatus(0);
            $xmlwriter->setHeaderMessage(str_replace('[USER]', $nickname->coloredToPlain($nickname->getDefaultNickname($user_id), false), $l->g('invitation_sent')));
          } else {
            $xmlwriter->setHeaderStatus(1);
            $xmlwriter->setHeaderMessage(str_replace('[USER]', $nickname->coloredToPlain($nickname->getDefaultNickname($user_id), false), $l->g('user_is_already_in_your_room')));
          }
        } else {
          // Send an invitation
          $xmlwriter->setHeaderStatus(0);
          $invitation->addInvitation($current_user->id, $user_id, $session->_s_room_id);
          $xmlwriter->setHeaderMessage(str_replace('[USER]', $nickname->coloredToPlain($nickname->getDefaultNickname($user_id), false), $l->g('invitation_sent')));
        }
        $session->_db_freeList();
      } else {
        // User is not online
        $xmlwriter->setHeaderStatus(1);
        $xmlwriter->setHeaderMessage(str_replace('[USER]', $nickname->coloredToPlain($nickname->getDefaultNickname($user_id), false), $l->g('user_not_online')));
      }
      $current_user->_db_freeList();
    }
  }
}
?>