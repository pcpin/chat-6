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


if (empty($count) || !is_scalar($count) || !pcpin_ctype_digit($count)) $count=1;

$message=$l->g('access_denied');
$status='-1'; // -1: Session is invalid


if (empty($ip) || gettype($ip)!='string') $ip='';
$ping_data_xml='';


// Get client session
if (is_object($session) && !empty($current_user->id) && $session->_s_user_id==$current_user->id && $current_user->is_admin==='y') {
  if ($ip!='') {
    $ping_result=PCPIN_Ping::icmp_ping($ip, $count);
    if (empty($ping_result)) {
      // Ping failed
      $message=$l->g('error');
      $status=1;
    } else {
      // Ping successful
      $message='OK';
      $status=0;
      foreach ($ping_result as $ping) {
        $ping_data_xml.='    <ping>'.htmlspecialchars($ping).'</ping>'."\n";
      }
    }
  } else {
    // Client is not online
    $message=$l->g('client_not_online');
    $status=1;
  }

}


echo '<?xml version="1.0" encoding="UTF-8"?>
<pcpin_xml>
  <message>'.htmlspecialchars($message).'</message>
  <status>'.htmlspecialchars($status).'</status>
  <ping_data>
'.$ping_data_xml.'
  </ping_data>
</pcpin_xml>';
die();
?>