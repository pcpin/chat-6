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

if (!is_object($session) || empty($current_user->id)) {
  header('Location: '.PCPIN_FORMLINK.'?'.md5(microtime()));
  die();
}

if (!isset($category_id) || !is_scalar($category_id)) $category_id=0;

_pcpin_loadClass('category'); $category=new PCPIN_Category($session);
if (!$category->_db_getList('name, creatable_rooms', 'id = '.$category_id, 1)) {
  // Category does not exists
  $_body_onload='window.close(); return false;';
}

if ($category->_db_list[0]['creatable_rooms']=='n' || $category->_db_list[0]['creatable_rooms']=='r' && $current_user->is_guest=='y') {
  // Room cannot be created in this category
  $_body_onload='window.close(); return false;';
}

$category_name=$category->_db_list[0]['name'];
$category->_db_freeList();

$title=str_replace('[CATEGORY]', $category_name, $l->g('create_new_room_in_category'));

// JS files
$_js_files[]='./js/create_user_room.js';

$_js_lng[]='room_name_empty';
$_js_lng[]='passwords_not_ident';
$_js_lng[]='password_too_short';


if (!isset($user_id)) $user_id=0;

$_body_onload[]='initNewuserRoomForm('.$category_id.')';

$_window_title.=' '.PCPIN_WINDOW_TITLE_SEPARATOR.' '.$title;

// Init template
_pcpin_loadClass('pcpintpl'); $tpl=new PcpinTpl();
$tpl->setBasedir('./tpl');
$tpl->readTemplatesFromFile('./create_user_room.tpl');


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

$tpl->addVars('main', array('title'=>htmlspecialchars($title),
                            'room_name_length_max'=>PCPIN_ROOM_NAME_LENGTH_MAX,
                            ));
?>