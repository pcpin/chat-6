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

// Default: Do not load context menu user options
$_load_cm_user_options=false;

if (empty($current_user->id) || $session->_s_user_id!=$current_user->id) {
  header('Location: '.PCPIN_FORMLINK.'?'.md5(microtime()));
  die();
}

// Get available languages
$languages=array();
if (!empty($session->_conf_all['allow_language_selection'])) {
  $languages=$l->getLanguages(false);
}

if (empty($profile_user_id)) $profile_user_id=$current_user->id;
if ($profile_user_id!=$current_user->id && ($current_user->is_admin!=='y' || !$current_user->_db_getList('login', 'id =# '.$profile_user_id, 1))) {
  $profile_user_id=$current_user->id;
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
    $avatar->_db_freeList();
  }
}

$_body_onload[1000000]='initProfile('.$session->_conf_all['nickname_length_min'].','
                                     .$session->_conf_all['nickname_length_max'].','
                                     .'\''.$session->_conf_all['default_nickname_color'].'\','
                                     .$session->_conf_all['avatars_max_count'].','
                                     .$session->_conf_all['nicknames_max_count'].','
                                     .$profile_user_id.','
                                     .($show_avatar_gallery_link? 'true' : 'false').','
                                     .($session->_conf_all['allow_language_selection']? 'true' : 'false').','
                                     .(($session->_conf_all['allow_account_unsubscribe'] && $profile_user_id==$current_user->id)? 'true' : 'false')
                                     .')';



// Init template
_pcpin_loadClass('pcpintpl'); $tpl=new PcpinTpl();
$tpl->setBasedir('./tpl');
$tpl->readTemplatesFromFile('./profile_main.tpl');

// JS files
$_js_files[]='./js/profile.js';
$_js_files[]='./js/context_menu_user_options.js';

// JS language expressions
$_js_lng[]='users_profile';
$_js_lng[]='days';
$_js_lng[]='hours';
$_js_lng[]='minutes';
$_js_lng[]='seconds';
$_js_lng[]='enter_new_password';
$_js_lng[]='confirm_password';
$_js_lng[]='email_invalid';
$_js_lng[]='passwords_not_ident';
$_js_lng[]='password_empty';
$_js_lng[]='password_too_short';
$_js_lng[]='gender_m';
$_js_lng[]='gender_f';
$_js_lng[]='gender_-';
$_js_lng[]='delete_avatar';
$_js_lng[]='confirm_delete_avatar';
$_js_lng[]='primary';
$_js_lng[]='active';
$_js_lng[]='delete_nickname';
$_js_lng[]='edit';
$_js_lng[]='confirm_delete_nickname';
$_js_lng[]='sure_activate_account';
$_js_lng[]='change_own_level_error';
$_js_lng[]='sure_change_user_level';
$_js_lng[]='really_sure';
$_js_lng[]='delete';
$_js_lng[]='delete_my_account_confirmation';


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

// Display language selection
if (!empty($session->_conf_all['allow_language_selection'])) {
  $tpl->addVar('language_selection', 'display', true);
  foreach ($languages as $data) {
    $tpl->addVars('language_selection_option', array('id'=>htmlspecialchars($data['id']),
                                                     'local_name'=>htmlspecialchars($data['local_name']),
                                                     'selected'=>$data['id']==$session->_s_language_id? 'selected="selected"' : '',
                                                     ));
    $tpl->parseTemplate('language_selection_option', 'a');
  }
}

?>