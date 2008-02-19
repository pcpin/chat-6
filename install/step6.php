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
$body_onload[]='initLanguagesForm()';

?>
<table class="tbl" cellspacing="0" cellpadding="0" width="100%">
  <tr>
    <td class="tbl_header_main" colspan="2">
      Language files
    </td>
  </tr>

  <tr>
    <td class="tbl_row" style="text-align:center;">
      Please select languages you would like to install:
      <br /><br />
<?php

?>
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