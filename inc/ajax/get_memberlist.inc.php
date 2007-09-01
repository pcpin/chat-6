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
 * Get memberlist
 * @param   string    $nickname             If specified, the search will be performed
 * @param   int       $sort_by              Sort by... Values: see $user->getMemberlist()
 * @param   int       $sort_dir             Sort direction. Values: see $user->getMemberlist()
 * @param   int       $page_nr              Page number.
 * @param   boolean   $banned_only          Optional. If TRUE, then only banned users will be listed
 * @param   boolean   $muted_only           Optional. If TRUE, then only muted users will be listed
 * @param   boolean   $moderators_only      Optional. If TRUE, then only moderators will be listed
 * @param   boolean   $admins_only          Optional. If TRUE, then only admins will be listed
 * @param   boolean   $not_activated_only   Optional. If TRUE, then only not activated user accounts will be listed
 */
$message=$l->g('access_denied');
$status='-1'; // -1: Session is invalid

_pcpin_loadClass('room'); $room=new PCPIN_Room($session);
_pcpin_loadClass('category'); $category=new PCPIN_Category($session);

if (!isset($nickname) || !is_scalar($nickname)) $nickname='';
if (!isset($sort_by)  || !pcpin_ctype_digit($sort_by)) $sort_by=0;
if (!isset($sort_dir) || !pcpin_ctype_digit($sort_dir)) $sort_dir=0;
if (!isset($page) || !pcpin_ctype_digit($page)) $page=0;
if (!isset($banned_only) || $current_user->is_admin!=='y') $banned_only=false;
if (!isset($muted_only) || $current_user->is_admin!=='y') $muted_only=false;
if (!isset($moderators_only) || $current_user->is_admin!=='y') $moderators_only=false;
if (!isset($admins_only) || $current_user->is_admin!=='y') $admins_only=false;
if (!isset($not_activated_only) || $current_user->is_admin!=='y') $not_activated_only=false;

$members_xml='';
$total_members_count=0;

if (is_object($session) && !empty($current_user->id)) {
  $message='OK';
  $status=0;

  $nickname=trim($nickname);

  // Get total members (respective search query)
  $total_members_count=$current_user->getMemberlist(true, 0, 0, 0, 0, $nickname, !empty($banned_only), !empty($muted_only), !empty($moderators_only), !empty($admins_only), !empty($not_activated_only));

  $total_pages=ceil($total_members_count/$session->_conf_all['memberlist_page_records']);
  if (empty($page) || !pcpin_ctype_digit($page)) {
    $page=1;
  } elseif ($page>$total_pages && $total_pages>0) {
    $page=$total_pages;
  }

  // Get memberlist
  $limitstart=$session->_conf_all['memberlist_page_records']*($page-1);
  $members=$current_user->getMemberlist(false, $limitstart, $session->_conf_all['memberlist_page_records'], $sort_by, $sort_dir, $nickname, !empty($banned_only), !empty($muted_only), !empty($moderators_only), !empty($admins_only), !empty($not_activated_only));
  $members_count=count($members);

  // Create XML
  foreach ($members as $member) {
    $moderated_rooms='';
    $moderated_categories='';
    $room_ids=array();
    if (!empty($moderators_only)) {
      // Get moderated categories
      if (!empty($member['moderated_categories']) && $category->_db_getList('name', 'id IN '.$member['moderated_categories'], 'name ASC')) {
        foreach ($category->_db_list as $category_data) {
          $moderated_categories.='      <moderated_category>'.htmlspecialchars($category_data['name']).'</moderated_category>'."\n";
        }
        $category->_db_freeList();
        if ($room->_db_getList('id', 'category_id IN '.$member['moderated_categories'])) {
          foreach ($room->_db_list as $room_data) {
            $room_ids[]=$room_data['id'];
          }
          $room->_db_freeList();
        }
      }
      // Get moderated rooms
      if (!empty($member['moderated_rooms']) && $room->_db_getList('id', 'id IN '.$member['moderated_rooms'])) {
        foreach ($room->_db_list as $room_data) {
          $room_ids[]=$room_data['id'];
        }
        $room->_db_freeList();
      }
      $room_ids=array_unique($room_ids);
      if (!empty($room_ids) && $room->_db_getList('name', 'id IN '.implode(',', $room_ids), 'name ASC')) {
        foreach ($room->_db_list as $room_data) {
          $moderated_rooms.='      <moderated_room>'.htmlspecialchars($room_data['name']).'</moderated_room>'."\n";
        }
        $room->_db_freeList();
      }
    }
    $members_xml.='    <member>'."\n";
    foreach ($member as $key=>$val) {
      $members_xml.='      <'.$key.'>'.htmlspecialchars($val).'</'.$key.'>'."\n";
    }
    $members_xml.=$moderated_categories;
    $members_xml.=$moderated_rooms;
    $members_xml.='    </member>'."\n";
  }
}


echo '<?xml version="1.0" encoding="UTF-8"?>
<pcpin_xml>
  <message>'.htmlspecialchars($message).'</message>
  <status>'.htmlspecialchars($status).'</status>
  <total_members>'.htmlspecialchars($total_members_count).'</total_members>
  <page>'.htmlspecialchars($page).'</page>
  <total_pages>'.htmlspecialchars($total_pages).'</total_pages>
  <members>
'.$members_xml.'  </members>
</pcpin_xml>';
die();
?>