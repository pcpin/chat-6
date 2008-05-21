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

_pcpin_loadClass('nickname'); $nickname=new PCPIN_Nickname($session);

if (!isset($moderator_user_id) || !pcpin_ctype_digit($moderator_user_id)) $moderator_user_id=0;

$name='';
if (!empty($moderator_user_id)) {
  if (''==($name=($nickname->getDefaultNickname($moderator_user_id)))) {
    if ($current_user->_db_getList('login', 'id = '.$moderator_user_id, 1)) {
      $name=$current_user->_db_list[0]['login'];
      $current_user->_db_freeList();
    }
  } else {
    $name=$nickname->coloredToPlain($name, true);
  }
  if ($name!='') {
    $_body_onload[]='$(\'nickname_search\').value=\''.addslashes($name).'\'';
    $_body_onload[]='moderatorSearchUser('.(!empty($popup)? 'true' : 'false').')';
  }
}

// JS files
$_js_files[]='./js/admin/edit_moderator.js';


// JS language expressions
$_js_lng[]='no_members_found';
$_js_lng[]='edit_moderator';
$_js_lng[]='chat_category';
$_js_lng[]='chat_room';
$_js_lng[]='category_has_no_rooms';

$_body_onload[]='initEditModeratorWindow('.(!empty($popup)? 'true' : 'false').')';

// Initialize template handler
_pcpin_loadClass('pcpintpl'); $tpl=new PcpinTpl();
$tpl->setBasedir('./tpl');
$tpl->readTemplatesFromFile('./admin/edit_moderator.tpl');

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

?>