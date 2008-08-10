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

if (empty($current_user->id) || $session->_s_user_id!=$current_user->id) {
  header('Location: '.PCPIN_FORMLINK.'?'.md5(microtime()));
  die();
}

_pcpin_loadClass('room'); $room=new PCPIN_Room($session);

if (!empty($session->_conf_all['default_room']) && $room->_db_getList('id', 'id = '.$session->_conf_all['default_room'], 1)) {
  // Default room specified and exists
  $room->_db_freeList();
  if ($room->putUser($current_user->id, $session->_conf_all['default_room'])) {
    header('Location: '.PCPIN_FORMLINK.'?s_id='.$session->_s_id.'&inc=chat_room&ts='.time());
    die();
  }
}

if (!empty($session->_s_room_id)) {
  // User was in chat room. Push him out.
  $room->putUser($current_user->id, 0, $session->_s_stealth_mode=='y', 'n');
}


$_body_onload[1000000]='initRoomSelection('.$session->_conf_all['updater_interval'].','
                                           .'\''.$current_user->room_selection_view.'\','
                                           .((!empty($session->_conf_all['userlist_avatar_thumb']) && 2==PCPIN_GD_VERSION)? 'true' : 'false').','
                                           .((!empty($session->_conf_all['userlist_privileged_flags']) && 2==PCPIN_GD_VERSION)? 'true' : 'false').','
                                           .(!empty($session->_conf_all['userlist_gender_icon'])? 'true' : 'false')
                                           .')';

$_load_cm_user_options=true;

// Init template
_pcpin_loadClass('pcpintpl'); $tpl=new PcpinTpl();
$tpl->setBasedir('./tpl');
$tpl->readTemplatesFromFile('./room_selection.tpl');

// JS files
$_js_files[]='./js/room_selection.js';
$_js_files[]='./js/room_structure.js';
$_js_files[]='./js/user.js';

// JS language expressions
$_js_lng[]='user_invited_you';
$_js_lng[]='select_room';
$_js_lng[]='room_password';
$_js_lng[]='online_status_0';
$_js_lng[]='online_status_1';
$_js_lng[]='online_status_2';
$_js_lng[]='online_status_3';
$_js_lng[]='users_profile';
$_js_lng[]='user_is_admin';
$_js_lng[]='user_is_moderator';
$_js_lng[]='gender';
$_js_lng[]='gender_m';
$_js_lng[]='gender_f';
$_js_lng[]='gender_-';
$_js_lng[]='subcategories';
$_js_lng[]='category_has_no_rooms';
$_js_lng[]='chat_rooms';
$_js_lng[]='chat_room';
$_js_lng[]='chat_categories';
$_js_lng[]='chat_category';
$_js_lng[]='show_online_users';
$_js_lng[]='hide_online_users';
$_js_lng[]='user';
$_js_lng[]='users';
$_js_lng[]='rooms';
$_js_lng[]='edit';
$_js_lng[]='enter_reason';
$_js_lng[]='optional';
$_js_lng[]='enter_duration';
$_js_lng[]='canceled_duration_invalid';
$_js_lng[]='ban_canceled_ip_equals';
$_js_lng[]='muted_locally';
$_js_lng[]='permanently_globalmuted';
$_js_lng[]='globalmuted_until';
$_js_lng[]='yes';
$_js_lng[]='no';
$_js_lng[]='create_new_room';
$_js_lng[]='room_is_password_protected';
$_js_lng[]='active';
$_js_lng[]='profile';
$_js_lng[]='guest';
$_js_lng[]='registered';
$_js_lng[]='admin';


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

// Add other vars
$tpl->addVar('main', 'welcome_message', htmlspecialchars(str_replace('[USER]', $current_user->login, $l->g('welcome_user'))));
if ($current_user->is_guest=='n') {
  $tpl->addVar('last_login', 'last_login', htmlspecialchars($current_user->previous_login>'0000-00-00 00:00:00'? $current_user->makeDate(PCPIN_Common::datetimeToTimestamp($current_user->previous_login)) : $l->g('never')));
}

$template->addVar('moderator_user_options', 'display', $current_user->moderated_rooms!='' || $current_user->is_admin==='y');
$template->addVar('admin_user_options', 'display', $current_user->is_admin==='y');
?>