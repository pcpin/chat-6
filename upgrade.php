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

$__pcpin_upgrade['file_version']=6.05;
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

      case 6.00:
      case 6.01:
        // Add more font sizes. See http://bugs.pcpin.com/view.php?id=224
        $__pcpin_upgrade['session']->_db_query("UPDATE `".PCPIN_DB_PREFIX."config` SET `_conf_choices` = '9=9|10=10|11=11|12=12|13=13|14=14|15=15|16=16|17=17|18=18|19=19|20=20' WHERE `_conf_name` = 'default_font_size' LIMIT 1");
        $__pcpin_upgrade['session']->_db_query("UPDATE `".PCPIN_DB_PREFIX."config` SET `_conf_choices` = '9=9|10=10|11=11|12=12|13=13|14=14|15=15|16=16|17=17|18=18|19=19|20=20' WHERE `_conf_name` = 'font_sizes' LIMIT 1");
      break;

    }
    // All versions: Store new version number
    $__pcpin_upgrade['session']->_db_query('DELETE FROM `'.PCPIN_DB_PREFIX.'version`');
    $__pcpin_upgrade['session']->_db_query('INSERT INTO `'.PCPIN_DB_PREFIX.'version` ( `version`, `version_check_key`, `last_version_check` ) VALUES ( "'.$__pcpin_upgrade['session']->_db_escapeStr($__pcpin_upgrade['file_version'], false).'", "-", NOW() )');
  }
} else {
  die('Fatal error: Your installation is broken. Reinstall needed!');
}

unset($__pcpin_upgrade);

// Trying to delete this file
@unlink('./upgrade.php');

?>