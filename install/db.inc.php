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


define('PCPIN_INSTALL_MODE', true);

// Send headers
header('Content-type: application/octet-stream');
header('Expires: '.gmdate('D, d M Y H:i:s').' GMT');
header('Content-Disposition: attachment; filename="db.inc.php"');
header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
header('Pragma: public');

if (!isset($_POST['host'])) $_POST['host']='';
if (!isset($_POST['user'])) $_POST['user']='';
if (!isset($_POST['password'])) $_POST['password']='';
if (!isset($_POST['database'])) $_POST['database']='';
if (!isset($_POST['prefix'])) $_POST['prefix']='';

if ($src=file_get_contents('./database/db.inc.php_')) {
  $src=str_replace('{{HOST}}', str_replace("'", '\\\'', $_POST['host']), $src);
  $src=str_replace('{{USER}}', str_replace("'", '\\\'', $_POST['user']), $src);
  $src=str_replace('{{PASSWORD}}', str_replace("'", '\\\'', $_POST['password']), $src);
  $src=str_replace('{{DATABASE}}', str_replace("'", '\\\'', $_POST['database']), $src);
  $src=str_replace('{{PREFIX}}', str_replace("'", '\\\'', $_POST['prefix']), $src);
  echo $src;
}
die();
?>