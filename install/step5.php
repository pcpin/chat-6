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
$js_files[]='step5.js';
$body_onload[]='checkPreviousInstallation()';


?>
<table class="tbl" cellspacing="0" cellpadding="0" width="100%">
  <tr>
    <td class="tbl_header_main">
      Check for previous installation
    </td>
  </tr>

  <tr id="no_previous_installations" style="display:none">
    <td class="tbl_row" style="text-align:center">
      <br /><br />
      No previous PCPIN Chat installations detected.
      <br /><br /><br />
    </td>
  </tr>

  <tr id="previous_installation_too_old" style="display:none">
    <td class="tbl_row">
      <br /><br />
      <span style="color:#880000"><b>INCOMPATIBLE VERSION WARNING:</b></span>
      <br />
      Setup has detected PCPIN Chat version <span id="too_old_version" style="font-weight:bold">&nbsp;</span> installed on this server.
      This version is too old. Existing data cannot be imported into PCPIN Chat 6.
      <br />
      If you want to keep your settings and users, then you have to upgrade your installation to version <b>5.13</b> and <i>then</i> upgrade it to version <b>6</b>.
      <br /><br /><br />
    </td>
  </tr>

  <tr id="previous_installation_ok" style="display:none">
    <td class="tbl_row" style="text-align:center;">
      <b>Previous installation detected!</b>
      <br />
      Setup has detected PCPIN Chat version <span id="onstalled_ok_version" style="font-weight:bold">&nbsp;</span> installed on this server.
      Please select the data you would like to import into the new version:
      <br /><br />
      <table class="tbl" cellspacing="0" cellpadding="0" style="border:0">
        <tr id="row_keep_users">
          <td class="tbl_row">
            <label for="keep_users">
              <input type="checkbox" id="keep_users" onclick="setImportFlag(this)"> Users
            </label>
          </td>
        </tr>
        <tr id="row_keep_smilies">
          <td class="tbl_row">
            <label for="keep_smilies">
              <input type="checkbox" id="keep_smilies" onclick="setImportFlag(this)"> Smilies
            </label>
          </td>
        </tr>
        <tr id="row_keep_settings">
          <td class="tbl_row">
            <label for="keep_settings">
              <input type="checkbox" id="keep_settings" onclick="setImportFlag(this)"> Chat settings
            </label>
          </td>
        </tr>
        <tr id="row_keep_rooms">
          <td class="tbl_row">
            <label for="keep_rooms">
              <input type="checkbox" id="keep_rooms" onclick="setImportFlag(this)"> Chat rooms
            </label>
          </td>
        </tr>
        <tr id="row_keep_bad_words">
          <td class="tbl_row">
            <label for="keep_bad_words">
              <input type="checkbox" id="keep_bad_words" onclick="setImportFlag(this)"> &quot;Bad words&quot; filter
            </label>
          </td>
        </tr>
        <tr id="row_keep_ip_filter">
          <td class="tbl_row">
            <label for="keep_ip_filter">
              <input type="checkbox" id="keep_ip_filter" onclick="setImportFlag(this)"> Filtered IP addresses
            </label>
          </td>
        </tr>
        <tr id="row_keep_avatar_gallery">
          <td class="tbl_row">
            <label for="keep_avatar_gallery">
              <input type="checkbox" id="keep_avatar_gallery" onclick="setImportFlag(this)"> Avatar Gallery
            </label>
          </td>
        </tr>
        <tr id="row_keep_banners">
          <td class="tbl_row">
            <label for="keep_banners">
              <input type="checkbox" id="keep_banners" onclick="setImportFlag(this)"> Banners
            </label>
          </td>
        </tr>
        <tr id="row_keep_languages">
          <td class="tbl_row">
            <label for="keep_languages">
              <input type="checkbox" id="keep_languages" onclick="setImportFlag(this)"> Languages
            </label>
          </td>
        </tr>
      </table>
    </td>
  </tr>

  <tr>
    <td class="tbl_row" style="text-align: right">
      <button type="button" onclick="window.history.go(-1)" title="Back">Back</button>
      &nbsp;
      <button type="button" onclick="window.location.href='./install.php?step=6&ts='+unixTimeStamp()" title="Continue">Continue</button>
    </td>
  </tr>

</table>