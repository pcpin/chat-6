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
 * Update banner
 * @param   int       $banner_id          Banner ID
 * @param   string    $name               Banner name
 * @param   string    $active             Active flag
 * @param   string    $source_type        Source type
 * @param   string    $source             Banner source URL or HTML code
 * @param   string    $display_position   Display position
 * @param   int       $max_views          Views limit
 * @param   int       $start_year         Start date: year
 * @param   int       $start_month        Start date: month
 * @param   int       $start_day          Start date: day
 * @param   int       $start_hour         Start date: hour
 * @param   int       $start_minute       Start date: minute
 * @param   int       $expires_year       Expiration date: year
 * @param   int       $expires_month      Expiration date: month
 * @param   int       $expires_day        Expiration date: day
 * @param   int       $expires_hour       Expiration date: hour
 * @param   int       $expires_minute     Expiration date: minute
 * @param   int       $expires_never      If not empty, then banner will never expire
 * @param   int       $width              Banner width
 * @param   int       $height             Banner height
 */

_pcpin_loadClass('banner'); $banner=new PCPIN_Banner($session);

if (!isset($banner_id)) $banner_id=0;
if (!isset($name)) $name='';
if (!isset($active)) $active='';
if (!isset($source_type)) $source_type='';
if (!isset($source)) $source='';
if (!isset($display_position)) $display_position='';
if (!isset($max_views)) $max_views=0;
if (!isset($start_year)) $start_year=0;
if (!isset($start_month)) $start_month=0;
if (!isset($start_day)) $start_day=0;
if (!isset($start_hour)) $start_hour=0;
if (!isset($start_minute)) $start_minute=0;
if (!isset($expires_year)) $start_year=0;
if (!isset($expires_month)) $start_month=0;
if (!isset($expires_day)) $start_day=0;
if (!isset($expires_hour)) $start_hour=0;
if (!isset($expires_minute)) $start_minute=0;
if (!isset($width)) $width=0;
if (!isset($height)) $height=0;

$errortext=array();
if (is_object($session) && !empty($current_user->id) && $current_user->is_admin==='y') {
  $xmlwriter->setHeaderMessage('OK');
  $xmlwriter->setHeaderStatus(0);

  $banner_id*=1;

  if ($banner->_db_getList('id', 'id = '.$banner_id, 1)) {

    $name=trim($name);
    $active=trim($active);
    $source_type=trim($source_type);
    $source=trim($source);
    $display_position=trim($display_position);
    $max_views*=1;
    $start_year*=1;
    $start_month*=1;
    $start_day*=1;
    $start_hour*=1;
    $start_minute*=1;
    $expires_year*=1;
    $expires_month*=1;
    $expires_day*=1;
    $expires_hour*=1;
    $expires_minute*=1;
    $width*=1;
    $height*=1;

    // Name
    if ($name=='') {
      // Name empty
      $errortext[]=$l->g('banner_name_empty_error');
    } elseif ($banner->_db_getList('id', 'id != '.$banner_id, 'name LIKE '.$name, 1)) {
      // Banner with this name already exists
      $errortext[]=$l->g('banner_name_already_exists');
      $banner->_db_freeList();
    }

    //  Position
    if (   $display_position!='p'
        && $display_position!='m'
        && $display_position!='t'
        && $display_position!='b') {
      $display_position='t';
    }

    // Validate start date
    if (   !@checkdate($start_month, $start_day, $start_year)
        || $start_hour>60 || $start_hour<0
        || $start_minute>60 || $start_minute<0
       ) {
      $errortext[]=$l->g('start_date_invalid');
    }

    // Validate expiration date
    if (  empty($expires_never) &&
        (   !@checkdate($expires_month, $expires_day, $expires_year)
         || $expires_hour>60 || $expires_hour<0
         || $expires_minute>60 || $expires_minute<0
         )
       ) {
      $errortext[]=$l->g('expiration_date_invalid');
    }

    // Width
    if ($display_position!='t' && $display_position!='b' && $width<=0) {
      $errortext[]=$l->g('width_invalid');
    }

    // Width
    if ($display_position!='t' && $display_position!='b' && $height<=0) {
      $errortext[]=$l->g('height_invalid');
    }

    if (empty($errortext)) {
      $start_date="$start_year-$start_month-$start_day $start_hour:$start_minute:00";
      $expiration_date=empty($expires_never)? "$expires_year-$expires_month-$expires_day $expires_hour:$expires_minute:00" : '0000-00-00 00:00:00';
      if ($banner->updateBanner($banner_id, false, true,
                                $name,
                                $active,
                                $source_type,
                                $source,
                                $display_position,
                                $max_views,
                                $start_date,
                                $expiration_date,
                                $width,
                                $height)) {
        $xmlwriter->setHeaderMessage($l->g('banner_updated'));
      } else {
        $errortext[]=$l->g('error');
      }
    }
  } else {
    $errortext[]=$l->g('banner_not_exists');
  }
}

if (!empty($errortext)) {
  $xmlwriter->setHeaderStatus(1);
  $xmlwriter->setHeaderMessage(implode("\n", $errortext));
}
?>