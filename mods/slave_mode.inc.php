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

if (function_exists('debug_backtrace')) {
  $_pcpin_dbt=debug_backtrace();
  if (is_array($_pcpin_dbt) && empty($_pcpin_dbt[0])) {
    die('Access denied');
  }
  unset($_pcpin_dbt);
}

_pcpin_loadClass('binaryfile'); $binaryfile=new PCPIN_BinaryFile($session);
_pcpin_loadClass('avatar'); $avatar=new PCPIN_Avatar($session);
_pcpin_loadClass('room'); $room=new PCPIN_Room($session);
_pcpin_loadClass('category'); $category=new PCPIN_Category($session);

if (PCPIN_SLAVE_MODE && !empty($_pcpin_slave_userdata) && !empty($session) && is_object($session)) {
  $language_id=$_pcpin_slave_userdata['language'];
  if ($_pcpin_slave_userdata['is_guest']!=='n') {
    // User is guest
    $guest_login=1;
  } else {
    // Registered user
    // Check user
    if ($current_user->_db_getList('login = '.$_pcpin_slave_userdata['login'], 1)) {
      // User exists
      $current_user_set=$current_user->_db_list[0];
      $current_user->_db_freeList();
      // Check wether user already logged in or not
      if ($session->_db_getList('_s_user_id = '.$current_user_set['id'], '_s_online_status != 3', 1)) {
        // User already logged in
        PCPIN_Common::dieWithError(1, $l->g('you_already_logged_in'));
      } else {
        // User is not logged in yet
        $login=$current_user_set['login'];
        $_pcpin_slave_userdata_md5_password=$_pcpin_slave_userdata['password'];
        // Update user main data
        $update_args=array();
        foreach ($_pcpin_slave_userdata as $key=>$val) {
          if (!is_null($val) && isset($current_user_set[$key]) && $current_user_set[$key]!=$val) {
            $update_args[$key]=$val;
          }
        }
        // Moderator?
        $update_args['moderated_rooms']='';
        $update_args['moderated_categories']='';
        if ($_pcpin_slave_userdata['is_moderator']==='y') {
          if ($room->_db_getList('id', 'id ASC')) {
            foreach ($room->_db_list as $data) {
              $update_args['moderated_rooms'].=$data['id'].',';
            }
            $room->_db_freeList();
            $update_args['moderated_rooms']=trim($update_args['moderated_rooms'], ',');
          }
          if ($category->_db_getList('id', 'id ASC')) {
            foreach ($category->_db_list as $data) {
              $update_args['moderated_categories'].=$data['id'].',';
            }
            $category->_db_freeList();
            $update_args['moderated_categories']=trim($update_args['moderated_categories'], ',');
          }
        }
        $current_user->_db_updateRow($current_user_set['id'], 'id', $update_args);
        // Update additional userdata
        $current_userdata->_db_getList('user_id = '.$current_user_set['id'], 1);
        $current_userdata_set=$current_userdata->_db_list[0];
        $current_userdata->_db_freeList();
        $update_args=array();
        foreach ($_pcpin_slave_userdata as $key=>$val) {
          if (!is_null($val) && isset($current_userdata_set[$key]) && $current_userdata_set[$key]!=$val) {
            $update_args[$key]=$val;
          }
        }
        if (!empty($update_args)) {
          $current_userdata->_db_updateRow($current_user_set['id'], 'user_id', $update_args);
        }
        // Avatar
        $avatar->deleteAvatar($current_user_set['id']);
        if (!empty($_pcpin_slave_userdata['avatar'])) {
          $new_avatar_data=null;
          if (PCPIN_IMAGE_CHECK_OK===PCPIN_Image::checkImage($new_avatar_data, $_pcpin_slave_userdata['avatar'], $session->_conf_all['avatar_image_types'], 0, 0, 0, true)) {
            if ($binaryfile->newBinaryFile(file_get_contents($_pcpin_slave_userdata['avatar']), $new_avatar_data['mime'], $new_avatar_data['width'], $new_avatar_data['height'], 'log')) {
              $avatar->addAvatar($binaryfile->id, $current_user_set['id']);
            }
          }
        }
      }
    } else {
      // User not exists yet
      $login=$_pcpin_slave_userdata['login'];
      // Create new user
      $current_user->newUser($_pcpin_slave_userdata['login'],
                             PCPIN_Common::randomString(32),
                             $_pcpin_slave_userdata['email'],
                             $_pcpin_slave_userdata['hide_email'],
                             'n',
                             ''
                             );
      $current_user->password=$_pcpin_slave_userdata['password'];
      $_pcpin_slave_userdata_md5_password=$_pcpin_slave_userdata['password'];
      $current_user->_db_updateObj($current_user->id);
      // Userdata
      $current_userdata->_db_getList('user_id = '.$current_user->id, 1);
      $current_userdata_set=$current_userdata->_db_list[0];
      $current_userdata->_db_freeList();
      $update_args=array();
      foreach ($_pcpin_slave_userdata as $key=>$val) {
        if (!is_null($val) && isset($current_userdata_set[$key]) && $current_userdata_set[$key]!=$val) {
          $update_args[$key]=$val;
        }
      }
      if (!empty($update_args)) {
        $current_userdata->_db_updateRow($current_user->id, 'user_id', $update_args);
      }
      // Avatar
      if (!empty($_pcpin_slave_userdata['avatar'])) {
        $new_avatar_data=null;
        if (PCPIN_IMAGE_CHECK_OK===PCPIN_Image::checkImage($new_avatar_data, $_pcpin_slave_userdata['avatar'], $session->_conf_all['avatar_image_types'], 0, 0, 0, true)) {
          if ($binaryfile->newBinaryFile(file_get_contents($_pcpin_slave_userdata['avatar']), $new_avatar_data['mime'], $new_avatar_data['width'], $new_avatar_data['height'], 'log')) {
            $avatar->addAvatar($binaryfile->id, $current_user->id);
          }
        }
      }
    }
  }
  // Log user in
  require('./inc/ajax/do_login.inc.php');
  if ($status==0) {
    header('Location: '.PCPIN_FORMLINK.'?s_id='.urlencode($session->_s_id));
    die();
  }
  
}

?>