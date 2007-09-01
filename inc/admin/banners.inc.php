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

// JS files
$_js_files[]='./js/admin/banners.js';


// JS language expressions
$_js_lng[]='no_banners_yet';
$_js_lng[]='yes';
$_js_lng[]='no';
$_js_lng[]='url';
$_js_lng[]='custom';
$_js_lng[]='at_window_top';
$_js_lng[]='at_window_bottom';
$_js_lng[]='in_popup_window';
$_js_lng[]='between_messages';
$_js_lng[]='every_x_minutes';
$_js_lng[]='every_x_messages';
$_js_lng[]='never';
$_js_lng[]='edit';
$_js_lng[]='delete';
$_js_lng[]='preview';
$_js_lng[]='confirm_delete_banner';
$_js_lng[]='edit_banner';
$_js_lng[]='banner_name_empty_error';
$_js_lng[]='start_date_invalid';
$_js_lng[]='expiration_date_invalid';
$_js_lng[]='width_invalid';
$_js_lng[]='height_invalid';
$_js_lng[]='unlimited';

$_body_onload[]='initBannersWindow('.$session->_conf_all['top_banner_height'].', '.$session->_conf_all['bottom_banner_height'].')';

// Initialize template handler
_pcpin_loadClass('pcpintpl'); $tpl=new PcpinTpl();
$tpl->setBasedir('./tpl');
$tpl->readTemplatesFromFile('./admin/banners.tpl');

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