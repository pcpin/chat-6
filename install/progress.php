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

$body_onload=array();
#$body_onload[]='$(\'contents_div\').style.width=\'100%\'';
$body_onload[]='$(\'contents_div\').style.display=\'\'';

?>
<table border="0" cellspacing="0" cellpadding="0" class="tbl" width="100%">
  <tr>
    <td class="tbl_header_main">
      Installation steps
    </td>
  </tr>
  <tr>
    <td class="tbl_row" style="padding-top:15px;padding-bottom:15px;">
      <span id="step_1" class="status_open">
        <span id="step_1_prepend"></span>
        Welcome!
      </span>
    </td>
  </tr>
  <tr>
    <td class="tbl_row" style="padding-top:15px;padding-bottom:15px;">
      <span id="step_2" class="status_open">
        <span id="step_2_prepend"></span>
        License information
      </span>
    </td>
  </tr>
  <tr>
    <td class="tbl_row" style="padding-top:15px;padding-bottom:15px;">
      <span id="step_3" class="status_open">
        <span id="step_3_prepend"></span>
        Server information
      </span>
    </td>
  </tr>
  <tr>
    <td class="tbl_row" style="padding-top:15px;padding-bottom:15px;">
      <span id="step_4" class="status_open">
        <span id="step_4_prepend"></span>
        Database connection
      </span>
    </td>
  </tr>
  <tr>
    <td class="tbl_row" style="padding-top:15px;padding-bottom:15px;">
      <span id="step_5" class="status_open">
        <span id="step_5_prepend"></span>
        Data import
      </span>
    </td>
  </tr>
  <tr>
    <td class="tbl_row" style="padding-top:15px;padding-bottom:15px;">
      <span id="step_6" class="status_open">
        <span id="step_6_prepend"></span>
        Language files
      </span>
    </td>
  </tr>
  <tr>
    <td class="tbl_row" style="padding-top:15px;padding-bottom:15px;">
      <span id="step_7" class="status_open">
        <span id="step_7_prepend"></span>
        Administrator account
      </span>
    </td>
  </tr>
  <tr>
    <td class="tbl_row" style="padding-top:15px;padding-bottom:15px;">
      <span id="step_8" class="status_open">
        <span id="step_8_prepend"></span>
        Install chat
      </span>
    </td>
  </tr>
</table>