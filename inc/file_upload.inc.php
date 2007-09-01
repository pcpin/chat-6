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

if (empty($current_user->id)) {
  die();
}

$_window_title.=' '.PCPIN_WINDOW_TITLE_SEPARATOR.' '.$l->g('upload_file');

_pcpin_loadClass('message'); $msg=new PCPIN_Message($session);

if (empty($profile_user_id) || $current_user->is_admin!=='y') {
  $profile_user_id=$current_user->id;
}

if (!isset($f_target) || !is_scalar($f_target)) {
  $f_target='/dev/null';
}

// Validate uploaded file
if (isset($f_submitted)) {
  $upload_status=array('code'=>100, 'message'=>$l->g('file_upload_error'));
} else {
  $upload_status=null;
}
$binaryfile_id=0;
$width=0;
$height=0;
$filename='';

if (!empty($f_data) && is_array($f_data) && isset($f_data['error']) && isset($f_data['tmp_name']) && isset($f_data['size'])) {
  $filename=$f_data['name'];
  $upload_status=array('code'=>10, 'message'=>$l->g('error'));
  if ($f_data['error']==UPLOAD_ERR_NO_FILE || $f_data['error']==UPLOAD_ERR_OK && empty($f_data['size'])) {
    // No file was uploaded or file is empty
    $upload_status=array('code'=>-1, 'message'=>$l->g('file_upload_error'));
  } elseif ($f_data['error']!=UPLOAD_ERR_OK) {
    // File upload error
    $upload_status=array('code'=>100, 'message'=>$l->g('file_upload_error'));
  } else {

    switch ($f_target) {

      case 'avatar': // New Avatar
        // Check avatars number limit
        _pcpin_loadClass('avatar'); $avatar=new PCPIN_Avatar($session);
        $avatar->_db_getList('COUNT', 'user_id = '.$profile_user_id);
        if ($avatar->_db_list_count>=$session->_conf_all['avatars_max_count']) {
          // Limit reached
          $upload_status=array('code'=>10, 'message'=>str_replace('[NUMBER]', $session->_conf_all['avatars_max_count'], $l->g('avatars_limit_reached')));
        } else {
          // Check image data
          $img_data=null;
          switch (PCPIN_Image::checkImage($img_data,
                                          $f_data['tmp_name'],
                                          $session->_conf_all['avatar_image_types'],
                                          $session->_conf_all['avatar_max_width'],
                                          $session->_conf_all['avatar_max_height'],
                                          $session->_conf_all['avatar_max_filesize'],
                                          false)) {

            case  PCPIN_IMAGE_CHECK_OK: // Image OK
              $upload_status=array('code'=>0, 'message'=>$l->g('avatar_uploaded'));
            break;

            case PCPIN_IMAGE_CHECK_ERROR_FILE: // File does not exists / not readable
              $upload_status=array('code'=>100, 'message'=>$l->g('file_upload_error'));
            break;

            case PCPIN_IMAGE_CHECK_ERROR_MIME: // MIME not allowed
            case PCPIN_IMAGE_CHECK_ERROR_NOT_IMAGE: // File is not an image or has incompatible format
              $upload_status=array('code'=>200, 'message'=>$l->g('image_type_not_allowed'));
            break;

            case PCPIN_IMAGE_CHECK_ERROR_WIDTH: // Image width larger than allowed
            case PCPIN_IMAGE_CHECK_ERROR_HEIGHT: // Image height larger than allowed
              $upload_status=array('code'=>300, 'message'=>str_replace('[WIDTH]', $session->_conf_all['avatar_max_width'], str_replace('[HEIGHT]', $session->_conf_all['avatar_max_height'], $l->g('image_too_large'))));
            break;

            case PCPIN_IMAGE_CHECK_ERROR_FILESIZE: // Image file size larger than allowed
              $upload_status=array('code'=>400, 'message'=>str_replace('[SIZE]', $session->_conf_all['avatar_max_filesize'], $l->g('file_too_large')));
            break;

          }
        }
        if ($upload_status['code']===0) {
          // Image OK
          $width=$img_data['width'];
          $height=$img_data['height'];
          _pcpin_loadClass('binaryfile'); $binaryfile=new PCPIN_BinaryFile($session);
          if ($binaryfile->newBinaryFile(file_get_contents($f_data['tmp_name']), $img_data['mime'], $width, $height, 'log')) {
            if (!empty($binaryfile->id)) {
              $binaryfile_id=$binaryfile->id;
              $avatar->addAvatar($binaryfile->id, $profile_user_id);
            }
          }
          $msg->addMessage(1010, 'n', 0, '', $session->_s_room_id, 0, $profile_user_id);
        }
      break;

      case 'avatar_gallery_image': // New Avatar for Gallery
        if ($current_user->is_admin!=='y') {
          break;
        }
        // Check image data
        $img_data=null;
        switch (PCPIN_Image::checkImage($img_data,
                                        $f_data['tmp_name'],
                                        $session->_conf_all['avatar_image_types'],
                                        $session->_conf_all['avatar_max_width'],
                                        $session->_conf_all['avatar_max_height'],
                                        $session->_conf_all['avatar_max_filesize'],
                                        false)) {

          case  PCPIN_IMAGE_CHECK_OK: // Image OK
            $upload_status=array('code'=>0, 'message'=>$l->g('avatar_uploaded'));
          break;

          case PCPIN_IMAGE_CHECK_ERROR_FILE: // File does not exists / not readable
            $upload_status=array('code'=>100, 'message'=>$l->g('file_upload_error'));
          break;

          case PCPIN_IMAGE_CHECK_ERROR_MIME: // MIME not allowed
          case PCPIN_IMAGE_CHECK_ERROR_NOT_IMAGE: // File is not an image or has incompatible format
            $upload_status=array('code'=>200, 'message'=>$l->g('image_type_not_allowed'));
          break;

          case PCPIN_IMAGE_CHECK_ERROR_WIDTH: // Image width larger than allowed
          case PCPIN_IMAGE_CHECK_ERROR_HEIGHT: // Image height larger than allowed
            $upload_status=array('code'=>300, 'message'=>str_replace('[WIDTH]', $session->_conf_all['avatar_max_width'], str_replace('[HEIGHT]', $session->_conf_all['avatar_max_height'], $l->g('image_too_large'))));
          break;

          case PCPIN_IMAGE_CHECK_ERROR_FILESIZE: // Image file size larger than allowed
            $upload_status=array('code'=>400, 'message'=>str_replace('[SIZE]', $session->_conf_all['avatar_max_filesize'], $l->g('file_too_large')));
          break;

        }
        if ($upload_status['code']===0) {
          // Image OK
          $width=$img_data['width'];
          $height=$img_data['height'];
          _pcpin_loadClass('binaryfile'); $binaryfile=new PCPIN_BinaryFile($session);
          if ($binaryfile->newBinaryFile(file_get_contents($f_data['tmp_name']), $img_data['mime'], $width, $height, '')) {
            $binaryfile_id=$binaryfile->id;
            if (!empty($binaryfile->id)) {
              _pcpin_loadClass('tmpdata'); $tmpdata=new PCPIN_TmpData($session);
              $tmpdata->_db_deleteRowMultiCond(array('user_id'=>$current_user->id, 'type'=>4));
              $tmpdata->addRecord(4, $current_user->id, $binaryfile_id, $filename);
            }
          }
        }
      break;

      case 'language_file': // Language file
        if ($current_user->is_admin!=='y') {
          break;
        }
        $language_id=0;
        $l2=new PCPIN_Language($session);
        $import_status=$l2->importLanguage(file_get_contents($f_data['tmp_name']), $language_id);
        unset($l2);
        if ($import_status==0 && $language_id>0) {
          // Language imported
          $l->_db_getList('name,local_name', 'id = '.$language_id, 1);
          $upload_status=array('code'=>0, 'message'=>str_replace('[NAME]', $l->_db_list[0]['name'].' ('.$l->_db_list[0]['local_name'].')', $l->g('language_import_success')));
          $l->_db_freeList();
        } else {
          // Invalid language file
          switch ($import_status) {

            case 10 :
            default  :
              $upload_status=array('code'=>1000, 'message'=>$l->g('invalid_language_file'));
            break;

            case 100 :
              $l->_db_getList('name', 'id = '.$language_id, 1);
              $upload_status=array('code'=>1000, 'message'=>str_replace('[NAME]', $l->_db_list[0]['name'], $l->g('language_already_exists')));
              $l->_db_freeList();
            break;

          }
        }
      break;

      case 'msg_attachment': // Message attachment
        $msg_attachments_limit=$session->_conf_all['msg_attachments_limit'];
        if (empty($session->_s_room_id)) {
          // User is not in room
          $upload_status=array('code'=>100, 'message'=>$l->g('file_upload_error'));
        } elseif (!file_exists($f_data['tmp_name']) || !is_file($f_data['tmp_name']) || !is_readable($f_data['tmp_name'])) {
          // File upload error
          $upload_status=array('code'=>100, 'message'=>$l->g('file_upload_error'));
        } elseif (filesize($f_data['tmp_name'])>$session->_conf_all['msg_attachments_maxsize']*1024) {
          // File too large
          $upload_status=array('code'=>400, 'message'=>str_replace('[SIZE]', $session->_conf_all['msg_attachments_maxsize']*1024, $l->g('file_too_large')));
        } else {
          // Check attachments limit
          _pcpin_loadClass('tmpdata'); $tmpdata=new PCPIN_TmpData($session);
          $tmpdata->_db_getList('COUNT', 'type = 3', 'user_id = '.$session->_s_user_id);
          if ($tmpdata->_db_list_count>=$msg_attachments_limit) {
            // Max attachments limit reached
            $upload_status=array('code'=>100, 'message'=>$l->g('file_upload_error'));
          } else {
            $upload_status=array('code'=>0, 'message'=>'OK');
          }
        }
        if ($upload_status['code']===0) {
          // Get MIME type
          $mime_type=$f_data['type']; // TODO: detect real MIME type
          _pcpin_loadClass('binaryfile'); $binaryfile=new PCPIN_BinaryFile($session);
          if ($binaryfile->newBinaryFile(file_get_contents($f_data['tmp_name']), $mime_type, 0, 0, 'room|'.$session->_s_room_id)) {
            $binaryfile_id=$binaryfile->id;
            if (!empty($binaryfile->id)) {
              _pcpin_loadClass('tmpdata'); $tmpdata=new PCPIN_TmpData($session);
              $tmpdata->addRecord(3, $current_user->id, $binaryfile_id, $filename);
            }
          }
        }
      break;

      case 'room_image': // New room image
        // Room image will be saved into tmpdata table
        // Check image data
        $img_data=null;
        switch (PCPIN_Image::checkImage($img_data,
                                        $f_data['tmp_name'],
                                        $session->_conf_all['room_img_image_types'],
                                        $session->_conf_all['room_img_max_width'],
                                        $session->_conf_all['room_img_max_height'],
                                        $session->_conf_all['room_img_max_filesize'],
                                        false)) {

          case  PCPIN_IMAGE_CHECK_OK: // Image OK
            $upload_status=array('code'=>0, 'message'=>'OK');
          break;

          case PCPIN_IMAGE_CHECK_ERROR_FILE: // File does not exists / not readable
            $upload_status=array('code'=>100, 'message'=>$l->g('file_upload_error'));
          break;

          case PCPIN_IMAGE_CHECK_ERROR_MIME: // MIME not allowed
          case PCPIN_IMAGE_CHECK_ERROR_NOT_IMAGE: // File is not an image or has incompatible format
            $upload_status=array('code'=>200, 'message'=>$l->g('image_type_not_allowed'));
            break;

          case PCPIN_IMAGE_CHECK_ERROR_WIDTH: // Image width larger than allowed
          case PCPIN_IMAGE_CHECK_ERROR_HEIGHT: // Image height larger than allowed
            $upload_status=array('code'=>300, 'message'=>str_replace('[WIDTH]', $session->_conf_all['room_img_max_width'], str_replace('[HEIGHT]', $session->_conf_all['room_img_max_height'], $l->g('image_too_large'))));
          break;

          case PCPIN_IMAGE_CHECK_ERROR_FILESIZE: // Image file size larger than allowed
            $upload_status=array('code'=>400, 'message'=>str_replace('[SIZE]', $session->_conf_all['room_img_max_filesize'], $l->g('file_too_large')));
          break;

        }
        if ($upload_status['code']===0) {
          // Image OK
          $width=$img_data['width'];
          $height=$img_data['height'];
          _pcpin_loadClass('binaryfile'); $binaryfile=new PCPIN_BinaryFile($session);
          if ($binaryfile->newBinaryFile(file_get_contents($f_data['tmp_name']), $img_data['mime'], $width, $height, 'log')) {
            $binaryfile_id=$binaryfile->id;
            if (!empty($binaryfile->id)) {
              _pcpin_loadClass('tmpdata'); $tmpdata=new PCPIN_TmpData($session);
              $tmpdata->deleteUserRecords($current_user->id, 1);
              $tmpdata->addRecord(1, $current_user->id, $binaryfile_id, $filename);
            }
          }
        }
      break;

      case 'smilie_image': // New smilie image
        if ($current_user->is_admin!=='y') {
          break;
        }
        // Smilie image will be saved into tmpdata table
        // Check image data
        $img_data=null;
        switch (PCPIN_Image::checkImage($img_data,
                                        $f_data['tmp_name'],
                                        '',
                                        0,
                                        0,
                                        0,
                                        false)) {

          case  PCPIN_IMAGE_CHECK_OK: // Image OK
            $upload_status=array('code'=>0, 'message'=>'OK');
          break;

          case PCPIN_IMAGE_CHECK_ERROR_FILE: // File does not exists / not readable
            $upload_status=array('code'=>100, 'message'=>$l->g('file_upload_error'));
          break;

          case PCPIN_IMAGE_CHECK_ERROR_MIME: // MIME not allowed
          case PCPIN_IMAGE_CHECK_ERROR_NOT_IMAGE: // File is not an image or has incompatible format
            $upload_status=array('code'=>200, 'message'=>$l->g('image_type_not_allowed'));
          break;

        }
        if ($upload_status['code']===0) {
          // Image OK
          $width=$img_data['width'];
          $height=$img_data['height'];
          _pcpin_loadClass('binaryfile'); $binaryfile=new PCPIN_BinaryFile($session);
          if ($binaryfile->newBinaryFile(file_get_contents($f_data['tmp_name']), $img_data['mime'], $width, $height, '')) {
            $binaryfile_id=$binaryfile->id;
            if (!empty($binaryfile->id)) {
              _pcpin_loadClass('tmpdata'); $tmpdata=new PCPIN_TmpData($session);
              $tmpdata->_db_deleteRowMultiCond(array('user_id'=>$current_user->id, 'type'=>2));
              $tmpdata->addRecord(2, $current_user->id, $binaryfile_id, $filename);
            }
          }
        }
      break;

    }
  }
}

