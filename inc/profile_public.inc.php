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

if (!is_object($session)) {
  header('Location: '.PCPIN_FORMLINK.'?'.md5(microtime()));
  die();
}

// JS files
$_js_files[]='./js/profile_public.js';

// JS language expressions
$_js_lng[]='avatar';
$_js_lng[]='seconds';
$_js_lng[]='minutes';
$_js_lng[]='hours';
$_js_lng[]='days';
$_js_lng[]='user_is_logged_in';
$_js_lng[]='user_is_not_logged_in';
$_js_lng[]='invite_user_to_your_room';
$_js_lng[]='gender_m';
$_js_lng[]='gender_f';
$_js_lng[]='gender_-';
$_js_lng[]='guest';


if (!isset($user_id)) $user_id=0;

$_body_onload[]='initProfilePublic('.$user_id.', '.$session->_conf_all['avatars_max_count'].')';

$_window_title.=' '.PCPIN_WINDOW_TITLE_SEPARATOR.' '.$l->g('user_profile');

// Init template
_pcpin_loadClass('pcpintpl'); $tpl=new PcpinTpl();
$tpl->setBasedir('./tpl');
$tpl->readTemplatesFromFile('./profile_public.tpl');


// Add language expressions to template
foreach ($tpl->tpl_vars_plain as $var) {
  if (0===strpos($var, 'LNG_')) {
    $var=strtolower($var);
    $tpl->addGlobalVar($var, htmlspecialchars($l->g(substr($var, 4))));
  }
}

// Add global vars to template
foreach ($global_tpl_vars as $key=>$val) {
  $tpl->addGlobalVar($key, htmlspecialchars($val));
}


?>