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
 * Delete banner
 */

$message=$l->g('access_denied');
$status='-1'; // -1: Session is invalid

_pcpin_loadClass('banner'); $banner=new PCPIN_Banner($session);

if (!isset($banner_id)) $banner_id=0;

// Get client session
if (is_object($session) && !empty($current_user->id) && $current_user->is_admin==='y') {
  $message=$l->g('banner_deleted');
  $status=0;
  $banner->deleteBanner($banner_id*1);
}


echo '<?xml version="1.0" encoding="UTF-8"?>
<pcpin_xml>
  <message>'.htmlspecialchars($message).'</message>
  <status>'.htmlspecialchars($status).'</status>
</pcpin_xml>';
die();
?>