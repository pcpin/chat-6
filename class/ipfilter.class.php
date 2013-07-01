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
 * Class PCPIN_IPFilter
 * Filter IP addresses
 * @author Kanstantin Reznichak <k.reznichak@pcpin.com>
 * @copyright Copyright &copy; 2007, Konstantin Reznichak
 */
class PCPIN_IPFilter extends PCPIN_Session {

  /**
   * IP Address ID
   * @var   int
   */
  var $id=0;

  /**
   * IP address type (IPv4 or IPv6)
   * @var   string
   */
  var $type = 'IPv4';

  /**
   * IP address mask. IP address mask can contain digits and/or wildcard characters '*' or '?' only, delimitered by '.' character.
   * Following wildcards are allowed:
   *    *   Matches any number of characters, even zero characters
   *    ?   Matches exactly one character
   * @var   string
   */
  var $address='';

  /**
   * Record date (MySQL DATETIME)
   * @var   string
   */
  var $added_on='';

  /**
   * Expiration date (MySQL DATETIME). Empty value or "0000-00-00 00:00:00" means no expiration.
   * @var   string
   */
  var $expires='';

  /**
   * Record description
   * @var   string
   */
  var $description='';

  /**
   * Functionality of the record
   *    "d" Deny
   *    "a" Allow
   * @var   string
   */
  var $action=0;





  /**
   * Constructor
   * @param   object  &$sessionhandler  Session handler
   */
  function PCPIN_IPFilter(&$sessionhandler) {
    // Init object
    $this->_s_init($sessionhandler, $this);
  }


  /**
   * Validate IP address mask.
   * IP address mask can contain digits and/or wildcard characters '*' or '?' only, delimitered by '.' character.
   * @param   string    $type     IP address type (IPv4 or IPv6)
   * @param   string    $ipmask   IP address mask in format 'XXX.XXX.XXX.XXX'
   * @return  boolean   TRUE if IP address mask is valid or FALSE if not
   */
  function checkIPMask($type = '', $ipmask='') {
    if ($type === 'IPv6') {
      return (bool) preg_match('/^[0-9A-F\?\*\:]{1,45}$/i', $ipmask); // Incomplete and really primitive - allows more than needed, just as quick-and-dirty workaround.
    } else {
      return (bool) preg_match('/^([0-9\*\?]{1,3}\.)+([0-9\*\?]{1,3}\.)+([0-9\*\?]{1,3}\.)+([0-9\*\?]{1,3})$/', $ipmask);
    }
  }


  /**
   * Add new IP address into the database
   * @param   string    $type         IP address type (IPv4 or IPv6)
   * @param   string    $ip           IP address
   * @param   string    $expires      Expiration date (MySQL DATETIME). Empty value means no expiration.
   * @param   string    $description  Description
   * @param   string    $action       Record type (allow/deny)
   * @return  boolean   TRUE on success or FALSE on error
   */
  function addAddress($type = '', $ip='', $expires='', $description='', $action='d') {
    $this->id=0;
    $this->type = $type;
    $this->address=$ip;
    $this->added_on=date('Y-m-d H:i:s');
    $this->expires=($expires=='')? '0000-00-00 00:00:00' : $expires;
    $this->description=_pcpin_substr(trim($description), 0, 255);
    $this->action=$action;
    if ($result=$this->_db_insertObj()) {
      $this->id=$this->_db_lastInsertID();
    }
    return $result;
  }


  /**
   * Delete IP address from the database
   * @param   int   $ip_id    ID of IP address
   * @return  boolean   TRUE on success or FALSE on error
   */
  function deleteAddress($ip_id=0) {
    $ok=false;
    if (!empty($ip_id)) {
      $ok=$this->_db_deleteRow($ip_id);
    }
    return $ok;
  }


  /**
   * Check IP address against stored in database filter
   * @param   string    $ip             IP address to check
   * @param   int       $skip_record    If non-empty, then the record with this ID will be ignored
   * @return  mixed   (boolean) FALSE if IP address is not blocked or (array) block reason and block expiration if IP address is blocked
   */
  function isBlocked($ip='', $skip_record=0) {
    $blocked=false;
    $blocked_reason='IP filter not configured';
    $blocked_expires='0000-00-00 00:00:00';
    $ip=trim($ip);
    if ($ip!='') {
      // Is IP address blocked?
      $query=$this->_db_makeQuery(1400, $ip, $skip_record, date('Y-m-d H:i:s'), false !== strpos($ip, ':')? 'IPv6' : 'IPv4');
      $result=$this->_db_query($query);
      $allowed=false;
      while ($data=$this->_db_fetch($result, MYSQL_ASSOC)) {
        if ($data['action']=='d') {
          // IP is blocked. Access denied.
          $blocked=array('reason'=>$data['description'],
                         'expires'=>$data['expires']);
          break;
        } elseif ($data['action']=='a') {
          // IP is allowed. Still needs to be non-blocked.
          $allowed=true;
        }
      }
      $this->_db_freeResult($result);
      if (false===$blocked && false===$allowed) {
        // IP is not blocked, but not allowed. Access denied.
        $blocked=array('reason'=>$blocked_reason,
                       'expires'=>$blocked_expires);
      }
    }
    return $blocked;
  }


  /**
   * Get IP addresses list
   * @param   int   $sortby     Sort by (0: Address, 1: Action type, 2: Expiration date, 3: Description, 4: "Added on" date)
   * @param   int   $sortdir    Sort direction (0: Ascending, 1: Descending)
   * @return  boolean   TRUE on success or FALSE on error
   */
  function readAddresses($sortby=0, $sortdir=0) {
    $list=array();
    $query=$this->_db_makeQuery(1410, $sortby, $sortdir);
    $result=$this->_db_query($query);
    while ($data=$this->_db_fetch($result, MYSQL_ASSOC)) {
      $list[]=$data;
    }
    $this->_db_freeResult($result);
    return $list;
  }


}
?>