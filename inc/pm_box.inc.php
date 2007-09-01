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

_pcpin_loadClass('nickname'); $nickname=new PCPIN_Nickname($session);

$_js_files[]='./js/user.js';
$_js_files[]='./js/context_menu_user_options.js';
$_js_files[]='./js/pm_box.js';

$_js_lng[]='private_message';


_pcpin_loadClass('pcpintpl'); $tpl=new PcpinTpl();
$tpl->setBasedir('./tpl');
$tpl->readTemplatesFromFile('./pm_box.tpl');



if (empty($target_user_id) || $target_user_id==$current_user->id || !$current_user->_db_getList('id', 'id = '.$target_user_id, 1)) {
  $_body_onload[]='window.close()';
} else {
  $current_user->_db_freeList();
  $target_user_nickname=$nickname->getDefaultNickname($target_user_id);
  $_body_onload[]='initPMBox('.$target_user_id.', 60)';
  $_window_title=$nickname->coloredToPlain($target_user_nickname, false).'::'.$l->g('private_message').' ('.$session->_conf_all['chat_name'].')';
}

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

// Display fonts
$tpl->addVar('fonts', 'fonts', htmlspecialchars($session->_conf_all['font_families']));
$tpl->addVar('fonts', 'font_sizes', htmlspecialchars($session->_conf_all['font_sizes']));

?>