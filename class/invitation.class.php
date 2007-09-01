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
 * Class PCPIN_Invitation
 * Manage invitations
 * @author Konstantin Reznichak <k.reznichak@pcpin.com>
 * @copyright Copyright &copy; 2007, Konstantin Reznichak
 */
class PCPIN_Invitation extends PCPIN_Session {

  /**
   * Invitation ID
   * @var   int
   */
  var $id=0;

  /**
   * ID of user who sent an invitation
   * @var   int
   */
  var $author_id=0;

  /**
   * Nickname of user who sent an invitation
   * @var   string
   */
  var $author_nickname='';

  /**
   * ID of user that was invited
   * @var   int
   */
  var $target_user_id=0;

  /**
   * ID of room the user was invited to join
   * @var   int
   */
  var $room_id=0;

  /**
   * Name of room the user was invited to join
   * @var   string
   */
  var $room_name='';




  /**
   * Constructor
   * @param   object  &$sessionhandler  Session handler
   */
  function PCPIN_Invitation(&$sessionhandler) {
    // Init object
    $this->_s_init($sessionhandler, $this);
  }


  /**
   * Add new invitation
   * @param   int       $author_id        Author ID
   * @param   int       $target_user_id   Target user ID
   * @param   int       $room_id          Room ID
   * @return  boolean TRUE on success or FALSE on error
   */
  function addInvitation($author_id=0, $target_user_id=0, $room_id=0) {
    $result=false;
    if (!empty($author_id) && !empty($target_user_id) && !empty($room_id)) {
      $query=$this->_db_makeQuery(1500, $author_id, $target_user_id, $room_id);
      if ($result=$this->_db_query($query)) {
        $this->_db_freeResult($result);
        $result=true;
      }
    }
    return $result;
  }


  /**
   * Get new invitations for user
   * @param   int       $user_id        User ID
   * @param   boolean   $check_only     If TRUE, then invitations will be not deleted from database.
   * @param   int       $id             Optional. Invitation ID. If not empty, then only one invitation with $id will be returned.
   * @return  array
   */
  function getNewInvitations($user_id=0, $check_only=false, $id=0) {
    $invitations=array();
    if (!empty($user_id)) {
      if (!empty($id)) {
        $this->_db_getList('id = '.$id, 'target_user_id = '.$user_id);
      } else {
        $this->_db_getList('target_user_id = '.$user_id, 'id ASC');
      }
      if (!empty($this->_db_list)) {
        foreach ($this->_db_list as $data) {
          $invitations[]=$data;
          if (false===$check_only) {
            $this->_db_deleteRow($data['id']);
          }
        }
        $this->_db_freeList();
      }
    }
    return $invitations;
  }


  /**
   * Delete all invitations sent to or by the user
   * @param   int   $user_id      User ID
   * @return  boolean   TRUE on success or FALSE on error
   */
  function deleteUserInvitations($user_id=0) {
    $result=false;
    if (!empty($user_id)) {
      // Invitations sent by user
      $this->_db_deleteRowMultiCond(array('author_id'=>$user_id), true);
      // Invitations sent to user
      $this->_db_deleteRowMultiCond(array('target_user_id'=>$user_id), true);
    }
    return $result;
  }


}
?>