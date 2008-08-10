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
 * Class PCPIN_Message_Log
 * Manage message logs
 * @author Konstantin Reznichak <k.reznichak@pcpin.com>
 * @copyright Copyright &copy; 2007, Konstantin Reznichak
 */
class PCPIN_Message_Log extends PCPIN_Session {

  /**
   * Message ID
   * @var   int
   */
  var $message_id=0;

  /**
   * Message type (see PCPIN_Message::type)
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
   * ID of chat category the message was posted into
   * @var   int
   */
  var $category_id=0;

  /**
   * Name of chat category the message was posted into
   * @var   string
   */
  var $category_name='';

  /**
   * ID of chat room the message was posted into
   * @var   int
   */
  var $room_id=0;

  /**
   * Name of chat room the message was posted into
   * @var   string
   */
  var $room_name='';

  /**
   * Target category ID
   * @var   int
   */
  var $target_category_id=0;

  /**
   * Target category name
   * @var   string
   */
  var $target_category_name='';

  /**
   * Target room ID
   * @var   int
   */
  var $target_room_id=0;

  /**
   * Target room name
   * @var   string
   */
  var $target_room_name='';

  /**
   * Author ID. Empty value: message was created by system.
   * @var   int
   */
  var $author_id=0;

  /**
   * Author IP. Empty value: message was created by system.
   * @var   string
   */
  var $author_ip='';

  /**
   * Author nickname. Empty value: message was created by system.
   * @var   string
   */
  var $author_nickname='';

  /**
   * ID of user the message targeted to
   * @var   int
   */
  var $target_user_id=0;

  /**
   * Nickname of user the message targeted to
   * @var   string
   */
  var $target_user_nickname='';

  /**
   * Message privacy level (see PCPIN_Message::privacy)
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
  function PCPIN_Message_Log(&$sessionhandler) {
    // Init object
    $this->_s_init($sessionhandler, $this);
  }


  /**
   * Add new log record
   * @param   int       $message_id     Message ID
   * @return  boolean TRUE on success or FALSE on error
   */
  function addLogRecord($message_id=0) {
    $result=false;
    if (!empty($message_id)) {
      if ($result=$this->_db_query($this->_db_makeQuery(2030, $message_id))) {
        if ($data=$this->_db_fetch($result, MYSQL_ASSOC)) {
          if (!empty($data['id'])) {
            $this->message_id=$data['id'];
            $this->type=$data['type'];
            $this->offline=$data['offline'];
            $this->date=$data['date'];
            $this->category_id=$data['category_id'];
            $this->category_name=$data['category_name'];
            $this->room_id=$data['room_id'];
            $this->room_name=$data['room_name'];
            $this->target_category_id=$data['target_category_id'];
            $this->target_category_name=$data['target_category_name'];
            $this->target_room_id=$data['target_room_id'];
            $this->target_room_name=$data['target_room_name'];
            $this->author_id=$data['author_id'];
            $this->author_ip=$data['author_ip'];
            $this->author_nickname=$data['author_nickname'];
            $this->target_user_id=$data['target_user_id'];
            $this->target_user_nickname=$data['target_user_nickname'];
            $this->privacy=$data['privacy'];
            $this->body=$data['body'];
            $this->css_properties=$data['css_properties'];
            if ($this->_db_insertObj()) {
              $result=true;
            }
          }
        }
        $this->_db_freeResult($result);
      }
    }
    return $result;
  }


  /**
   * Delete old logs
   */
  function cleanUp() {
    if (!empty($this->_conf_all['logging_period'])) {
      if ($result=$this->_db_query($this->_db_makeQuery(2040, date('Y-m-d H:i:s', time()-86400*$this->_conf_all['logging_period'])))) {
        $this->_db_freeResult($result);
      }
    }
  }

}
?>