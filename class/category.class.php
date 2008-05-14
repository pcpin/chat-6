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
 * Class PCPIN_Category
 * Manage chat room categories
 * @author Konstantin Reznichak <k.reznichak@pcpin.com>
 * @copyright Copyright &copy; 2007, Konstantin Reznichak
 */
class PCPIN_Category extends PCPIN_Session {

  /**
   * Category ID
   * @var   int
   */
  var $id=0;

  /**
   * parent category ID. Top-level categories have empty parent ID.
   * @var   int
   */
  var $parent_id=0;

  /**
   * Category name
   * @var   string
   */
  var $name='';

  /**
   * Category description
   * @var   string
   */
  var $description='';

  /**
   * Flag: Can users create rooms in this category?
   * Values:
   *    "n": nobody
   *    "r": registered users only
   *    "g": guests and registered users (=everyone)
   * @var   string
   */
  var $creatable_rooms='';

  /**
   * Listing position
   * @var   int
   */
  var $listpos=0;




  /**
   * Constructor
   * @param   object  &$sessionhandler  Session handler
   */
  function PCPIN_Category(&$sessionhandler) {
    // Init object
    $this->_s_init($sessionhandler, $this);
  }


  /**
   * Get chat rooms list grouped in categories (tree) and sorted by name, including list of users in each room
   * This method creates an array with categories where element with KEY 0 is a category tree built from references to other elements (categories)
   * @param   int       $current_user_id    Optional. ID of user who calls this method
   * @param   int       $userlist_room_id   Optional. If empty: get userlist for all rooms, if not empty: get userlist for specified room only
   * @param   boolean   $recursion          Optional. Default TRUE. If TRUE, tree will be returned, otherwise: plain array.
   * @return  array
   */
  function getTree($current_user_id=0, $userlist_room_id=0, $recursion=true) {
    $categories=array();
    $categories[0]=array('id'=>0,
                         'name'=>'[ROOT]',
                         'description'=>'[ROOT]',
                         'parent_id'=>'-1',
                         'creatable_rooms'=>0,
                         'creatable_rooms_flag'=>'n',
                         'category'=>array(),
                         'room'=>array()
                         );
    if (!pcpin_ctype_digit($current_user_id)) {
      $current_user_id=0;
    }
    $query=$this->_db_makeQuery(1200, $current_user_id);
    if ($result=$this->_db_query($query)) {
      while ($data=$this->_db_fetch($result, MYSQL_ASSOC)) {
        if (empty($userlist_room_id) || $userlist_room_id==$data['room_id']) {
          $user_data=array('id'=>$data['user_id'],
                           'nickname'=>$data['nickname'],
                           'nickname_plain'=>$data['nickname_plain'],
                           'avatar_bid'=>$data['avatar_bid'],
                           'online_status'=>$data['online_status'],
                           'online_status_message'=>$data['online_status_message'],
                           'muted_locally'=>$data['muted_locally'],
                           'global_muted'=>$data['global_muted'],
                           'global_muted_by'=>$data['global_muted_by'],
                           'global_muted_by_username'=>$data['global_muted_by_username'],
                           'global_muted_until'=>$data['global_muted_until'],
                           'global_muted_permanently'=>$data['global_muted_permanently'],
                           'global_muted_reason'=>$data['global_muted_reason'],
                           'ip_address'=>$data['ip_address'],
                           'gender'=>$data['gender'],
                           'is_admin'=>$data['is_admin'],
                           'is_moderator'=>$data['is_moderator'],
                           'is_guest'=>$data['is_guest'],
                           );
        }
        $room_data=array('id'=>$data['room_id'],
                         'name'=>$data['room_name'],
                         'description'=>$data['room_description'],
                         'background_image'=>$data['background_image'],
                         'background_image_width'=>$data['background_image_width'],
                         'background_image_height'=>$data['background_image_height'],
                         'default_message_color'=>$data['default_message_color'],
                         'password_protected'=>$data['password_protected'],
                         'moderated_by_me'=>$data['moderated_by_me'],
                         'users_count'=>0,
                         'user'=>array(),
                         );
        $category_data=array('id'=>$data['category_id'],
                             'name'=>$data['category_name'],
                             'description'=>$data['category_description'],
                             'parent_id'=>$data['category_parent_id'],
                             'creatable_rooms'=>$data['creatable_rooms'],
                             'creatable_rooms_flag'=>$data['creatable_rooms_flag'],
                             'category'=>array(),
                             'room'=>array(),
                             );
        if (!isset($categories[$data['category_id']])) {
          $categories[$data['category_id']]=$category_data;
        }
        if(!is_null($data['room_id']) && !isset($categories[$data['category_id']]['room'][$data['room_id']])) {
          $categories[$data['category_id']]['room'][$data['room_id']]=$room_data;
        }
        if (!empty($data['user_id'])) {
          $categories[$data['category_id']]['room'][$data['room_id']]['users_count']++;
          if (!empty($user_data)) {
            $categories[$data['category_id']]['room'][$data['room_id']]['user'][$data['user_id']]=$user_data;
          }
        }
      }
      $this->_db_freeResult($result);
    }
    // Make recursion
    if ($recursion) {
      foreach ($categories as $category_id=>$category_data) {
        if (isset($categories[$category_data['parent_id']])) {
          // Category has a parent
          $categories[$category_data['parent_id']]['category'][$category_id]=&$categories[$category_id];
        }
      }
    } else {
      unset($categories[0]);
      $categories=array(0=>array($categories));
    }
    return $categories;
  }


