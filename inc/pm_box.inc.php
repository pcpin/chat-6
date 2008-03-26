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

// Load colorbox
$_load_colorbox=true;

// Load smiliebox
$_load_smiliebox=true;

// Default: Do not context menu user options
$_load_cm_user_options=true;

_pcpin_loadClass('nickname'); $nickname=new PCPIN_Nickname($session);

$_js_files[]='./js/user.js';
$_js_files[]='./js/pm_box.js';

$_js_lng[]='private_message';


_pcpin_loadClass('pcpintpl'); $tpl=new PcpinTpl();
$tpl->setBasedir('./tpl');
$tpl->readTemplatesFromFile('./pm_box.tpl');



if (empty($target_user_id) || $target_user_id==$current_user->id || !$current_user->_db_getList('id', 'id = '.$target_user_id, 1)) {
  $_body_onload[]='window.close()';
} else {
  $_body_onload[]='initSmilieList()';
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

// Add smilies to the main template
_pcpin_loadClass('smilie'); $smilie=new PCPIN_Smilie($session);
$smilies=$smilie->getSmilies();
if (!empty($smilies)) {
  // Append empty elements to smilies array
  $smilies_append=$session->_conf_all['smilies_per_row']-count($smilies)%$session->_conf_all['smilies_per_row'];
  if ($smilies_append!=$session->_conf_all['smilies_per_row'] && $smilies_append>0) {
    for ($i=0; $i<$smilies_append; $i++) {
      array_push($smilies, array('id'=>'',
                                 'binaryfile_id'=>'',
                                 'code'=>'',
                                 'description'=>'',
                                 ));
    }
  }
  $col=1;
  $maxcol=0;
  foreach ($smilies as $smilie_data) {
    $template->addVars('smiliebox_col', array('id'=>htmlspecialchars($smilie_data['id']),
                                              'binaryfile_id'=>htmlspecialchars($smilie_data['binaryfile_id']),
                                              'code'=>htmlspecialchars($smilie_data['code']),
                                              'description'=>htmlspecialchars($smilie_data['description']),
                                              's_id'=>htmlspecialchars($session->_s_id),
                                              'padding_top'=>htmlspecialchars($inc=='pm_box' || $session->_conf_all['smilies_position']!=0? 8 : 0),
                                              'padding_bottom'=>htmlspecialchars($inc=='pm_box' || $session->_conf_all['smilies_position']!=0? 8 : 0),
                                              'padding_left'=>htmlspecialchars($inc=='pm_box' || $session->_conf_all['smilies_position']!=0? 8 : 0),
                                              'padding_right'=>htmlspecialchars(8),
                                              ));
    $template->parseTemplate('smiliebox_col', 'a');
    if ($col>$maxcol) {
      $maxcol=$col;
    }
    if (++$col>$session->_conf_all['smilies_per_row'] && ($inc=='pm_box' || $session->_conf_all['smilies_position']!=0)) {
      $template->parseTemplate('smiliebox_row', 'a');
      $template->clearTemplate('smiliebox_col', 'a');
      $col=1;
    }
  }
  if ($inc=='pm_box' || $session->_conf_all['smilies_position']!=0) {
    $template->addVar('smiliebox_header_row', 'header_row_colspan', htmlspecialchars($maxcol));
  }
}
unset($smilies);

?>