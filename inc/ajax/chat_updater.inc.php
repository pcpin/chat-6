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
 * @param   boolean   $pref_allow_sounds    Optional. Current state of client's "Allow sounds" preference
 * @param   boolean   $pref_message_color   Optional. Current message color
 */

_pcpin_loadClass('message'); $msg=new PCPIN_Message($session);
_pcpin_loadClass('category'); $category=new PCPIN_Category($session);
_pcpin_loadClass('nickname'); $nickname=new PCPIN_Nickname($session);
_pcpin_loadClass('room'); $room=new PCPIN_Room($session);
_pcpin_loadClass('invitation'); $invitation=new PCPIN_Invitation($session);
_pcpin_loadClass('avatar'); $avatar=new PCPIN_Avatar($session);
_pcpin_loadClass('badword'); $badword=new PCPIN_Badword($session);
_pcpin_loadClass('banner'); $banner=new PCPIN_Banner($session);

$default_nicknames=array(); // cached nicknames

$last_sent_message_time=$session->_s_last_sent_message_time<='0000-00-00 00:00:00'? 0 : PCPIN_Common::datetimeToTimestamp($session->_s_last_sent_message_time);
$last_sent_message_hash=$session->_s_last_sent_message_hash;
$last_sent_message_repeats_count=$session->_s_last_sent_message_repeats_count;
$last_message_id=$session->_s_last_message_id;

if (!isset($room_id) || !is_scalar($room_id)) $room_id=0;

if (!empty($first_request)) {
  $full_request=1;
}

$xml_data=array();