  /**
   * Get category moderators
   * @param   int       $id       Room ID
   * @return  array
   */
  function getModerators($id=0) {
    $moderators=array();
    if (!empty($id)) {
      $query=$this->_db_makeQuery(1610, $id);
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
   * Create new category
   * @param   int       $parent_id        Parent category ID. Empty value means "Root" category.
   * @param   string    $name             Category name
   * @param   string    $description      Category description
   * @param   string    $creatable_rooms  Flag: Can users create rooms in this category? See $this->creatable_rooms
   * @return  boolean  TRUE on success or FALSE on error
   */
  function addCategory($parent_id=0, $name='', $description='', $creatable_rooms='n') {
    $this->id=0;
    $result=false;
    $this->parent_id=$parent_id;
    $this->name=$name;
    $this->description=$description;
    $this->creatable_rooms=$creatable_rooms;
    // Calculate listing position
    $this->listpos=0;
    if ($this->_db_getList('listpos', 'parent_id = '.$parent_id, 'listpos DESC', 1)) {
      $this->listpos=$this->_db_list[0]['listpos']+1;
    }
    if ($result=$this->_db_insertObj()) {
      $this->id=$this->_db_lastInsertID();
    }
    return $result;
  }


  /**
   * Update category data in object and/or database
   * @param   int       $id                   Category ID
   * @param   boolean   $obj                  If TRUE, then object properties will be updated
   * @param   boolean   $db                   If TRUE, then database table will be updated
   * @param   int       $parent_id            Parent category ID. NULL: do not change.
   * @param   string    $name                 Category name. NULL: do not change.
   * @param   string    $description          Category description. NULL: do not change.
   * @param   string    $creatable_rooms      Creatable rooms flag. NULL: do not change.
   * @param   int       $listpos              Listing position. NULL: do not change.
   * @return  boolean TRUE on success or FALSE on error
   */
  function updateCategory($id, $obj=false, $db=false,
                          $parent_id=null,
                          $name=null,
                          $description=null,
                          $creatable_rooms=null,
                          $listpos=null
                          ) {
    $result=false;
    if (!empty($id)) {
      if (true===$obj && $id==$this->id) {
        $result=true;
        if (!is_null($parent_id)) $this->parent_id=$parent_id;
        if (!is_null($name)) $this->name=$name;
        if (!is_null($description)) $this->description=$description;
        if (!is_null($creatable_rooms)) $this->creatable_rooms=$creatable_rooms;
        if (!is_null($listpos)) $this->listpos=$listpos;
      }
      if (true===$db) {
        $param=array();
        if (!is_null($parent_id)) $param['parent_id']=$parent_id;
        if (!is_null($name)) $param['name']=$name;
        if (!is_null($description)) $param['description']=$description;
        if (!is_null($creatable_rooms)) $param['creatable_rooms']=$creatable_rooms;
        if (!is_null($listpos)) $param['listpos']=$listpos;
        $result=$this->_db_updateRow($id, 'id', $param);
      }
    }
    return $result;
  }


  /**
   * Delete category and all contained rooms
   * @param   int   $id   Category ID
   * @return  boolean TRUE on success or FALSE on error
   */
  function deleteCategory($id=0) {
    $result=false;
    if (!empty($id) && $this->_db_getList('id = '.$id, 1)) {
      // Are there rooms in this category?
      _pcpin_loadClass('room'); $room=new PCPIN_Room($this);
      if ($room->_db_getList('id', 'category_id = '.$id)) {
        // Delete category rooms
        $rooms=$room->_db_list;
        $room->_db_freeList();
        foreach ($rooms as $room_data) {
          $room->deleteRoom($room_data['id']);
        }
      }
      // Update "moderated_categories" field by category moderators
      $moderators=$this->getModerators($id);
      if (!empty($moderators)) {
        _pcpin_loadClass('user'); $user=new PCPIN_User($this);
        foreach ($moderators as $data) {
          $user->_db_updateRow($data['id'], 'id', array('moderated_categories'=>trim(str_replace(','.$id.',', ',', ','.$data['moderated_categories'].','), ',')));
        }
      }
      // Delete category
      $this->_db_deleteRow($id);
    }
    return $result;
  }



}
?>