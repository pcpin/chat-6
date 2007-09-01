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

$install_possible=false;

// Get PHP version
$php_ok=false;
$_pcpin_php_needed=explode('.', PCPIN_REQUIRESPHP);
$_pcpin_php_exists=explode('.', phpversion());
define('PCPIN_PHP5', $_pcpin_php_exists[0]==5);
foreach ($_pcpin_php_needed as $_pcpin_key=>$_pcpin_val) {
  if (!isset($_pcpin_php_exists[$_pcpin_key])) {
    // Installed PHP version is OK
    $php_ok=true;
    $install_possible=true;
    break;
  } else {
    if ($_pcpin_val>$_pcpin_php_exists[$_pcpin_key]) {
      break;
      // PHP version is too old
    } elseif ($_pcpin_val<$_pcpin_php_exists[$_pcpin_key]) {
      // Installed PHP version is OK
      $php_ok=true;
      $install_possible=true;
      break;
    }
  }
}
unset($_pcpin_key);
unset($_pcpin_val);
unset($_pcpin_php_needed);
unset($_pcpin_php_exists);

// Get GD version
$gd=0;
$gd_string='Not installed';
if (function_exists('gd_info')) {
  if ($gd_info=@gd_info()) {
    $gd_string=ereg_replace('[[:alpha:][:space:]()]+', '', $gd_info['GD Version']);
    $gd=substr($gd_string, 0, strpos($gd_string, '.'));
  }
}

// Check mbstring
$mbstring_ok=false;
if (extension_loaded('mbstring')) {
  $mbstring_ok=true;
}

// Check MySQL extension
$mysql_ok=false;
if (function_exists('mysql_connect')) {
  $mysql_ok=true;
} else {
  $install_possible=false;
}

?>
<table class="tbl" cellspacing="0" cellpadding="0" width="100%">
  <tr>
    <td class="tbl_header_main" colspan="4">
      Server information
    </td>
  </tr>
  <tr>
    <td class="tbl_header_sub">
      <b>&nbsp;</b>
    </td>
    <td class="tbl_header_sub" style="text-align:center">
      <b>Local value</b>
    </td>
    <td class="tbl_header_sub" style="text-align:center">
      <b>Minimum recommended value</b>
    </td>
    <td class="tbl_header_sub" style="text-align:center">
      <b>Status</b>
    </td>
  </tr>
  <tr>
    <td class="tbl_row">
      PHP engine version
    </td>
    <td class="tbl_row" style="text-align:center">
      <?php echo htmlspecialchars(phpversion()) ?>
    </td>
    <td class="tbl_row" style="text-align:center">
      <?php echo htmlspecialchars(PCPIN_REQUIRESPHP) ?>
    </td>
    <td class="tbl_row" style="text-align:center">
<?php
if (!$php_ok) {
?>
      <b style="color:#ff0000">FATAL</b>
      <br />
      PCPIN Chat 6 cannot be installed<br />on this server
<?php
} else {
?>
      <b style="color:#008800">OK</b>
<?php
}
?>
    </td>
  </tr>

  <tr>
    <td class="tbl_row">
      GD library version
    </td>
    <td class="tbl_row" style="text-align:center">
      <?php echo htmlspecialchars($gd_string) ?>
    </td>
    <td class="tbl_row" style="text-align:center">
      2
    </td>
    <td class="tbl_row" style="text-align:center">
<?php
if ($gd<2) {
?>
      <b style="color:#dd9900">WARNING</b>
      <br />
      Some image features<br />will be not available
<?php
} else {
?>
      <b style="color:#008800">OK</b>
<?php
}
?>
    </td>
  </tr>

  <tr>
    <td class="tbl_row">
      Multibyte support (mbstring)
    </td>
    <td class="tbl_row" style="text-align:center">
      <?php echo $mbstring_ok? 'Installed' : 'Not installed' ?>
    </td>
    <td class="tbl_row" style="text-align:center">
      Installed
    </td>
    <td class="tbl_row" style="text-align:center">
<?php
if (!$mbstring_ok) {
?>
      <b style="color:#dd9900">WARNING</b>
      <br />
      This feature<br />is recommended
<?php
} else {
?>
      <b style="color:#008800">OK</b>
<?php
}
?>
    </td>
  </tr>

  <tr>
    <td class="tbl_row">
      MySQL support
    </td>
    <td class="tbl_row" style="text-align:center">
      <?php echo $mysql_ok? 'Installed' : 'Not installed' ?>
    </td>
    <td class="tbl_row" style="text-align:center">
      Installed
    </td>
    <td class="tbl_row" style="text-align:center">
<?php
if (!$mysql_ok) {
?>
      <b style="color:#ff0000">FATAL</b>
      <br />
      PCPIN Chat 6 cannot be installed<br />on this server
<?php
} else {
?>
      <b style="color:#008800">OK</b>
<?php
}
?>
    </td>
  </tr>

  <tr>
    <td class="tbl_row" style="text-align: right" colspan="4">
      <button type="button" onclick="window.history.go(-1)" title="Back">Back</button>
      &nbsp;
      <button type="button" onclick="<?php if (!$install_possible) echo 'alert(\'PCPIN Chat 6 cannot be installed on this server.\'); return false; '; ?>window.location.href='./install.php?step=4&ts='+unixTimeStamp()" title="Continue">Continue</button>
    </td>
  </tr>

</table>