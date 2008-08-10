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
$_js_files[]='./js/admin/custom_profile_fields.js';


// JS language expressions
$_js_lng[]='single_text_field';
$_js_lng[]='textarea';
$_js_lng[]='url';
$_js_lng[]='email_address';
$_js_lng[]='simple_choice';
$_js_lng[]='multiple_choice';
$_js_lng[]='gender_m';
$_js_lng[]='gender_f';
$_js_lng[]='gender_-';
$_js_lng[]='everybody';
$_js_lng[]='registered_users_only';
$_js_lng[]='moderators_only';
$_js_lng[]='admins_only';
$_js_lng[]='profile_owner';
$_js_lng[]='yes';
$_js_lng[]='no';
$_js_lng[]='move_up';
$_js_lng[]='move_down';
$_js_lng[]='edit';
$_js_lng[]='delete';
$_js_lng[]='sure_delete_field';
$_js_lng[]='name_empty_error';
$_js_lng[]='no_options_specified';


$_body_onload[]='initCustomFieldsWindow()';

// Initialize template handler
_pcpin_loadClass('pcpintpl'); $tpl=new PcpinTpl();
$tpl->setBasedir('./tpl');
$tpl->readTemplatesFromFile('./admin/custom_profile_fields.tpl');

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