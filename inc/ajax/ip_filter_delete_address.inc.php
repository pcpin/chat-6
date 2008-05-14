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
* Delete address(es) from IP filter table
* @param  array   $ids    IDs of addresses
*/

_pcpin_loadClass('ipfilter'); $ipfilter=new PCPIN_IPFilter($session);

if (!isset($ids) || !is_array($ids)) $ids=array();

$errortext=array();
if (is_object($session) && !empty($current_user->id) && $current_user->is_admin==='y') {
  if (!empty($ids)) {
    $xmlwriter->setHeaderMessage($l->g('selected_addresses_were_deleted'));
    $xmlwriter->setHeaderStatus(0);
    foreach ($ids as $id) {
      if ($ipfilter->_db_getList('address', 'id = '.$id, 1)) {
        // Check wether IP address can be deleted from filter without blocking current user
        if ($ipfilter->isBlocked(PCPIN_CLIENT_IP, $id)) {
          // Not good
          $errortext[]=str_replace('[ADDRESS]', $ipfilter->_db_list[0]['address'], $l->g('own_ip_cant_be_deleted'));
        } else {
          // Delete address
          $ipfilter->deleteAddress($id);
        }
      }
    }
  } else {
    $xmlwriter->setHeaderMessage($l->g('error'));
    $xmlwriter->setHeaderStatus(1);
  }
}

if (!empty($errortext)) {
  $xmlwriter->setHeaderStatus(1);
  $xmlwriter->setHeaderMessage(implode("\n", $errortext));
}
?>