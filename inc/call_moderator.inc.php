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

if (empty($current_user->id)) {
  header('Location: '.PCPIN_FORMLINK.'?'.md5(microtime()));
  die();
}

$_js_files[]='./js/call_moderator.js';

$_js_lng[]='abuser_nickname_empty';
$_js_lng[]='violation_category_not_selected';


_pcpin_loadClass('nickname'); $nickname=new PCPIN_Nickname($session);

_pcpin_loadClass('pcpintpl'); $tpl=new PcpinTpl();
$tpl->setBasedir('./tpl');
$tpl->readTemplatesFromFile('./call_moderator.tpl');



$_body_onload[]='initCMBox()';
$_window_title.=' '.PCPIN_WINDOW_TITLE_SEPARATOR.' SOS '.PCPIN_WINDOW_TITLE_SEPARATOR.' '.$l->g('call_moderator');

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

$tpl->addVar('main', 'nickname', $nickname->coloredToHTML($current_nickname));
$tpl->addVar('main', 'room_name', htmlspecialchars($current_room_name));
?>