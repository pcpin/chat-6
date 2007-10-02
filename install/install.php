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

if (!defined('PCPIN_INSTALL_MODE') || true!==PCPIN_INSTALL_MODE) {
  header('Location: ../install.php');
  die();
}

error_reporting(0);

define('PCPIN_INSTALL_VERSION', '6.05');
define('PCPIN_REQUIRESPHP', '4.3.3');
define('PCPIN_REQUIRESMYSQL', '4.0.15');

if (file_exists('./funcs.inc.php')) {
  require('./funcs.inc.php');
} elseif (file_exists('../../funcs.inc.php')) {
  require_once('../../funcs.inc.php');
}

/**
 * Yes, we extract superglobals. We know, how to handle them.
 */
if (get_magic_quotes_gpc()) {
  $_pcpin_magic_quotes_sybase=ini_get('magic_quotes_sybase')=='1';
  $_GET=_pcpin_stripSlashesRecursive($_GET, $_pcpin_magic_quotes_sybase);
  $_POST=_pcpin_stripSlashesRecursive($_POST, $_pcpin_magic_quotes_sybase);
  unset($_pcpin_magic_quotes_sybase);
}
extract($_POST);
extract($_GET);



// This is a function from PCPIN_Common class
function _pcpin_stripSlashesRecursive($target, $magic_quotes_sybase=false) {
  if (!empty($target) && is_array($target)) {
    foreach ($target as $key=>$val) {
      if (is_array($val)) {
        // Value is an array. Start recursion.
        $target[$key]=_pcpin_stripSlashesRecursive($val, $magic_quotes_sybase);
      } elseif (is_scalar($val)) {
        // Strip slashes from scalar value
        if ($magic_quotes_sybase) {
          $target[$key]=str_replace("''", "'", $val);
        } else {
          $target[$key]=stripslashes($val);
        }
      } else {
        // Leave value unchanged.
        $target[$key]=$val;
      }
    }
  }
  return $target;
}


?>