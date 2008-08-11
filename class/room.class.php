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
 * Class PCPIN_Room
 * Manage chat rooms
 * @author Konstantin Reznichak <k.reznichak@pcpin.com>
 * @copyright Copyright &copy; 2007, Konstantin Reznichak
 */
class PCPIN_Room extends PCPIN_Session {

  /**
   * Room ID
   * @var   int
   */
  var $id=0;

  /**
   * Room type ("p": Permanent room, "u": User room)
   * @var   string
   */
  var $type='';

  /**
   * Room creation date (MySQL DATETIME)
   * @var   string
   */
  var $date_created='';

  /**
   * Room name
   * @var   string
   */
  var $name='';

  /**
   * Room category ID
   * @var   int
   */
  var $category_id='';

  /**
   * Room description
   * @var   string
   */
  var $description='';

  /**
   * Online users count
   * @var   int
   */
  var $users_count=0;

  /**
   * Default message color
   * @var   string
   */
  var $default_message_color='';

  /**
   * Room password. If not empty, then room is password-protected
   * @var   string
   */
  var $password='';

  /**
   * Binaryfile ID of room background image
   * @var   int
   */
  var $background_image=0;

  /**
   * The time last user left the room (MySQL DATETIME)
   * @var   string
   */
  var $last_ping='';

  /**
   * Listing position
   * @var   int
   */
  var $listpos=0;



  /**
   * Constructor
   * @param   object  &$sessionhandler  Session handler
   */
  function PCPIN_Room(&$sessionhandler) {
    // Init object
    $this->_s_init($sessionhandler, $this);
  }


  /**
   * Put user into/out of a room
   * @param   int       $user_id          User ID
   * @param   int       $target_room_id   ID of room where to put user into
   * @param   boolean   $skip_msg         If TRUE, then system message 115 will be NOT inserted
   * @param   string    $stealth_mode     "Stealth" mode flag ("y"/"n")
   * @return  boolean TRUE on success or FALSE on error
   */
  function putUser($user_id=0, $target_room_id=0, $skip_msg=false, $stealth_mode='n') {
    $ok=false;
    _pcpin_loadClass('message'); $message=new PCPIN_Message($this);
    _pcpin_loadClass('session'); $session=new PCPIN_Session($this, '', true);
    // Get user's session
    if (!empty($user_id) && $session->_db_getList('_s_user_id = '.$user_id, 1)) {
      // Session exists
      if ($target_room_id!=$session->_db_list[0]['_s_room_id']) {
        if (!empty($session->_db_list[0]['_s_room_id'])) {
          // Put user out of a room
          $ok=true;
          if ($this->_db_getList('users_count', 'id = '.$session->_db_list[0]['_s_room_id'], 1)) {
            $this->updateRoom($session->_db_list[0]['_s_room_id'], false, true, null, null, null, null, $this->_db_list[0]['users_count']-1, null, null, null, date('Y-m-d H:i:s'));
          }
          if (true!==$skip_msg) {
            $message->addMessage(115, 'n', 0, '', $session->_db_list[0]['_s_room_id'], 0, $user_id.'/'.$session->_db_list[0]['_s_room_id']);
          }
        }
        if (!empty($target_room_id)) {
          // Put user into a room
          if ($this->_db_getList('users_count', 'id = '.$target_room_id, 1)) {
            $ok=true;
            $this->updateRoom($target_room_id, false, true, null, null, null, null, $this->_db_list[0]['users_count']+1, null, null, null, date('Y-m-d H:i:s'));
          } else {
            // Room does not exists
            $target_room_id=0;
          }
          if (true!==$skip_msg) {
            $message->addMessage(111, 'n', 0, '', $target_room_id, 0, $user_id.'/'.$target_room_id);
          }
        }
        // Update session
        $session->_s_updateSession($session->_db_list[0]['_s_id'], false, true, null, null, $target_room_id, null, null, null, null, (!empty($target_room_id)? date('Y-m-d H:i:s') : ''), null, null, null, $stealth_mode, null, null, '0000-00-00 00:00:00', '');
        if ($session->_db_list[0]['_s_online_status']!=1) {
          $session->_db_setObject($session->_db_list[0]);
          $session->_s_setOnlineStatus(1);
        }
      } else {
        $ok=true;
      }
      // Delete temporary message attachments
      _pcpin_loadClass('tmpdata'); $tmpdata=new PCPIN_TmpData($this);
      $tmpdata->deleteUserRecords($user_id, 3);
    }
    return $ok;
  }

