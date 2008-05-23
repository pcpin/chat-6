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
 * Update smilie
 * @param   int     $smilie_id      Smilie ID
 * @param   string  $code           New smilie code
 * @param   string  $description    New smilie description
 */

_pcpin_loadClass('smilie'); $smilie=new PCPIN_Smilie($session);

if (!isset($smilie_id) || !pcpin_ctype_digit($smilie_id)) $smilie_id=0;
if (!isset($code) || !is_scalar($code)) $code='';
if (!isset($description) || !is_scalar($description)) $description='';

// Get client session
if (is_object($session) && !empty($current_user->id) && $session->_s_user_id==$current_user->id && $current_user->is_admin==='y') {
  $xmlwriter->setHeaderMessage($l->g('changes_saved'));
  $xmlwriter->setHeaderStatus(0);
  if (!empty($smilie_id)) {
    $smilie->updateSmilie($smilie_id, $code, $description);
  }
}
?>