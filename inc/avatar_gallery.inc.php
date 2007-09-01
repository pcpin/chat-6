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

if (!is_object($session) || empty($session->_s_user_id)) {
  header('Location: '.PCPIN_FORMLINK.'?'.md5(microtime()));
  die();
}

if (empty($session->_conf_all['avatar_gallery'])) {
  // Avatar gallery is disabled
  die('access denied');
}

if (empty($profile_user_id) || $current_user->is_admin!=='y') {
  $profile_user_id=$current_user->id;
}


// Get avatars
_pcpin_loadClass('avatar'); $avatar=new PCPIN_Avatar($session);
$avatars=$avatar->getGalleryAvatars();

// JS files
$_js_files[]='./js/avatar_gallery.js';

$_js_lng[]='avatar';

$_body_onload[]='initAvatarGallery('.addslashes(htmlspecialchars($profile_user_id)).')';

$_window_title.=' '.PCPIN_WINDOW_TITLE_SEPARATOR.' '.$l->g('avatar_gallery');



// Init template
_pcpin_loadClass('pcpintpl'); $tpl=new PcpinTpl();
$tpl->setBasedir('./tpl');
$tpl->readTemplatesFromFile('./avatar_gallery.tpl');


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


// Add avatars
$tpl->addVar('main', 'header_colspan', htmlspecialchars($session->_conf_all['gallery_avatars_per_row']));
while (count($avatars)%$session->_conf_all['gallery_avatars_per_row']>0) {
  $avatars[]=array('id'=>0,
                   'binaryfile_id'=>0,
                   'width'=>0,
                   'height'=>0,
                   );
}
$col=0;
foreach ($avatars as $avatar_data) {
  $tpl->addVars('avatar_gallery_col', array('id'=>htmlspecialchars($avatar_data['id']),
                                            'binaryfile_id'=>htmlspecialchars($avatar_data['binaryfile_id']),
                                            ));
  $tpl->parseTemplate('avatar_gallery_col', 'a');
  if (++$col==$session->_conf_all['gallery_avatars_per_row']) {
    $tpl->parseTemplate('avatar_gallery_row', 'a');
    $tpl->clearTemplate('avatar_gallery_col');
    $col=0;
  }
}

?>