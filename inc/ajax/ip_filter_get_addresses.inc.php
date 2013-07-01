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
* Get addresses listed in IP filter table
* @param  int   $sort_by    Sort by (0: Address, 1: Action type, 2: Expiration date, 3: Description, 4: "Added on" date)
* @param  int   $sort_dir   Sort direction (0: Ascending, 1: Descending)
*/

_pcpin_loadClass('ipfilter'); $ipfilter=new PCPIN_IPFilter($session);

$ip_addresses=array();

if (!isset($sort_by)) $sort_by=0;
if (!isset($sort_dir)) $sort_dir=0;

// Get client session
if (is_object($session) && !empty($current_user->id) && $current_user->is_admin==='y') {
  $xmlwriter->setHeaderMessage('OK');
  $xmlwriter->setHeaderStatus(0);
  $addresses=$ipfilter->readAddresses($sort_by, $sort_dir);
  foreach ($addresses as $address_data) {
    $ip_addresses[]=array('id'=>$address_data['id'],
                          'type'=>$address_data['type'],
                          'mask'=>$address_data['address'],
                          'added_on'=>$current_user->makeDate(PCPIN_Common::datetimeToTimestamp($address_data['added_on'])),
                          'expires'=>($address_data['expires']>'0000-00-00 00:00:00')? $current_user->makeDate(PCPIN_Common::datetimeToTimestamp($address_data['expires'])) : $l->g('never'),
                          'action'=>$address_data['action'],
                          'description'=>$address_data['description'],
                          );
  }
}
$xmlwriter->setData(array('address'=>$ip_addresses));
?>