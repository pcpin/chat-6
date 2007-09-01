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
 * Class PCPIN_Message_Log_Attachment
 * Manage message attachments logs
 * @author Konstantin Reznichak <k.reznichak@pcpin.com>
 * @copyright Copyright &copy; 2007, Konstantin Reznichak
 */
class PCPIN_Message_Log_Attachment extends PCPIN_Session {

  /**
   * Message ID
   * @var   int
   */
  var $message_id=0;

  /**
   * Filename
   * @var   string
   */
  var $filename='';

  /**
   * Attachment body
   * @var   string
   */
  var $body='';

  /**
   * Attachment size in bytes
   * @var   int
   */
  var $size=0;

  /**
   * MIME type
   * @var   string
   */
  var $mime_type='';


  /**
   * Constructor
   * @param   object  &$sessionhandler  Session handler
   */
  function PCPIN_Message_Log_Attachment(&$sessionhandler) {
    // Init object
    $this->_s_init($sessionhandler, $this);
  }


  /**
   * Add new attachment log record
   * @param   int       $message_id     Message ID
   * @param   string    $filename       Filename
   * @param   string    $body           Body
   * @param   int       $size           Size
   * @param   string    $mime_type      MIME type
   * @return  boolean TRUE on success or FALSE on error
   */
  function addLogRecord($message_id=0, $filename='', $body='', $size=0, $mime_type='') {
    $result=false;
    if (!empty($message_id) && !empty($filename)) {
      $this->message_id=$message_id;
      $this->filename=$filename;
      $this->body=$body;
      $this->size=$size;
      $this->mime_type=$mime_type;
      if ($this->_db_insertObj()) {
        $result=true;
      }
    }
    return $result;
  }

}
?>