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
 * Class PCPIN_Disallowed_Name
 * Manage disallowed usernames
 * @author Konstantin Reznichak <k.reznichak@pcpin.com>
 * @copyright Copyright &copy; 2007, Konstantin Reznichak
 */
class PCPIN_Disallowed_Name extends PCPIN_Session {

  /**
   * Name ID
   * @var   int
   */
  var $id=0;

  /**
   * Name
   * @var   string
   */
  var $name='';

  /**
   * Names array as returned by $this->getDisallowedNames()
   * @var   string
   */
  var $names_cache=array();




  /**
   * Constructor
   * @param   object  &$sessionhandler  Session handler
   */
  function PCPIN_Disallowed_Name(&$sessionhandler) {
    // Init object
    $this->_s_init($sessionhandler, $this);
  }

  /**
   * Get disallowed names
   * @return  array
   */
  function getDisallowedNames() {
    if ($this->_db_getList('name ASC')) {
      $this->names_cache=$this->_db_list;
      $this->_db_freeList();
    }
    return $this->names_cache;
  }

  /**
   * Change name
   * @param   int       $id           Name ID
   * @param   string    $name         Name
   * @return  boolean   TRUE on success or false on error
   */
  function updateName($id=0, $name='') {
    $ok=false;
    if (!empty($id)) {
      $this->id=$id;
      $this->name=$name;
      $ok=$this->_db_updateObj($id);
    }
    return $ok;
  }

  /**
   * Delete name from database
   * @param   int     $id     Name ID
   * @return  boolean TRUE on success or false on error
   */
  function deleteName($id=0) {
    $ok=false;
    if (!empty($id)) {
      $ok=$this->_db_deleteRow($id);
    }
    return $ok;
  }

  /**
   * Add new name
   * @param   string    $name         Name
   * @return  boolean   TRUE on success or false on error
   */
  function addName($name='') {
    $ok=false;
    $this->id=0;
    $this->name=$name;
    if ($ok=$this->_db_insertObj()) {
      $this->id=$this->_db_lastInsertID();
    }
    return $ok;
  }

  /**
   * Check string for containing disallowed names
   * @param   string    $string         String to check
   * @return  boolean TRUE if strong does not contains disallowed names, FALSE otherwise
   */
  function checkString($string='') {
    $result=true;
    if ($string!='') {
      if (empty($this->names_cache)) {
        $this->getDisallowedNames();
      }
      if (!empty($this->names_cache)) {
        $string=_pcpin_strtolower($string);
        foreach ($this->names_cache as $name_data) {
          if (false!==_pcpin_strpos($string, _pcpin_strtolower($name_data['name']))) {
            $result=false;
            break;
          }
        }
      }
    }
    return $result;
  }

}
?>