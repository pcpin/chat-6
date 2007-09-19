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

$message=$l->g('access_denied');
$status='-1'; // -1: Session is invalid

_pcpin_loadClass('nickname'); $nickname=new PCPIN_Nickname($session);
_pcpin_loadClass('avatar'); $avatar=new PCPIN_Avatar($session);

if (!isset($user_id)) $user_id=0;
$profile_data_xml='';


// Get user data
$current_user->_db_getList('email, hide_email, joined, time_online, global_muted_until, global_muted_permanently, is_guest', 'id = '.$user_id, 1);

if (is_object($session) && !empty($current_user->_db_list)) {
  $message='OK';
  $status=0;
  $current_userdata->_db_getList('user_id = '.$user_id, 1);

  $userdata=array('hide_email'=>!empty($current_user->_db_list[0]['hide_email']),
                  'email'=>empty($current_user->_db_list[0]['hide_email'])? $current_user->_db_list[0]['email'] : $l->g('hidden'),
                  'registration_date'=>PCPIN_Common::datetimeToTimestamp($current_user->_db_list[0]['joined']),
                  'time_online'=>$current_user->_db_list[0]['time_online'],
                  'homepage'=>$current_userdata->_db_list[0]['homepage'],
                  'gender'=>$current_userdata->_db_list[0]['gender'],
                  'age'=>$current_userdata->_db_list[0]['age'],
                  'icq'=>$current_userdata->_db_list[0]['icq'],
                  'msn'=>$current_userdata->_db_list[0]['msn'],
                  'aim'=>$current_userdata->_db_list[0]['aim'],
                  'yim'=>$current_userdata->_db_list[0]['yim'],
                  'location'=>$current_userdata->_db_list[0]['location'],
                  'occupation'=>$current_userdata->_db_list[0]['occupation'],
                  'interests'=>$current_userdata->_db_list[0]['interests'],
                  'avatars'=>$avatar->getAvatars($user_id),
                  'online_status'=>-1,
                  'online_status_message'=>'',
                  'nickname'=>$nickname->getDefaultNickname($user_id),
                  'header'=>'',
                  'invitable'=>0,
                  'muted_locally'=>(false!==strpos(','.$current_user->muted_users.',', ','.$user_id.','))? '1' : '0',
                  'global_muted'=>$current_user->_db_list[0]['global_muted_until']>date('Y-m-d H:i:s') || $current_user->_db_list[0]['global_muted_permanently']=='y',
                  'global_muted_until'=>PCPIN_Common::datetimeToTimestamp($current_user->_db_list[0]['global_muted_until']),
                  'ip_address'=>'',
                  'is_guest'=>$current_user->_db_list[0]['is_guest'],
                  );
  $current_user->_db_freeList();
  $userdata['header']=str_replace('[USER]', $nickname->coloredToPlain($userdata['nickname'], false), $l->g('users_profile'));
  // Get online status
  if ($session->_db_getList('_s_created, _s_online_status, _s_online_status_message, _s_room_id, _s_ip', '_s_user_id = '.$user_id, 1)) {
    // User is online
    $userdata['time_online']=$current_user->calculateOnlineTime($user_id);
    $userdata['online_status']=$session->_db_list[0]['_s_online_status'];
    $userdata['online_status_message']=$session->_db_list[0]['_s_online_status_message'];
    if (!empty($session->_s_room_id) && $session->_db_list[0]['_s_room_id']!=$session->_s_room_id) {
      $userdata['invitable']=1;
    }
    if ($current_user->is_admin==='y') {
      $userdata['ip_address']=$session->_db_list[0]['_s_ip'];
    }
    $session->_db_freeList();
  }

  // Create XML
  foreach ($userdata as $key=>$val) {
    if ($key=='avatars') {
      foreach ($val as $avatar_data) {
        $profile_data_xml.='    <avatar>
      <id>'.htmlspecialchars($avatar_data['id']).'</id>
      <binaryfile_id>'.htmlspecialchars($avatar_data['binaryfile_id']).'</binaryfile_id>
      <width>'.htmlspecialchars($avatar_data['width']).'</width>
      <height>'.htmlspecialchars($avatar_data['height']).'</height>
    </avatar>
';
      }
    } elseif (is_scalar($val)) {
      $profile_data_xml.='    <'.$key.'>'.htmlspecialchars($val).'</'.$key.'>'."\n";
    }
  }
}


echo '<?xml version="1.0" encoding="UTF-8"?>
<pcpin_xml>
  <message>'.htmlspecialchars($message).'</message>
  <status>'.htmlspecialchars($status).'</status>
  <profile_data>
'.$profile_data_xml.'
  </profile_data>
</pcpin_xml>';
die();
?>