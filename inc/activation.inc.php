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

$message=$l->g('access_denied');

if (!isset($activation_code) || !is_scalar($activation_code)) {
  $activation_code='';
}

_pcpin_loadClass('user'); $user=new PCPIN_User($session);
_pcpin_loadClass('message'); $msg=new PCPIN_Message($session);

_pcpin_loadClass('pcpintpl'); $tpl=new PcpinTpl();
$tpl->setBasedir('./tpl');
$tpl->readTemplatesFromFile('./dummy.tpl');


if ($activation_code!='') {

  if (isset($activate_email)) {
    // New email address activation
    if ($user->_db_getList('email_new != ',
                           'email_new_activation_code = '.md5($activation_code),
                           1)) {
      // Requested email address found and activation code is OK
      $user->_db_setObject($user->_db_list[0]);
      $user->email=$user->email_new;
      $user->email_new='';
      $user->email_new_date='';
      $user->email_new_activation_code='';
      $user->_db_updateObj($user->id);
      $message=$l->g('new_email_activated');
      if ($session->_db_getList('_s_room_id', '_s_user_id = '.$user->id, 1)) {
        // User is online
        $msg->addMessage(1010, 'n', 0, '', $session->_db_list[0]['_s_room_id'], 0, $user->id);
      }
    } else {
      // Invalid activation code
      $message=$l->g('invalid_activation_code');
    }
  } elseif (isset($activate_account)) {
    // New account activation
    if ($user->_db_getList('id',
                           'activated = n',
                           'activation_code = '.md5($activation_code),
                           1)) {
      // Activate user account
      $user_id=$user->_db_list[0]['id'];
      $user->_db_freeList();
      if ($user->activateUser($user_id)) {
        $user->_db_loadObj($user_id);
        $message=$l->g('your_account_activated');
        if (!empty($session->_conf_all['new_user_notification'])) {
          // Send notification to admins
          if ($current_user->_db_getList('email', 'is_admin = y')) {
            $emails=$current_user->_db_list;
            $current_user->_db_freeList();
            foreach ($emails as $data) {
              $email_body=$l->g('email_new_user_notification');
              $email_body=str_replace('[CHAT_NAME]', $session->_conf_all['chat_name'], $email_body);
              $email_body=str_replace('[EMAIL_ADDRESS]', $user->email, $email_body);
              $email_body=str_replace('[USERNAME]', $user->login, $email_body);
              $email_body=str_replace('[REMOTE_IP]', PCPIN_CLIENT_IP, $email_body);
              $email_body=str_replace('[SENDER]', $session->_conf_all['chat_email_sender_name'], $email_body);
              PCPIN_Email::send('"'.$session->_conf_all['chat_email_sender_name'].'"'.' <'.$session->_conf_all['chat_email_sender_address'].'>', $data['email'], $session->_conf_all['chat_name'].': '.$l->g('new_account_created'), null, null, $email_body);
            }
          }
        }
      } else {
        // Activation failed (should not happen)
        $message=$l->g('invalid_activation_code');
      }
    } else {
      // Invalid activation code
      $message=$l->g('invalid_activation_code');
    }
  }
}

$message=str_replace('\'', '\\\'', htmlspecialchars($l->g($message)));
$message=str_replace("\n", '\\n', str_replace("\r", '\\r', $message));
$_body_onload[]='alert(\''.$message.'\')';
$_body_onload[]='window.close()';
?>