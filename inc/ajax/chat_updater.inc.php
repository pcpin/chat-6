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
 * Get current chat state
 * @param   int       $room_id              ID of the room where the request was sent from
 * @param   boolean   $first_request        If TRUE, then full room information and full userlist will be returned,
 *                                          returned messages count will be limited by 'init_display_messages_count'
 * @param   boolean   $full_request         If TRUE, then full room information and full userlist will be returned
 * @param   int       $get_last_msgs        If > 0, then last X messages will be returned (including already delivered messages)
 * @param   boolean   $pref_timestamp       Optional. Current state of client's "Display message timestamp" preference
 * @param   boolean   $pref_message_color   Optional. Current message color
 */
$message=$l->g('access_denied');
$status='-1'; // -1: Session is invalid

_pcpin_loadClass('message'); $msg=new PCPIN_Message($session);
_pcpin_loadClass('category'); $category=new PCPIN_Category($session);
_pcpin_loadClass('nickname'); $nickname=new PCPIN_Nickname($session);
_pcpin_loadClass('room'); $room=new PCPIN_Room($session);
_pcpin_loadClass('invitation'); $invitation=new PCPIN_Invitation($session);
_pcpin_loadClass('avatar'); $avatar=new PCPIN_Avatar($session);
_pcpin_loadClass('badword'); $badword=new PCPIN_Badword($session);
_pcpin_loadClass('banner'); $banner=new PCPIN_Banner($session);

$default_nicknames=array(); // cached nicknames

if (!isset($room_id) || !is_scalar($room_id)) $room_id=0;
$full_data='';
$chat_messages='';
if (!empty($first_request)) {
  $full_request=1;
}

$categories_xml='';
$invitations_xml='';
$banner_display_positions_xml='';

