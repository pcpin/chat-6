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


if (!isset($recursion)) $recursion=1;

$invitations_arrived=0;
$messages_arrived=0;
$categories_tree=array();

if (!empty($current_user->id)) {
  $xmlwriter->setHeaderMessage('OK');
  $xmlwriter->setHeaderStatus(0);
  // Get room structure
  _pcpin_loadClass('category'); $category=new PCPIN_Category($session);
  $categories_tree=$category->getTree($current_user->id, 0, !empty($recursion));
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
$xmlwriter->setData(array('category'=>!empty($categories_tree)? $categories_tree[0] : array(),
                          'additional_data'=>array('new_invitations'=>$invitations_arrived,
                                                   'new_messages'=>$messages_arrived)
                          )
                    );
?>