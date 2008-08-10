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

_pcpin_loadClass('message'); $msg=new PCPIN_Message($session);

if (empty($profile_user_id) || $profile_user_id!=$current_user->id && $current_user->is_admin!=='y') {
  $profile_user_id=$current_user->id;
}
if ($profile_user_id!=$current_user->id) {
  $profile_user=new PCPIN_User($session);
  $profile_user->_db_loadObj($profile_user_id);
} else {
  $profile_user=&$current_user;
}

$activation_required='';
if (!empty($profile_user_id)) {
  if (!isset($email) || !is_scalar($email)) {
    $email='';
  } else {
    $email=_pcpin_substr(trim($email), 0, 255);
  }
  if (!PCPIN_Common::checkEmail($email, $session->_conf_all['email_validation_level'])) {
    // Email invalid
    $xmlwriter->setHeaderStatus(1);
    $xmlwriter->setHeaderMessage($l->g('email_invalid'));
  } else {
    if (!$current_user->checkEmailUnique($profile_user_id, $email)) {
      // Email address already taken
      $xmlwriter->setHeaderStatus(1);
      $xmlwriter->setHeaderMessage($l->g('email_already_taken'));
    } else {
      // Email address is free
      if ($current_user->is_admin!=='y' && !empty($session->_conf_all['activate_new_emails'])) {
        // Email address needs to be activated
        $activation_required=1;
        $email_new_activation_code=PCPIN_Common::randomString(18, 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789');
        $profile_user->email_new=$email;
        $profile_user->email_new_date=date('Y-m-d H:i:s');
        $profile_user->email_new_activation_code=md5($email_new_activation_code);
        $profile_user->_db_updateObj($profile_user->id);
        $email_body=$l->g('email_email_address_activation');
        $email_body=str_replace('[HOURS]', $session->_conf_all['new_email_activation_timeout'], $email_body);
        $email_body=str_replace('[SENDER]', $session->_conf_all['chat_email_sender_name'], $email_body);
        $email_body=str_replace('[ACTIVATION_URL]', str_replace(' ', '%20', $session->_conf_all['base_url']).'?activate_email&activation_code='.urlencode($email_new_activation_code), $email_body);
        $email_body=str_replace('[CHAT_NAME]', $session->_conf_all['chat_name'], $email_body);
        PCPIN_Email::send('"'.$session->_conf_all['chat_email_sender_name'].'"'.' <'.$session->_conf_all['chat_email_sender_address'].'>', $email, $l->g('email_address_activation'), null, null, $email_body);
        $xmlwriter->setHeaderStatus(0);
        $xmlwriter->setHeaderMessage(str_replace('[EMAIL]', $email, $l->g('email_address_activation_sent')));
      } else {
        // Save new email address
        $activation_required=0;
        $profile_user->email=$email;
        $profile_user->email_new='';
        $profile_user->email_new_date='';
        $profile_user->email_new_activation_code='';
        $profile_user->_db_updateObj($profile_user->id);
        $xmlwriter->setHeaderStatus(0);
        $xmlwriter->setHeaderMessage($l->g('email_address_changed'));
        $msg->addMessage(1010, 'n', 0, '', $session->_s_room_id, 0, $profile_user_id);
      }
    }
  }
}
$xmlwriter->setData(array('email'=>$email, 'activation_required'=>$activation_required));
?>