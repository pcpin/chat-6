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
 Following variables are available:
+------------------------------------+------------------------------------------------------------+
| Variable name                      | Variable description                                       |
+------------------------------------+------------------------------------------------------------+
| $_pcpin_online_users_count         | Online users count.                                        |
+------------------------------------+------------------------------------------------------------+
| $_pcpin_online_users               | An array with online users' names.                         |
+------------------------------------+------------------------------------------------------------+
| $_pcpin_online_users_colored       | An array with HTML-colored online users' names.            |
+------------------------------------+------------------------------------------------------------+
| $_pcpin_registered_users_count     | Registered users count.                                    |
+------------------------------------+------------------------------------------------------------+
| $_pcpin_registered_users           | An array with registered users' names.                     |
+------------------------------------+------------------------------------------------------------+
| $_pcpin_registered_users_colored   | An array with HTML-colored registered users' names.        |
+------------------------------------+------------------------------------------------------------+
| $_pcpin_rooms_count                | Chat rooms count.                                          |
+------------------------------------+------------------------------------------------------------+
| $_pcpin_rooms                      | Chat rooms' names.                                         |
+------------------------------------+------------------------------------------------------------+
*/


header('Content-Type: text/html; charset=UTF-8');
header('Expires: '.gmdate('D, d M Y H:i:s').' GMT');
header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
header('Pragma: public');

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
  <meta http-equiv="Content-Type" content="text/xml; charset=utf-8" />
</head>
<body>
<div align="center">
  <h2>This is an example page. You may edit it as you need.</h2>
  <table border="1" cellspacing="0" cellpadding="5">
    <tr valign="top">
      <td>Online users count:</td>
      <td><?php echo $_pcpin_online_users_count; ?></td>
    </tr>
    <tr valign="top">
      <td>Online users:</td>
      <td><?php echo implode(', ', $_pcpin_online_users); ?>&nbsp;</td>
    </tr>
    <tr valign="top">
      <td>Online users (colored):</td>
      <td><?php echo implode(', ', $_pcpin_online_users_colored); ?>&nbsp;</td>
    </tr>
    <tr valign="top">
      <td>Registered users count:</td>
      <td><?php echo $_pcpin_registered_users_count; ?></td>
    </tr>
    <tr valign="top">
      <td>Registered users:</td>
      <td><?php echo implode(', ', $_pcpin_registered_users); ?></td>
    </tr>
    <tr valign="top">
      <td>Registered users (colored):</td>
      <td><?php echo implode(', ', $_pcpin_registered_users_colored); ?></td>
    </tr>
    <tr valign="top">
      <td>Chat rooms count:</td>
      <td><?php echo $_pcpin_rooms_count; ?></td>
    </tr>
    <tr valign="top">
      <td>Chat rooms:</td>
      <td><?php echo implode(', ', $_pcpin_rooms); ?></td>
    </tr>
  </table>
</div>
</body>
</html>
