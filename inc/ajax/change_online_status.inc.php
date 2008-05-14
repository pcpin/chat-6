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

if (!isset($online_status) || !is_scalar($online_status)) {
  $online_status=0;
}
if (!isset($online_status_message) || !is_scalar($online_status_message)) {
  $online_status_message='';
} else {
  $online_status_message=trim($online_status_message);
}

if (!empty($current_user->id)) {
  $xmlwriter->setHeaderMessage('OK');
  $xmlwriter->setHeaderStatus(0);
  if (!empty($online_status) && $session->_s_online_status!=$online_status || $session->_s_online_status_message!=$online_status_message) {
    $session->_s_setOnlineStatus($online_status, $online_status_message);
  }
}
?>