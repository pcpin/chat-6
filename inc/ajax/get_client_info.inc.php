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

if (!isset($user_id)) $user_id=0;
$client_data=array();

// Get client session
if (is_object($session) && !empty($current_user->id) && $session->_s_user_id==$current_user->id && $current_user->is_admin==='y') {
  if ($session->_db_getList('_s_user_id = '.$user_id, 1)) {
    // Client is online
    $xmlwriter->setHeaderMessage('OK');
    $xmlwriter->setHeaderStatus(0);
    $sessiondata=$session->_db_list[0];
    $session->_db_freeList();
    $client_data=array('ip'=>$sessiondata['_s_ip'],
                       'host'=>gethostbyaddr($sessiondata['_s_ip']),
                       'agent'=>$sessiondata['_s_client_agent_name'].' '.$sessiondata['_s_client_agent_version'],
                       'os'=>$sessiondata['_s_client_os'],
                       'session_start'=>$current_user->makeDate(PCPIN_Common::datetimeToTimestamp($sessiondata['_s_created'])),
                       );
    // Get language name
    $l->_db_getList('name, iso_name', 'id = '.$sessiondata['_s_language_id'], 1);
    $client_data['language']=$l->_db_list[0]['name'].' ('.$l->_db_list[0]['iso_name'].')';
    $l->_db_freeList();
  } else {
    // Client is not online
    $xmlwriter->setHeaderMessage($l->g('client_not_online'));
    $xmlwriter->setHeaderStatus(1);
  }

}
$xmlwriter->setData(array('client_data'=>$client_data));
?>