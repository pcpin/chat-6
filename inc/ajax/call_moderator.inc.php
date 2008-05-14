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
 * @param   string    $abuse_nickname
 * @param   string    $abuse_category
 * @param   string    $abuse_description
 */

_pcpin_loadClass('message'); $msg=new PCPIN_Message($session);
_pcpin_loadClass('room'); $room=new PCPIN_Room($session);

if (!isset($abuse_nickname)) $abuse_nickname='-';
if (!isset($abuse_category)) $abuse_category=0;
if (!isset($abuse_description)) $abuse_description='';

if (!empty($current_user->id)) {
  // Get moderators
  if (empty($session->_s_room_id)) {
    // User is not in room
    $xmlwriter->setHeaderMessage($l->g('error'));
    $xmlwriter->setHeaderStatus(1);
  } else {
    $xmlwriter->setHeaderMessage($l->g('abuse_report_sent'));
    $xmlwriter->setHeaderStatus(0);
    $moderators=$room->getModerators($session->_s_room_id);
    if (empty($moderators)) {
      // Room has no moderators. Admin(s) will receive an abuse then.
      $moderators=$current_user->getAdmins();
    }
    _pcpin_loadClass('nickname'); $nickname=new PCPIN_Nickname($session);
    $old_language_id=$l->id;
    // Create message body
    $body=$current_user->id.'/'.$session->_s_room_id.'/'.($abuse_category*1).'/'.trim(str_replace('/', ' ', $abuse_nickname)).'/'.trim($abuse_description);
    foreach ($moderators as $data) {
      _pcpin_loadClass('user'); $tmp_user=new PCPIN_User($session);
      $tmp_user->_db_loadObj($data['id']);
      if (!empty($data['is_online'])) {
        // User is online
        $msg->addMessage(4001, 'n', $session->_s_user_id, $current_nickname, 0, $data['id'], $body, date('Y-m-d H:i:s'), 2);
      } else {
        // Add offline message
//      $msg->addMessage(4001, 'y', $session->_s_user_id, $current_nickname, 0, $data['id'], $body, date('Y-m-d H:i:s'), 2);
      }
      // Load language
      if ($tmp_user->language_id!=$l->id) {
        if (true!==$l->setLanguage($tmp_user->language_id) && (empty($session->_conf_all['default_language']) || true!==$l->setLanguage($session->_conf_all['default_language']))) {
          $l->setLanguage($old_language_id);
        }
      }
      $violation_category='';
      switch ($abuse_category) {

        case 1:
          $violation_category=$l->g('spam');
        break;

        case 2:
          $violation_category=$l->g('insult');
        break;

        case 3:
          $violation_category=$l->g('adult_content');
        break;

        case 4:
          $violation_category=$l->g('illegal_content');
        break;

        case 5:
          $violation_category=$l->g('harassment');
        break;

        case 6:
          $violation_category=$l->g('fraud');
        break;

        case 7:
        default:
          $violation_category=$l->g('other');
        break;

      }
      // Send an email
      $email_body= $l->g('date').":\n\t".$tmp_user->makeDate(time())."\n"
                  .$l->g('room_name').":\n\t".$current_room_name."\n"
                  .$l->g('author').":\n\t".$nickname->coloredToPlain($current_nickname, false)."\n"
                  .$l->g('violation_category').":\n\t".$violation_category."\n"
                  .$l->g('abuser_nickname').":\n\t".$abuse_nickname."\n"
                  .$l->g('violation_description').":\n\n".$abuse_description."\n"
                  ;
      PCPIN_Email::send('"'.$session->_conf_all['chat_email_sender_name'].'"'.' <'.$session->_conf_all['chat_email_sender_address'].'>', $tmp_user->email, $session->_conf_all['chat_name'].': '.$l->g('abuse_arrived'), null, null, $email_body);
    }
    if ($old_language_id!=$l->id) {
      // Restore original language
      $l->setLanguage($old_language_id);
    }
  }
}
?>