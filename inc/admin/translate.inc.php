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
$_js_files[]='./js/admin/translate.js';
$_js_files[]='./js/language.js';


// JS language expressions
$_js_lng[]='pages';
$_js_lng[]='goto_first_page';
$_js_lng[]='first';
$_js_lng[]='goto_previous_page';
$_js_lng[]='page';
$_js_lng[]='goto_next_page';
$_js_lng[]='goto_last_page';
$_js_lng[]='last';
$_js_lng[]='code';
$_js_lng[]='discard_changes_continue';
$_js_lng[]='please_select';
$_js_lng[]='please_select_language';


$_body_onload[]='initTranslationPage('.(!empty($new_translation)? 'true' : 'false').')';

// Initialize template handler
_pcpin_loadClass('pcpintpl'); $tpl=new PcpinTpl();
$tpl->setBasedir('./tpl');
$tpl->readTemplatesFromFile('./admin/translate.tpl');

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