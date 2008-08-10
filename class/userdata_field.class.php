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
 * Class PCPIN_UserData_Field
 * Manage user data fields
 * @author Konstantin Reznichak <k.reznichak@pcpin.com>
 * @copyright Copyright &copy; 2007, Konstantin Reznichak
 */
class PCPIN_UserData_Field extends PCPIN_Session {

  /**
   * ID
   * @var int
   */
  var $id=0;

  /**
   * Field name
   * @var string
   */
  var $name='';

  /**
   * Default value
   * @var string
   */
  var $default_value='';

  /**
   * Flag. If "y": the field is custom (created in Admin area), if "n": native field
   * @var string
   */
  var $custom='';

  /**
   * Type of the field.
   * Possible values:
   *  +-------------+---------------------------------------------------------------------------------+
   *  | string      | Any characters, can not contain line breaks, max. length: 255 characters        |
   *  +-------------+---------------------------------------------------------------------------------+
   *  | text        | Any characters, max. length: 65535 characters                                   |
   *  +-------------+---------------------------------------------------------------------------------+
   *  | url         | URL                                                                             |
   *  +-------------+---------------------------------------------------------------------------------+
   *  | email       | Email address                                                                   |
   *  +-------------+---------------------------------------------------------------------------------+
   *  | choice      | Simple choice, value must be one of listed in $this->choices                    |
   *  +-------------+---------------------------------------------------------------------------------+
   *  | multichoice | Multiple choice, value can contain one or multiple variants from $this->choices |
   *  +-------------+---------------------------------------------------------------------------------+
   * @var string
   */
  var $type='';

  /**
   * Choices for use with 'choice' or 'multichoice' field types.
   * Values separated by ASCII(0A) sequence
   * @var string
   */
  var $choices='';

  /**
   * Field visibility
   * Possible values:
   *  +------------+--------------------------------------------------------+
   *  | public     | Field is visible to everyone                           |
   *  +------------+--------------------------------------------------------+
   *  | registered | Field is visible to registered users only              |
   *  +------------+--------------------------------------------------------+
   *  | moderator  | Field is visible to moderators and administrators only |
   *  +------------+--------------------------------------------------------+
   *  | admin      | Field is visible to administrators only                |
   *  +------------+--------------------------------------------------------+
   * @var string
   */
  var $visibility='';

  /**
   * Who can change value of this field?
   * Possible values:
   *  +-------+----------------------------------+
   *  | user  | Profile owner and administrators |
   *  +-------+----------------------------------+
   *  | admin | Administrators only              |
   *  +-------+----------------------------------+
   * @var string
   */
  var $writeable='';

  /**
   * In which order display the field. Lower value represents higher display position.
   * @var int
   */
  var $order=0;

  /**
   * Flag, if "y": the field is disabled and wont be displayed
   * @var string
   */
  var $disabled='';




  /**
   * Constructor
   * @param   object  &$sessionhandler  Session handler
   */
  function PCPIN_UserData_Field(&$sessionhandler) {
    // Init object
    $this->_s_init($sessionhandler, $this);
  }


  /**
   * Update userdata object and/or database row
   * @param   int       $field_id         Field ID
   * @param   boolean   $obj              If TRUE, then object properties will be updated
   * @param   boolean   $db               If TRUE, then database table will be updated
   * @param   string    $name
   * @param   string    $default_value
   * @param   string    $custom
   * @param   string    $type
   * @param   string    $choices
   * @param   string    $visibility
   * @param   string    $writeable
   * @param   string    $order
   * @param   string    $disabled
   * @return  boolean TRUE on success or FALSE on error
   */
  function updateFieldData($field_id, $obj=false, $db=false,
                           $name=null,
                           $default_value=null,
                           $custom=null,
                           $type=null,
                           $choices=null,
                           $visibility=null,
                           $writeable=null,
                           $order=null,
                           $disabled=null
                           ) {
    $result=false;
    if (!empty($field_id)) {
      if (!is_null($choices)) {
        $choices=str_replace("\r", "\n", $choices);
        do {
          $choices=str_replace("\n\n", "\n", $choices);
        } while(false!==strpos($choices, "\n\n"));
        $choices=trim($choices);
      }
      if (true===$obj) {
        $result=true;
        if (!is_null($name)) $this->name=$name;
        if (!is_null($default_value)) $this->default_value=$default_value;
        if (!is_null($custom)) $this->custom=$custom;
        if (!is_null($type)) $this->type=$type;
        if (!is_null($choices)) $this->choices=$choices;
        if (!is_null($visibility)) $this->visibility=$visibility;
        if (!is_null($writeable)) $this->writeable=$writeable;
        if (!is_null($order)) $this->order=$order;
        if (!is_null($disabled)) $this->disabled=$disabled;
      }
      if (true===$db) {
        $param=array();
        if (!is_null($name)) $param['name']=$name;
        if (!is_null($default_value)) $param['default_value']=$default_value;
        if (!is_null($custom)) $param['custom']=$custom;
        if (!is_null($type)) $param['type']=$type;
        if (!is_null($choices)) $param['choices']=$choices;
        if (!is_null($visibility)) $param['visibility']=$visibility;
        if (!is_null($writeable)) $param['writeable']=$writeable;
        if (!is_null($order)) $param['order']=$order;
        if (!is_null($disabled)) $param['disabled']=$disabled;
        $result=$this->_db_updateRow($user_id, 'user_id', $param);
      }
    }
    return $result;
  }


