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

// Load colorbox
$_load_colorbox=true;

// JS files
$_js_files[]='./js/admin/settings.js';
$_js_files[]='./js/language.js';


// JS language expressions
$_js_lng[]='setting_error';
$_js_lng[]='check_url';
$_js_lng[]='unlimited';

if (!isset($group) || !is_scalar($group)) $group='';

switch ($group) {
  default         : die('Access denied');       break;
  case 'account'  : $title=$l->g('account');    break;
  case 'banners'  : $title=$l->g('banners');    break;
  case 'chat'     : $title=$l->g('chat');       break;
  case 'design'   : $title=$l->g('design');     break;
  case 'security' : $title=$l->g('security');   break;
  case 'server'   : $title=$l->g('server');     break;
  case 'slave'    : $title=$l->g('slave_mode'); break;
}

$_body_onload[]='initSettingsForm(\''.htmlspecialchars($group).'\')';

// Initialize template handler
_pcpin_loadClass('pcpintpl'); $tpl=new PcpinTpl();
$tpl->setBasedir('./tpl');
$tpl->readTemplatesFromFile('./admin/settings.tpl');

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

$tpl->addVar('main', 'title', $title);
?>