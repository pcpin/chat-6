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
 * Class PCPIN_Message
 * Manage messages
 * @author Konstantin Reznichak <k.reznichak@pcpin.com>
 * @copyright Copyright &copy; 2007, Konstantin Reznichak
 */
class PCPIN_Message extends PCPIN_Session {

  /**
   * Message ID
   * @var   int
   */
  var $id=0;

  /**
   * Message type
   * Possible values are:
   *            +-------+----------------------------------------------------+---------------------------------+-------------------------+
   *            |  Type | Body format                                        | Description                     | Body example            |
   *            +-------+----------------------------------------------------+---------------------------------+-------------------------+
   *            |   101 | <user_id>                                          | User X entered the chat         | 25                      |
   *            +-------+----------------------------------------------------+---------------------------------+-------------------------+
   *            |   102 | <user_id>/<status>/<status_message>                | User X changed online status.   | 34/2/phone              |
   *            |       |                                                    | Online status codes are         |                         |
   *            |       |                                                    | described in session.class.php  |                         |
   *            +-------+----------------------------------------------------+---------------------------------+-------------------------+
   *            |   105 | <user_id>                                          | User X left the chat            | 12                      |
   *            +-------+----------------------------------------------------+---------------------------------+-------------------------+
   *            |   111 | <user_id>/<room_id>                                | User X entered room Y           | 8/24                    |
   *            +-------+----------------------------------------------------+---------------------------------+-------------------------+
   *            |   115 | <user_id>/<room_id>                                | User X left room Y              | 21/5                    |
   *            +-------+----------------------------------------------------+---------------------------------+-------------------------+
   *            |  1010 | <user_id>                                          | User data changed for user X    | 34                      |
   *            +-------+----------------------------------------------------+---------------------------------+-------------------------+
   *            |  1100 | -                                                  | Room structure changed          | -                       |
   *            +-------+----------------------------------------------------+---------------------------------+-------------------------+
   *            |  3001 | <user_id>/<message>                                | User X posted a message         | 15/Hello                |
   *            +-------+----------------------------------------------------+---------------------------------+-------------------------+
   *            |  4001 | <user_id>/<room_id>/<category>/<abusernick>/<body> | User with ID <user_id> posted   | 5/12/2/Foo/Abuse        |
   *            |       |                                                    | an abuse message from room with |                         |
   *            |       |                                                    | ID <room_id> of the category    |                         |
   *            |       |                                                    | <category> about abuser         |                         |
   *            |       |                                                    | <abusernick> with a description.|                         |
   *            +-------+----------------------------------------------------+---------------------------------+-------------------------+
   *            |  ================================================= CONTROL MESSAGES =================================================  |
   *            +-------+----------------------------------------------------+---------------------------------+-------------------------+
   *            | 10001 |                                                    | Clear chat messages area by     |                         |
   *            |       |                                                    | all users in room defined in    |                         |
   *            |       |                                                    | $this->target_room_id or all    |                         |
   *            |       |                                                    | rooms (if $this->target_room_id |                         |
   *            |       |                                                    | is empty)                       |                         |
   *            +-------+----------------------------------------------------+---------------------------------+-------------------------+
   *            | 10101 | <tgt_user_id>/<user_id>/<reason>                   | User with ID <tgt_user_id> was  | 9/12/Do not spam!       |
   *            |       |                                                    | kicked by user with ID          |                         |
   *            |       |                                                    | <user_id> with a reason. Kicked |                         |
   *            |       |                                                    | user will be also banned for a  |                         |
   *            |       |                                                    | time period (seconds) defined   |                         |
   *            |       |                                                    | in 'ban_kicked' config var.     |                         |
   *            +-------+----------------------------------------------------+---------------------------------+-------------------------+
   *            | 10105 | <tgt_user_id>/<user_id>/<minutes>/<reason>         | User with ID <tgt_user_id> was  | 45/1/15/Bye!            |
   *            |       |                                                    | banned by user with ID <user_id>|                         |
   *            |       |                                                    | for <minutes> minutes due to    |                         |
   *            |       |                                                    | <reason>.                       |                         |
   *            +-------+----------------------------------------------------+---------------------------------+-------------------------+
   *            | 10106 | <tgt_user_id>/<user_id>/<minutes>/<reason>         | User with ID <tgt_user_id> and  | 45/1/15/Bye!            |
   *            |       |                                                    | his IP address were banned by   |                         |
   *            |       |                                                    | user with ID <user_id> for      |                         |
   *            |       |                                                    | <minutes> minutes due to        |                         |
   *            |       |                                                    | <reason>.                       |                         |
   *            +-------+----------------------------------------------------+---------------------------------+-------------------------+
   *            | 10107 | <tgt_user_id>/<user_id>                            | User with ID <tgt_user_id> was  | 15/9                    |
   *            |       |                                                    | unbanned by user with ID        |                         |
   *            |       |                                                    | <user_id>                       |                         |
   *            +-------+----------------------------------------------------+---------------------------------+-------------------------+
   *            | 10110 | <tgt_user_id>/<user_id>/<minutes>/<reason>         | User with ID <tgt_user_id> was  | 45/5/4/Flooding         |
   *            |       |                                                    | global muted by user with ID    |                         |
   *            |       |                                                    | <user_id> for <minutes> minutes |                         |
   *            |       |                                                    | due to <reason>.                |                         |
   *            +-------+----------------------------------------------------+---------------------------------+-------------------------+
   *            | 10111 | <tgt_user_id>/<user_id>                            | User with ID <tgt_user_id> was  | 45/5                    |
   *            |       |                                                    | global unmuted by user with ID  |                         |
   *            |       |                                                    | <user_id>.                      |                         |
   *            +-------+----------------------------------------------------+---------------------------------+-------------------------+
   *            | 10200 | <user_id>                                          | Forse chat room userlist reload | 28                      |
   *            |       |                                                    | for user with ID <user_id>.     |                         |
   *            +-------+----------------------------------------------------+---------------------------------+-------------------------+
   * @var   int
   */
  var $type=0;