  /**
   * Add new field
   * @param   string    $name
   * @param   string    $default_value
   * @param   string    $type
   * @param   string    $choices
   * @param   string    $visibility
   * @param   string    $writeable
   * @param   string    $disabled
   * @return int ID of the new field or 0 on error
   */
  function addNewField(
                       $name,
                       $default_value='',
                       $type='string',
                       $choices='',
                       $visibility='public',
                       $writeable='user',
                       $disabled='n'
                       ) {
    $return=0;
    $name=trim($name);
    $default_value=trim($default_value);
    $choices=trim($choices);
    $choices=str_replace("\r", "\n", $choices);
    do {
      $choices=str_replace("\n\n", "\n", $choices);
    } while(false!==strpos($choices, "\n\n"));
    $choices=trim($choices);
    if ($name!='' && $type!='') {
      $this->id=0;
      $this->name=$name;
      $this->default_value=$default_value;
      $this->custom='y';
      $this->type=$type;
      $this->choices=$choices;
      $this->visibility=$visibility;
      $this->writeable=$writeable;
      $this->order=0;
      $this->disabled=$disabled;
      // Get highest order
      if ($this->_db_getList('order', 'order DESC', 1)) {
        $this->order=$this->_db_list[0]['order']+1;
        $this->_db_freeList();
      }
      if ($this->_db_insertObj()) {
        $this->id=$this->_db_lastInsertID();
        $return=$this->id;
      }
    }
    return $return;
  }


  /**
   * Delete existing field
   * @param     int   $field_id     Field ID
   */
  function deleteField($field_id) {
    if (!empty($field_id) && $this->_db_getList('id', 'id =# '.$field_id, 'custom = y', 1)) {
      $this->_db_freeList();
      if ($this->_db_deleteRow($field_id)) {
        // Delete field from all userdata records
        _pcpin_loadClass('userdata'); $userdata=new PCPIN_UserData($this);
        $userdata->_db_deleteRow($field_id, 'field_id', true);
      }
    }
  }


  /**
   * Get userdata fields list
   * @param 
   * @return  array
   */
  function getFields() {
    $return=array();
    $query=$this->_db_makeQuery(2140, $this->_s_language_id);
    if ($result=$this->_db_query($query)) {
      while ($data=$this->_db_fetch($result, MYSQL_ASSOC)) {
        $return[]=$data;
      }
      $this->_db_freeResult($result);
    }
    return $return;
  }


  /**
   * Update userdata field
   * @param   int       $field_id               Field ID
   * @param   string    $name                   Field name. NULL: do not change.
   * @param   string    $default_value          Default value. NULL: do not change.
   * @param   string    $type                   Field type. NULL: do not change.
   * @param   string    $choices                Choices. NULL: do not change.
   * @param   string    $visibility             Visibility. NULL: do not change.
   * @param   string    $disabled               Disabled. NULL: do not change.
   * @return  boolean TRUE on success or FALSE on error
   */
  function updateField($field_id,
                       $name=null,
                       $default_value=null,
                       $type=null,
                       $choices=null,
                       $visibility=null,
                       $writeable=null,
                       $disabled=null
                       ) {
    $result=false;
    if (!empty($field_id) && $this->_db_getList('custom', 'id =# '.$field_id, 1)) {
      $custom=$this->_db_list[0]['custom']=='y';
      $this->_db_freeList();
      $param=array();
      if ($custom && !is_null($name)) $param['name']=$name;
      if ($custom && !is_null($type)) $param['type']=$type;
      if ($custom && !is_null($choices)) $param['choices']=$choices;
      if ($custom && !is_null($default_value)) $param['default_value']=$default_value;
      if (!is_null($visibility)) $param['visibility']=$visibility;
      if (!is_null($writeable)) $param['writeable']=$writeable;
      if (!is_null($disabled)) $param['disabled']=$disabled;
      $result=$this->_db_updateRow($field_id, 'id', $param);
    }
    return $result;
  }


  /**
   * Increase/decrease userdata field display order
   * @param   int       $field_id         Field ID
   * @param   boolean   $increase         If TRUE: increase order, if FASLE: decrease order
   */
  function updateFieldOrder($field_id, $order) {
    if (!empty($field_id) && $this->_db_getList('order', 'id =# '.$field_id, 1)) {
      $current_order=$this->_db_list[0]['order'];
      $this->_db_freeList();
      if (!$order && $this->_db_getList('id,order', 'id !=# '.$field_id, 'order < '.$current_order, 'order DESC', 1)) {
        // Increase order
        $this->_db_updateRow($field_id, 'id', array('order'=>$this->_db_list[0]['order']));
        $this->_db_updateRow($this->_db_list[0]['id'], 'id', array('order'=>$current_order));
        $this->_db_freeList();
      } elseif ($order && $this->_db_getList('id,order', 'id !=# '.$field_id, 'order > '.$current_order, 'order ASC', 1)) {
        // Decrease order
        $this->_db_updateRow($field_id, 'id', array('order'=>$this->_db_list[0]['order']));
        $this->_db_updateRow($this->_db_list[0]['id'], 'id', array('order'=>$current_order));
        $this->_db_freeList();
      }
    }
  }


}
?>