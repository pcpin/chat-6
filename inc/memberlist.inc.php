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

if (!is_object($session)) {
  header('Location: '.PCPIN_FORMLINK.'?'.md5(microtime()));
  die();
}

// Default: Do not context menu user options
$_load_cm_user_options=true;

if ($current_user->is_admin!=='y') {
  unset($muted_members);
  unset($banned_members);
  unset($moderator_members);
  unset($admin_members);
}

// JS files
$_js_files[]='./js/memberlist.js';
$_js_files[]='./js/user.js';

$_js_lng[]='online_status_0';
$_js_lng[]='online_status_1';
$_js_lng[]='online_status_2';
$_js_lng[]='online_status_3';
$_js_lng[]='days';
$_js_lng[]='hours';
$_js_lng[]='minutes';
$_js_lng[]='seconds';
$_js_lng[]='gender';
$_js_lng[]='gender_m';
$_js_lng[]='gender_f';
$_js_lng[]='gender_-';
$_js_lng[]='avatar';
$_js_lng[]='user_is_admin';
$_js_lng[]='user_is_moderator';
$_js_lng[]='enter_reason';
$_js_lng[]='optional';
$_js_lng[]='enter_duration';
$_js_lng[]='canceled_duration_invalid';
$_js_lng[]='ban_canceled_ip_equals';
$_js_lng[]='muted_locally';
$_js_lng[]='permanently_globalmuted';
$_js_lng[]='globalmuted_until';
$_js_lng[]='muted_until';
$_js_lng[]='ignored';
$_js_lng[]='permanently_banned';
$_js_lng[]='banned_until';
$_js_lng[]='page';
$_js_lng[]='pages';
$_js_lng[]='first';
$_js_lng[]='last';
$_js_lng[]='goto_first_page';
$_js_lng[]='goto_last_page';
$_js_lng[]='goto_previous_page';
$_js_lng[]='goto_next_page';
$_js_lng[]='no_members_found';
$_js_lng[]='server';
$_js_lng[]='permanently';
$_js_lng[]='banned_only';
$_js_lng[]='muted_only';
$_js_lng[]='all_members';
$_js_lng[]='edit_profile';
$_js_lng[]='edit_moderator';
$_js_lng[]='moderators_only';
$_js_lng[]='admins_only';
$_js_lng[]='never';
$_js_lng[]='not_activated_accounts';
$_js_lng[]='guest';
$_js_lng[]='delete_user';
$_js_lng[]='sure_delete_user';
$_js_lng[]='really_sure';

$_body_onload[]='initMemberlist('.(!empty($session->_conf_all['userlist_gender_icon'])? 'true' : 'false').','
                                 .((!empty($session->_conf_all['userlist_avatar_thumb']) && 2==PCPIN_GD_VERSION)? 'true' : 'false').','
                                 .((!empty($session->_conf_all['userlist_privileged_flags']) && 2==PCPIN_GD_VERSION)? 'true' : 'false')
                                 .')';

$_window_title.=' '.PCPIN_WINDOW_TITLE_SEPARATOR.' '.$l->g('memberlist');

// Init template
_pcpin_loadClass('pcpintpl'); $tpl=new PcpinTpl();
$tpl->setBasedir('./tpl');
$tpl->readTemplatesFromFile('./memberlist.tpl');


// Add language expressions to template
foreach ($tpl->tpl_vars_plain as $var) {
  if (0===strpos($var, 'LNG_')) {
    $var=strtolower($var);
    $tpl->addGlobalVar($var, htmlspecialchars($l->g(substr($var, 4))));
  }
}

// Add global vars to template
foreach ($global_tpl_vars as $key=>$val) {
  $tpl->addGlobalVar($key, htmlspecialchars($val));
}

$tpl->addVar('admin_filter_options', 'display', $current_user->is_admin==='y');
if (isset($banned_members)) {
  $tpl->addVar('admin_filter_options', 'banned_members_checked', 'checked="checked"');
} elseif (isset($muted_members)) {
  $tpl->addVar('admin_filter_options', 'muted_members_checked', 'checked="checked"');
} elseif (isset($moderator_members)) {
  $tpl->addVar('admin_filter_options', 'moderator_members_checked', 'checked="checked"');
} elseif (isset($admin_members)) {
  $tpl->addVar('admin_filter_options', 'admin_members_checked', 'checked="checked"');
} else {
  $tpl->addVar('admin_filter_options', 'all_members_checked', 'checked="checked"');
}

$template->addVar('moderator_user_options', 'display', $current_user->is_admin==='y');
$template->addVar('admin_user_options', 'display', $current_user->is_admin==='y');
$tpl->addVar('admin_filter_options_not_activated', 'display', !PCPIN_SLAVE_MODE);
?>