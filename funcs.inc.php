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
 * This file contains function declarations
 */


if (!function_exists('pcpin_ctype_digit')) {
  /**
   * Checks if all of the characters in the provided string are numerical
   * @param   mixed     $text   The tested string
   * @return  boolean   TRUE if every character in $text is a decimal digit, FALSE otherwise
   */
  function pcpin_ctype_digit($text='') {
    $result=false;
    if (is_scalar($text) && $text!='') {
      $result=true;
      $text_len=strlen($text);
      for ($i=0; $i<$text_len; $i++) {
        $c=ord(substr($text, $i, 1));
        if ($c<48 || $c>57) {
          $result=false;
          break;
        }
      }
    }
    return $result;
  }
}


if (!function_exists('PCPIN_ErrorHandler')) {
  // Some PHP5 constants
  /**
   * Custom error handler
   * @param   int       $errlvl     Level of the error raised
   * @param   string    $errstr     Error message
   * @param   string    $errfile    Filename that the error was raised in
   * @param   int       $errline    Line number the error was raised at
   * @return  boolean   TRUE if every character in $text is a decimal digit, FALSE otherwise
   */
  function PCPIN_ErrorHandler($errlvl, $errstr, $errfile, $errline) {
    $error_type='';
    if ($errlvl===E_NOTICE) {
      $error_type='Notice';
    } elseif ($errlvl===E_WARNING || $errlvl===E_CORE_WARNING || $errlvl===E_COMPILE_WARNING) {
      $error_type='Warning';
    } elseif ($errlvl===E_ERROR || $errlvl===E_CORE_ERROR || $errlvl===E_COMPILE_ERROR) {
      $error_type='Fatal error';
    } elseif ($errlvl===E_PARSE) {
      $error_type='Parse error';
    } elseif (defined('E_STRICT') && $errlvl===E_STRICT) {
      if (PCPIN_DEBUGMODE_STRICT) {
        $error_type='Strict Standards';
      }
    } else {
      $error_type='Error';
    }
    if ($error_type!='') {
      @error_log("[".date('Y-m-d H:i:s')."] $error_type: $errstr in $errfile on line $errline\n", 3, PCPIN_ERRORLOG);
    }
  }
}

if (!function_exists('_pcpin_loadClass')) {
  /**
   * Load class file, if not loaded yet
   * @param   string    $class      Class name
   */
  function _pcpin_loadClass($class) {
    require_once(PCPIN_CHAT_ROOT_DIR.'/class/'.strtolower(trim($class)).'.class.php');
  }
}

/**
 *********************************************************************************************
 * Here comes the mbstring UTF-8 stuff
 *********************************************************************************************
 */
if (extension_loaded('mbstring')) {
  // Set internal mbstring encoding to UTF-8
  mb_internal_encoding('UTF-8');
  // Get function overloading mbstring setting
  $_pcpin_mb_overloading=ini_get('mbstring.func_overload');
  // Define wether to call mb_* instead of *
  define('PCPIN_MB_PREFIX_MAIL', ( $_pcpin_mb_overloading &1 )? '' : 'mb_'); // mail* overloading already active
  define('PCPIN_MB_PREFIX_STR', ( $_pcpin_mb_overloading &2 )? '' : 'mb_'); // str* overloading already active
  define('PCPIN_MB_PREFIX_EREG', ( $_pcpin_mb_overloading &4 )? '' : 'mb_'); // ereg* overloading already active
} else {
  // Force overloading to itself. The code will be not UTF-8 safe.
  // ... waiting for PHP6 (c'mon guys, give power)
  define('PCPIN_MB_PREFIX_MAIL', '');
  define('PCPIN_MB_PREFIX_STR', '');
  define('PCPIN_MB_PREFIX_EREG', '');
}

// mail*() functions
if (!function_exists('_pcpin_mail')) {
  function _pcpin_mail() {
    $args=func_get_args();
    return call_user_func_array(PCPIN_MB_PREFIX_MAIL.'mail', $args);
  }
}

// str*() functions
if (!function_exists('_pcpin_strlen')) {
  function _pcpin_strlen() {
    $args=func_get_args();
    return call_user_func_array(PCPIN_MB_PREFIX_STR.'strlen', $args);
  }
}
if (!function_exists('_pcpin_strpos')) {
  function _pcpin_strpos() {
    $args=func_get_args();
    return call_user_func_array(PCPIN_MB_PREFIX_STR.'strpos', $args);
  }
}
if (!function_exists('_pcpin_strrpos')) {
  function _pcpin_strrpos() {
    $args=func_get_args();
    return call_user_func_array(PCPIN_MB_PREFIX_STR.'strrpos', $args);
  }
}
if (!function_exists('_pcpin_substr')) {
  function _pcpin_substr() {
    $args=func_get_args();
    return call_user_func_array(PCPIN_MB_PREFIX_STR.'substr', $args);
  }
}
if (!function_exists('_pcpin_strtolower')) {
  function _pcpin_strtolower() {
    $args=func_get_args();
    return call_user_func_array(PCPIN_MB_PREFIX_STR.'strtolower', $args);
  }
}
if (!function_exists('_pcpin_strtoupper')) {
  function _pcpin_strtoupper() {
    $args=func_get_args();
    return call_user_func_array(PCPIN_MB_PREFIX_STR.'strtoupper', $args);
  }
}
if (!function_exists('_pcpin_substr_count')) {
  function _pcpin_substr_count() {
    $args=func_get_args();
    return call_user_func_array(PCPIN_MB_PREFIX_STR.'substr_count', $args);
  }
}

// ereg*() functions
if (!function_exists('_pcpin_ereg')) {
  function _pcpin_ereg() {
    $args=func_get_args();
    return call_user_func_array(PCPIN_MB_PREFIX_EREG.'ereg', $args);
  }
}
if (!function_exists('_pcpin_eregi')) {
  function _pcpin_eregi() {
    $args=func_get_args();
    return call_user_func_array(PCPIN_MB_PREFIX_EREG.'eregi', $args);
  }
}
if (!function_exists('_pcpin_ereg_replace')) {
  function _pcpin_ereg_replace() {
    $args=func_get_args();
    return call_user_func_array(PCPIN_MB_PREFIX_EREG.'ereg_replace', $args);
  }
}
if (!function_exists('_pcpin_eregi_replace')) {
  function _pcpin_eregi_replace() {
    $args=func_get_args();
    return call_user_func_array(PCPIN_MB_PREFIX_EREG.'eregi_replace', $args);
  }
}
if (!function_exists('_pcpin_split')) {
  function _pcpin_split() {
    $args=func_get_args();
    return call_user_func_array(PCPIN_MB_PREFIX_EREG.'split', $args);
  }
}

?>