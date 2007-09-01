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

if (empty($current_user->id) || $current_user->is_admin!=='y') {
  header('Location: '.PCPIN_FORMLINK.'?'.md5(microtime()));
  die();
}


_pcpin_loadClass('version'); $version=new PCPIN_Version($session);


// Load version data
if ($version->_db_getList(1)) {
  $current_version=$version->_db_list[0]['version'];
  $last_check=($version->_db_list[0]['last_version_check']>'0000-00-00 00:00:00')?
                    $current_user->makeDate(PCPIN_Common::datetimeToTimestamp($version->_db_list[0]['last_version_check']))
                  : $l->g('never');
  $new_version_available=$version->_db_list[0]['new_version_available'];
  $new_version_url=$version->_db_list[0]['new_version_url'];
} else {
  $current_version=6.0;
  $last_check=$l->g('never');
  $new_version_available=$current_version;
  $new_version_url='';
}

$current_version=number_format($current_version, 2, '.', '');
$new_version_available=number_format($new_version_available, 2, '.', '');

if (!empty($do_check)) {
  // Check for new version
  // Generate new security key
  $key=PCPIN_Common::randomString(36, 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789_-()[].,');
  $version->setVersionCheckKey($key);
  $session->_s_updateSession($session->_s_id, true, true, null, null, null, md5($key));
  header('Location: '.PCPIN_VERSIONCHECKER_URL.'?'.htmlspecialchars($key));
  die();
}

// Initialize template handler
_pcpin_loadClass('pcpintpl'); $tpl=new PcpinTpl();
$tpl->setBasedir('./tpl');
$tpl->readTemplatesFromFile('./admin/versions.tpl');

// Add global vars to template
foreach ($global_tpl_vars as $key=>$val) {
  $tpl->addGlobalVar($key, htmlspecialchars($val));
}

// Add language expressions to template
foreach ($tpl->tpl_vars_plain as $var) {
  if (0===strpos($var, 'LNG_')) {
    $var=strtolower($var);
    $tpl->addGlobalVar($var, htmlspecialchars($l->g(substr($var, 4))));
  }
}

if (empty($do_check)) {
  // Display form
  $tpl->addvars('main', array('current_version'=>htmlspecialchars($current_version),
                              'last_check'=>htmlspecialchars($last_check)
                              ));
  if ($current_version<$new_version_available) {
    $tpl->addVars('newer_version', array('display'=>true,
                                         'url'=>$new_version_url,
                                         'newversionavailable'=>htmlspecialchars(str_replace('[VERSION]', $new_version_available, $l->g('new_version_is_available')))
                                         ));
  } elseif (isset($version_checked)) {
    $tpl->addVar('no_new_version', 'display', true);
  }
}

?>