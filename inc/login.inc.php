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

// Get available languages
if (!isset($language_id)) $language_id=0;

$languages=array();
if (!empty($session->_conf_all['allow_language_selection']) && !empty($session->_conf_all['login_language_selection'])) {
  $languages=$l->getLanguages(false);
  // Any language already selected
  if (!empty($language_id)) {
    foreach ($languages as $data) {
      if ($language_id==$data['id'] || $language_id==$data['iso_name']) {
        $preselect_language=$data['id'];
        break;
      }
    }
  }
  if (empty($preselect_language)) {
    // Get proposed by client languages
    $preselect_language=0;
    $accept_languages=!empty($_SERVER['HTTP_ACCEPT_LANGUAGE'])? explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']) : array();
    foreach ($accept_languages as $val) {
      $val=strpos($val, ';')? substr($val, 0, strpos($val, ';')) : $val;
      foreach ($languages as $data) {
        if (strtolower(trim($val))==$data['iso_name']) {
          $preselect_language=$data['id'];
          break;
        }
      }
      if (!empty($preselect_language)) {
        break;
      }
    }
  }
  if (empty($preselect_language)) {
    $preselect_language=$session->_conf_all['default_language'];
  }
  $l->setLanguage($preselect_language);
}

_pcpin_loadClass('pcpintpl'); $tpl=new PcpinTpl();
$tpl->setBasedir('./tpl');
$tpl->readTemplatesFromFile('./login.tpl');

$_body_onload[]='initLoginForm('.htmlspecialchars($session->_conf_all['login_length_min']).', '
                                .htmlspecialchars($session->_conf_all['login_length_max']).', '
                                .(!empty($admin_login)? 'true' : 'false')
                                .')';

// JS file for login
$_js_files[]='./js/login.js';

// JS language expressions
$_js_lng[]='username_empty';
$_js_lng[]='email_invalid';
$_js_lng[]='username_length_error';
$_js_lng[]='password_too_short';
$_js_lng[]='passwords_not_ident';

// Add global vars to template
foreach ($global_tpl_vars as $key=>$val) {
  $tpl->addGlobalVar($key, htmlspecialchars($val));
}

// Add language expressions to template
foreach ($tpl->tpl_vars_plain as $var) {
  if (0===strpos($var, 'LNG_')) {
    $var=strtolower($var);
    $tpl->addGlobalVar($var, htmlspecialchars($l->g(substr($var, 4))));
  }
}

$tpl->addVar('main', 'login_maxlength', htmlspecialchars($session->_conf_all['login_length_max']));

$tpl->addVar('guest_login', 'display', empty($hide_account_options) && $session->_conf_all['allow_guests']);
$tpl->addVar('account_options', 'display', empty($hide_account_options));

$tpl->addVars('chat_summary', array('display'=>!empty($session->_conf_all['display_startup_summary']),
                                    'height'=>htmlspecialchars($session->_conf_all['startup_summary_height']),
                                    ));

if (!empty($direct_login) && !empty($login) && !empty($password)) {
  $tpl->addVars('main', array('login_username'=>htmlspecialchars($login),
                              'login_password'=>htmlspecialchars($password),
                              ));
  $_body_onload[]='doLogin()';
}

// Language selection
if (!empty($session->_conf_all['allow_language_selection']) && !empty($session->_conf_all['login_language_selection'])) {
  $tpl->addVar('language_selection', 'display', true);
  foreach ($languages as $data) {
    $tpl->addVars('language_selection_option', array('id'=>htmlspecialchars($data['id']),
                                                     'local_name'=>htmlspecialchars($data['local_name']),
                                                     'selected'=>($data['id']==$l->id)? 'selected="selected"' : '',
                                                     ));
    $tpl->parseTemplate('language_selection_option', 'a');
  }
}

// Display "Register" link
$tpl->addVar('account_options_register', 'display', $session->_conf_all['allow_user_registration']);
?>