  /**
   * Create new room
   * @param   int       $category_id            Parent category ID
   * @param   string    $type                   Room type
   * @param   string    $name                   Room name
   * @param   string    $description            Room description
   * @param   string    $default_message_color  Default message color
   * @param   string    $password               Password. If not empty, then room will be password-protected.
   * @param   int       $background_image       Binaryfile-ID of room background image
   * @return  boolean TRUE on success or FALSE on error
   */
  function createRoom($category_id=0, $type='', $name='', $description='', $default_message_color='', $password='', $background_image=0) {
    $result=false;
    if (!empty($category_id) && $name!='') {
      $this->id=0;
      $this->type=$type;
      $this->date_created=date('Y-m-d H:i:s');
      $this->name=$name;
      $this->category_id=$category_id;
      $this->description=$description;
      $this->users_count=0;
      $this->default_message_color=$default_message_color;
      $this->password=$password!=''? md5($password) : '';
      $this->background_image=$background_image;
      $this->last_ping=date('Y-m-d H:i:s');
      $this->listpos=0;
      // Calculate listing position
      if ($this->_db_getList('listpos', 'category_id = '.$category_id, 'listpos DESC', 1)) {
        $this->listpos=$this->_db_list[0]['listpos']+1;
      }
      if ($result=$this->_db_insertObj()) {
        $this->id=$this->_db_lastInsertID();
      }
      // Update "moderated_rooms" field by category moderators
      _pcpin_loadClass('category'); $category=new PCPIN_Category($this);
      $moderators=$category->getModerators($category_id);
      if (!empty($moderators)) {
        _pcpin_loadClass('user'); $user=new PCPIN_User($this);
        foreach ($moderators as $data) {
          $rooms=array_unique(explode(',', trim($data['moderated_rooms'].','.$this->id, ',')));
          sort($rooms);
          $user->_db_updateRow($data['id'], 'id', array('moderated_rooms'=>implode(',', $rooms)));
        }
      }
    }
    return $result;
  }


  /**
   * Delete room
   * @param   int       $id       Room ID
   * @return  boolean TRUE on success or FALSE on error
   */
  function deleteRoom($id=0) {
    $result=false;
    if (!empty($id) && $this->_db_getList('background_image', 'id = '.$id, 1)) {
      if ($result=$this->_db_deleteRow($id)) {
        _pcpin_loadClass('binaryfile'); $binaryfile=new PCPIN_BinaryFile($this);
        $binaryfile->deleteBinaryFile($this->_db_list[0]['background_image']);
      }
      $this->_db_freeList();
      // Update "moderated_rooms" field by category and room moderators
      $moderators=$this->getModerators($id);
      if (!empty($moderators)) {
        _pcpin_loadClass('user'); $user=new PCPIN_User($this);
        foreach ($moderators as $data) {
          $user->_db_updateRow($data['id'], 'id', array('moderated_rooms'=>trim(str_replace(','.$id.',', ',', ','.$data['moderated_rooms'].','), ',')));
        }
      }
    }
    return $result;
  }


  /**
   * Get room moderators
   * @param   int       $id       Room ID
   * @return  array
   */
  function getModerators($id=0) {
    $moderators=array();
    if (!empty($id)) {
      $query=$this->_db_makeQuery(1600, $id);
      if ($result=$this->_db_query($query)) {
        while ($data=$this->_db_fetch($result, MYSQL_ASSOC)) {
          $moderators[]=$data;
        }
        $this->_db_freeResult($result);
      }
    }
    return $moderators;
  }


  /**
   * Update room data in object and/or database
   * @param   int       $id                       Room ID
   * @param   boolean   $obj                      If TRUE, then object properties will be updated
   * @param   boolean   $db                       If TRUE, then database table will be updated
   * @param   string    $type                     Room type. NULL: do not change.
   * @param   string    $name                     Category name. NULL: do not change.
   * @param   int       $category_id              Category ID. NULL: do not change.
   * @param   string    $description              Category description. NULL: do not change.
   * @param   int       $users_count              Users count. NULL: do not change.
   * @param   string    $default_message_color    Default message color. NULL: do not change.
   * @param   string    $password                 Room password. NULL: do not change.
   * @param   int       $background_image         Binaryfile ID of room background image. NULL: do not change.
   * @param   string    $last_ping                Last ping. NULL: do not change.
   * @param   int       $listpos                  Listing position. NULL: do not change.
   * @return  boolean TRUE on success or FALSE on error
   */
  function updateRoom($id, $obj=false, $db=false,
                      $type=null,
                      $name=null,
                      $category_id=null,
                      $description=null,
                      $users_count=null,
                      $default_message_color=null,
                      $password=null,
                      $background_image=null,
                      $last_ping=null,
                      $listpos=null
                      ) {
    $result=false;
    if (!empty($id)) {
      if (true===$obj && $id==$this->id) {
        $result=true;
        if (!is_null($type)) $this->type=$type;
        if (!is_null($name)) $this->name=$name;
        if (!is_null($category_id)) $this->category_id=$category_id;
        if (!is_null($description)) $this->description=$description;
        if (!is_null($users_count)) $this->users_count=$users_count;
        if (!is_null($default_message_color)) $this->default_message_color=$default_message_color;
        if (!is_null($password)) $this->password=$password;
        if (!is_null($background_image)) $this->background_image=$background_image;
        if (!is_null($last_ping)) $this->last_ping=$last_ping;
        if (!is_null($listpos)) $this->listpos=$listpos;
      }
      if (true===$db) {
        $param=array();
        if (!is_null($type)) $param['type']=$type;
        if (!is_null($name)) $param['name']=$name;
        if (!is_null($category_id)) $param['category_id']=$category_id;
        if (!is_null($description)) $param['description']=$description;
        if (!is_null($users_count)) $param['users_count']=$users_count;
        if (!is_null($default_message_color)) $param['default_message_color']=$default_message_color;
        if (!is_null($password)) $param['password']=$password;
        if (!is_null($background_image)) $param['background_image']=$background_image;
        if (!is_null($last_ping)) $param['last_ping']=$last_ping;
        if (!is_null($listpos)) $param['listpos']=$listpos;
        $result=$this->_db_updateRow($id, 'id', $param);
      }
    }
    return $result;
  }


}
?>