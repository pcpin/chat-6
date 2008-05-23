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
 * Class PCPIN_Smilie
 * Manage smilies
 * @author Konstantin Reznichak <k.reznichak@pcpin.com>
 * @copyright Copyright &copy; 2007, Konstantin Reznichak
 */
class PCPIN_Smilie extends PCPIN_Session {

  /**
   * Smilie ID
   * @var   int
   */
  var $id=0;

  /**
   * Smilie code
   * @var   string
   */
  var $code='';

  /**
   * Smilie description
   * @var   string
   */
  var $description='';

  /**
   * ID of smilie image file in "binaryfile" table
   * @var   int
   */
  var $binaryfile_id=0;




  /**
   * Constructor
   * @param   object  &$sessionhandler  Session handler
   */
  function PCPIN_Smilie(&$sessionhandler) {
    // Init object
    $this->_s_init($sessionhandler, $this);
  }


  /**
   * Add new smilie
   * @param   int     $binaryfile_id  ID of smilie image binary file
   * @param   string  $code           Smilie code
   * @param   string  $description    Smilie description
   * @return  boolean TRUE on success or FALSE on error
   */
  function addSmilie($binaryfile_id, $code, $description='') {
    $result=false;
    $this->id=0;
    $code=trim($code);
    $description=trim($description);
    if (!empty($binaryfile_id) && $code!='') {
      $this->id=0;
      $this->binaryfile_id=$binaryfile_id;
      $this->code=$code;
      $this->description=$description;
      if ($result=$this->_db_insertObj()) {
        $this->id=$this->_db_lastInsertID();
      }
    }
    return $result;
  }


  /**
   * Delete smilie
   * @param   int   $id     Smilie ID
   * @return  boolean   TRUE on success or FALSE on error
   */
  function deleteSmilie($id) {
    $result=false;
    if (!empty($id) && $this->_db_getList('binaryfile_id', 'id =# '.$id, 1)) {
      $result=true;
      $binaryfile_id=$this->_db_list[0]['binaryfile_id'];
      $this->_db_freeList();
      // Delete binary file
      _pcpin_loadClass('binaryfile'); $binaryfile=new PCPIN_BinaryFile($this);
      $binaryfile->deleteBinaryFile($binaryfile_id);
      // Delete smilie
      $this->_db_deleteRow($id);
    }
    return $result;
  }


  /**
   * Update smilie
   * @param   int       $id             Smilie ID
   * @param   string    $code           New smilie code
   * @param   string    $description    New smilie description
   * @return  boolean   TRUE on success or FALSE on error
   */
  function updateSmilie($id, $code, $description='') {
    $result=false;
    $code=trim($code);
    $description=trim($description);
    if (!empty($id) && $code>='' && $this->_db_getList('code,description', 'id =# '.$id, 1)) {
      $result=true;
      if ($this->_db_list[0]['code']!=$code || $this->_db_list[0]['description']!=$description) {
        $this->_db_updateRow($id, 'id', array('code'=>$code, 'description'=>$description));
      }
      $this->_db_freeList();
    }
    return $result;
  }


  /**
   * Get smilies
   * @return  array
   */
  function getSmilies() {
    $smilies=array();
    $this->_db_getList('id ASC');
    $smilies=$this->_db_list;
    $this->_db_freeList();
    return $smilies;
  }


}
?>