_pcpin_loadClass('pcpintpl'); $tpl=new PcpinTpl();
$tpl->setBasedir('./tpl');
$tpl->readTemplatesFromFile('./file_upload.tpl');

// JS files
$_js_files[]='./js/file_upload.js';

if (!empty($upload_status)) {
  $message=str_replace('\'', '\\\'', htmlspecialchars($upload_status['message']));
  $message=str_replace("\n", '\\n', str_replace("\r", '\\r', $message));
  $_body_onload[]='parseUploadResponse('.$upload_status['code'].', \''.$message.'\', '.$binaryfile_id.', '.$width.', '.$height.', \''.str_replace('\'', '\\\'', $filename).'\')';
} else {
  $_body_onload[]='initUploadForm(\''.$f_target.'\')';
}

// Add global vars to template
foreach ($global_tpl_vars as $key=>$val) {
  $tpl->addGlobalVar($key, htmlspecialchars($val));
}

// Add language expressions to template
foreach ($tpl->tpl_vars_plain as $var) {
  if (0===strpos($var, 'LNG_')) {
    $var=strtolower($var);
    $tpl->addGlobalVar($var, htmlspecialchars($l->g(substr($var, 4))));
  }
}

$tpl->addVar('main', 'profile_user_id', htmlspecialchars($profile_user_id));
?>