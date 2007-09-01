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
 * Class PCPIN_BinaryFile
 * Manage binary files
 * @author Konstantin Reznichak <k.reznichak@pcpin.com>
 * @copyright Copyright &copy; 2007, Konstantin Reznichak
 */
class PCPIN_BinaryFile extends PCPIN_Session {

  /**
   * ID
   * @var   int
   */
  var $id=0;

  /**
   * Contents
   * @var   string
   */
  var $body='';

  /**
   * Size of the contents in bytes
   * @var   int
   */
  var $size=0;

  /**
   * MIME-Type of the contents
   * @var   string
   */
  var $mime_type='';

  /**
   * Last modification date (MySQL DATETIME)
   * @var   string
   */
  var $last_modified='';

  /**
   * For images only: width in pixels
   * @var   int
   */
  var $width=0;

  /**
   * For images only: height in pixels
   * @var   int
   */
  var $height=0;

  /**
   * If not empty: the file is protected and can be accessed by specified users only.
   * Possible values:
   *    log         :   Can be accessed by any logged in user
   *    reg         :   Can be accessed by any registered user
   *    room|<id>   :   Can be accessed by user that is currently in room with ID <id>
   *    user|<id>   :   Can be accessed by user with ID <id>
   * Values can be concatented using "/", eg. "reg/room|32" means that file can be
   * accessed only by registered users from room with ID 32.
   * NOTE: The $protected string can not be longer than 255 chars.
   * @var   string
   */
  var $protected='';



  /**
   * Constructor. Initialize BinaryFile class.
   * @param   object  &$sessionhandler  Session handler
   */
  function PCPIN_BinaryFile(&$sessionhandler) {
    // Init object
    $this->_s_init($sessionhandler, $this);
  }

  /**
   * Add new binary file
   * @param   string    $body       Binary file body
   * @param   string    $mime       MIME-Type of the file
   * @param   int       $width      Width in pixels (images only)
   * @param   int       $height     Height in pixels (images only)
   * @param   string    $protected  Restricted access to file
   * @return  boolean   TRUE on success or FALSE on error
   */
  function newBinaryFile($body='', $mime='', $width=0, $height=0, $protected=''){
    $this->id=0;
    $this->body=$body;
    $this->size=strlen($this->body);
    $this->mime_type=$mime;
    $this->last_modified=date('Y-m-d H:i:s');
    $this->width=$width;
    $this->height=$height;
    $this->protected=$protected;
    if ($result=$this->_db_insertObj()) {
      $this->id=$this->_db_lastInsertID();
    }
    return $result;
  }


  /**
   * Delete binary file
   * @param   int       $id     Binary file ID
   * @return  boolean   TRUE on success or FALSE on error
   */
  function deleteBinaryFile($id=0) {
    $result=false;
    if (!empty($id) && $this->_db_getList('id', 'id = '.$id, 1)) {
      $result=$this->_db_deleteRow($id);
    }
    return $result;
  }


  /**
   * Get information about binary file
   * @param   int       $id     Binary file ID
   * @return  array
   */
  function getInfo($id=0) {
    $result=array();
    if (!empty($id)) {
      if ($this->_db_getList('size,mime_type,last_modified,width,height', 'id = '.$id, 1)) {
        $result=$this->_db_list[0];
        $this->_db_freeList();
      }
    }
    return $result;
  }


}
?>