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

// Load colorbox
$_load_colorbox=true;

// Default: Do not context menu user options
$_load_cm_user_options=true;

if (empty($current_user->id) || $session->_s_user_id!=$current_user->id) {
  header('Location: '.PCPIN_FORMLINK.'?'.md5(microtime()));
  die();
}

if (empty($profile_user_id) || $current_user->is_admin!=='y') {
  $profile_user_id=$current_user->id;
}

_pcpin_loadClass('room'); $room=new PCPIN_Room($session);

// Get available languages
$languages=array();
if (!empty($session->_conf_all['allow_language_selection']) && $session->_s_user_id==$profile_user_id) {
  $languages=$l->getLanguages(false);
}

if (   (!empty($just_logged_in) || (!isset($do_edit) || $current_user->is_admin!=='y') && !isset($own_profile))
    && !empty($session->_conf_all['default_room'])
    && $room->_db_getList('id', 'id = '.$session->_conf_all['default_room'], 1)) {
  // Default room specified and exists
  $room->_db_freeList();
  if ($room->putUser($profile_user_id, $session->_conf_all['default_room'])) {
    header('Location: '.PCPIN_FORMLINK.'?s_id='.$session->_s_id.'&inc=chat_room&ts='.time());
    die();
  }
}
if (!empty($session->_s_room_id) && (!isset($do_edit) || $current_user->is_admin!=='y') && !isset($own_profile)) {
  // User was in chat room. Push him out.
  $room->putUser($current_user->id, 0, $session->_s_stealth_mode=='y', 'n');
}


if (isset($do_edit) && $current_user->is_admin==='y' && $current_user->_db_getList('id = '.$profile_user_id, 1)) {
  // Load other user's profile
  $profile_user=new PCPIN_User($session);
  $profile_user->_db_setObject($current_user->_db_list[0]);
  $current_user->_db_freeList();
  $profile_userdata=new PCPIN_UserData($session);
  $profile_userdata->_db_loadObj($profile_user->id, 'user_id');
} else {
  // Own profile
  $profile_user=&$current_user;
  $profile_userdata=&$current_userdata;
  unset($do_edit);
}

// Display "Avatar gallery" link?
$show_avatar_gallery_link=false;
if (!empty($session->_conf_all['avatar_gallery'])) {
  _pcpin_loadClass('avatar'); $avatar=new PCPIN_Avatar($session);
  if ($avatar->_db_getList('COUNT', 'user_id = 0')) {
    if ($avatar->_db_list_count>1) {
      // There are more that one default avatar
      $show_avatar_gallery_link=true;
    }
  }
}

$_body_onload[1000000]='initProfile('.$session->_conf_all['nickname_length_min'].','
                                     .$session->_conf_all['nickname_length_max'].','
                                     .'\''.$profile_userdata->homepage.'\','
                                     .'\''.$profile_userdata->gender.'\','
                                     .$session->_conf_all['updater_interval'].','
                                     .'\''.$session->_conf_all['default_nickname_color'].'\','
                                     .(!empty($current_user->hide_email)? 'true' : 'false').','
                                     .$session->_conf_all['avatars_max_count'].','
                                     .$session->_conf_all['nicknames_max_count'].','
                                     .$session->_conf_all['room_selection_display_type'].','
                                     .(!empty($session->_conf_all['userlist_gender_icon'])? 'true' : 'false').','
                                     .((!empty($session->_conf_all['userlist_avatar_thumb']) && 2==PCPIN_GD_VERSION)? 'true' : 'false').','
                                     .((!empty($session->_conf_all['userlist_privileged_flags']) && 2==PCPIN_GD_VERSION)? 'true' : 'false').','
                                     .((isset($do_edit))? 'true' : 'false').','
                                     .$profile_user_id.','
                                     .((isset($own_profile))? 'true' : 'false').','
                                     .($show_avatar_gallery_link? 'true' : 'false')
                                     .')';


// Calculate time spent online
$online_seconds=$current_user->calculateOnlineTime($profile_user->id);
$online_days=floor($online_seconds/86400);
$online_seconds-=$online_days*86400;
$online_hours=floor($online_seconds/3600);
$online_seconds-=$online_hours*3600;
$online_minutes=floor($online_seconds/60);
$online_seconds-=$online_minutes*60;


// Init template
_pcpin_loadClass('pcpintpl'); $tpl=new PcpinTpl();
$tpl->setBasedir('./tpl');
$tpl->readTemplatesFromFile('./profile_main.tpl');

// JS files
$_js_files[]='./js/profile.js';
$_js_files[]='./js/room_structure.js';
$_js_files[]='./js/user.js';

