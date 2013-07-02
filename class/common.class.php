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
 * Class PCPIN_Common
 * Contains commonly used static-only methods
 * @static
 * @author Konstantin Reznichak <k.reznichak@pcpin.com>
 * @copyright Copyright &copy; 2007, Konstantin Reznichak
 */
class PCPIN_Common {


  /**
   * Removes slashes from all scalar array values recursively
   * @param   array     $target               Target array
   * @param   boolean   $magic_quotes_sybase  Use magic_quotes_sybase stripping only?
   * @return  array     Array with stripped slashes
   */
  function stripSlashesRecursive($target, $magic_quotes_sybase=false) {
    if (!empty($target) && is_array($target)) {
      foreach ($target as $key=>$val) {
        if (is_array($val)) {
          // Value is an array. Start recursion.
          $target[$key]=PCPIN_Common::stripSlashesRecursive($val, $magic_quotes_sybase);
        } elseif (is_scalar($val)) {
          // Strip slashes from scalar value
          if ($magic_quotes_sybase) {
            $target[$key]=str_replace("''", "'", $val);
          } else {
            $target[$key]=stripslashes($val);
          }
        } else {
          // Leave value unchanged.
          $target[$key]=$val;
        }
      }
    }
    return $target;
  }


  /**
   * Convert MySQL DATETIME string into UNIX TIMESTAMP
   * @param    string    $datetime     Date in MySQL TIMESTAMP format
   * @return   int       $timestamp    Date im UNIX TIMESTAMP format
   */
  function datetimeToTimestamp($datetime='') {
    $timestamp=0;
    if (strlen($datetime)==19) {
      // Generate timestamp
      $timestamp=@mktime(substr($datetime, 11, 2),
                         substr($datetime, 14, 2),
                         substr($datetime, 17, 2),
                         substr($datetime, 5, 2),
                         substr($datetime, 8, 2),
                         substr($datetime, 0, 4));
    }
    return $timestamp;
  }

  /**
   * Generate random string from pattern
   * @param   int       $length     Desired string length
   * @param   string    $pattern    Pattern to use
   * @param   boolean   $binary     Optional. If TRUE, then the pattern is a binary string and will be handled byte-by-byte.
   * @return  string    Generated random string
   */
  function randomString($length=0, $pattern='ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789', $binary=false) {
    $out='';
    if ($length>0 && $pattern!='') {
      if (!$binary) {
        // Pattern is a text string
        $pattern_length_minus_one=strlen($pattern)-1;
        for ($i=0; $i<$length; $i++) {
          $out.=substr($pattern, mt_rand(0, $pattern_length_minus_one), 1);
        }
      } else {
        // Pattern is a binary string
        $pattern_length_minus_one=_pcpin_strlen($pattern)-1;
        for ($i=0; $i<$length; $i++) {
          $out.=_pcpin_substr($pattern, mt_rand(0, $pattern_length_minus_one), 1);
        }
      }
    }
    return $out;
  }

  /**
   * Display error message and terminate program
   * @param    int       $errno    Error number to return
   * @param    string    $errstr   Error string to display
   */
  function dieWithError($errno=0, $errstr='') {
    echo $errstr;
    exit($errno);
  }


  /**
   * E-Mail address validator
   * @param   string  $email  E-Mail address
   * @param   int     $level    Validation level
   *                              Value     Description
   *                                0         No validation
   *                                1         Well-formness check
   *                                2         Hostname (or DNS record, if Hostname failed) resolution
   *                                3         Recipient account availability check (violates RFC, use with care!)
   * @return  boolean TRUE if email address is valid or FALSE if not
   */
  function checkEmail($email='', $level=1) {
    $valid=false;
    $email=trim($email);
    if ($email!='') {
      $valid=true;
      if ($level>=1) {
        // Well-formness check
        $valid = (bool) preg_match('/^([a-zA-Z0-9]+[\._-]?)+[a-zA-Z0-9]+@(((([a-zA-Z0-9]+-?)+[a-zA-Z0-9]+)|([a-zA-Z0-9]{2,}))+\.)+[a-zA-Z]{2,4}$/', $email);
        if ($valid && $level>=2) {
          // Hostname (or DNS record, if Hostname failed) resolution
          $hostname=strtolower(substr($email, strpos($email, '@')+1));
          $host=gethostbyname($hostname);
          if ($host==$hostname) {
            $host='';
          }
          if ($host=='') {
            // Hostname resolutiion failed
            // Check DNS record
            $valid=PCPIN_TCP::checkDNS_record($hostname);
          } else {
            $valid=true;
          }
          if ($valid && $level>=3) {
            // Recipient account availability check
            $valid=false;
            // Get MX records
            $ips=PCPIN_TCP::getMXRecords($hostname);
            if (empty($ips)) {
              // No MX records found. Using Hostname.
              $ips=gethostbynamel($hostname);
            }
            // Trying to open connection
            $conn=false;
            foreach ($ips as $ip) {
              $conn=null;
              $errno=null;
              $errstr=null;
              if (PCPIN_TCP::connectHost($conn, $errno, $errstr, $ip, 10)) {
                // Connection opened
                break;
              }
            }
            $sender_host=(!empty($_SERVER['HTTP_HOST']) && $_SERVER['HTTP_HOST']!='')? $_SERVER['HTTP_HOST'] : 'UNKNOWN.HOST';
            if (!empty($conn)) {
              $line='';
              // Gest SMTP server signature
              if (PCPIN_TCP::readLastLineConn($conn, $line)) {
                if (220===PCPIN_TCP::getStatus($line)) {
                  // Send 'HELO' command
                  if (PCPIN_TCP::writeDataConn($conn, "HELO $sender_host\r\n")) {
                    // Get an answer
                    if (PCPIN_TCP::readLastLineConn($conn, $line)) {
                      // Check response status
                      if (250===PCPIN_TCP::getStatus($line)) {
                        // Start email conversation
                        if (PCPIN_TCP::writeDataConn($conn, "MAIL FROM: <test@$sender_host>\r\n")) {
                          // Get an answer
                          if (PCPIN_TCP::readLastLineConn($conn, $line)) {
                            // Check response status
                            if (250===PCPIN_TCP::getStatus($line)) {
                              // Specify recipient mailbox
                              if (PCPIN_TCP::writeDataConn($conn, "RCPT TO: <".$email.">\r\n")) {
                                // Get an answer
                                if (PCPIN_TCP::readLastLineConn($conn, $line)) {
                                  // Status 250: mailbox exists :)
                                  $valid=250===PCPIN_TCP::getStatus($line);
                                }
                              }
                            }
                          }
                        }
                      }
                    }
                  }
                }
              }
            }
          }
        }
      }
    }
    return $valid;
  }


