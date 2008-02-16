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
 * Class PCPIN_TmpData
 * Manage temporary data
 * @author Konstantin Reznichak <k.reznichak@pcpin.com>
 * @copyright Copyright &copy; 2007, Konstantin Reznichak
 */
class PCPIN_TmpData extends PCPIN_Session {

  /**
   * ID
   * @var   int
   */
  var $id=0;

  /**
   * Type. Possible values:
   *    1:  Room background image
   *    2:  Smilie image
   *    3:  Message attachment
   *    4:  Gallery avatar
   * @var   int
   */
  var $type=0;

  /**
   * Owner' user ID
   * @var   int
   */
  var $user_id=0;

  /**
   * Binary file ID
   * @var   int
   */
  var $binaryfile_id=0;

  /**
   * Original file name
   * @var   string
   */
  var $filename='';




  /**
   * Constructor. Initialize Avatar class.
   * @param   object  &$sessionhandler  Session handler
   */
  function PCPIN_TmpData(&$sessionhandler) {
    // Init object
    $this->_s_init($sessionhandler, $this);
  }


  /**
   * Add new record
   * @param   int     $type           Type
   * @param   int     $user_if        User ID
   * @param   int     $binaryfile_id  ID of avatar image binary file
   * @param   string  $filename       Filename
   */
  function addRecord($type, $user_id, $binaryfile_id=0, $filename='') {
    $this->id=0;
    if (!empty($type) && !empty($user_id)) {
      $this->type=$type;
      $this->user_id=$user_id;
      $this->binaryfile_id=$binaryfile_id;
      $this->filename=$filename;
      if ($this->_db_insertObj()) {
        $this->id=$this->_db_lastInsertID();
      }
    }
  }


  /**
   * Delete all records owned by user
   * @param   int       $user_id          Avatar owner' User ID
   * @param   int       $type             Optional. Data type.
   * @param   int       $binaryfile_id    Optional. Binaryfile ID.
   * @param   boolean   $keep_binary      Optional. If TRUE, then binaryfile will be not deleted.
   */
  function deleteUserRecords($user_id=0, $type=0, $binaryfile_id=0, $keep_binary=false) {
    _pcpin_loadClass('binaryfile'); $binaryfile=new PCPIN_BinaryFile($this);
    if (!empty($user_id)) {
      if (empty($type)) {
        $this->_db_getList('id,binaryfile_id', 'user_id = '.$user_id);
      } else {
        $this->_db_getList('id,binaryfile_id', 'user_id = '.$user_id, 'type = '.$type);
      }
      if (!empty($this->_db_list)) {
        $list=$this->_db_list;
        $this->_db_freeList();
        foreach ($list as $data) {
          if (empty($binaryfile_id) || $binaryfile_id==$data['binaryfile_id']) {
            if (true!==$keep_binary) {
              // Delete binary file
              $binaryfile->deleteBinaryFile($data['binaryfile_id']);
            }
            // Delete record
            $this->_db_deleteRow($data['id']);
          }
        }
      }
    }
  }


}
?>