if (!empty($room_id) && !empty($current_user->id)) {
  if (empty($session->_s_room_id)) {
    // User is not in chat room
    $xmlwriter->setHeaderMessage('User is not in chat room');
    $xmlwriter->setHeaderStatus(100);
  } elseif ($session->_s_room_id != $room_id) {
    // User is in other room
    $xmlwriter->setHeaderMessage('User is in other room');
    $xmlwriter->setHeaderStatus(200);
  } elseif (!$room->_db_getList('id', 'id = '.$room_id, 1)) {
    // Room does not exists (anymore)
    $xmlwriter->setHeaderMessage('Room does not exists');
    $xmlwriter->setHeaderStatus(300);
  } else {
    $xmlwriter->setHeaderStatus(0);
    $xmlwriter->setHeaderMessage('OK');
    if (!empty($first_request)) {
      $xml_data['welcome_message']=str_replace('[ROOM]', $current_room_name, $l->g('welcome_to_chat_room'));
    }
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
                    && false===strpos(','.$current_user->moderated_rooms.',', ','.$target_room_id.',') // Mod can sent msgs to any room that he moderates
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
                  if ($message_ok) {
                    // Delete all messages
                    $msg->deleteMessages(null, time());
                  }
                } else {
                  // "/clear <room>"
                  $message_ok=$current_user->is_admin==='y' || false!==strpos(','.$current_user->moderated_rooms.',', ','.$target_room_id.',');
                  if ($message_ok) {
                    // Delete all messages
                    $msg->deleteMessages(null, time(), $target_room_id);
                  }
                }
              break;

              default:
                // Unknown command
                $message_ok=false;
              break;

            }

            if (true===$message_ok && (empty($session->_conf_all['flood_protection_message_delay']) || $session->_conf_all['flood_protection_message_delay']<=time()-$last_sent_message_time)) {
              // Check flooding
              if ($last_sent_message_hash==md5($body)) {
                $last_sent_message_repeats_count++;
              } else {
                $last_sent_message_hash=md5($body);
                $last_sent_message_repeats_count=0;
              }
              if (!empty($session->_conf_all['flood_protection_max_messages']) && !empty($session->_conf_all['flood_protection_mute_time']) && $current_user->is_admin!=='y' && $last_sent_message_repeats_count>=$session->_conf_all['flood_protection_max_messages']) {
                // Message was flooded. Mute author.
                $current_user->globalMuteUnmute($current_user->id, 1, $session->_conf_all['flood_protection_mute_time']/60, $l->g('flooding'));
                $msg->addMessage(10110, 'n', 0, $l->g('server'), $session->_s_room_id, 0, $current_user->id.'/0/'.($session->_conf_all['flood_protection_mute_time']/60).'/'.$l->g('flooding'), date('Y-m-d H:i:s'), 0, '');
                $last_sent_message_repeats_count=0;
                $last_sent_message_hash='';
                break; // Ignore further messages
              } else {
                // Add message to database
                $last_sent_message_time=time();
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
      $msg_array=array();
      foreach ($messages as $message_data) {
        $last_message_id=($last_message_id<$message_data['id'])? $message_data['id'] : $last_message_id;
        $msg_parts=explode('/', $message_data['body']);

        if ($message_data['type']=='4001') {
          // Abuse
          if ($message_data['target_user_id']==$session->_s_user_id) {
            $abuse_msg_parts=explode('/', $message_data['body'], 5);
            if ($room->_db_getList('name', 'id = '.$abuse_msg_parts[1], 1)) {
              $room_name=$room->_db_list[0]['name'];
              $room->_db_freeList();
            } else {
              $room_name='-';
            }
            switch ($abuse_msg_parts[2]) {
              case '1':
                $abuse_category=$l->g('spam');
              break;
              case '2':
                $abuse_category=$l->g('insult');
              break;
              case '3':
                $abuse_category=$l->g('adult_content');
              break;
              case '4':
                $abuse_category=$l->g('illegal_content');
              break;
              case '5':
                $abuse_category=$l->g('harassment');
              break;
              case '6':
                $abuse_category=$l->g('fraud');
              break;
              default:
                $abuse_category=$l->g('other');
              break;
            }
            if (!isset($xml_data['abuses'])) {
              $xml_data['abuses']=array('abuse'=>array());
            }
            $xml_data['abuses']['abuse'][]=array('id'=>$message_data['id'],
                                                 'date'=>$current_user->makeDate($message_data['date']),
                                                 'author_id'=>$message_data['author_id'],
                                                 'author_nickname'=>$message_data['author_nickname'],
                                                 'category'=>$abuse_category,
                                                 'room_id'=>$abuse_msg_parts[1],
                                                 'room_name'=>$room_name,
                                                 'abuser_nickname'=>$abuse_msg_parts[3],
                                                 'description'=>$abuse_msg_parts[4],
                                                 );
          }
          continue;
        }

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
          $message_data['body']=$badword->filterString($message_data['body']);
          $message_data['actor_nickname']=$actor_nickname;
          $msg_array[]=$message_data;
        }
      }
      if (!empty($msg_array)) {
        $xml_data['chat_message']=$msg_array;
      }
      unset($msg_array);
    }
    if (!empty($full_request)) {
      // Collect full data
      $xml_data['category']=$category->getTree($current_user->id, $session->_s_room_id);
      unset($xml_data['category'][0]); // Root element not needed; get flat data
    }
  }
  // Get new invitations
  $invitations=$invitation->getNewInvitations($current_user->id, true);
  if (!empty($invitations)) {
    $xml_data['invitation']=$invitations;
  }
  unset($invitations);
  // "Message timestamp" preference
  if (!empty($pref_timestamp) && $current_user->show_message_time!='y') {
    $current_user->show_message_time='y';
    $current_user->_db_updateObj($current_user->id);
  } elseif (empty($pref_timestamp) && $current_user->show_message_time!='n') {
    $current_user->show_message_time='n';
    $current_user->_db_updateObj($current_user->id);
  }
  // "Allow sounds" preference
  if (!empty($pref_allow_sounds) && $current_user->allow_sounds!='y') {
    $current_user->allow_sounds='y';
    $current_user->_db_updateObj($current_user->id);
  } elseif (empty($pref_allow_sounds) && $current_user->allow_sounds!='n') {
    $current_user->allow_sounds='n';
    $current_user->_db_updateObj($current_user->id);
  }
  // "Message color" preference
  if (!empty($pref_message_color) && $current_user->outgoing_message_color!=$pref_message_color) {
    $current_user->outgoing_message_color=$pref_message_color;
    $current_user->_db_updateObj($current_user->id);
  }
  // Get display positions of displayable banners
  $banner_display_positions=$banner->checktRoomBanners();
  if (!empty($banner_display_positions)) {
    $xml_data['banner_display_position']=$banner_display_positions;
  }
  unset($banner_display_positions);

  if (   $last_message_id>$session->_s_last_message_id
      || $last_sent_message_time>PCPIN_Common::datetimeToTimestamp($session->_s_last_sent_message_time)
      || $last_sent_message_hash!=$session->_s_last_sent_message_hash
      || $last_sent_message_repeats_count!=$session->_s_last_sent_message_repeats_count
      ) {
    // Update session
    $session->_s_updateSession($session->_s_id, true, true,
                               null,
                               null,
                               null,
                               null,
                               null,
                               null,
                               $last_message_id>$session->_s_last_message_id? $last_message_id : null,
                               null,
                               null,
                               null,
                               null,
                               null,
                               null,
                               $last_sent_message_time>PCPIN_Common::datetimeToTimestamp($session->_s_last_sent_message_time)? date('Y-m-d H:i:s', $last_sent_message_time) : null,
                               $last_sent_message_hash!=$session->_s_last_sent_message_hash? $last_sent_message_hash : null,
                               $last_sent_message_repeats_count!=$session->_s_last_sent_message_repeats_count? $last_sent_message_repeats_count : null
                               );
  }
}

$xmlwriter->setData($xml_data);
?>