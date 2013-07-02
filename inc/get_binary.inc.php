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
 * Load and output a binary file
 * @param   int   $b_id     ID of the binary file
 * @param   int   $b_x      If file is an image: Desired image width. If empty, image will be not resized.
 * @param   int   $b_y      If file is an image: Desired image height. If empty, image will be not resized.
 * @param   int   $bg_r     If file is an image and will be resized: Desired red component of image background (0..255). Default value is taken from configuration.
 * @param   int   $bg_g     If file is an image and will be resized: Desired green component of image background (0..255). Default value is taken from configuration.
 * @param   int   $bg_b     If file is an image and will be resized: Desired blue component of image background (0..255). Default value is taken from configuration.
 */

// Use cache (for future purposes)
$cache_expires=31536000; // Cache: 365 days
_pcpin_loadClass('binaryfile'); $binaryfile=new PCPIN_BinaryFile($session);
if (!empty($b_id) && is_scalar($b_id) && $binaryfile->_db_getList('protected, mime_type, size, body', 'id = '.$b_id, 1)) {
  if ($binaryfile->_db_list[0]['protected']!='') {
    // Binaryfile is protected
    $protection_parts=explode('/', $binaryfile->_db_list[0]['protected']);
    foreach ($protection_parts as $part) {
      switch ($part) {

        case 'log':
          if (empty($current_user->id)) {
            die();
          }
        break;

        case 'reg':
          if ($current_user->is_guest=='y') {
            die();
          }
        break;

        case 'room':
          if (empty($session->_s_room_id) || $session->_s_room_id!=substr($part, strpos('|')+1)) {
            die();
          }
        break;

        case 'user':
          if (empty($current_user->id) || $current_user->id!=substr($part, strpos('|')+1)) {
            die();
          }
        break;

      }
    }
  }
  header('Expires: '.gmdate('D, d M Y H:i:s', time()+$cache_expires).' GMT');
  if (PCPIN_CLIENT_AGENT_NAME=='IE') {
    header('Cache-Control: Public');
    header('Pragma: Public');
  } else {
    header('Pragma: Public');
  }
  $thumb_loaded=false;
  if (   true===$session->_conf_all['allow_gd']
      && !empty($b_x) && pcpin_ctype_digit($b_x)
      && !empty($b_y) && pcpin_ctype_digit($b_y)
      ) {
    // Thumbnail
    if (   !isset($bg_r) || !pcpin_ctype_digit($bg_r) || $bg_r<0 || $bg_r>255
        || !isset($bg_g) || !pcpin_ctype_digit($bg_g) || $bg_g<0 || $bg_g>255
        || !isset($bg_b) || !pcpin_ctype_digit($bg_b) || $bg_b<0 || $bg_b>255
        ) {
      $bg_r=hexdec(substr($session->_conf_all['thumb_background'], 0, 2));
      $bg_g=hexdec(substr($session->_conf_all['thumb_background'], 2, 2));
      $bg_b=hexdec(substr($session->_conf_all['thumb_background'], 4, 2));
    }
    $thumb_img='';
    if (PCPIN_Image::makeThumb($thumb_img,
                                null,
                                null,
                                $binaryfile->_db_list[0]['body'],
                                $b_y,
                                $b_x,
                                'jpg',
                                $bg_r,
                                $bg_g,
                                $bg_b)
      ) {
      $thumb_loaded=true;
      header('Content-type: image/jpeg');
      $etag = md5($thumb_img);
      header('Etag: '.$etag);
      if (isset($_SERVER['HTTP_IF_NONE_MATCH']) && $_SERVER['HTTP_IF_NONE_MATCH'] === $etag) {
        header('HTTP/1.1 304 Not Modified');
      } else {
        echo $thumb_img;
      }
    }
  }
  if (!$thumb_loaded) {
    if (!empty($filename)) {
      header('Content-Disposition: inline; filename="'.$filename.'"');
    }
    header('Content-type: '.$binaryfile->_db_list[0]['mime_type']);
    $etag = md5($binaryfile->_db_list[0]['body']);
    header('Etag: '.$etag);
    if (isset($_SERVER['HTTP_IF_NONE_MATCH']) && $_SERVER['HTTP_IF_NONE_MATCH'] === $etag) {
      header('HTTP/1.1 304 Not Modified');
    } else {
      echo $binaryfile->_db_list[0]['body'];
    }
  }
}
// No HTML output allowed!
die();
?>