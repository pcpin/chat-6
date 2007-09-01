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
$_js_files[]='./js/admin/languages.js';
$_js_files[]='./js/language.js';


// JS language expressions
$_js_lng[]='edit';
$_js_lng[]='delete';
$_js_lng[]='yes';
$_js_lng[]='no';
$_js_lng[]='sure_to_delete_language';
$_js_lng[]='download';
$_js_lng[]='download_language_file';

if (!isset($download_language) || !is_scalar($download_language)) $download_language=0;


if (!empty($download_language) && $l->_db_getList('iso_name', 'id = '.$download_language, 1)) {
  // Language file requested
  $language_iso_name=strtolower($l->_db_list[0]['iso_name']);
  $l->_db_freeList();
  // Get language array
  if ($lng_raw=$l->exportLanguage($download_language)) {
    // Send headers
    header('Content-type: application/octet-stream');
    header('Expires: '.gmdate('D, d M Y H:i:s').' GMT');
    header('Content-Disposition: attachment; filename="pcpin_lng_'.$language_iso_name.'.bin"');
    if (PCPIN_CLIENT_AGENT_NAME=='IE') {
      header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
      header('Pragma: public');
    } else {
      header('Pragma: no-cache');
    }
    // Output language file
    echo $lng_raw;
    die();
  }
}


$_body_onload[]='initLanguagesPage()';

// Initialize template handler
_pcpin_loadClass('pcpintpl'); $tpl=new PcpinTpl();
$tpl->setBasedir('./tpl');
$tpl->readTemplatesFromFile('./admin/languages.tpl');

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