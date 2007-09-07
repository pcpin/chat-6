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

_pcpin_loadClass('config');
_pcpin_loadClass('session');
_pcpin_loadClass('version');

$__pcpin_upgrade=array();

$__pcpin_upgrade['file_version']=6.02;
$__pcpin_upgrade['init_class']=$__pcpin_init_class; // copy, not reference!
$__pcpin_upgrade['init_class']->_conf_all=array(1); // just a dummy
$__pcpin_upgrade['session']=new PCPIN_Session($__pcpin_upgrade['init_class'], '', true);
$__pcpin_upgrade['version']=new PCPIN_Version($__pcpin_upgrade['session']);



if ($__pcpin_upgrade['version']->_db_getList('version', 'version DESC', 1)) {
  $__pcpin_upgrade['db_version']=$__pcpin_upgrade['version']->_db_list[0]['version'];
  $__pcpin_upgrade['version']->_db_freeList();
  if ($__pcpin_upgrade['db_version']<$__pcpin_upgrade['file_version']) {
    // Database upgrade needed
    switch ($__pcpin_upgrade['db_version']) {

      default:
        // All versions
        $__pcpin_upgrade['version']->setVersion($__pcpin_upgrade['file_version']);
      break;

    }
  }
} else {
  die('Fatal error: Your installation is broken. Reinstall needed!');
}

unset($__pcpin_upgrade);

?>