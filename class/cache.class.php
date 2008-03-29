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
 * Class PCPIN_Cache
 * Manage cached data
 * @author Konstantin Reznichak <k.reznichak@pcpin.com>
 * @copyright Copyright &copy; 2008, Konstantin Reznichak
 */
class PCPIN_Cache extends PCPIN_DB {

  /**
   * ID (max. 255 chars)
   * @var   string
   */
  var $id='';

  /**
   * Contents
   * @var   string
   */
  var $contents='';




  /**
   * Constructor
   * @param   object  &$sessionhandler  Session handler
   */
  function PCPIN_Cache() {
    // Init object
    $this->_s_init($sessionhandler, $this);
  }


  /**
   * Get record
   * @param   string  $id     Record ID
   * @return  mixed   (string) cache contents or NULL if no record found
   */
  function getRecord($id) {
    $cache=null;
    if ($this->_db_getList('id =# '.$id, 1)) {
      $cache=$this->_db_list[0]['contents'];
      $this->_db_freeList();
    }
    return $cache;
  }


  /**
   * Add new record
   * @param   string    $id         Record ID (max. 255 chars)
   * @param   string    $contents   Contents
   */
  function addRecord($id, $contents='') {
    $result=$this->_db_query($this->_db_makeQuery(2110, $id, $contents));
    $this->_db_freeResult($result);
  }

}
?>