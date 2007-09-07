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
$js_files[]='./step7.js';
$body_onload[]='initFinalCheckTables()';


?>
<table id="overview_tbl" class="tbl" cellspacing="0" cellpadding="0" width="100%">
  <tr>
    <td class="tbl_header_main" colspan="2">
      Install chat
    </td>
  </tr>

  <tr>
    <td class="tbl_row" colspan="2" style="text-align:center">
      <br />
      <b>Please check the data below and click "INSTALL".</b>
      <br /><br />
    </td>
  </tr>

  <tr>
    <td class="tbl_header_sub" colspan="2">
      Database
    </td>
  </tr>
  <tr>
    <td class="tbl_row">
      Database server host name:
    </td>
    <td class="tbl_row" id="db_data_host">
    </td>
  </tr>
  <tr>
    <td class="tbl_row">
      Database server username:
    </td>
    <td class="tbl_row" id="db_data_user">
    </td>
  </tr>
  <tr>
    <td class="tbl_row">
      Database name:
    </td>
    <td class="tbl_row" id="db_data_database">
    </td>
  </tr>
  <tr>
    <td class="tbl_row">
      Name prefix for all tables used by chat:
    </td>
    <td class="tbl_row" id="db_data_prefix">
    </td>
  </tr>

  <tr>
    <td class="tbl_header_sub" colspan="2">
      Import data
    </td>
  </tr>
  <tr>
    <td id="import_settings_list" class="tbl_row" colspan="2">
      &nbsp;
    </td>
  </tr>

  <tr>
    <td class="tbl_header_sub" colspan="2">
      Administrator account
    </td>
  </tr>
  <tr id="administrator_account_no_new" style="display:none">
    <td class="tbl_row" colspan="2">
      Do not create new administrator account
    </td>
  </tr>
  <tr id="administrator_account_username_row" style="display:none">
    <td class="tbl_row">
      Username:
    </td>
    <td id="administrator_account_username" class="tbl_row">
    </td>
  </tr>
  <tr id="administrator_account_email_row" style="display:none">
    <td class="tbl_row">
      E-Mail address:
    </td>
    <td id="administrator_account_email" class="tbl_row">
    </td>
  </tr>

  <tr>
    <td class="tbl_row" colspan="2" style="text-align: right">
      <button type="button" onclick="window.history.go(-1)" title="Back">Back</button>
      &nbsp;
      <button type="button" onclick="startInstallation()" title="Continue">INSTALL</button>
    </td>
  </tr>

</table>


<table id="installation_progress" class="tbl" cellspacing="0" cellpadding="0" width="100%" style="display:none">
  <tr>
    <td class="tbl_header_main" colspan="2">
      Installation progress
    </td>
  </tr>
  <tr>
    <td class="tbl_header_sub" width="60%">
      Installation step
    </td>
    <td class="tbl_header_sub">
      Status
    </td>
  </tr>
  <tr id="install_complete" style="display:none">
    <td class="tbl_row" colspan="2" style="text-align:center">
      <br />
      <h2 style="color:#008800"><b>Installation complete!</b></h2>
      <span style="color:#880000"><b>Delete directory &quot;install&quot; before you continue!</b></span>
      <br /><br />
      Please log into Admin Panel and configure your chat now:
      &nbsp;
      <button type="button" onclick="openAdminPanel()" title="Open Admin Panel">Open Admin Panel</button>
      <br /><br /><br />
      In case of any questions or problems regarding this software please visit our
      <a href="http://community.pcpin.com/" title="PCPIN Community Forums" target="_blank">Community Forums</a>.
      <br /><br /><br />
      Thank you for choosing PCPIN Chat!
      <br /><br />
    </td>
  </tr>
</table>

<form id="admin_panel_form" action="./admin.php" method="post" target="pcpin_chat_admin_panel" style="margin:0px">
  <input type="hidden" name="admin_login" value="1" />
  <input type="hidden" name="direct_login" value="1" />
  <input type="hidden" name="login" value="" />
  <input type="hidden" name="password" value="" />
</form>