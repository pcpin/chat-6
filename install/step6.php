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

  <tr id="language_selection_header">
    <td class="tbl_row" style="text-align:center;">
      Please select languages you would like to install:
      <br /><br />
      <table border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td class="tbl_row" width="49%">&nbsp;</td>
          <td class="tbl_row" nowrap="nowrap" style="text-align:left" id="languages_cell">
          </td>
          <td class="tbl_row" width="49%">&nbsp;</td>
        </tr>
      </table>
    </td>
  </tr>

  <tr id="no_languages_found" style="display:none">
    <td class="tbl_row" colspan="2" style="text-align:center;color:#dd0000;">
      <br /><br />
      <b>ERROR: No language files found</b>
      <br /><br /><br />
    </td>
  </tr>

  <tr id="default_language_row">
    <td class="tbl_row" colspan="2" style="text-align: center">
      Default language:
      <select id="default_language" onchange="setDefaultLanguage(this)">
        <option value="">------------- Please select -------------</option>
      </select>
    </td>
  </tr>

  <tr>
    <td class="tbl_row" colspan="2" style="text-align: right">
      <button type="button" onclick="window.history.go(-1)" title="Back">Back</button>
      &nbsp;
      <button id="continue_btn" type="button" onclick="validateLanguages()" title="Continue">Continue</button>
    </td>
  </tr>

</table>