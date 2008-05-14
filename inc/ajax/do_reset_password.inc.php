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


if (!isset($email) || !is_scalar($email)) $email='';
if (!isset($language_id) || !is_scalar($language_id)) $language_id=0;

// Load language
if ($language_id!=$l->id) {
  $old_language_id=$l->id;
  if (true!==$l->setLanguage($language_id)) {
    $l->setLanguage($old_language_id);
  }
}

$errortext=array();

$user_id=0;
$login='';
$email=trim($email);

if (!PCPIN_Common::checkEmail($email)) {
  $errortext[]=$l->g('email_invalid');
}
if (empty($errortext)) {
  // Check data
  if ($current_user->_db_getList('id,login', 'email = '.$email, 'activated = y', 'is_guest = n', 1)) {
    // Email address found
    $user_id=$current_user->_db_list[0]['id'];
    $login=$current_user->_db_list[0]['login'];
    $current_user->_db_freeList();
  } else {
    // Wrong Email
    $errortext[]=$l->g('email_not_found');
  }
}

if (!empty($errortext)) {
  $xmlwriter->setHeaderStatus(1);
  $xmlwriter->setHeaderMessage('- '.implode("\n- ", $errortext));
} else {
  // Reset password
  $password_new=PCPIN_Common::randomString(mt_rand(6, 8), 'abcdefghijklmnopqrstuvwxyz0123456789');
  $current_user->_db_updateRow($user_id, 'id', array('password_new'=>md5($password_new)));
  // Send "password reset" email
  $email_body=$l->g('email_password_reset');
  $email_body=str_replace('[CHAT_NAME]', $session->_conf_all['chat_name'], $email_body);
  $email_body=str_replace('[USERNAME]', $login, $email_body);
  $email_body=str_replace('[PASSWORD]', $password_new, $email_body);
  $email_body=str_replace('[URL]', str_replace(' ', '%20', $session->_conf_all['base_url']), $email_body);
  $email_body=str_replace('[SENDER]', $session->_conf_all['chat_email_sender_name'], $email_body);
  PCPIN_Email::send('"'.$session->_conf_all['chat_email_sender_name'].'"'.' <'.$session->_conf_all['chat_email_sender_address'].'>', $email, $l->g('password_reset'), null, null, $email_body);
  $xmlwriter->setHeaderStatus(0);
  $xmlwriter->setHeaderMessage(str_replace('[EMAIL]', $email, $l->g('new_password_sent')));
}
?>