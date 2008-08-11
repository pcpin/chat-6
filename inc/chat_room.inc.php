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

$_force_buggy_doctype=true; // Do not hide <!DOCTYPE> declaration for IE6

// Load colorbox
$_load_colorbox=true;

// Load smiliebox
$_load_smiliebox=true;

// Default: Do not context menu user options
$_load_cm_user_options=true;

if (empty($current_user->id) || $session->_s_user_id!=$current_user->id || empty($session->_s_room_id)) {
  header('Location: '.PCPIN_FORMLINK.'?'.md5(microtime()));
  die();
}

// Delete temporary message attachments
_pcpin_loadClass('tmpdata'); $tmpdata=new PCPIN_TmpData($session);
$tmpdata->deleteUserRecords($current_user->id, 3);

// Get room background image
_pcpin_loadClass('room'); $room=new PCPIN_Room($session);
$room->_db_getList('background_image', 'id = '.$session->_s_room_id, 1);
$background_image=$room->_db_list[0]['background_image'];
$room->_db_freeList();

// Get default avatar
_pcpin_loadClass('avatar'); $avatar=new PCPIN_Avatar($session);
$avatars=$avatar->getAvatars($current_user->id, 1);
if (!empty($avatars)) {
  $avatar_bid=$avatars[0]['binaryfile_id'];
} else {
  $avatar_bid=0;
}
unset($avatars);


// JS files to load
$_js_files[]='./js/user.js';
$_js_files[]='./js/message_queue.js';
$_js_files[]='./js/chat_room.js';
$_js_files[]='./js/commands.js';

$_js_lng[]='user_entered_this_room';
$_js_lng[]='user_left_this_room';
$_js_lng[]='nickname_matches_multiple';
$_js_lng[]='nickname_matches_empty';
$_js_lng[]='command_not_found';
$_js_lng[]='user_kicked_with_reason';
$_js_lng[]='user_kicked_without_reason';
$_js_lng[]='user_banned_with_reason';
$_js_lng[]='user_banned_without_reason';
$_js_lng[]='user_banned_permanently_with_reason';
$_js_lng[]='user_banned_permanently_without_reason';
$_js_lng[]='online_status';
$_js_lng[]='online_status_0';
$_js_lng[]='online_status_1';
$_js_lng[]='online_status_2';
$_js_lng[]='online_status_3';
$_js_lng[]='sure_to_log_out';
$_js_lng[]='sure_to_leave_room';
$_js_lng[]='help_hint';
$_js_lng[]='muted_locally';
$_js_lng[]='cannot_apply_cmd_to_yourself';
$_js_lng[]='enter_reason';
$_js_lng[]='optional';
$_js_lng[]='enter_duration';
$_js_lng[]='canceled_duration_invalid';
$_js_lng[]='user_globalmuted_with_reason';
$_js_lng[]='user_globalmuted_without_reason';
$_js_lng[]='user_globalmuted_permanently_with_reason';
$_js_lng[]='user_globalmuted_permanently_without_reason';
$_js_lng[]='permanently_globalmuted';
$_js_lng[]='globalmuted_until';
$_js_lng[]='ignored';
$_js_lng[]='you_are_muted_until';
$_js_lng[]='you_are_muted_permanently';
$_js_lng[]='ban_canceled_ip_equals';
$_js_lng[]='gender';
$_js_lng[]='gender_m';
$_js_lng[]='gender_f';
$_js_lng[]='gender_-';
$_js_lng[]='avatar';
$_js_lng[]='user_is_admin';
$_js_lng[]='user_is_this_room_moderator';
$_js_lng[]='show_message_time';
$_js_lng[]='hide_message_time';
$_js_lng[]='delete';
$_js_lng[]='attachment';
$_js_lng[]='auto_scroll';
$_js_lng[]='on';
$_js_lng[]='off';
$_js_lng[]='said_message';
$_js_lng[]='whispered_message';
$_js_lng[]='room_password';

// Init smilies after load
$_body_onload[]='initSmilieList()';

// Init chat room client
$_body_onload[]='initChatRoom('.$session->_s_room_id.', '
                               .$session->_conf_all['updater_interval'].', '
                               .$session->_conf_all['userlist_width'].', '
                               .$session->_conf_all['userlist_position'].', '
                               .$session->_conf_all['controls_height'].', '
                               .$session->_conf_all['message_length_max'].', \''
                               .htmlspecialchars($session->_conf_all['default_font_family']).'\', \''
                               .$session->_conf_all['default_font_size'].'\', '
                               .($session->_s_stealth_mode=='y'? 'true' : 'false').','
                               .(!empty($session->_conf_all['userlist_gender_icon'])? 'true' : 'false').','
                               .((!empty($session->_conf_all['userlist_avatar_thumb']) && 2==PCPIN_GD_VERSION)? 'true' : 'false').','
                               .((!empty($session->_conf_all['userlist_privileged_flags']) && 2==PCPIN_GD_VERSION)? 'true' : 'false').','
                               .(($current_user->show_message_time=='y')? 'true' : 'false').','
                               .(($current_user->allow_sounds=='y')? 'true' : 'false').','
                               .'\''.htmlspecialchars($current_user->outgoing_message_color).'\', '
                               .'\''.htmlspecialchars($session->_conf_all['default_room_background_color']).'\', '
                               .$session->_conf_all['msg_attachments_limit'].','
                               .$session->_conf_all['top_banner_height'].', '
                               .$session->_conf_all['bottom_banner_height'].', '
                               .$session->_conf_all['banner_refresh_rate'].', '
                               .$session->_conf_all['popup_banner_period'].', '
                               .$session->_conf_all['msg_banner_period'].', '
                               .$session->_conf_all['smilies_position'].', '
                               .$session->_conf_all['smilies_row_height'].', '
                               .$session->_conf_all['flood_protection_message_delay']
                               .')';

