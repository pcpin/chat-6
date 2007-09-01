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
 * Class PCPIN_Ping
 * Executes ping command
 * @static
 * @author Konstantin Reznichak <k.reznichak@pcpin.com>
 * @copyright Copyright &copy; 2006, Konstantin Reznichak
 */
class PCPIN_Ping {


  /**
   * Send ICMP ECHO_REQUEST to network host using system binaries.
   * Returns an array containing ping times in milliseconds for each request.
   * This method may fail, if:
   *    - Web server has no access to system "ping" binary (eg. runs in chrooted mode)
   *    - ICMP ping requests are blocked by the firewall
   *    - shell_exec() function is disabled in php.ini
   * @param     string    $host     Hostname or IP address
   * @param     int       $count    Stop after sending count ECHO_REQUEST packets
   * @return  array
   */
  function icmp_ping($host, $count=3) {
    $ping_result=array();
    $host=trim($host);
    if ($host!='' && $count>0) {
      $os=PCPIN_Common::guessOS();
      $result=false;
      switch ($os) {

        case 'windows'  :
          $result=shell_exec('ping -n '.$count.' '.$host);
        break;

        case 'unix'  :
          $result=shell_exec('ping -c '.$count.' '.$host);
        break;

      }
      // Parse result
      if (!empty($result)) {
        $data=explode("\n", str_replace("\r", "\n", $result));
        foreach ($data as $line) {
          $line=strtolower($line);
          if (false!==strpos($line, 'ms') && false!==strpos($line, 'ttl') && (false!==strpos($line, '=') || false!==strpos($line, '<'))) {
            $parts=explode('=', str_replace('<', '=', $line));
            foreach ($parts as $part) {
              if (false!==strpos($part, 'ms')) {
                $ping_result[]=trim(substr($part, 0, strpos($part, 'ms')));
                break;
              }
            }
          }
        }
      }
    }
    return $ping_result;
  }


}
?>