// JS language expressions
$_js_lng[]='users_profile';
$_js_lng[]='avatar';
$_js_lng[]='delete_avatar';
$_js_lng[]='confirm_delete_avatar';
$_js_lng[]='confirm_delete_nickname';
$_js_lng[]='enter_new_nickname';
$_js_lng[]='nickname_empty_error';
$_js_lng[]='nickname_too_short_error';
$_js_lng[]='nickname_too_long_error';
$_js_lng[]='delete_nickname';
$_js_lng[]='enter_new_email_address';
$_js_lng[]='email_invalid';
$_js_lng[]='enter_new_password';
$_js_lng[]='password_empty';
$_js_lng[]='password_too_short';
$_js_lng[]='user_is_admin';
$_js_lng[]='user_is_moderator';
$_js_lng[]='gender';
$_js_lng[]='gender_m';
$_js_lng[]='gender_f';
$_js_lng[]='gender_-';
$_js_lng[]='select_room';
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
$_js_lng[]='user_invited_you';
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
$_js_lng[]='enter_your_homepage';
$_js_lng[]='enter_your_age';
$_js_lng[]='enter_your_icq';
$_js_lng[]='enter_your_msn';
$_js_lng[]='enter_your_aim';
$_js_lng[]='enter_your_yim';
$_js_lng[]='enter_your_location';
$_js_lng[]='enter_your_occupation';
$_js_lng[]='enter_your_interests';
$_js_lng[]='create_new_room';
$_js_lng[]='room_is_password_protected';
$_js_lng[]='active';
$_js_lng[]='your_profile';
$_js_lng[]='profile';
$_js_lng[]='your_nicknames';
$_js_lng[]='nicknames';
$_js_lng[]='your_avatars';
$_js_lng[]='avatars';
$_js_lng[]='guest';
$_js_lng[]='registered';
$_js_lng[]='admin';
$_js_lng[]='sure_change_user_level';
$_js_lng[]='really_sure';
$_js_lng[]='change_own_level_error';
$_js_lng[]='select_new_level_or_cancel';
$_js_lng[]='delete_yourself_error';
$_js_lng[]='sure_delete_user';
$_js_lng[]='primary';
$_js_lng[]='room_password';
$_js_lng[]='sure_activate_account';
$_js_lng[]='online_status_0';
$_js_lng[]='online_status_1';
$_js_lng[]='online_status_2';
$_js_lng[]='online_status_3';


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
$tpl->addVars('main', array('welcome_message'=>htmlspecialchars(str_replace('[USER]', $current_user->login, $l->g('welcome_user'))),
                            'registration_date'=>htmlspecialchars($current_user->makeDate(PCPIN_Common::datetimeToTimestamp($profile_user->joined))),
                            'email_address'=>htmlspecialchars($profile_user->email),
                            'homepage'=>htmlspecialchars($profile_userdata->homepage),
                            'homepage_urlencoded'=>urlencode($profile_userdata->homepage),
                            'age'=>htmlspecialchars($profile_userdata->age),
                            'icq'=>htmlspecialchars($profile_userdata->icq),
                            'msn'=>htmlspecialchars($profile_userdata->msn),
                            'aim'=>htmlspecialchars($profile_userdata->aim),
                            'yim'=>htmlspecialchars($profile_userdata->yim),
                            'location'=>htmlspecialchars($profile_userdata->location),
                            'occupation'=>htmlspecialchars($profile_userdata->occupation),
                            'interests'=>htmlspecialchars($profile_userdata->interests),
                            'hide_email'=>htmlspecialchars(!empty($profile_user->hide_email)? $l->g('yes') : $l->g('no')),
                            'online_seconds'=>htmlspecialchars($online_seconds),
                            'profile_username_hidden'=>htmlspecialchars($profile_user->login)
                            ));

if ($current_user->is_guest=='n') {
  $tpl->addVar('last_login', 'last_login', htmlspecialchars($profile_user->previous_login>'0000-00-00 00:00:00'? $current_user->makeDate(PCPIN_Common::datetimeToTimestamp($profile_user->previous_login)) : $l->g('never')));
}

// Show total online time
$tpl->addVar('online_days', 'days', htmlspecialchars($online_days));
$tpl->addVar('online_hours', 'hours', htmlspecialchars($online_hours));
$tpl->addVar('online_minutes', 'minutes', htmlspecialchars($online_minutes));

// Language selection
if (!empty($session->_conf_all['allow_language_selection']) && $session->_s_user_id==$profile_user_id) {
  $tpl->addVar('language_selection', 'display', true);
  foreach ($languages as $data) {
    $tpl->addVars('language_selection_option', array('id'=>htmlspecialchars($data['id']),
                                                     'local_name'=>htmlspecialchars($data['local_name']),
                                                     'selected'=>$data['id']==$session->_s_language_id? 'selected="selected"' : '',
                                                     ));
    $tpl->parseTemplate('language_selection_option', 'a');
  }
}

// Display "Change password" link
$tpl->addVar('change_password', 'display', $current_user->is_guest=='n');

$template->addVar('moderator_user_options', 'display', $current_user->moderated_rooms!='' || $current_user->is_admin==='y');
$template->addVar('admin_user_options', 'display', $current_user->is_admin==='y');
?>