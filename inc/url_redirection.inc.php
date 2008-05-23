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

if (!is_object($session)) { die('Access denied'); }

$allowed_schemes=array('aaa',    'aaas',  'about', 'acap',  'aim',  'callto', 'cap',    'cid',  'crid',   'data',
                       'dav',    'dict',  'dns',   'ed2k',  'fax',  'feed',   'file',   'ftp',  'go',     'gopher',
                       'http',   'https', 'imap',  'imaps', 'irc',  'ircs',   'lastfm', 'ldap', 'mailto', 'mailto',
                       'mid',    'mms',   'msnim', 'news',  'nfs',  'nntp',   'pop',    'pop3', 'pop3s',  'pops',
                       'pres',   'rsync', 'sftp',  'sip',   'sips', 'skype',  'smb',    'snmp', 'ssh',    'tel',
                       'telnet', 'urn',   'wais',  'xmpp',  'ymsgr' );

if (!isset($external_url)) $external_url='';
$external_url=urldecode($external_url);
$url_data=parse_url($external_url);
if (!isset($url_data['scheme'])) {
  // No scheme specified. Assuming "http"
  $external_url='http://'.$external_url;
  $url_data['scheme']='http';
}
if (!in_array($url_data['scheme'], $allowed_schemes, true)) {
  die();
}

header('Content-Type: text/html; charset=UTF-8');
header('Expires: '.gmdate('D, d M Y H:i:s').' GMT');
if (PCPIN_CLIENT_AGENT_NAME=='IE') {
  header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
  header('Pragma: public');
}else{
  header('Pragma: no-cache');
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>
<body onload="document.location.href='<?php echo htmlspecialchars($external_url); ?>'">
</body>
</html>
<?php
die();
?>