   /**
    * Determines platform (OS), browser and version of the user
    * @param  string  $user_agent  Contents of 'User-Agent' header sent by client's browser
    * @param  string  &$os         A reference to variable where OS version will be saved
    * @param  string  &$agnt_name  A reference to variable where browser vendor will be saved
    * @param  string  &$agnt_ver   A reference to variable where browser version will be saved
    */
   function getClientInfo($user_agent='', &$os, &$agnt_name, &$agnt_ver) {
     // OS
     $oses=array('Win', 'Mac', 'Linux', 'Unix', 'OS/2', 'Other');
     foreach ($oses as $os_name) {
       $os=$os_name;
       if (false!==strpos($user_agent, $os_name)) {
         break;
       }
     }
    // Browser name and version
    if (preg_match('@Opera(/| )([0-9\.]+)@', $user_agent, $matches)) {
      $agnt_ver=$matches[2];
      $agnt_name='OPERA';
    } elseif (preg_match('@MSIE ([0-9\.]+)@', $user_agent, $matches)) {
      $agnt_ver=$matches[1];
      $agnt_name='IE';
    } elseif (preg_match('@OmniWeb/([0-9\.]+)@', $user_agent, $matches)) {
      $agnt_ver=$matches[1];
      $agnt_name='OMNIWEB';
    } elseif (preg_match('@(Konqueror/)(.*)(;)@', $user_agent, $matches)) {
      $agnt_ver=$matches[2];
      $agnt_name='KONQUEROR';
    } elseif (   preg_match('@Mozilla/([0-9\.]+)@', $user_agent, $matches)
              && preg_match('@Safari/([0-9\.]*)@', $user_agent, $matches2)) {
      $agnt_ver=$matches[1].'.'.$matches2[1];
      $agnt_name='SAFARI';
    } elseif (preg_match('@Mozilla/([0-9\.]+)@', $user_agent, $matches)) {
      $agnt_ver=$matches[1];
      $agnt_name='MOZILLA';
    } else {
      $agnt_ver=0;
      $agnt_name='OTHER';
    }
  }


  /**
   * Guess host OS family. Returns "windows", "unix" or "unknown"
   * @return    string
   */
  function guessOS() {
    $os='unknown';
    if (!isset($_SERVER)) $_SERVER=array();
    if (!isset($_ENV)) $_ENV=array();
    $v=array_merge($_ENV, $_SERVER);
    if (isset($v['OS']) && $v['OS']!='') {
      if (0===strpos(strtolower($v['OS']), 'win')) {
        $os='windows';
      } else {
        $os='unix';
      }
    } elseif (isset($v['PATH']) && $v['PATH']!='') {
      if (false!==strpos($v['PATH'], '\\')) {
        $os='windows';
      } elseif (false!==strpos($v['PATH'], '/')) {
        $os='unix';
      }
    }
    if ($os=='unknown' && isset($v['Path']) && $v['Path']!='') {
      if (false!==strpos($v['Path'], '\\')) {
        $os='windows';
      } elseif (false!==strpos($v['Path'], '/')) {
        $os='unix';
      }
    }
    return $os;
  }


  /**
   * Decode HEX-encoded binary string
   * @param   string    $encoded    Encoded string
   * @return  string
   */
  function hexToString($encoded='') {
    $decoded='';
    if (is_scalar($encoded) && $encoded!='') {
      $len=strlen($encoded);
      if ($len%2) {
        $encoded='0'.$encoded;
        $len++;
      }
      for ($i=0; $i<$len; $i+=2) {
        $decoded.=chr(hexdec(substr($encoded, $i, 2)));
      }
    }
    return $decoded;
  }
   

}
?>