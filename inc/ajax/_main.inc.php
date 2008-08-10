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

// Initiate XML writer object
_pcpin_loadClass('xmlwrite'); $xmlwriter=new PCPIN_XMLWrite($ajax);

// Defaults
$xmlwriter->setHeaderMessage('ACCESS_DENIED');
$xmlwriter->setHeaderStatus(-1);

if (!empty($ajax) && is_scalar($ajax)) {
  // AJAX request

  switch ($ajax) {

    case 'add_avatar_gallery':
      // Add new avatar into Avatar gallery
      require_once('./inc/ajax/add_avatar_gallery.inc.php');
    break;

    case 'add_banner':
      // Add new banner
      require_once('./inc/ajax/add_banner.inc.php');
    break;

    case 'add_disallowed_name':
      // Add new disallowed username
      require_once('./inc/ajax/add_disallowed_name.inc.php');
    break;

    case 'add_filtered_word':
      // Add new bad word to filter
      require_once('./inc/ajax/add_filtered_word.inc.php');
    break;

    case 'add_new_user':
      // Add new user
      if (PCPIN_SLAVE_MODE) {
        // Not allowed in Slave mode
        $xmlwriter->setHeaderMessage('SLAVE_MODE');
        $xmlwriter->setHeaderStatus(1);
      } else {
        require_once('./inc/ajax/add_new_user.inc.php');
      }
    break;

    case 'add_nickname':
      // Add new nickname
      require_once('./inc/ajax/add_nickname.inc.php');
    break;

    case 'add_smilie':
      // Add new smilie
      require_once('./inc/ajax/add_smilie.inc.php');
    break;

    case 'activate_user':
      // Manually activate user account
      require_once('./inc/ajax/activate_user.inc.php');
    break;

    case 'ban':
      // Ban user / IP address
      require_once('./inc/ajax/ban.inc.php');
    break;

    case 'call_moderator':
      // "Call moderator"
      require_once('./inc/ajax/call_moderator.inc.php');
    break;

    case 'change_email':
      // Change email address
      require_once('./inc/ajax/change_email.inc.php');
    break;

    case 'change_email_visibility':
      // Change email address visibility
      require_once('./inc/ajax/change_email_visibility.inc.php');
    break;

    case 'change_online_status':
      // Change online status
      require_once('./inc/ajax/change_online_status.inc.php');
    break;

    case 'change_password':
      // Change password
      require_once('./inc/ajax/change_password.inc.php');
    break;

    case 'chat_updater':
      // Chat updater request
      require_once('./inc/ajax/chat_updater.inc.php');
    break;

    case 'create_category':
      // Create new chat category
      require_once('./inc/ajax/create_category.inc.php');
    break;

    case 'create_custom_profile_field':
      // Create new custom profile field
      require_once('./inc/ajax/create_custom_profile_field.inc.php');
    break;

    case 'create_room':
      // Create new chat room
      require_once('./inc/ajax/create_room.inc.php');
    break;

    case 'create_user_room':
      // Create user room
      require_once('./inc/ajax/create_user_room.inc.php');
    break;

    case 'copy_language':
      // Copy a language
      require_once('./inc/ajax/copy_language.inc.php');
    break;

    case 'delete_avatar':
      // Delete avatar
      require_once('./inc/ajax/delete_avatar.inc.php');
    break;

    case 'delete_avatar_gallery':
      // Delete avatar from Avatar Gallery
      require_once('./inc/ajax/delete_avatar_gallery.inc.php');
    break;

    case 'delete_banner':
      // Delete banner
      require_once('./inc/ajax/delete_banner.inc.php');
    break;

    case 'delete_disallowed_name':
      // Delete disallowed name
      require_once('./inc/ajax/delete_disallowed_name.inc.php');
    break;

    case 'delete_category':
      // Delete chat category
      require_once('./inc/ajax/delete_category.inc.php');
    break;

    case 'delete_custom_profile_field':
      // Delete custom profile field
      require_once('./inc/ajax/delete_custom_profile_field.inc.php');
    break;

    case 'delete_filtered_word':
      // Delete bad word from filter
      require_once('./inc/ajax/delete_filtered_word.inc.php');
    break;

    case 'delete_language':
      // Delete language
      require_once('./inc/ajax/delete_language.inc.php');
    break;

    case 'delete_msg_attachment':
      // Delete temporary message attachment
      require_once('./inc/ajax/delete_msg_attachment.inc.php');
    break;

    case 'delete_nickname':
      // Delete nickname
      require_once('./inc/ajax/delete_nickname.inc.php');
    break;

    case 'delete_room':
      // Delete chat room
      require_once('./inc/ajax/delete_room.inc.php');
    break;

    case 'delete_smilie':
      // Delete smilie
      require_once('./inc/ajax/delete_smilie.inc.php');
    break;

    case 'delete_user':
      // Delete user
      require_once('./inc/ajax/delete_user.inc.php');
    break;

    case 'do_login':
      // Login attempt
      if (PCPIN_SLAVE_MODE && empty($admin_login)) {
        // Not allowed in Slave mode
        $xmlwriter->setHeaderMessage('SLAVE_MODE');
        $xmlwriter->setHeaderStatus(1);
      } else {
        require_once('./inc/ajax/do_login.inc.php');
      }
    break;

    case 'do_logout':
      // Log out
      require_once('./inc/ajax/do_logout.inc.php');
    break;

    case 'do_reset_password':
      // Reset password
      if (PCPIN_SLAVE_MODE) {
        // Not allowed in Slave mode
        $xmlwriter->setHeaderMessage('SLAVE_MODE');
        $xmlwriter->setHeaderStatus(1);
      } else {
        require_once('./inc/ajax/do_reset_password.inc.php');
      }
    break;

    case 'do_register':
      // Register new account
      if (PCPIN_SLAVE_MODE) {
        // Not allowed in Slave mode
        $xmlwriter->setHeaderMessage('SLAVE_MODE');
        $xmlwriter->setHeaderStatus(1);
      } else {
        require_once('./inc/ajax/do_register.inc.php');
      }
    break;

    case 'enter_chat_room':
      // Enter chat room
      require_once('./inc/ajax/enter_chat_room.inc.php');
    break;

    case 'get_avatars':
      // Get avatars
      require_once('./inc/ajax/get_avatars.inc.php');
    break;

    case 'get_avatars_gallery':
      // Get avatar gallery
      require_once('./inc/ajax/get_avatars_gallery.inc.php');
    break;

    case 'get_banners':
      // Get banners
      require_once('./inc/ajax/get_banners.inc.php');
    break;

    case 'get_client_info':
      // Get client info
      require_once('./inc/ajax/get_client_info.inc.php');
    break;

    case 'get_custom_profile_fields':
      // Get custom user profile fields
      require_once('./inc/ajax/get_custom_profile_fields.inc.php');
    break;

    case 'get_disallowed_names':
      // Get disallowed names
      require_once('./inc/ajax/get_disallowed_names.inc.php');
    break;

    case 'get_filtered_words':
      // Get word blacklist
      require_once('./inc/ajax/get_filtered_words.inc.php');
    break;

    case 'get_invitations':
      // Get new invitations
      require_once('./inc/ajax/get_invitations.inc.php');
    break;

    case 'get_languages':
      // Get available languages list
      require_once('./inc/ajax/get_languages.inc.php');
    break;

    case 'get_memberlist':
      // Get memberlist
      require_once('./inc/ajax/get_memberlist.inc.php');
    break;

    case 'get_moderator_data':
      // Get moderator data
      require_once('./inc/ajax/get_moderator_data.inc.php');
    break;

    case 'get_nicknames':
      // Get nicknames list
      require_once('./inc/ajax/get_nicknames.inc.php');
    break;

    case 'get_ping':
      // Get ping
      require_once('./inc/ajax/get_ping.inc.php');
    break;

    case 'get_settings':
      // Get settings
      require_once('./inc/ajax/get_settings.inc.php');
    break;

    case 'get_slave_mode_masters':
      // Get master modules for slave mode
      require_once('./inc/ajax/get_slave_mode_masters.inc.php');
    break;

    case 'get_smilies':
      // Get smilies
      require_once('./inc/ajax/get_smilies.inc.php');
    break;

    case 'get_room_structure':
      // Get room tree
      require_once('./inc/ajax/get_room_structure.inc.php');
    break;

    case 'get_new_messages':
      // Get new messages (called from outside of chat room)
      require_once('./inc/ajax/get_new_messages.inc.php');
    break;

    case 'globalmute':
      // Global mute/unmute user
      require_once('./inc/ajax/globalmute.inc.php');
    break;

    case 'invite':
      // Invite user to join a room
      require_once('./inc/ajax/invite.inc.php');
    break;

    case 'ip_filter_add_address':
      // Add new address to IP filter
      require_once('./inc/ajax/ip_filter_add_address.inc.php');
    break;

    case 'ip_filter_delete_address':
      // Delete IP addresses from filter table
      require_once('./inc/ajax/ip_filter_delete_address.inc.php');
    break;

    case 'ip_filter_get_addresses':
      // Get filtered IP addresses
      require_once('./inc/ajax/ip_filter_get_addresses.inc.php');
    break;

    case 'kick':
      // Kick user out of chat
      require_once('./inc/ajax/kick.inc.php');
    break;

    case 'load_banner':
      // Load banner data
      require_once('./inc/ajax/load_banner.inc.php');
    break;

    case 'manage_language_expressions':
      // Manage expressions for selected language
      require_once('./inc/ajax/manage_language_expressions.inc.php');
    break;

    case 'mute_unmute_locally':
      // Mute or unmute user locally
      require_once('./inc/ajax/mute_unmute_locally.inc.php');
    break;

    case 'optimize_db':
      // Optimize database
      require_once('./inc/ajax/optimize_db.inc.php');
    break;

    case 'set_avatar_from_gallery':
      // Set avatar from Avatar Gallery
      require_once('./inc/ajax/set_avatar_from_gallery.inc.php');
    break;

    case 'set_default_nickname':
      // Set new default nickname
      require_once('./inc/ajax/set_default_nickname.inc.php');
    break;

    case 'set_primary_avatar':
      // Set new primary avatar
      require_once('./inc/ajax/set_primary_avatar.inc.php');
    break;

    case 'set_primary_avatar_gallery':
      // Set new primary Gallery Avatar
      require_once('./inc/ajax/set_primary_avatar_gallery.inc.php');
    break;

    case 'set_room_selection_view':
      // Set new room selection view type
      require_once('./inc/ajax/set_room_selection_view.inc.php');
    break;

    case 'set_user_language':
      // Set new user language
      require_once('./inc/ajax/set_user_language.inc.php');
    break;

    case 'set_user_level':
      // Set new user level
      require_once('./inc/ajax/set_user_level.inc.php');
    break;

    case 'unban':
      // Ban user
      require_once('./inc/ajax/unban.inc.php');
    break;

    case 'update_banner':
      // Update banner data
      require_once('./inc/ajax/update_banner.inc.php');
    break;

    case 'update_category':
      // Update category data
      require_once('./inc/ajax/update_category.inc.php');
    break;

    case 'update_custom_profile_field':
      // Update custom profile field
      require_once('./inc/ajax/update_custom_profile_field.inc.php');
    break;

    case 'update_language':
      // Update language data
      require_once('./inc/ajax/update_language.inc.php');
    break;

    case 'update_moderator':
      // Update moderator data
      require_once('./inc/ajax/update_moderator.inc.php');
    break;

    case 'update_nickname':
      // Update nickname
      require_once('./inc/ajax/update_nickname.inc.php');
    break;

    case 'update_room':
      // Update room data
      require_once('./inc/ajax/update_room.inc.php');
    break;

    case 'update_settings':
      // Update settings
      require_once('./inc/ajax/update_settings.inc.php');
    break;

    case 'update_smilie':
      // Update smilie data
      require_once('./inc/ajax/update_smilie.inc.php');
    break;

    case 'update_userdata':
      // Update userdata (in "userdata" table)
      if (PCPIN_SLAVE_MODE) {
        // Not allowed in Slave mode
        $xmlwriter->setHeaderMessage('SLAVE_MODE');
        $xmlwriter->setHeaderStatus(1);
      } else {
        require_once('./inc/ajax/update_userdata.inc.php');
      }
    break;

  }
  
}

// Show timers
if (PCPIN_DEBUGMODE && PCPIN_LOG_TIMER) {
  $end_times=explode(' ', microtime());
  $start_times=explode(' ', $_pcpin_log_timer_start);
  $start=1*(substr($start_times[1], -5).substr($start_times[0], 1, 5));
  $end=1*(substr($end_times[1], -5).substr($end_times[0], 1, 5));
  $diff=$end-$start;
  $mysql_usage=$_GET['_pcpin_log_mysql_usage'];
  $xmlwriter->setDebugTimers(array('total'=>number_format($diff, 3, '.', ''),
                                   'code'=>number_format($diff-$mysql_usage, 3, '.', ''),
                                   'db'=>number_format($mysql_usage, 3, '.', '')
                                   ));
}

// Send headers
header('Content-Type: text/xml; charset=UTF-8');
header('Expires: '.gmdate('D, d M Y H:i:s').' GMT');
header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
header('Pragma: no-cache');

// Send XML
echo $xmlwriter->makeXML();

// Terminate script
// Warning: do not remove next line!
die();
?>