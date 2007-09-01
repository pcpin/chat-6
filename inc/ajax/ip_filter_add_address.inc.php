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
 * Add new address to the IP filter table
 * @param  string    $mask             IP address mask
 * @param  int       $expires_year     Expiration date: year
 * @param  int       $expires_month    Expiration date: month
 * @param  int       $expires_day      Expiration date: day
 * @param  int       $expires_hour     Expiration date: hour
 * @param  int       $expires_minute   Expiration date: minute
 * @param  int       $expires_never    If not empty, then IP address will never expire
 * @param  string    $description      Additional information
 * @param  string    $action           Filter action ("a": allow or "d": deny)
 */

$message=$l->g('access_denied');
$status='-1'; // -1: Session is invalid

_pcpin_loadClass('ipfilter'); $ipfilter=new PCPIN_IPFilter($session);

if (!isset($mask)) $mask='';
if (!isset($expires)) $expires='';
if (!isset($description)) $description='';
if (!isset($action)) $action='d';

$errortext=array();
if (is_object($session) && !empty($current_user->id) && $current_user->is_admin==='y') {
  $message='OK';
  $status=0;

  $mask=trim($mask);
  $description=trim($description);
  $action=trim($action);

  // Validate expiration date
  if (  empty($expires_never) &&
      (   !@checkdate($expires_month, $expires_day, $expires_year)
       || !pcpin_ctype_digit($expires_hour) || $expires_hour>60 || $expires_hour<0
       || !pcpin_ctype_digit($expires_minute) || $expires_minute>60 || $expires_minute<0
       )
     ) {
    $errortext[]=$l->g('expiration_date_invalid');
  }

  // Check mask
  if (!$ipfilter->checkIPMask($mask)) {
    $errortext[]=$l->g('ip_mask_invalid');
  }

  if (empty($errortext)) {
    if ($ipfilter->addAddress($mask, empty($expires_never)? ("$expires_year-$expires_month-$expires_day $expires_hour:$expires_minute:00") : '', $description, $action)) {
      $message=$l->g('ip_address_added');
      // Ensure, that current user can access the software with new record
      if ($ipfilter->isBlocked(PCPIN_CLIENT_IP)) {
        // Not good
        $ipfilter->deleteAddress($ipfilter->id);
        $errortext[]=str_replace('[ADDRESS]', $mask, $l->g('own_ip_cant_be_banned'));
      }
    } else {
      $errortext[]=$l->g('error');
    }
  }
}

if (!empty($errortext)) {
  $status=1;
  $message=implode("\n", $errortext);
}

echo '<?xml version="1.0" encoding="UTF-8"?>
<pcpin_xml>
  <message>'.htmlspecialchars($message).'</message>
  <status>'.htmlspecialchars($status).'</status>
</pcpin_xml>';
die();
?>