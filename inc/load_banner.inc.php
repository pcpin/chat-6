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

if (!is_object($session)) {
  die('Access denied');
}

_pcpin_loadClass('banner'); $banner=new PCPIN_Banner($session);

if (empty($banner_id)) {
  $banner_data=$banner->getRandomBanner($load_banner);
} elseif ($banner->_db_getList('id = '.$banner_id, 1)) {
  $banner_data=$banner->_db_list[0];
}

if (empty($banner_data)) {
  // No banners loaded
  header('Location: dummy.html');
} else {
  switch ($banner_data['source_type']) {

    case 'u':
      header('Location: '.PCPIN_FORMLINK.'?external_url='.urlencode($banner_data['source']));
      die();
    break;

    case 'c':
      header('Content-Type: text/html; charset=UTF-8');
      header('Expires: '.gmdate('D, d M Y H:i:s').' GMT');
      if (PCPIN_CLIENT_AGENT_NAME=='IE') {
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');
      }else{
        header('Pragma: no-cache');
      }
      header('Expires: '.gmdate('D, d M Y H:i:s', time()-86400).' GMT');
      echo $banner_data['source']; die();
    break;

  }
}

die();
?>