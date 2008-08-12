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
 * Add new user account
 * @param     int       $language_id      Default language for the user
 * @param     string    $login            Username
 * @param     string    $email            Email address
 * @param     string    $password         New password
 */

_pcpin_loadClass('badword'); $badword=new PCPIN_Badword($session);
_pcpin_loadClass('disallowed_name'); $disallowed_name=new PCPIN_Disallowed_Name($session);


if (is_object($session) && !empty($current_user->id) && $current_user->is_admin==='y') {

  if (!isset($login) || !is_scalar($login)) $login='';
  if (!isset($email) || !is_scalar($email)) $email='';
  if (!isset($password) || !is_scalar($password)) $password='';

  if (empty($language_id) || !is_scalar($language_id)) {
    $language_id=$session->_conf_all['default_language'];
  }
  $old_language_id=$l->id;

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
  if (_pcpin_strlen($password)==0) {
    $errortext[]=$l->g('password_empty');
  } elseif (_pcpin_strlen($password)<3) {
    $errortext[]=$l->g('password_too_short');
  }

  if (!empty($errortext)) {
    $xmlwriter->setHeaderStatus(1);
    $xmlwriter->setHeaderMessage('- '.implode("\n- ", $errortext));
  } else {
    if ($language_id!=$l->id) {
      // Load language
      if (true!==$l->setLanguage($language_id)) {
        $l->setLanguage($old_language_id);
      }
    }
    // Create user
    $current_user->newUser($login, $password, $email, 1, 'n', '', $l->id);
    // No account activation required. Send "welcome" email.
    $email_body=$l->g('email_welcome_new_user');
    $email_body=str_replace('[CHAT_NAME]', $session->_conf_all['chat_name'], $email_body);
    $email_body=str_replace('[EMAIL_ADDRESS]', $email, $email_body);
    $email_body=str_replace('[USERNAME]', $login, $email_body);
    $email_body=str_replace('[PASSWORD]', $password, $email_body);
    $email_body=str_replace('[URL]', str_replace(' ', '%20', $session->_conf_all['base_url']), $email_body);
    $email_body=str_replace('[SENDER]', $session->_conf_all['chat_email_sender_name'], $email_body);
    PCPIN_Email::send('"'.$session->_conf_all['chat_email_sender_name'].'"'.' <'.$session->_conf_all['chat_email_sender_address'].'>', $email, $l->g('new_account_created'), null, null, $email_body);
    $l->setLanguage($old_language_id);
    $xmlwriter->setHeaderStatus(0);
    $xmlwriter->setHeaderMessage($l->g('new_user_added'));
    if (!empty($session->_conf_all['new_user_notification'])) {
      // Send notification to admins
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
            $l->setLanguage($old_language_id);
          }
          foreach ($emails as $email) {
            $email_body=$l->g('email_new_user_notification');
            $email_body=str_replace('[CHAT_NAME]', $session->_conf_all['chat_name'], $email_body);
            $email_body=str_replace('[EMAIL_ADDRESS]', $email, $email_body);
            $email_body=str_replace('[USERNAME]', $login, $email_body);
            $email_body=str_replace('[REMOTE_IP]', PCPIN_CLIENT_IP, $email_body);
            $email_body=str_replace('[SENDER]', $session->_conf_all['chat_email_sender_name'], $email_body);
            PCPIN_Email::send('"'.$session->_conf_all['chat_email_sender_name'].'"'.' <'.$session->_conf_all['chat_email_sender_address'].'>', $email, $session->_conf_all['chat_name'].': '.$l->g('new_account_created'), null, null, $email_body);
          }
        }
      }
    }
  }
}
?>