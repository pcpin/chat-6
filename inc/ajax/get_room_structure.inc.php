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


$invitations_arrived=0;
$messages_arrived=0;
$categories_xml='';

if (!empty($current_user->id)) {
  $message='OK';
  $status=0;

  // Get room structure
  _pcpin_loadClass('category'); $category=new PCPIN_Category($session);
  $categories=$category->getTree($current_user->id);
  makeCategoriesXML(array(0=>$categories[0]), 0);

  // Are there any invitations or abuse reports?
  if (!empty($current_user->id)) {
    _pcpin_loadClass('invitation'); $invitation=new PCPIN_Invitation($session);
    $invitations=$invitation->getNewInvitations($current_user->id, true);
    $invitations_arrived=(!empty($invitations))? 1 : 0;
    unset($invitations);
    _pcpin_loadClass('message'); $msg=new PCPIN_Message($session);
    $messages=$msg->getNewMessages($session->_s_user_id);
    if (!empty($messages)) {
      $messages_arrived=1;
    }
  }
}


echo '<?xml version="1.0" encoding="UTF-8"?>
<pcpin_xml>
<message>'.htmlspecialchars($message).'</message>
<status>'.htmlspecialchars($status).'</status>
  <categories>'.$categories_xml.'
  </categories>
  <additional_data>
    <new_invitations>'.htmlspecialchars($invitations_arrived).'</new_invitations>
    <new_messages>'.htmlspecialchars($messages_arrived).'</new_messages>
  </additional_data>
</pcpin_xml>';
die();

/**
 * Create category tree (recursively)
 * @param   array   $categories   Categories
 * @param   int     $depth        Current depth
 */
function makeCategoriesXML($categories, $depth) {
  global $categories_xml;
  global $current_user;
  global $l;
  $depth_pad='  '.str_pad('', $depth*2, ' ', STR_PAD_LEFT);
  foreach ($categories as $category_id=>$category_data) {
    $categories_xml.='
  '.$depth_pad.'<category>
  '.$depth_pad.'  <id>'.htmlspecialchars($category_id).'</id>
  '.$depth_pad.'  <parent_id>'.(is_null($category_data['parent_id'])? '' : htmlspecialchars($category_data['parent_id'])).'</parent_id>
  '.$depth_pad.'  <name>'.htmlspecialchars($category_data['name']).'</name>
  '.$depth_pad.'  <creatable_rooms>'.htmlspecialchars($category_data['creatable_rooms']).'</creatable_rooms>
  '.$depth_pad.'  <creatable_rooms_flag>'.htmlspecialchars($category_data['creatable_rooms_flag']).'</creatable_rooms_flag>
  '.$depth_pad.'  <description>'.htmlspecialchars($category_data['description']).'</description>';
    foreach ($category_data['rooms'] as $room_id=>$room_data) {
      $categories_xml.='
  '.$depth_pad.'  <room>
  '.$depth_pad.'    <id>'.htmlspecialchars($room_id).'</id>
  '.$depth_pad.'    <background_image>'.htmlspecialchars($room_data['background_image']).'</background_image>
  '.$depth_pad.'    <background_image_width>'.htmlspecialchars($room_data['background_image_width']).'</background_image_width>
  '.$depth_pad.'    <background_image_height>'.htmlspecialchars($room_data['background_image_height']).'</background_image_height>
  '.$depth_pad.'    <password_protected>'.htmlspecialchars($room_data['password_protected']).'</password_protected>
  '.$depth_pad.'    <moderated_by_me>'.htmlspecialchars((   $current_user->is_admin==='y'
                                                         || false!==strpos(','.$current_user->moderated_rooms.',', ','.$room_id.',')
                                                         )? '1' : '').'</moderated_by_me>
  '.$depth_pad.'    <name>'.htmlspecialchars($room_data['name']).'</name>
  '.$depth_pad.'    <description>'.htmlspecialchars($room_data['description']).'</description>
  '.$depth_pad.'    <default_message_color>'.htmlspecialchars($room_data['default_message_color']).'</default_message_color>';
      foreach ($room_data['users'] as $user_id=>$user_data) {
        if ($user_data['global_muted_until']>date('Y-m-d H:i:s')) {
          $global_muted='1';
          $global_muted_by=$user_data['global_muted_by'];
          $global_muted_by_username=$user_data['global_muted_by_username'];
          $global_muted_until=PCPIN_Common::datetimeToTimestamp($user_data['global_muted_until'])+$current_user->time_zone_offset-date('Z');
          $global_muted_reason=$user_data['global_muted_reason'];
        } elseif ($user_data['global_muted_permanently']=='y') {
          $global_muted='1';
          $global_muted_by=$user_data['global_muted_by'];
          $global_muted_by_username=$user_data['global_muted_by_username'];
          $global_muted_until='';
          $global_muted_reason=$user_data['global_muted_reason'];
        } else {
          $global_muted='';
          $global_muted_by='';
          $global_muted_by_username='';
          $global_muted_until='';
          $global_muted_reason='';
        }
        if ($user_data['online_status_message']=='') {
          $user_data['online_status_message']=$l->g('online_status_'.$user_data['online_status']);
        }
        $categories_xml.='
  '.$depth_pad.'    <user>
  '.$depth_pad.'      <id>'.htmlspecialchars($user_id).'</id>
  '.$depth_pad.'      <nickname>'.htmlspecialchars($user_data['nickname']).'</nickname>
  '.$depth_pad.'      <nickname_plain>'.htmlspecialchars($user_data['nickname_plain']).'</nickname_plain>
  '.$depth_pad.'      <online_status>'.htmlspecialchars($user_data['online_status']).'</online_status>
  '.$depth_pad.'      <online_status_message>'.htmlspecialchars($user_data['online_status_message']).'</online_status_message>
  '.$depth_pad.'      <muted_locally>'.htmlspecialchars((false!==strpos(','.$current_user->muted_users.',', ','.$user_id.','))? '1' : '0').'</muted_locally>
  '.$depth_pad.'      <global_muted>'.htmlspecialchars($global_muted).'</global_muted>
  '.$depth_pad.'      <global_muted_until>'.htmlspecialchars($global_muted_until).'</global_muted_until>
  '.$depth_pad.'      <ip_address>'.htmlspecialchars($current_user->is_admin==='y'? $user_data['ip_address'] : '').'</ip_address>
  '.$depth_pad.'      <gender>'.htmlspecialchars($user_data['gender']).'</gender>
  '.$depth_pad.'      <avatar_bid>'.htmlspecialchars($user_data['avatar_bid']).'</avatar_bid>
  '.$depth_pad.'      <is_admin>'.htmlspecialchars($user_data['is_admin']).'</is_admin>
  '.$depth_pad.'      <is_moderator>'.htmlspecialchars($user_data['is_moderator']).'</is_moderator>
  '.$depth_pad.'    </user>';
      }
    $categories_xml.='
  '.$depth_pad.'  </room>';
    }
    if (!empty($category_data['children'])) {
      $categories_xml.='
  '.$depth_pad.'  <categories>';
      $depth+=2;
      makeCategoriesXML($category_data['children'], $depth);
      $depth-=2;
      $categories_xml.='
  '.$depth_pad.'  </categories>';
    }
    $categories_xml.='
  '.$depth_pad.'</category>';
  }
}
?>