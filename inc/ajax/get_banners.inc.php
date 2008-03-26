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
 * Get banners list (Admin area)
 */

_pcpin_loadClass('banner'); $banner=new PCPIN_Banner($session);

$banners_xml='';

// Get client session
if (is_object($session) && !empty($current_user->id) && $current_user->is_admin==='y') {
  $message='OK';
  $status=0;
  $banners=$banner->getBanners();
  foreach ($banners as $banner_data) {
    $banners_xml.='  <banner>
    <id>'.htmlspecialchars($banner_data['id']).'</id>
    <name>'.htmlspecialchars($banner_data['name']).'</name>
    <active>'.htmlspecialchars($banner_data['active']).'</active>
    <source_type>'.htmlspecialchars($banner_data['source_type']).'</source_type>
    <source>'.htmlspecialchars($banner_data['source']).'</source>
    <display_position>'.htmlspecialchars($banner_data['display_position']).'</display_position>
    <views>'.htmlspecialchars($banner_data['views']).'</views>
    <max_views>'.htmlspecialchars($banner_data['max_views']).'</max_views>
    <start_date>'.htmlspecialchars(PCPIN_Common::datetimeToTimestamp($banner_data['start_date'])).'</start_date>
    <expiration_date>'.htmlspecialchars($banner_data['expiration_date']>'0000-00-00 00:00:00'? PCPIN_Common::datetimeToTimestamp($banner_data['expiration_date']) : '0').'</expiration_date>
    <width>'.htmlspecialchars($banner_data['width']).'</width>
    <height>'.htmlspecialchars($banner_data['height']).'</height>
  </banner>
';
  }
}


echo '<?xml version="1.0" encoding="UTF-8"?>
<pcpin_xml>
  <message>'.htmlspecialchars($message).'</message>
  <status>'.htmlspecialchars($status).'</status>
'.$banners_xml
.'</pcpin_xml>';
die();
?>