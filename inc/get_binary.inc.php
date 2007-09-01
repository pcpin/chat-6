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

// Use cache (for future purposes)
$cache_expires=31536000; // Cache: 365 days
_pcpin_loadClass('binaryfile'); $binaryfile=new PCPIN_BinaryFile($session);
if (!empty($b_id) && is_scalar($b_id) && $binaryfile->_db_getList('protected, mime_type, size, body', 'id = '.$b_id, 1)) {
  if ($binaryfile->_db_list[0]['protected']!='') {
    // Binaryfile is protected
    // Get userdata
    _pcpin_loadClass('user'); $current_user=new PCPIN_User($session);
    if (!empty($session->_s_user_id)) {
      $current_user->_db_loadObj($session->_s_user_id);
    }
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
  if ($cache_expires>0) {
    // Cache allowed
    if (PCPIN_CLIENT_AGENT_NAME=='IE') {
      header('Cache-Control: Public');
      header('Pragma: Public');
    } else {
      header('Pragma: Public');
    }
  } else {
    // Cache not allowed
    if (PCPIN_CLIENT_AGENT_NAME=='IE') {
      header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
      header('Pragma: No-cache');
    } else {
      header('Pragma: no-cache');
    }
  }
  $thumb_img='';
  if (   true===$session->_conf_all['allow_gd']
      && !empty($b_x) && pcpin_ctype_digit($b_x)
      && !empty($b_y) && pcpin_ctype_digit($b_y)
      && PCPIN_Image::makeThumb($thumb_img,
                                null,
                                null,
                                $binaryfile->_db_list[0]['body'],
                                $b_y,
                                $b_x,
                                'jpg',
                                hexdec(substr($session->_conf_all['thumb_background'], 0, 2)),
                                hexdec(substr($session->_conf_all['thumb_background'], 2, 2)),
                                hexdec(substr($session->_conf_all['thumb_background'], 4, 2)))) {
    // Thumbnail
    header('Content-type: image/jpeg');
    header('Content-Length: '.strlen($thumb_img));
    echo $thumb_img;
  } else {
    header('Content-type: '.$binaryfile->_db_list[0]['mime_type']);
    header('Content-Length: '.$binaryfile->_db_list[0]['size']);
    echo $binaryfile->_db_list[0]['body'];
  }
}
// No HTML output allowed!
die();
?>