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
 * Class PCPIN_TCP
 * Contains methods for TCP communication
 * @static
 * @author Konstantin Reznichak <k.reznichak@pcpin.com>
 * @copyright Copyright &copy; 2006, Konstantin Reznichak
 */
class PCPIN_TCP {



  /**
   * Check if are any DNS records corresponding to a given Internet host name or IP address
   * @param   string  $hostname   Host name or IP address
   * @return  boolean  TRUE if any records are found or FALSE if no records were found or if an error occurred
   */
  function checkDNS_record($hostname='') {
    $result=false;
    $hostname=strtolower(trim($hostname));
    if ($hostname!='') {
      if (function_exists('checkdnsrr')) {
        // Non-Windows platform
        $result=checkdnsrr($hostname, 'ANY');
      } else {
        // Windows platform
        $output=null;
        @exec('nslookup.exe -type=ANY '.$hostname, $output);
        if (!empty($output)) {
          foreach ($output as $line) {
            if (0===strpos(strtolower($line), $hostname)) {
              // DNS record found
              $result=true;
              break;
            }
          }
        }
      }
    }
    return $result;
  }


  /**
   * Get MX records as IP addresses corresponding to a given
   * Internet host name sorted by weight
   * @param   string  $hostname   Host name
   * @return  array   Array with IP addresses
   */
  function getMXRecords($hostname='') {
    $ips=array();
    if ($hostname!='') {
      $records=array();
      if (function_exists('getmxrr')) {
        // Non-Windows platform
        $mxhosts=null;
        $weights=null;
        if (false!==getmxrr($hostname, $mxhosts, $weights)) {
          // Sort MX records by weight
          $key_host=array();
          foreach ($mxhosts as $key=>$host) {
            if (!isset($key_host[$weights[$key]])) {
              $key_host[$weights[$key]]=array();
            }
            $key_host[$weights[$key]][]=$host;
          }
          unset($weights);
          $records=array();
          ksort($key_host);
          foreach ($key_host as $hosts) {
            foreach ($hosts as $host) {
              $records[]=$host;
            }
          }
        }
      } else {
        // Windows platform
        $result=shell_exec('nslookup.exe -type=MX '.$hostname);
        if ($result!='') {
          $matches=null;
          if (preg_match_all("'^.*MX preference = (\d{1,10}), mail exchanger = (.*)$'simU", $result, $matches)) {
            if (!empty($matches[2])) {
              array_shift($matches);
              array_multisort($matches[0], $matches[1]);
              $records=$matches[1];
            }
          }
        }
      }
    }
    // Resolve host names
    if (!empty($records)) {
      foreach ($records as $rec) {
        if ($resolved=gethostbynamel($rec)) {
          foreach ($resolved as $ip) {
            $ips[]=$ip;
          }
        }
      }
    }
    return $ips;
  }


  /**
   * Open socket connection to specified host
   * @param   resource  $conn       A reference to connection handler
   * @param   int       $errno      If an error occured: error number
   * @param   string    $errstr     If an error occured: error description
   * @param   string    $hostname   Host name or IP address
   * @param   int       $timeout    Connection timeout
   * @return  boolean
   */
  function connectHost(&$conn, &$errno, &$errstr, $host='', $timeout=30) {
    if ($host!='') {
      $errno=null;
      $errstr=null;
      $conn=fsockopen(gethostbyname($host), 25, $errno, $errstr, $timeout);
      if (false===$conn || !is_resource($conn)) {
        $conn=null;
        $result=false;
      } else {
        $result=true;
      }
    }
    return $result;
  }


  /**
   * Reads line from a socket connection. Lines must end with CRLF sequence
   * @param   resource  $conn       A reference to connection handler
   * @param   string    $line       A reference to read line
   * @return  boolean   TRUE on success or FALSE on error
   */
  function readLineConn(&$conn, &$line) {
    $result=false;
    $line='';
    if (!empty($conn) && is_resource($conn)) {
      $char='';
      $last_char='';
      do {
        $last_char=$char;
        if (false===$char=fgetc($conn)) {
          break;
        } else {
          $line.=$char;
        }
      } while (($last_char.$char)!="\r\n" && $char!==false);
      if ($line!='') {
        $result=true;
      }
    }
    return $result;
  }


  /**
   * Reads the last line from a socket connection
   * @param   resource  $conn       A reference to connection handler
   * @param   string    $line       A reference to read line
   * @return  boolean   TRUE on success or FALSE on error
   */
  function readLastLineConn(&$conn, &$line) {
    $result=false;
    $line='';
    if (!empty($conn) && is_resource($conn) && !feof($conn)) {
      while (PCPIN_TCP::readLineConn($conn, $line)) {
        if ($line=='') {
          break;
        } elseif (substr($line, 3, 1)==' ') {
          $result=true;
          break;
        }
      }
    }
    return $result;
  }


  /**
   * Parses status code from response line
   * @param   string    $line       Response line
   * @return  int   Status code
   */
  function getStatus($line='') {
    $status=0;
    if ($line!='') {
      $status=(int)substr($line, 0, strpos($line, ' '));
    }
    return $status;
  }


  /**
   * Send data to a socket connection
   * @param   resource  $conn       A reference to connection handler
   * @param   string    $data       Data to send
   * @return  boolean   TRUE on success or FALSE on error
   */
  function writeDataConn(&$conn, $data='') {
    $result=false;
    if (is_resource($conn)) {
      $result=fwrite($conn, $data);
    }
    return $result;
  }


}
?>