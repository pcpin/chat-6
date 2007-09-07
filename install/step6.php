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
$js_files[]='./step6.js';
$body_onload[]='initAdminAccountForm()';


?>
<table class="tbl" cellspacing="0" cellpadding="0" width="100%">
  <tr>
    <td class="tbl_header_main" colspan="2">
      Administrator account
    </td>
  </tr>

  <tr id="no_new_admin_account_row" style="display:none">
    <td class="tbl_row" style="text-align:center;" colspan="2">
      <label for="no_new_admin_account">
        <input type="checkbox" id="no_new_admin_account" onclick="setNoAdminAccount(this.checked)"> Do not create new Administrator account
      </label>
    </td>
  </tr>
  <tr>
    <td class="tbl_row" style="text-align:right">
      Administrator username:
    </td>
    <td class="tbl_row">
      <input type="text" id="admin_account_username" size="32" maxlength="32" onchange="setAdminUsername(this)" />
    </td>
  </tr>
  <tr>
    <td class="tbl_row" style="text-align:right">
      Administrator password:
    </td>
    <td class="tbl_row">
      <input type="password" id="admin_account_password" size="32" maxlength="32" onchange="setAdminPassword(this)" />
    </td>
  </tr>
  <tr>
    <td class="tbl_row" style="text-align:right">
      Administrator E-Mail address:
    </td>
    <td class="tbl_row">
      <input type="text" id="admin_account_email" size="32" maxlength="255" onchange="setAdminEmail(this)" />
    </td>
  </tr>

  <tr>
    <td class="tbl_row" colspan="2" style="text-align: right">
      <button type="button" onclick="window.history.go(-1)" title="Back">Back</button>
      &nbsp;
      <button type="button" onclick="validateAdminAccount()" title="Continue">Continue</button>
    </td>
  </tr>

</table>