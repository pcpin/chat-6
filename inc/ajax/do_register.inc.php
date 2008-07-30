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

if ($session->_conf_all['allow_user_registration']) {

  _pcpin_loadClass('badword'); $badword=new PCPIN_Badword($session);
  _pcpin_loadClass('disallowed_name'); $disallowed_name=new PCPIN_Disallowed_Name($session);

  if (!isset($login) || !is_scalar($login)) $login='';
  if (!isset($password) || !is_scalar($password)) $password='';
  if (!isset($email) || !is_scalar($email)) $email='';
  if (!isset($language_id) || !is_scalar($language_id)) $language_id=$l->id;

  // Load language
  if ($language_id!=$l->id) {
    $old_language_id=$l->id;
    if (true!==$l->setLanguage($language_id)) {
      $l->setLanguage($old_language_id);
    }
  }
  $errortext=array();

  $login=trim($login);
  $email=trim($email);

  if ($login=='') {
    $errortext[]=$l->g('username_empty');
  } elseif (_pcpin_strlen($login)<$session->_conf_all['login_length_min'] || _pcpin_strlen($login)>$session->_conf_all['login_length_max']) {
    $errortext[]=str_replace('[MIN]', $session->_conf_all['login_length_min'], str_replace('[MAX]', $session->_conf_all['login_length_max'], $l->g('username_length_error')));
  } elseif (!$current_user->checkUsernameUnique($login)) {
    $errortext[]=$l->g('username_already_taken');
  } elseif (true!==$badword->checkString($login) || true!==$disallowed_name->checkString($login)) {
    $errortext[]=$l->g('username_not_available');
  }
  if (!PCPIN_Common::checkEmail($email)) {
    $errortext[]=$l->g('email_invalid');
  } elseif (!$current_user->checkEmailUnique(0, $email)) {
    $errortext[]=$l->g('email_already_taken');
  }

  if (_pcpin_strlen($password)<3) {
    $errortext[]=$l->g('password_too_short');
  }
  
  if (!empty($errortext)) {
    $xmlwriter->setHeaderStatus(1);
    $xmlwriter->setHeaderMessage('- '.implode("\n- ", $errortext));
  } else {
    // Create user
    if (!empty($session->_conf_all['activate_new_accounts'])) {
      $activation_code_plain=PCPIN_Common::randomString(18, 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789');
      $activation_code=md5($activation_code_plain);
    } else {
      $activation_code_plain='';
      $activation_code='';
    }
    $current_user->newUser($login, $password, $email, 1, 'n', $activation_code, $l->id);
    if (empty($session->_conf_all['activate_new_accounts'])) {
      // No account activation required. Send "welcome" email.
      $email_body=$l->g('email_welcome_new_user');
      $email_body=str_replace('[CHAT_NAME]', $session->_conf_all['chat_name'], $email_body);
      $email_body=str_replace('[EMAIL_ADDRESS]', $email, $email_body);
      $email_body=str_replace('[USERNAME]', $login, $email_body);
      $email_body=str_replace('[PASSWORD]', $password, $email_body);
      $email_body=str_replace('[URL]', str_replace(' ', '%20', $session->_conf_all['base_url']), $email_body);
      $email_body=str_replace('[SENDER]', $session->_conf_all['chat_email_sender_name'], $email_body);
      PCPIN_Email::send('"'.$session->_conf_all['chat_email_sender_name'].'"'.' <'.$session->_conf_all['chat_email_sender_address'].'>', $email, $l->g('new_account_created'), null, null, $email_body);
      $xmlwriter->setHeaderStatus(0);
      $xmlwriter->setHeaderMessage($l->g('your_account_created'));
      if (!empty($session->_conf_all['new_user_notification'])) {
        // Send notification to admins
        $old_language_id=$l->id;
        if ($current_user->_db_getList('email,language_id', 'is_admin = y')) {
          $users=$current_user->_db_list;
          $current_user->_db_freeList();
          // Group users by language
          $language_emails=array();
          foreach ($users as $data) {
            if (!isset($language_users[$data['language_id']])) {
              $language_emails[$data['language_id']]=array();
            }
            $language_emails[$data['language_id']][]=$data['email'];
          }
          unset($users);
          foreach ($language_emails as $language_id=>$emails) {
            if (true!==$l->setLanguage($language_id)) {
              $l->setLanguage($session->_s_language_id);
            }
            foreach ($emails as $admin_email) {
              $email_body=$l->g('email_new_user_notification');
              $email_body=str_replace('[CHAT_NAME]', $session->_conf_all['chat_name'], $email_body);
              $email_body=str_replace('[EMAIL_ADDRESS]', $email, $email_body);
              $email_body=str_replace('[USERNAME]', $login, $email_body);
              $email_body=str_replace('[REMOTE_IP]', PCPIN_CLIENT_IP, $email_body);
              $email_body=str_replace('[SENDER]', $session->_conf_all['chat_email_sender_name'], $email_body);
              PCPIN_Email::send('"'.$session->_conf_all['chat_email_sender_name'].'"'.' <'.$session->_conf_all['chat_email_sender_address'].'>', $admin_email, $session->_conf_all['chat_name'].': '.$l->g('new_account_created'), null, null, $email_body);
            }
          }
          // Restore original language
          if ($l->id!=$old_language_id) {
            $l->setLanguage($old_language_id);
          }
        }
      }
    } else {
      if ($session->_conf_all['activate_new_accounts']==1) {
        // Send activation email
        $email_body=$l->g('email_new_account_activation');
        $email_body=str_replace('[CHAT_NAME]', $session->_conf_all['chat_name'], $email_body);
        $email_body=str_replace('[EMAIL_ADDRESS]', $email, $email_body);
        $email_body=str_replace('[USERNAME]', $login, $email_body);
        $email_body=str_replace('[PASSWORD]', $password, $email_body);
        $email_body=str_replace('[URL]', str_replace(' ', '%20', $session->_conf_all['base_url']), $email_body);
        $email_body=str_replace('[SENDER]', $session->_conf_all['chat_email_sender_name'], $email_body);
        $email_body=str_replace('[ACTIVATION_URL]', str_replace(' ', '%20', $session->_conf_all['base_url']).'?activate_account&activation_code='.urlencode($activation_code_plain), $email_body);
        $email_body=str_replace('[HOURS]', $session->_conf_all['new_account_activation_timeout'], $email_body);
        PCPIN_Email::send('"'.$session->_conf_all['chat_email_sender_name'].'"'.' <'.$session->_conf_all['chat_email_sender_address'].'>', $email, $l->g('new_account_activation'), null, null, $email_body);
        $xmlwriter->setHeaderStatus(0);
        $xmlwriter->setHeaderMessage(str_replace('[EMAIL_ADDRESS]', $email, $l->g('account_activation_email_sent')));
      } else {
        // Manual activation by Admin
        $xmlwriter->setHeaderStatus(0);
        $xmlwriter->setHeaderMessage($l->g('account_will_be_activated_by_admin'));
        // Send notification to admins
        $old_language_id=$l->id;
        if ($current_user->_db_getList('email,language_id', 'is_admin = y')) {
          $users=$current_user->_db_list;
          $current_user->_db_freeList();
          // Group users by language
          $language_emails=array();
          foreach ($users as $data) {
            if (!isset($language_users[$data['language_id']])) {
              $language_emails[$data['language_id']]=array();
            }
            $language_emails[$data['language_id']][]=$data['email'];
          }
          unset($users);
          foreach ($language_emails as $language_id=>$emails) {
            if (true!==$l->setLanguage($language_id)) {
              $l->setLanguage($session->_s_language_id);
            }
            foreach ($emails as $admin_email) {
              $email_body=$l->g('email_new_user_activation_notification');
              $email_body=str_replace('[CHAT_NAME]', $session->_conf_all['chat_name'], $email_body);
              $email_body=str_replace('[EMAIL_ADDRESS]', $email, $email_body);
              $email_body=str_replace('[USERNAME]', $login, $email_body);
              $email_body=str_replace('[REMOTE_IP]', PCPIN_CLIENT_IP, $email_body);
              $email_body=str_replace('[SENDER]', $session->_conf_all['chat_email_sender_name'], $email_body);
              PCPIN_Email::send('"'.$session->_conf_all['chat_email_sender_name'].'"'.' <'.$session->_conf_all['chat_email_sender_address'].'>', $admin_email, $session->_conf_all['chat_name'].': '.$l->g('new_account_activation'), null, null, $email_body);
            }
          }
          // Restore original language
          if ($l->id!=$old_language_id) {
            $l->setLanguage($old_language_id);
          }
        }
      }
    }
  }
}
?>