  /**
   * Flag: if has value "y", then the message will not be deleted from the database until target
   * user receives it. target_user_id *MUST* be set in this case.
   * @var   string
   */
  var $offline='n';

  /**
   * Message post date (MySQL DATETIME)
   * @var   string
   */
  var $date='';

  /**
   * Author ID. Empty value: message was created by system.
   * @var   int
   */
  var $author_id=0;

  /**
   * Author nickname. Empty value: message was created by system.
   * @var   string
   */
  var $author_nickname='';

  /**
   * Target room ID.
   * Empty value: message will be delivered to privileged users (empty $this->target_user_id) or to single user (non-empty $this->target_user_id)
   * will receive the message or the message is a global message.
   * @var   int
   */
  var $target_room_id=0;

  /**
   * ID of user the message targeted to.
   * Empty value: all users (depending on $this->target_room_id) will receive the message.
   * @var   int
   */
  var $target_user_id=0;

  /**
   * Message privacy level. Only makes sense with non-empty target_user_id.
   * Possible values:
   *    0 :   Message is visible to other users in a room ("said" message)
   *    1 :   Message is invisible to other users in a room ("whispered" message)
   *    2 :   Message is invisible to other users in a room and may be displayed in a separate window ("private" message)
   * @var   int
   */
  var $privacy=0;

  /**
   * Message body
   * @var   string
   */
  var $body='';

  /**
   * Message body CSS properties
   * @var   string
   */
  var $css_properties='';




  /**
   * Constructor
   * @param   object  &$sessionhandler  Session handler
   */
  function PCPIN_Message(&$sessionhandler) {
    // Init object
    $this->_s_init($sessionhandler, $this);
  }


  /**
   * Delete multiple messages from database
   * @param   array   $ids              Message IDs
   * @param   int     $posted_before    Delete all messages older than supplied value (UNIX_TIMESTAMP)
   * @param   int     $room_id          Optional. Room ID.
   */
  function deleteMessages($ids=null, $posted_before=0, $room_id=0) {
    if ($posted_before>0 && $this->_db_getList('id,target_room_id', 'date < '.date('Y-m-d H:i:s', $posted_before), 'offline = n')) {
      if (!is_array($ids)) {
        $ids=array();
      }
      foreach ($this->_db_list as $data) {
        if (empty($room_id) || $data['target_room_id']==$room_id) {
          $ids[]=$data['id'];
        }
      }
      $this->_db_freeList();
      $ids=array_unique($ids);
    }
    if (!empty($ids) && is_array($ids)) {
      // Delete messages
      foreach ($ids as $id) {
        $this->_db_deleteRow($id);
      }
      // Delete attachments
      _pcpin_loadClass('attachment'); $attachment=new PCPIN_Attachment($this);
      foreach ($ids as $id) {
        $attachment->deleteAttachment(0, $id);
      }
    }
  }


