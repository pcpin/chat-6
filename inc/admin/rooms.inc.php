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
$_js_files[]='./js/admin/rooms.js';
$_js_files[]='./js/room_structure.js';


// JS language expressions
$_js_lng[]='category_has_no_rooms';
$_js_lng[]='chat_category';
$_js_lng[]='rooms';
$_js_lng[]='edit';
$_js_lng[]='delete';
$_js_lng[]='move_up';
$_js_lng[]='move_down';
$_js_lng[]='create_new_room_in_category';
$_js_lng[]='room_is_password_protected';
$_js_lng[]='edit_category';
$_js_lng[]='category_name_empty';
$_js_lng[]='edit_room';
$_js_lng[]='room_name_empty';
$_js_lng[]='passwords_not_ident';
$_js_lng[]='password_too_short';
$_js_lng[]='background_image';
$_js_lng[]='confirm_delete_category';
$_js_lng[]='confirm_delete_room';
$_js_lng[]='online_status_0';
$_js_lng[]='online_status_1';
$_js_lng[]='online_status_2';
$_js_lng[]='online_status_3';

$_body_onload[]='initRoomsForm()';

// Initialize template handler
_pcpin_loadClass('pcpintpl'); $tpl=new PcpinTpl();
$tpl->setBasedir('./tpl');
$tpl->readTemplatesFromFile('./admin/rooms.tpl');

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

$tpl->addVar('main', 'default_message_color', $session->_conf_all['default_message_color']);

?>