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
 * Class PCPIN_UserData
 * Manage additional user data
 * @author Konstantin Reznichak <k.reznichak@pcpin.com>
 * @copyright Copyright &copy; 2007, Konstantin Reznichak
 */
class PCPIN_UserData extends PCPIN_Session {

  /**
   * User ID
   * @var   int
   */
  var $user_id=0;

  /**
   * Field ID
   * @var   int
   */
  var $field_id=0;

  /**
   * Field value
   * @var   string
   */
  var $field_value='';



  /**
   * Constructor
   * @param   object  &$sessionhandler  Session handler
   */
  function PCPIN_UserData(&$sessionhandler) {
    // Init object
    $this->_s_init($sessionhandler, $this);
  }


  /**
   * Add new userdata record
   * @param   int     $user_id    User ID
   * @param   array   $fields     Associative array with field ID as KEY and field value as VAL
   */
  function addNewUserData($user_id, $fields=array()) {
    // Get available userdata fields
    _pcpin_loadClass('userdata_field'); $userdata_field=new PCPIN_UserData_Field($this);
    $userdata_field->_db_getList('id,default_value');
    $list=$userdata_field->_db_list;
    $userdata_field->_db_freeList();
    $fields_new=array();
    foreach ($list as $data) {
      if (isset($fields[$data['id']])) {
        $fields_new[$data['id']]=$fields[$data['id']];
      } else {
        $fields_new[$data['id']]=$data['default_value'];
      }
    }
    // Insert data rows
    $this->user_id=$user_id;
    foreach ($fields_new as $key=>$val) {
      $this->field_id=$key;
      $this->field_value=$val;
      $this->_db_insertObj();
    }
  }


  /**
   * Update userdata record
   * @param   int       $user_id          User ID
   */
  function deleteUserData($user_id) {
    if (!empty($user_id)) {
      $this->_db_deleteRow($user_id, 'user_id', true);
    }
  }


  /**
   * Get userdata fields for specified user
   * @param   int       $user_id          User ID
   * @return array
   */
  function getUserData($user_id) {
    $return=array();
    if (!empty($user_id)) {
      $query=$this->_db_makeQuery(2130, // 0
                                  $user_id, // 1
                                  $this->_s_language_id, // 2
                                  $this->_s_user_id // 3
                                  );
      if ($result=$this->_db_query($query)) {
        while ($data=$this->_db_fetch($result, MYSQL_ASSOC)) {
          $return[]=$data;
        }
        $this->_db_freeResult($result);
      }
    }
    return $return;
  }


}
?>