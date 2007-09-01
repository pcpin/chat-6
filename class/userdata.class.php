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
   * Homepage URL
   * @var   string
   */
  var $homepage='';

  /**
   * Gender ("m"/"f"/"-")
   * @var   string
   */
  var $gender='-';

  /**
   * Age
   * @var   string
   */
  var $age='';

  /**
   * ICQ number
   * @var   string
   */
  var $icq='';

  /**
   * MSN number
   * @var   string
   */
  var $msn='';

  /**
   * AIM number
   * @var   string
   */
  var $aim='';

  /**
   * YIM number
   * @var   string
   */
  var $yim='';

  /**
   * Location
   * @var   string
   */
  var $location='';

  /**
   * Occupation
   * @var   string
   */
  var $occupation='';

  /**
   * Interests
   * @var   string
   */
  var $interests='';



  /**
   * Constructor
   * @param   object  &$sessionhandler  Session handler
   */
  function PCPIN_UserData(&$sessionhandler) {
    // Init object
    $this->_s_init($sessionhandler, $this);
  }


  /**
   * Update userdata object and/or database row
   * @param   int       $user_id          User ID
   * @param   boolean   $obj              If TRUE, then object properties will be updated
   * @param   boolean   $db               If TRUE, then database table will be updated
   * @param   string    $gender           Gender. NULL: do not change.
   * @param   string    $age              Age. NULL: do not change.
   * @param   string    $icq              ICQ. NULL: do not change.
   * @param   string    $msn              MSN. NULL: do not change.
   * @param   string    $aim              AIM. NULL: do not change.
   * @param   string    $yim              YIM. NULL: do not change.
   * @param   string    $location         Location. NULL: do not change.
   * @param   string    $occupation       Occupation. NULL: do not change.
   * @param   string    $interests        Interests. NULL: do not change.
   * @param   string    $homepage         Home page. NULL: do not change.
   * @return  boolean TRUE on success or FALSE on error
   */
  function updateUserData($user_id, $obj=false, $db=false,
                          $gender=null,
                          $age=null,
                          $icq=null,
                          $msn=null,
                          $aim=null,
                          $yim=null,
                          $location=null,
                          $occupation=null,
                          $interests=null,
                          $homepage=null
                          ) {
    $result=false;
    if (!empty($user_id)) {
      if (true===$obj) {
        $result=true;
        if (!is_null($gender)) $this->gender=$gender;
        if (!is_null($age)) $this->age=$age;
        if (!is_null($icq)) $this->icq=$icq;
        if (!is_null($msn)) $this->msn=$msn;
        if (!is_null($aim)) $this->aim=$aim;
        if (!is_null($yim)) $this->yim=$yim;
        if (!is_null($location)) $this->location=$location;
        if (!is_null($occupation)) $this->occupation=$occupation;
        if (!is_null($interests)) $this->interests=$interests;
        if (!is_null($homepage)) $this->homepage=$homepage;
      }
      if (true===$db) {
        $param=array();
        if (!is_null($gender)) $param['gender']=$gender;
        if (!is_null($age)) $param['age']=$age;
        if (!is_null($icq)) $param['icq']=$icq;
        if (!is_null($msn)) $param['msn']=$msn;
        if (!is_null($aim)) $param['aim']=$aim;
        if (!is_null($yim)) $param['yim']=$yim;
        if (!is_null($location)) $param['location']=$location;
        if (!is_null($occupation)) $param['occupation']=$occupation;
        if (!is_null($interests)) $param['interests']=$interests;
        if (!is_null($homepage)) $param['homepage']=$homepage;
        $result=$this->_db_updateRow($user_id, 'user_id', $param);
      }
    }
    return $result;
  }

}
?>