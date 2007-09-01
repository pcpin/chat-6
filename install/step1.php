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


?>
<table class="tbl" cellspacing="0" cellpadding="0" width="100%">
  <tr>
    <td class="tbl_header_main">
      Welcome to PCPIN Chat <?php echo htmlspecialchars(PCPIN_INSTALL_VERSION) ?> installation
    </td>
  </tr>
  <tr>
    <td class="tbl_row" style="text-align:center; height: 250px; vertical-align: middle;">
      <b>Welcome to PCPIN Chat <?php echo htmlspecialchars(PCPIN_INSTALL_VERSION) ?> installation!</b>
      <br /><br />
      Read <a href="./INSTALL.txt" target="_blank">installation instructions</a> before you begin!
      <br /><br /><br />
      <button type="button" title="START installation" onclick="window.location.href='./install.php?step=2'">START Installation</button>
    </td>
  </tr>
</table>