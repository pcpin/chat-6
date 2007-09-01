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

/**
 * This file contains database configuration settings
 */

// Database server host name.
// Examples: 'localhost' or 'db.myhost.com'
$_pcpin_db_server = 'localhost';

// Database username
$_pcpin_db_user = 'root';

// Database password
$_pcpin_db_password = '';

// Database name
$_pcpin_db_database = 'chat';

// Prefix for all chat table names
$_pcpin_db_tbl_prefix = 'pcpin_';



///////////////////////////////////////////////////////////
// DO NOT EDIT OR DELETE ANYTHING BELOW THIS LINE !!!
///////////////////////////////////////////////////////////
if (defined('PCPIN_DB_DATA_LOADED')) {
  PCPIN_Common::dieWithError(1, 'Access denied');
} else {
  define('PCPIN_DB_DATA_LOADED', true);
}
if (function_exists('debug_backtrace')) {
  $_pcpin_dbt=debug_backtrace();
  if (is_array($_pcpin_dbt) && (!isset($_pcpin_dbt[0]) || basename($_pcpin_dbt[0]['file'])!=='init.inc.php' && basename($_pcpin_dbt[0]['file'])!=='check_db.php')) {
    die('Access denied');
  }
  unset($_pcpin_dbt);
}
$_pcpin_dbcn=md5(mt_rand(-time(), time()).microtime());
${$_pcpin_dbcn}=array();
${$_pcpin_dbcn}['server']=$_pcpin_db_server; unset($_pcpin_db_server);
${$_pcpin_dbcn}['user']=$_pcpin_db_user; unset($_pcpin_db_user);
${$_pcpin_dbcn}['password']=$_pcpin_db_password; unset($_pcpin_db_password);
${$_pcpin_dbcn}['database']=$_pcpin_db_database; unset($_pcpin_db_database);
${$_pcpin_dbcn}['tbl_prefix']=$_pcpin_db_tbl_prefix; unset($_pcpin_db_tbl_prefix);
?>