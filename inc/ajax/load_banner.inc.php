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

if (!isset($display_position)) $display_position='';

$banner_data_xml='';

_pcpin_loadClass('banner'); $banner=new PCPIN_Banner($session);

if (!empty($current_user->id)) {
  $message=$l->g('error');
  $status=1;

  if ($session->_s_room_id>0) {
    if ($banner_data=$banner->getRandomBanner($display_position)) {
      $message='OK';
      $status=0;
      foreach ($banner_data as $key=>$val) {
        if (   $key=='display_position'
            || $key=='width'
            || $key=='height'
            || $key=='id'
            ) {
          $banner_data_xml.='  <'.htmlspecialchars($key).'>'.htmlspecialchars($val).'</'.htmlspecialchars($key).'>'."\n";
        }
      }
    }
  }
}

echo '<?xml version="1.0" encoding="UTF-8"?>
<pcpin_xml>
<message>'.htmlspecialchars($message).'</message>
<status>'.htmlspecialchars($status).'</status>
<banner_data>
'.rtrim($banner_data_xml).'
</banner_data>
</pcpin_xml>';
die();
?>