  /**
   * Add new message
   * @param   int       $type             Message type
   * @param   string    $offline          'y' for offline message
   * @param   int       $author_id        Author ID
   * @param   string    $author_nickname  Author nickname
   * @param   int       $target_room_id   Target room ID
   * @param   int       $target_user_id   Target user ID
   * @param   string    $body             Message body
   * @param   string    $date             Message date
   * @param   int       $privacy          Privacy level
   * @param   string    $css_properties   Message CSS attributes
   * @return  boolean TRUE on success or FALSE on error
   */
  function addMessage($type=0, $offline='n', $author_id=0, $author_nickname='', $target_room_id=0, $target_user_id=0, $body='', $date='', $privacy=0, $css_properties='') {
    $result=false;
    $body=trim($body);
    if (!empty($type)) {
      $this->id=0;
      $this->type=$type;
      $this->offline=$offline;
      $this->date=!empty($date)? $date : date('Y-m-d H:i:s');
      $this->author_id=$author_id;
      $this->author_nickname=$author_nickname;
      $this->target_room_id=$target_room_id;
      $this->target_user_id=$target_user_id;
      $this->body=$body;
      $this->privacy=$privacy;
      $this->css_properties=$this->parseCssAttributes($css_properties);
      if ($this->_db_insertObj()) {
        $result=true;
        $this->id=$this->_db_lastInsertID();
        if (!empty($this->_conf_all['logging_period'])) {
          // Store log
          _pcpin_loadClass('message_log'); $message_log=new PCPIN_Message_Log($this);
          $message_log->addLogRecord($this->id);
        }
      }
    }
    return $result;
  }


  /**
   * Get new online messages for user
   * @param   int   $user_id      User ID
   * @param   int   $type         Optional. Message type. If not empty, only messages of this type will be returned
   * @return  array
   */
  function getNewMessages($user_id, $type=0) {
    $messages=array();
    if (!empty($user_id)) {
      $query=$this->_db_makeQuery(1300, $user_id, $type);
      if ($result=$this->_db_query($query)) {
        _pcpin_loadClass('attachment'); $attachment=new PCPIN_Attachment($this);
        while ($data=$this->_db_fetch($result, MYSQL_ASSOC)) {
          if ($data['has_attachments']=='1') {
            // Get attachments
            $data['attachment']=$attachment->getAttachments($data['id']);
          }
          $messages[]=$data;
        }
        $this->_db_freeResult($result);
      }
    }
    return $messages;
  }


  /**
   * Get last messages for user
   * @param   int   $user_id      User ID
   * @param   int   $count        Messages count to return
   * @return  array
   */
  function getLastMessages($user_id, $count=0) {
    $messages=array();
    if (!empty($user_id) && !empty($count)) {
      $query=$this->_db_makeQuery(1300, $user_id, $count*1, 3001);
      if ($result=$this->_db_query($query)) {
        _pcpin_loadClass('attachment'); $attachment=new PCPIN_Attachment($this);
        while ($data=$this->_db_fetch($result, MYSQL_ASSOC)) {
          if ($data['has_attachments']=='1') {
            // Get attachments
            $data['attachment']=$attachment->getAttachments($data['id']);
          }
          $messages[]=$data;
        }
        $this->_db_freeResult($result);
      }
    }
    return $messages;
  }


  /**
   * Check and rewrite CSS attributes string
   * @param   string    $css_properties    CSS attributes string
   * @return  string
   */
  function parseCssAttributes($css_properties='') {
    $css_properties_out='';
    $css_properties=trim($css_properties);
    if ($css_properties!='') {
      $attrs=explode(';', $css_properties);
      foreach ($attrs as $attr) {
        $attr_parts=explode(':', $attr);
        if (isset($attr_parts[1]) && !isset($attr_parts[2])) {
          $attr_parts[0]=trim($attr_parts[0]);
          $attr_parts[1]=trim($attr_parts[1]);
          switch ($attr_parts[0]) {
            case 'color':
              if (strlen($attr_parts[1])==7) {
                $css_properties_out.=$attr_parts[0].':'.$attr_parts[1].';';
              }
            break;
            case 'font-weight':
              if ($attr_parts[1]=='bold' || $attr_parts[1]=='700' || $attr_parts[1]=='normal' || $attr_parts[1]=='500') {
                $css_properties_out.=$attr_parts[0].':'.$attr_parts[1].';';
              }
            break;
            case 'font-style':
              if ($attr_parts[1]=='italic' || $attr_parts[1]=='normal') {
                $css_properties_out.=$attr_parts[0].':'.$attr_parts[1].';';
              }
            break;
            case 'text-decoration':
              if ($attr_parts[1]=='underline' || $attr_parts[1]=='none') {
                $css_properties_out.=$attr_parts[0].':'.$attr_parts[1].';';
              }
            break;
            case 'font-family':
              if (false!==strpos('|'.$this->_conf_all['font_families'].'|', '|'.$attr_parts[1].'|')) {
                $css_properties_out.=$attr_parts[0].':"'.$attr_parts[1].'";';
              }
            break;
            case 'font-size':
              if (false!==strpos('|'.$this->_conf_all['font_sizes'].'|', '|'.substr($attr_parts[1], 0, -2).'|')) {
                $css_properties_out.=$attr_parts[0].':'.$attr_parts[1].';';
              }
            break;
          }
        }
      }
    }
    return $css_properties_out;
  }



}
?>