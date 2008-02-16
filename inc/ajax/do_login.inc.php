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

$message=$l->g('login_failed');
$status='-1'; // -1: Session is invalid

_pcpin_loadClass('message'); $msg=new PCPIN_Message($session);
_pcpin_loadClass('ipfilter'); $ipfilter=new PCPIN_IPFilter($session);


if (!isset($login) || !is_scalar($login)) $login='';
if (!isset($password) || !is_scalar($password)) $password='';
if (!isset($time_zone_offset) || !is_scalar($time_zone_offset)) $time_zone_offset=date('Z');
if (!isset($language_id) || !is_scalar($language_id)) $language_id=0;

$password_ok=false;
$new_password_ok=false;
$userdata=array();

// Check IP address against IP filter
if (false!==$blocked=$ipfilter->isBlocked(PCPIN_CLIENT_IP)) {
  // IP address is blocked
  if ($blocked['expires']=='0000-00-00 00:00:00') {
    // IP is permanently banned
    if ($blocked['reason']!='') {
      $message=str_replace('[REASON]', $blocked['reason'], $l->g('you_are_banned_permanently_with_reason'));
    } else {
      $message=$l->g('you_are_banned_permanently_without_reason');
    }
  } else {
    // IP is temporarily banned
    $banned_until_str=$current_user->makeDate(PCPIN_Common::datetimeToTimestamp($blocked['expires']));
    if ($blocked['reason']!='') {
      $message=str_replace('[REASON]', $blocked['reason'], $l->g('you_are_banned_with_reason'));
    } else {
      $message=$l->g('you_are_banned_without_reason');
    }
    $message=str_replace('[DATE]', $banned_until_str, $message);
  }
} elseif ($login!='' && ($password!='' || PCPIN_SLAVE_MODE && $_pcpin_slave_userdata_md5_password!='')) {
  // Registered user login
  if ($current_user->_db_getList('login = '.$login, 1)) {
    // User exists
    $userdata=$current_user->_db_list[0];
    $current_user->_db_freeList();
    // Check password
    if (md5($password)==$userdata['password'] || PCPIN_SLAVE_MODE && $_pcpin_slave_userdata_md5_password!='' && $_pcpin_slave_userdata_md5_password==$userdata['password']) {
      // Password OK
      $password_ok=true;
    } elseif (md5($password)==$userdata['password_new']) {
      // New password OK
      $new_password_ok=true;
    }
    if (true===$password_ok || true===$new_password_ok) {
      // Login and password are OK
      // Account activated?
      if (!empty($session->_conf_all['activate_new_accounts']) && $userdata['activated']!='y') {
        // Account is NOT activated
        $message=$l->g('login_failed');
      } else {
        // User banned?
        if ($userdata['banned_until']>date('Y-m-d H:i:s')) {
          // User is temporarily banned
          $banned_until_str=$current_user->makeDate(PCPIN_Common::datetimeToTimestamp($userdata['banned_until']));
          if ($userdata['ban_reason']!='') {
            $message=str_replace('[REASON]', $userdata['ban_reason'], $l->g('you_are_banned_with_reason'));
          } else {
            $message=$l->g('you_are_banned_without_reason');
          }
          $message=str_replace('[DATE]', $banned_until_str, $message);
        } elseif ($userdata['banned_permanently']=='y') {
          // User is permanently banned
          if ($userdata['ban_reason']!='') {
            $message=str_replace('[REASON]', $userdata['ban_reason'], $l->g('you_are_banned_permanently_with_reason'));
          } else {
            $message=$l->g('you_are_banned_permanently_without_reason');
          }
        } else {
          $message='OK';
          $status=0;
          // Create new session and log it in
          if (!empty($admin_login) && $userdata['is_admin']==='y') {
            $backend_login='y';
          } else {
            unset($admin_login);
            $backend_login='n';
          }
          $session->_s_logIn($userdata['id'], $userdata['last_message_id'], $language_id, $backend_login);
          // Update user
          $current_user->_db_loadObj($userdata['id']);
          $current_user->previous_login=$current_user->last_login;
          $current_user->last_login=date('Y-m-d H:i:s');
          $current_user->time_zone_offset=$time_zone_offset;
          if ($new_password_ok) {
            $current_user->password=$current_user->password_new;
          }
          $current_user->password_new=md5(PCPIN_Common::randomString(mt_rand(30, 120)));
          $current_user->activated='y';
          $current_user->activation_code='';
          $current_user->_db_updateObj($session->_s_user_id);
          // Insert system message
          $msg->addMessage(101, 'n', 0, '', 0, 0, $session->_s_user_id);
        }
      }
    }
    unset($userdata);
  }
} elseif (!empty($guest_login)) {
  // Guest login
  if (empty($session->_conf_all['allow_guests'])) {
    // Guest login is disabled
    $message=$l->g('guest_login_disabled');
  } else {
    if ($login=='') {
      // Create new user record
      $user_created=false;
      $tries=100;
      do {
        $login=$l->g('guest').mt_rand(0, 999);
        if ($current_user->checkUsernameUnique($login) && $current_user->newUser($login, PCPIN_Common::randomString(mt_rand(100, 255)), '', 1, 'y')) {
          // User created
          $message='OK';
          $status=0;
          $user_created=true;
          // Create new session and log it in
          $session->_s_logIn($current_user->id, 0, $language_id);
          // Update user
          $current_user->_db_loadObj($current_user->id);
          $current_user->previous_login='0000-00-00 00:00:00';
          $current_user->last_login=date('Y-m-d H:i:s');
          $current_user->time_zone_offset=$time_zone_offset;
          $current_user->password_new=md5(PCPIN_Common::randomString(mt_rand(30, 120)));
          $current_user->_db_updateObj($session->_s_user_id);
          // Insert system message
          $msg->addMessage(101, 'n', 0, '', 0, 0, $session->_s_user_id);
          break;
        }
        if (--$tries==0) {
          break;
        }
      } while (true);
      if (!$user_created) {
        $message=$l->g('error');
      }
    }
  }
}

if (!PCPIN_SLAVE_MODE || !empty($admin_login)) {
  echo '<?xml version="1.0" encoding="UTF-8"?>
  <pcpin_xml>
  <message>'.htmlspecialchars($message).'</message>
  <status>'.htmlspecialchars($status).'</status>
  <s_id>'.htmlspecialchars($session->_s_id).'</s_id>
  </pcpin_xml>';
  die();
}
?>