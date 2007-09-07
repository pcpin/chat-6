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
$js_files[]='./step4.js';
$body_onload[]='initDbForm()';



?>
<table class="tbl" cellspacing="0" cellpadding="0" width="100%">
  <tr>
    <td class="tbl_header_main" colspan="2">
      Database connection
    </td>
  </tr>

  <tr>
    <td class="tbl_row">
      <b>Database server host name</b>
      <br />
      This is <i>usually</i> localhost
    </td>
    <td class="tbl_row">
      <input id="db_host" type="text" title="Database server host name" value="" size="32" maxlength="255" />
    </td>
  </tr>

  <tr>
    <td class="tbl_row">
      <b>Database server username</b>
    </td>
    <td class="tbl_row">
      <input id="db_user" type="text" title="Database server username" value="" size="32" maxlength="255" />
    </td>
  </tr>

  <tr>
    <td class="tbl_row">
      <b>Database server password</b>
    </td>
    <td class="tbl_row">
      <input id="db_password" type="password" title="Database server password" value="" size="32" maxlength="255" />
    </td>
  </tr>

  <tr>
    <td class="tbl_row">
      <b>Database name</b>
    </td>
    <td class="tbl_row">
      <input id="db_database" type="text" title="Database name" value="" size="32" maxlength="255" />
    </td>
  </tr>

  <tr>
    <td class="tbl_row">
      <b>Name prefix for all tables used by chat</b>
      <br />
      It is <i><u>not recommended</u></i> to change this value
    </td>
    <td class="tbl_row">
      <input id="db_prefix" type="text" title="Name prefix for all tables used by chat" value="" size="32" maxlength="15" />
    </td>
  </tr>

  <tr id="db_config_write_error" style="display:none">
    <td class="tbl_row" colspan="2" style="text-align:center">
      <br />
      <span style="color:#880000">
        Setup was unable to write your database configuration file <b>&quot;db.inc.php&quot;</b>.
        <br />
        Please download it and save on your server into the directory <b>&quot;./config&quot;</b> as file <b>&quot;db.inc.php&quot;</b>.
        <br /><br />
        <button type="button" onclick="downloadDbConfig()" title="Download file &quot;db.inc.php&quot;">Download file &quot;db.inc.php&quot;</button>
        <br /><br />
      </span>
    </td>
  </tr>

  <tr>
    <td class="tbl_row" style="text-align: right" colspan="2">
      <button type="button" onclick="window.history.go(-1)" title="Back">Back</button>
      &nbsp;
      <button type="button" onclick="storeDbData()" title="Continue">Continue</button>
    </td>
  </tr>

</table>
<form id="download_db_config_form" action="" method="post" style="margin:0px">
  <input type="hidden" name="host" value="" />
  <input type="hidden" name="user" value="" />
  <input type="hidden" name="password" value="" />
  <input type="hidden" name="database" value="" />
  <input type="hidden" name="prefix" value="" />
</form>