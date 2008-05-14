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
* Get smilies list
* @param  int   $sort_by    Sort by (0: Address, 1: Action type, 2: Expiration date, 3: Description, 4: "Added on" date)
* @param  int   $sort_dir   Sort direction (0: Ascending, 1: Descending)
*/

_pcpin_loadClass('smilie'); $smilie=new PCPIN_Smilie($session);

$smilies=array();

if (!isset($sort_by)) $sort_by=0;
if (!isset($sort_dir)) $sort_dir=0;

// Get client session
if (is_object($session) && !empty($current_user->id)) {
  $xmlwriter->setHeaderMessage('OK');
  $xmlwriter->setHeaderStatus(0);
  $smilies=$smilie->getSmilies();
}
$xmlwriter->setData(array('smilie'=>$smilies));
?>