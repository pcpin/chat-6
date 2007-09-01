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
 * Class PCPIN_Language_Expression
 * Manage language expressions
 * @author Konstantin Reznichak <k.reznichak@pcpin.com>
 * @copyright Copyright &copy; 2007, Konstantin Reznichak
 */
class PCPIN_Language_Expression extends PCPIN_Session {

  /**
   * Language ID
   * @var   int
   */
  var $language_id=0;

  /**
   * Expression code
   * @var   string
   */
  var $code='';

  /**
   * Expression value
   * @var   string
   */
  var $value='';

  /**
   * Flag. If "y", then value may contain line breaks.
   * @var   string
   */
  var $multi_row='';




  /**
   * Constructor
   * @param   object  &$sessionhandler  Session handler
   */
  function PCPIN_Language_Expression(&$sessionhandler) {
    // Init object
    $this->_s_init($sessionhandler, $this);
  }


  /**
   * Update language expression
   * @param   int       $language_id    Language ID
   * @param   string    $code           Code
   * @param   string    $value          Value
   * @return  boolean TRUE on success or FALSE on error
   */
  function updateExpression($language_id=0, $code='', $value='') {
    $result=false;
    if (!empty($language_id) && !empty($code)) {
      if ($result=$this->_db_query($this->_db_makeQuery(2070, $language_id, $code, $value))) {
        $this->_db_freeResult($result);
        $result=true;
      }
    }
    return $result;
  }


}
?>