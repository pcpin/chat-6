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

if (!is_object($session) || empty($current_user->id) || empty($invitation_id)) {
  header('Location: '.PCPIN_FORMLINK.'?'.md5(microtime()));
  die();
}

$_js_files[]='./js/invitation.js';

_pcpin_loadClass('invitation'); $invitation=new PCPIN_Invitation($session);
_pcpin_loadClass('nickname'); $nickname=new PCPIN_Nickname($session);

$invitations=$invitation->getNewInvitations($current_user->id, false, $invitation_id);
if (empty($invitations)) {
  $_body_onload[]='window.close()';
  $_body_onload[]='return false';
} else {
  $invitation_data=reset($invitations);

  // Init template
  _pcpin_loadClass('pcpintpl'); $tpl=new PcpinTpl();
  $tpl->setBasedir('./tpl');
  $tpl->readTemplatesFromFile('./invitation.tpl');

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

  $invitation_text=$l->g('user_invited_you');
  $invitation_text=str_replace('[ROOM]', $invitation_data['room_name'], $invitation_text);
  $invitation_text=str_replace('[USER]', '<b>'.$nickname->coloredToHTML($invitation_data['author_nickname']).'</b>', htmlspecialchars($invitation_text));
  $tpl->addVar('main', 'invitation_text', nl2br($invitation_text));

  $tpl->addGlobalVar('user_id', htmlspecialchars($invitation_data['author_id']));
  $tpl->addGlobalVar('room_id', htmlspecialchars($invitation_data['room_id']));
  $tpl->addGlobalVar('mute_user_locally', htmlspecialchars(str_replace('[USER]', $nickname->coloredToPlain($invitation_data['author_nickname'], false), $l->g('mute_user_locally'))));

}

$_window_title.=' '.PCPIN_WINDOW_TITLE_SEPARATOR.' '.$l->g('invitation');

?>