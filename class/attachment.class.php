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
 * Class PCPIN_Attachment
 * Manage message attachments
 * @author Konstantin Reznichak <k.reznichak@pcpin.com>
 * @copyright Copyright &copy; 2007, Konstantin Reznichak
 */
class PCPIN_Attachment extends PCPIN_Session {

  /**
   * ID
   * @var int
   */
  var $id=0;

  /**
   * Message ID
   * @var int
   */
  var $message_id=0;

  /**
   * Binaryfile ID
   * @var int
   */
  var $binaryfile_id=0;

  /**
   * Original file name
   * @var string
   */
  var $filename='';




  /**
   * Constructor
   * @param   object  &$sessionhandler  Session handler
   */
  function PCPIN_Attachment(&$sessionhandler) {
    // Init object
    $this->_s_init($sessionhandler, $this);
  }


  /**
   * Add new attachment
   * @param     int       $message_id       Message ID
   * @param     int       $binaryfile_id    Binaryfile ID
   * @param     string    $filename         Filename
   * @return  int
   */
  function addAttachment($message_id=0, $binaryfile_id=0, $filename='') {
    $this->id=0;
    if (!empty($message_id) && !empty($binaryfile_id) && !empty($filename)) {
      $this->message_id=$message_id;
      $this->binaryfile_id=$binaryfile_id;
      $this->filename=$filename;
      if ($this->_db_insertObj()) {
        $this->id=$this->_db_lastInsertID();
      }
    }
    return $this->id;
  }


  /**
   * Delete attachment
   * @param   int       $id               Optional. Attachment ID
   * @param   int       $message_id       Optional. Message ID
   * @return  boolean TRUE on success or FALSE on error
   */
  function deleteAttachment($id=0, $message_id=0) {
    $result=false;
    _pcpin_loadClass('binaryfile'); $binaryfile=new PCPIN_BinaryFile($this);
    if (!empty($id) && $this->_db_getList('binaryfile_id', 'id = '.$id, 1)) {
      $result=true;
      $binaryfile->deleteBinaryFile($this->_db_list[0]['binaryfile_id']);
      $this->_db_deleteRow($id);
    } elseif (!empty($message_id) && $this->_db_getList('id,binaryfile_id', 'message_id = '.$message_id)) {
      $result=true;
      $ids=$this->_db_list;
      foreach ($ids as $data) {
        $binaryfile->deleteBinaryFile($data['binaryfile_id']);
        $this->_db_deleteRow($data['id']);
      }
    }
    $this->_db_freeList();
    return $result;
  }


  /**
   * Get message attachments
   * @param     int       $message_id       Message ID
   * @return  array
   */
  function getAttachments($message_id=0) {
    $attachments=array();
    if (!empty($message_id) && $this->_db_getList('message_id = '.$message_id, 'id ASC')) {
      $attachments=$this->_db_list;
      $this->_db_freeList();
    }
    return $attachments;
  }


}
?>