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
 * This file returns new messages for the user.
 * This file can be called from offside of chat room.
 */

_pcpin_loadClass('message'); $msg=new PCPIN_Message($session);
_pcpin_loadClass('room'); $room=new PCPIN_Room($session);

$abuses_xml=array();
if (!empty($current_user->id)) {
  $xmlwriter->setHeaderMessage('OK');
  $xmlwriter->setHeaderStatus(0);
  $messages=$msg->getNewMessages($current_user->id);
  $last_message_id=$session->_s_last_message_id;
  foreach ($messages as $message_data) {
    $last_message_id=($last_message_id<$message_data['id'])? $message_data['id'] : $last_message_id;
    if ($message_data['type']==4001) {
      $msg_parts=explode('/', $message_data['body'], 5);
      if ($room->_db_getList('name', 'id = '.$msg_parts[1], 1)) {
        $room_name=$room->_db_list[0]['name'];
        $room->_db_freeList();
      } else {
        $room_name='-';
      }

      switch ($msg_parts[2]) {

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
      $abuses_xml[]=array('id'=>$message_data['id'],
                          'date'=>$current_user->makeDate(PCPIN_Common::datetimeToTimestamp($message_data['date'])),
                          'author_id'=>$message_data['author_id'],
                          'author_nickname'=>$message_data['author_nickname'],
                          'category'=>$abuse_category,
                          'room_id'=>$msg_parts[1],
                          'room_name'=>$room_name,
                          'abuser_nickname'=>$msg_parts[3],
                          'description'=>$msg_parts[4]
                          );
    }
  }
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
$xmlwriter->setData(array('abuse'=>$abuses_xml));
?>