// Init template
_pcpin_loadClass('pcpintpl'); $tpl=new PcpinTpl();
$tpl->setBasedir('./tpl');
$tpl->readTemplatesFromFile('./chat_room.tpl');

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

// Display fonts
$tpl->addVar('fonts', 'fonts', htmlspecialchars($session->_conf_all['font_families']));
$tpl->addVar('fonts', 'font_sizes', htmlspecialchars($session->_conf_all['font_sizes']));

// Display help texts
$help_texts=$l->getExpressions('cmd_help_');
asort($help_texts);
foreach ($help_texts as $code=>$expr) {
  $cmd_allowed=true;
  // Block privileged commands
  switch (str_replace('cmd_help_', '', $code)) {
    case 'admin':
    case 'ban':
    case 'mute':
    case 'unmute':
    case 'ipban':
      // Command can be called by chat administrator only
      $cmd_allowed=$current_user->is_admin==='y';
    break;
    case 'kick':
      // Command can be called by chat administrator or current room moderator only
      $cmd_allowed=$current_user->is_admin==='y' || true===$_is_moderator;
    break;
  }
  if (true===$cmd_allowed) {
    $expr=str_replace("\n", '@BR@', str_replace("\r\n", "\n", $expr));
    $tpl->addVars('cmd_help_records', array('cmd'=>htmlspecialchars(substr($code, 9)),
                                            'text'=>htmlspecialchars($expr)
                                            ));
    $tpl->parseTemplate('cmd_help_records', 'a');
  }
}

// "Attachment" button
$tpl->addVar('msg_attachment_btn', 'display', !empty($session->_conf_all['msg_attachments_limit']));

// "Leave this room" menu topic
$tpl->addVar('leave_room_link', 'display', empty($session->_conf_all['default_room']));

// "Sounds On/Off" button
$tpl->addVar('invert_sounds_btn', 'display', !empty($session->_conf_all['allow_sounds']));

// "Your profile" menu topic
$tpl->addVar('your_profile_button', 'display', !PCPIN_SLAVE_MODE);

// Room selection
$tpl->addVar('room_selection', 'display', empty($session->_conf_all['default_room']));

// Room background image
if (!empty($background_image)) {
  $tpl->addVar('main', 'room_background_image_url', PCPIN_FORMLINK.'?s_id='.$session->_s_id.'&b_id='.$background_image);
} else {
  $tpl->addVar('main', 'room_background_image_url', './pic/clearpixel_1x1.gif');
}

// Add smilies to the main template
_pcpin_loadClass('smilie'); $smilie=new PCPIN_Smilie($session);
$smilies=$smilie->getSmilies();
if (!empty($smilies)) {
  // Append empty elements to smilies array
  $smilies_append=$session->_conf_all['smilies_per_row']-count($smilies)%$session->_conf_all['smilies_per_row'];
  if ($smilies_append!=$session->_conf_all['smilies_per_row'] && $smilies_append>0) {
    for ($i=0; $i<$smilies_append; $i++) {
      array_push($smilies, array('id'=>'',
                                 'binaryfile_id'=>'',
                                 'code'=>'',
                                 'description'=>'',
                                 ));
    }
  }
  $col=1;
  $maxcol=0;
  foreach ($smilies as $smilie_data) {
    $template->addVars('smiliebox_col', array('id'=>htmlspecialchars($smilie_data['id']),
                                              'binaryfile_id'=>htmlspecialchars($smilie_data['binaryfile_id']),
                                              'code'=>htmlspecialchars($smilie_data['code']),
                                              'description'=>htmlspecialchars($smilie_data['description']),
                                              's_id'=>htmlspecialchars($session->_s_id),
                                              'padding_top'=>htmlspecialchars($inc=='pm_box' || $session->_conf_all['smilies_position']!=0? 8 : 0),
                                              'padding_bottom'=>htmlspecialchars($inc=='pm_box' || $session->_conf_all['smilies_position']!=0? 8 : 0),
                                              'padding_left'=>htmlspecialchars($inc=='pm_box' || $session->_conf_all['smilies_position']!=0? 8 : 0),
                                              'padding_right'=>htmlspecialchars(8),
                                              ));
    $template->parseTemplate('smiliebox_col', 'a');
    if ($col>$maxcol) {
      $maxcol=$col;
    }
    if (++$col>$session->_conf_all['smilies_per_row'] && ($inc=='pm_box' || $session->_conf_all['smilies_position']!=0)) {
      $template->parseTemplate('smiliebox_row', 'a');
      $template->clearTemplate('smiliebox_col', 'a');
      $col=1;
    }
  }
  if ($inc=='pm_box' || $session->_conf_all['smilies_position']!=0) {
    $template->addVar('smiliebox_header_row', 'header_row_colspan', htmlspecialchars($maxcol));
  }
}
unset($smilies);

// Add profile image source
$tpl->addVar('your_profile_button', 'avatar_bid', $avatar_bid);

// Admin and moderator controls
$tpl->addVar('admin_btn', 'display', $current_user->is_admin==='y');
$template->addVar('moderator_user_options', 'display', true===$_is_moderator || $current_user->is_admin==='y');
$template->addVar('admin_user_options', 'display', $current_user->is_admin==='y');

?>