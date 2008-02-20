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
 * Class PCPIN_Category
 * Manage chat room categories
 * @author Konstantin Reznichak <k.reznichak@pcpin.com>
 * @copyright Copyright &copy; 2007, Konstantin Reznichak
 */
class PCPIN_Failed_Login extends PCPIN_Session {

  /**
   * IP address
   * @var   string
   */
  var $ip='';

  /**
   * Continuous failed login count
   * @var   int
   */
  var $count=0;




  /**
   * Constructor
   * @param   object  &$sessionhandler  Session handler
   */
  function PCPIN_Failed_Login(&$sessionhandler) {
    // Init object
    $this->_s_init($sessionhandler, $this);
  }


  /**
   * Increase failed logins counter for specified IP address and ban it if maximum allowed login attempt limit exceeds.
   * This method must be called on every failed login attempt.
   * @param   string    $ip           IP address
   * @param   string    $ban_reason   Reason for ban, if issued
   */
  function increaseCounter($ip, $ban_reason='') {
    $ip=trim($ip);
    if (!empty($ip)) {
      if ($this->_db_query($this->_db_makeQuery(2090, $ip))) {
        if (!empty($this->_conf_all['ip_failed_login_limit']) && $this->_db_getList('count', 'ip =# '.$ip, 1)) {
          if ($this->_db_list[0]['count']>$this->_conf_all['ip_failed_login_limit']) {
            _pcpin_loadClass('ipfilter'); $ban=new PCPIN_IPFilter($this);
            $ban->addAddress($ip, date('Y-m-d H:i:s', time()+3600*$this->_conf_all['ip_failed_login_ban']), $ban_reason, 'd');
            $this->clearCounter($ip);
          }
        }
      }
    }
  }


  /**
   * Clear login attempts of a specified IP address.
   * This method must be called on every successfull login attempt.
   * @param   string    $ip     IP address
   */
  function clearCounter($ip) {
    $this->_db_deleteRow($ip, 'ip');
  }

}
?>