if (!empty($room_id) && !empty($current_user->id)) {
  if (empty($session->_s_room_id)) {
    // User is not in chat room
    $message='User is not in chat room';
    $status='100';
  } elseif ($session->_s_room_id != $room_id) {
    // User is in other room
    $message='User is in other room';
    $status='200';
  } elseif (!$room->_db_getList('id', 'id = '.$room_id, 1)) {
    // Room does not exists (anymore)
    $message='Room does not exists';
    $status='300';
  } else {
    $status='0';
    $message='OK';
    // Are there new messages in request?
    if (!empty($new_messages) && is_array($new_messages)) {
      // There are some new messages from user
      // Need to change online status?
      if ($session->_s_online_status!=1) {
        $session->_s_setOnlineStatus(1, $l->g('online_status_1'));
      }
      foreach ($new_messages as $data) {
        if (isset($data['body'])) {
          // Attachments?
          _pcpin_loadClass('tmpdata'); $tmpdata=new PCPIN_TmpData($session);
          $tmpdata->_db_getList('user_id = '.$current_user->id, 'type = 3');
          $tmpdata_list=$tmpdata->_db_list;
          $tmpdata->_db_freeList();
          if (!empty($tmpdata_list)) {
            $tmpdata->deleteUserRecords($current_user->id, 3, 0, true);
          }
          
          $data['body']=trim($data['body']);
          if ($data['body']!='' || !empty($tmpdata_list)) {

            $type=isset($data['type'])? $data['type'] : 0;
            $offline=isset($data['offline'])? $data['offline'] : 'n';
            $target_room_id=isset($data['target_room_id'])? $data['target_room_id'] : $session->_s_room_id;
            $target_user_id=isset($data['target_user_id'])? $data['target_user_id'] : 0;
            $body=_pcpin_substr($data['body'], 0, $session->_conf_all['message_length_max']);
            $privacy=isset($data['privacy'])? $data['privacy'] : 0;
            $css_properties=isset($data['css_properties'])? $data['css_properties'] : '';

            // Initial state: User must be not global muted
            $message_ok=$current_user->global_muted_until<date('Y-m-d H:i:s') && $current_user->global_muted_permanently=='n';

            if (empty($type)) {
              $message_ok=false;
              continue;
            }

            // Check target room
            if (!empty($target_room_id)) {
              if ($session->_s_room_id!=$target_room_id) {
                // A message to another room
                if (   $privacy==2 && $target_user_id>0 // PM can be sent from/to any room
                    && $current_user->is_admin!=='y' // Admin can send msgs to any room
                    && false===strpos(','.$target_room_id.',', ','.$current_user->moderated_rooms.',') // Mod can sent msgs to any room that he moderates
                    ) {
                  // TODO (ignoring...)
                  $message_ok=false;
                }
              }
            } else {
              // Global message to all rooms
              if ($current_user->is_admin!=='y') {
                // TODO (ignoring...)
                $message_ok=false;
              }
            }

            if (true!==$message_ok) {
              continue;
            }

            // Check message type
            switch ($type) {

              case '3001': // A text message
                if ($privacy==2) {
                  // PM does not needs a target room ID (will be delivered to user's room)
                  $target_room_id=0;
                }
                // Check message for containing bad words
                if (!empty($session->_conf_all['bad_language_mute']) && $current_user->is_admin!=='y' && false===$badword->checkString($body)) {
                  // Message contains bad words. Mute user.
                  $current_user->globalMuteUnmute($current_user->id, 1, $session->_conf_all['bad_language_mute'], $l->g('watch_your_language'));
                  $msg->addMessage(10110, 'n', 0, $l->g('server'), $session->_s_room_id, 0, $current_user->id.'/0/'.$session->_conf_all['bad_language_mute'].'/'.$l->g('watch_your_language'), date('Y-m-d H:i:s'), 0, '');
                }
              break;

              case '10001': // "/clear" command
                if (empty($target_room_id)) {
                  // "/clear all"
                  $message_ok=$current_user->is_admin==='y';
                } else {
                  // "/clear <room>"
                  $message_ok=$current_user->is_admin==='y' || false!==strpos(','.$target_room_id.',', ','.$current_user->moderated_rooms.',');
                }
              break;

              default:
                // Unknown command
                $message_ok=false;
              break;

            }

            if (true===$message_ok) {
              // Add message to database
              $msg->addMessage($type,
                               $offline,
                               $current_user->id,
                               $current_nickname,
                               $target_room_id,
                               $target_user_id,
                               $body,
                               date('Y-m-d H:i:s'),
                               $privacy,
                               $css_properties);
              // Attachments?
              _pcpin_loadClass('attachment'); $attachment=new PCPIN_Attachment($session);
              _pcpin_loadClass('message_log_attachment'); $message_log_attachment=new PCPIN_Message_Log_Attachment($session);
              _pcpin_loadClass('binaryfile'); $binaryfile=new PCPIN_BinaryFile($session);
              if (!empty($tmpdata_list)) {
                foreach ($tmpdata_list as $tmpdata_data) {
                  $attachment->addAttachment($msg->id, $tmpdata_data['binaryfile_id'], $tmpdata_data['filename']);
                  if (!empty($session->_conf_all['logging_period']) && $binaryfile->_db_getList('body,size,mime_type', 'id = '.$tmpdata_data['binaryfile_id'], 1)) {
                    $message_log_attachment->addLogRecord($msg->id, $tmpdata_data['filename'], $binaryfile->_db_list[0]['body'], $binaryfile->_db_list[0]['size'], $binaryfile->_db_list[0]['mime_type']);
                    $binaryfile->_db_freeList();
                  }
                }
              }
            }
          }
        }
      }
    }
    // Get new messages
    if (!empty($get_last_msgs) && $get_last_msgs>0) {
      $messages=$msg->getLastMessages($current_user->id, $get_last_msgs);
      $messages=array_reverse($messages);
    } elseif(!empty($first_request)) {
      $messages=$msg->getLastMessages($current_user->id, $session->_conf_all['init_display_messages_count']);
      $messages=array_reverse($messages);
    } else {
      $messages=$msg->getNewMessages($current_user->id);
    }
    if (!empty($messages)) {
      $chat_messages='';
      $msg_array=array();
      $last_message_id=$session->_s_last_message_id;
      foreach ($messages as $message_data) {
        $last_message_id=($last_message_id<$message_data['id'])? $message_data['id'] : $last_message_id;
        $msg_parts=explode('/', $message_data['body']);
        if (empty($full_request)) {
          // Need full data?
          switch ($message_data['type']) {

            case  '111':
            case  '115':
            case '1010':
            case '1100':
              $full_request=true;
            break;

            case '102': // User changed online status
              $parts=explode('/', $message_data['body']);
              if ($parts[2]=='') {
                // Empty online status message
                $parts[2]=$l->g('online_status_'.$parts[1]);
              }
              $message_data['body']=implode('/', $parts);
            break;

          }
        }
        // Get actor nickname
        $actor_nickname='';
        switch ($message_data['type']) {

          case   '102':
          case   '111':
          case   '115':
            if (!isset($default_nicknames[$msg_parts[0]])) {
              $default_nicknames[$msg_parts[0]]=$nickname->getDefaultNickname($msg_parts[0]);
            }
            if (''!=($actor_nick=$default_nicknames[$msg_parts[0]])) {
              $actor_nickname=$actor_nick;
            }
          break;

          case '10101':
          case '10105':
          case '10106':
          case '10110':
          case '10111':
            if (!isset($default_nicknames[$msg_parts[1]])) {
              if ($msg_parts[1]==0) {
                // System message
                $default_nicknames[$msg_parts[1]]=$l->g('server');
              } else {
                $default_nicknames[$msg_parts[1]]=$nickname->getDefaultNickname($msg_parts[1]);
              }
            }
            if (''!=($actor_nick=$default_nicknames[$msg_parts[1]])) {
              $actor_nickname=$actor_nick;
            }
          break;

        }
        if (empty($message_data['author_id']) || false===strpos(','.$current_user->muted_users.',', ','.$message_data['author_id'].',')) {
          $attachments_xml='';
          if (!empty($message_data['has_attachments'])) {
            foreach ($message_data['attachments'] as $attachment_data) {
              $attachments_xml.='<attachment>';
              $attachments_xml.='<id>'.htmlspecialchars($attachment_data['id']).'</id>';
              $attachments_xml.='<binaryfile_id>'.htmlspecialchars($attachment_data['binaryfile_id']).'</binaryfile_id>';
              $attachments_xml.='<filename>'.htmlspecialchars($attachment_data['filename']).'</filename>';
              $attachments_xml.='</attachment>';
            }
          }
          $msg_array[]='
    <message>
      <id>'.htmlspecialchars($message_data['id']).'</id>
      <type>'.htmlspecialchars($message_data['type']).'</type>
      <offline>'.htmlspecialchars($message_data['offline']).'</offline>
      <date>'.htmlspecialchars(PCPIN_Common::datetimeToTimestamp($message_data['date'])+$current_user->time_zone_offset-date('Z')).'</date>
      <author_id>'.htmlspecialchars($message_data['author_id']).'</author_id>
      <author_nickname>'.htmlspecialchars($message_data['author_nickname']).'</author_nickname>
      <target_user_id>'.htmlspecialchars($message_data['target_user_id']).'</target_user_id>
      <privacy>'.htmlspecialchars($message_data['privacy']).'</privacy>
      <body>'.htmlspecialchars($badword->filterString($message_data['body'])).'</body>
      <css_properties>'.htmlspecialchars($message_data['css_properties']).'</css_properties>
      <actor_nickname>'.htmlspecialchars($actor_nickname).'</actor_nickname>
      <has_attachments>'.htmlspecialchars($message_data['has_attachments']).'</has_attachments>
      '.$attachments_xml.'
    </message>';
        }
      }
      $chat_messages=implode('', $msg_array);
      if ($last_message_id>$session->_s_last_message_id) {
        // Update session
        $session->_s_updateSession($session->_s_id, true, true,
                                   null,
                                   null,
                                   null,
                                   null,
                                   null,
                                   null,
                                   $last_message_id);
      }
    }
    if (!empty($full_request)) {
      // Collect full data
      $categories=$category->getTree($current_user->id);
      $categories_xml='<categories>';
      unset($categories[0]); // Root element: not needed
      foreach ($categories as $category_id=>$category_data) {
        $categories_xml.='
    <category>
      <name>'.htmlspecialchars($category_data['name']).'</name>
      <description>'.htmlspecialchars($category_data['description']).'</description>';
        foreach ($category_data['rooms'] as $room_id=>$room_data) {
          $categories_xml.='
      <room>
        <id>'.htmlspecialchars($room_id).'</id>
        <background_image>'.htmlspecialchars($room_data['background_image']).'</background_image>
        <password_protected>'.htmlspecialchars($room_data['password_protected']).'</password_protected>
        <name>'.htmlspecialchars($room_data['name']).'</name>
        <description>'.htmlspecialchars($room_data['description']).'</description>
        <users_count>'.htmlspecialchars(count($room_data['users'])).'</users_count>
      </room>';
          if ($room_id==$session->_s_room_id) {
            $welcome_message=str_replace('[ROOM]', $room_data['name'], $l->g('welcome_to_chat_room'));
              $full_data.=
'<full_data>
    <welcome_message>'.htmlspecialchars($welcome_message).'</welcome_message>
    <category>
      <id>'.htmlspecialchars($category_id).'</id>
      <name>'.htmlspecialchars($category_data['name']).'</name>
      <description>'.htmlspecialchars($category_data['description']).'</description>
    </category>
    <room>
      <id>'.htmlspecialchars($session->_s_room_id).'</id>
      <name>'.htmlspecialchars($room_data['name']).'</name>
      <background_image>'.htmlspecialchars($room_data['background_image']).'</background_image>
      <description>'.htmlspecialchars($room_data['description']).'</description>
      <default_message_color>'.htmlspecialchars($room_data['default_message_color']).'</default_message_color>
    </room>
    <users>';
              foreach ($room_data['users'] as $user_id=>$user_data) {
                if ($user_data['online_status_message']=='') {
                  // Empty online status message
                  $user_data['online_status_message']=$l->g('online_status_'.$user_data['online_status']);
                }
                if ($user_data['global_muted_until']>date('Y-m-d H:i:s')) {
                  $global_muted='1';
                  $global_muted_by=$user_data['global_muted_by'];
                  $global_muted_by_username=$user_data['global_muted_by_username'];
                  $global_muted_until=PCPIN_Common::datetimeToTimestamp($user_data['global_muted_until'])+$current_user->time_zone_offset-date('Z');
                  $global_muted_reason=$user_data['global_muted_reason'];
                } elseif ($user_data['global_muted_permanently']=='y') {
                  $global_muted='1';
                  $global_muted_by=$user_data['global_muted_by'];
                  $global_muted_by_username=$user_data['global_muted_by_username'];
                  $global_muted_until='';
                  $global_muted_reason=$user_data['global_muted_reason'];
                } else {
                  $global_muted='';
                  $global_muted_by='';
                  $global_muted_by_username='';
                  $global_muted_until='';
                  $global_muted_reason='';
                }
                if ($current_user->is_admin==='y') {
                  $ip_address=$user_data['ip_address'];
                } else {
                  $ip_address='';
                }
                // Get first avatar
                if ($avatar->_db_getList('binaryfile_id', 'user_id = '.$user_id, 'id ASC')) {
                  // User has avatars
                  $user_data['avatar_bid']=$avatar->_db_list[0]['binaryfile_id'];
                } else {
                  // User has no avatars
                  if ($avatar->_db_getList('binaryfile_id', 'user_id = 0', 1)) {
                    $user_data['avatar_bid']=$avatar->_db_list[0]['binaryfile_id'];
                  } else {
                    $user_data['avatar_bid']=0;
                  }
                }
                $avatar->_db_freeList();

                $full_data.='
      <user>
        <id>'.htmlspecialchars($user_id).'</id>
        <nickname>'.htmlspecialchars($user_data['nickname']).'</nickname>
        <online_status>'.htmlspecialchars($user_data['online_status']).'</online_status>
        <online_status_message>'.htmlspecialchars($user_data['online_status_message']).'</online_status_message>
        <muted_locally>'.htmlspecialchars((false!==strpos(','.$current_user->muted_users.',', ','.$user_id.','))? '1' : '0').'</muted_locally>
        <global_muted>'.htmlspecialchars($global_muted).'</global_muted>
        <global_muted_until>'.htmlspecialchars($global_muted_until).'</global_muted_until>
        <ip_address>'.htmlspecialchars($ip_address).'</ip_address>
        <gender>'.htmlspecialchars($user_data['gender']).'</gender>
        <avatar_bid>'.htmlspecialchars($user_data['avatar_bid']).'</avatar_bid>
        <is_admin>'.htmlspecialchars($user_data['is_admin']).'</is_admin>
        <is_moderator>'.htmlspecialchars($user_data['is_moderator']).'</is_moderator>
      </user>';
            }
            $full_data.='
    </users>
  </full_data>';
          }
        }
        $categories_xml.='
    </category>';
      }
      $categories_xml.='
  </categories>';
    }
  }
  // Get new invitations
  $invitations=$invitation->getNewInvitations($current_user->id, true);
  foreach ($invitations as $invitation_data) {
    if (false===strpos(','.$current_user->muted_users.',', ','.$invitation_data['author_id'].',')) {
      $invitations_xml.='
  <invitation>
    <id>'.htmlspecialchars($invitation_data['id']).'</id>
  </invitation>';
    }
  }
  // "Message timestamp" preference
  if (!empty($pref_timestamp) && $current_user->show_message_time!='y') {
    $current_user->show_message_time='y';
    $current_user->_db_updateObj($current_user->id);
  } elseif (empty($pref_timestamp) && $current_user->show_message_time!='n') {
    $current_user->show_message_time='n';
    $current_user->_db_updateObj($current_user->id);
  }
  // "Message color" preference
  if (!empty($pref_message_color) && $current_user->outgoing_message_color!=$pref_message_color) {
    $current_user->outgoing_message_color=$pref_message_color;
    $current_user->_db_updateObj($current_user->id);
  }
  // Get display positions of displayable banners
  $banner_display_positions=$banner->checktRoomBanners();
  foreach ($banner_display_positions as $pos) {
    $banner_display_positions_xml.='    <banner_display_position>'.htmlspecialchars($pos).'</banner_display_position>'."\n";
  }
}

echo '<?xml version="1.0" encoding="UTF-8"?>
<pcpin_xml>
  <message>'.htmlspecialchars($message).'</message>
  <status>'.htmlspecialchars($status).'</status>
  <timestamp>'.htmlspecialchars(time()).'</timestamp>
  '.$full_data.'
  <chat_messages>'
  .$chat_messages.'
  </chat_messages>
  <invitations>
  '.$invitations_xml.'
  </invitations>
  '.$categories_xml.'
  <banner_display_positions>
'.rtrim($banner_display_positions_xml).'
  </banner_display_positions>
</pcpin_xml>';
die();
?>