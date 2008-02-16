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
 * Class PCPIN_DB
 * Manage database operations.
 * @author Konstantin Reznichak <k.reznichak@pcpin.com>
 * @copyright Copyright &copy; 2007, Konstantin Reznichak
 */
class PCPIN_DB {

  /**
   * Database connection handler
   * @var   resource
   */
  var $_db_conn=null;

  /**
   * Array with listed rows
   * @var   array
   */
  var $_db_list=null;

  /**
   * Elements count in $_db_list array
   * @var   int
   */
  var $_db_list_count=0;

  /**
   * Original client-server charsets
   * @var   array
   */
  var $_db_client_server_charsets=null;


  /**
   * Constructor.
   * Connect to database.
   * @param   object  &$caller        Caller object
   * @param   array   $db_conndata    Database connection data
   */
  function PCPIN_DB(&$caller, $db_conndata) {
    // Query args separator. DO NOT CHANGE!!!
    if (!defined('PCPIN_SQLQUERY_ARG_SEPARATOR_START')) define('PCPIN_SQLQUERY_ARG_SEPARATOR_START', chr(0).chr(255).PCPIN_Common::randomString(10));
    // Query args separator. DO NOT CHANGE!!!
    if (!defined('PCPIN_SQLQUERY_ARG_SEPARATOR_END')) define('PCPIN_SQLQUERY_ARG_SEPARATOR_END', chr(255).chr(0).PCPIN_Common::randomString(10));
    // Connect to database
    if (empty($this->_db_conn)) {
      if (!function_exists('mysql_connect')) {
        // MySQL extension is not loaded
        PCPIN_Common::dieWithError(1, '<b>Fatal error</b>: MySQL extension is not loaded');
      } elseif (!$this->_db_conn=mysql_connect($db_conndata['server'], $db_conndata['user'], $db_conndata['password'])) {
        // Failed to connect database server
        PCPIN_Common::dieWithError(1, '<b>Fatal error</b>: Failed to connect database server');
      } else {
        // Database server connected
        // Set UTF-8 character set for client-server communication
        $this->_db_setCharsets();
        // Disable MySQL strict mode
        $this->_db_query('SET SESSION sql_mode=""');
        // Trying do select database
        if (!mysql_select_db($db_conndata['database'], $this->_db_conn)) {
          // Failed to select database
          PCPIN_Common::dieWithError(1, '<b>Fatal error</b>: Failed to select database');
          $this->_db_close();
        } else {
          // Define database table names prefix
          if (!defined('PCPIN_DB_PREFIX')) define('PCPIN_DB_PREFIX', $db_conndata['tbl_prefix']);
        }
      }
    }
    unset($db_conndata);
    $this->_cache['_db_tabledata']=array(); // Cached table information ($this->_cache is a property of the parent class)
    $this->_db_pass_vars($this, $caller);
  }


  /**
   * Set UTF-8 character set for client-server communication
   */
  function _db_setCharsets() {
    // Store original charset settings
    if (empty($this->_db_client_server_charsets)) {
      $this->_db_client_server_charsets=array();
      $result=$this->_db_query('SHOW VARIABLES LIKE "character\_set\_%"');
      while ($data=$this->_db_fetch($result, MYSQL_NUM)) {
        $this->_db_client_server_charsets[$data[0]]=$data[1];
      }
    }
    // Set new charsets
    $this->_db_query('SET NAMES "utf8"');
  }


  /**
   * Restore original character sets for client-server communication
   */
  function _db_restoreCharsets() {
    if (!empty($this->_db_client_server_charsets)) {
      $result=$this->_db_query('SHOW VARIABLES LIKE "character\_set\_%"');
      while ($data=$this->_db_fetch($result, MYSQL_NUM)) {
        if (isset($this->_db_client_server_charsets[$data[0]]) && $this->_db_client_server_charsets[$data[0]]!=$data[1]) {
          $this->_db_query('SET '.$data[0].' = "'.$this->_db_client_server_charsets[$data[0]].'"');
        }
      }
    }
    $this->_db_client_server_charsets=array();
  }


  /**
   * Free result memory
   * @param   resource    &$result    Result identifier
   */
  function _db_freeResult(&$result) {
    if (is_resource($result)) {
      mysql_free_result($result);
    }
  }


  /**
   * Close database connection
   */
  function _db_close() {
    if (is_resource($this->_db_conn)) {
      mysql_close($this->_db_conn);
    }
    $this->_db_conn=null;
  }


  /**
   * Detect MySQL server software version
   * @return  mixed   Server software version string or false on error
   */
  function _db_mysql_version() {
    $version=false;
    if (is_resource($this->_db_conn)) {
      $result=$this->_db_query('SELECT VERSION()');
      if ($data=$this->_db_fetch($result, MYSQL_NUM)) {
        $version=$data[0];
      }
      $this->_db_freeResult($result);
    }
    return $version;
  }


  /**
   * Execute SQL query and return resulted resource
   * @param   string    $query        Query to execute
   * @param   boolean   $unbuffered   If TRUE, then query will be executed in unbuffered mode
   *                                  (without fetching and buffering the result rows). USE WITH CARE!!!
   * @return  mixed   Query execution resource or false on error
   */
  function _db_query($query='', $unbuffered=false) {
    $result=false;
    if (!empty($query)) {
      if (PCPIN_DEBUGMODE && (PCPIN_LOG_TIMER || PCPIN_SHOW_SLOW_QUERIES>0)) {
        $timer_start=microtime();
        if (PCPIN_LOG_TIMER && !isset($_GET['_pcpin_log_mysql_usage'])) {
          $_GET['_pcpin_log_mysql_usage']=0.0;
        }
      }
      if (true!==$unbuffered) {
        // Buffered query. Default.
        $result=mysql_query($query, $this->_db_conn);
      } else {
        // Unbuffered query. Use with care!
        $result=mysql_unbuffered_query($query, $this->_db_conn);
      }
#echo "$query\n\n";
      if (PCPIN_DEBUGMODE && (PCPIN_LOG_TIMER || PCPIN_SHOW_SLOW_QUERIES>0)) {
        $end_times=explode(' ', microtime());
        $start_times=explode(' ', $timer_start);
        $start=1*(substr($start_times[1], -5).substr($start_times[0], 1, 5));
        $end=1*(substr($end_times[1], -5).substr($end_times[0], 1, 5));
        $diff=$end-$start;
        if (PCPIN_LOG_TIMER) {
          $_GET['_pcpin_log_mysql_usage']+=$diff;
        }
        if (PCPIN_SHOW_SLOW_QUERIES>0 && $diff>PCPIN_SHOW_SLOW_QUERIES) {
          $diff_str=explode('.', round($diff, 4));
          if (!isset($diff_str[1])) {
            $diff_str[1]='';
          }
          $diff_str[1]=str_pad($diff_str[1], 4, '0', STR_PAD_RIGHT);
          if (PCPIN_SQL_LOGFILE=='*') {
            echo '<b>'.implode('.', $diff_str).":</b> $query <br />\n";
            flush();
          } elseif (PCPIN_SQL_LOGFILE!='' && $lfh=@fopen(PCPIN_SQL_LOGFILE, 'a')) {
            @fwrite($lfh, implode('.', $diff_str).': '.$query."\n");
            @fclose($lfh);
          }
        }
      }
      if (!$result) {
        // An error occured
        if (PCPIN_DEBUGMODE && PCPIN_SHOW_MYSQL_ERRORS) {
          $errno=mysql_errno($this->_db_conn);
          $errstr=mysql_error($this->_db_conn);
          if (PCPIN_SQL_LOGFILE=='*') {
            echo nl2br("Query:\n$query\nERROR ($errno): $errstr\n---------------------------------------\n");
            flush();
          } elseif (PCPIN_SQL_LOGFILE!='' && $lfh=@fopen(PCPIN_SQL_LOGFILE, 'a')) {
            @fwrite($lfh, "Query:\n$query\nERROR ($errno): $errstr\n---------------------------------------\n");
            @fclose($lfh);
          }
        }
      }
    }
    return $result;
  }


  /**
   * Fetch a query execution result row as an associative array, a numeric array, or both.
   * @param   resource  $result       Query execution result
   * @param   int       $result_type  The type of array that is to be fetched
   * @return  mixed   Fetched array or false if no mere data present
   */
  function _db_fetch($result, $result_type=MYSQL_ASSOC) {
    $data=false;
    if (is_resource($result)) {
      // Determine field types
      $field_types=array();
      $i=0;
      $num_fields=mysql_num_fields($result);
      while ($i<$num_fields) {
        $meta=mysql_fetch_field($result, $i);
        $name=$meta->name;
        $type=strtolower($meta->type);
        // Map data types
        if ($type=='string') {
          $type='';
        } elseif (false!==strpos($type, 'int')) {
          $type='int';
        } elseif (   false!==strpos($type, 'dec')
                  || false!==strpos($type, 'float')
                  || false!==strpos($type, 'real')
                  || false!==strpos($type, 'double')) {
          $type='float';
        } else {
          $type='';
        }
        switch($result_type) {
          case MYSQL_ASSOC  :
          default           :   $field_types[$name]=$type;
                                break;
          case MYSQL_NUM    :   $field_types[$i]=$type;
                                break;
          case MYSQL_BOTH   :   $field_types[$name]=$type;
                                $field_types[$i]=$type;
                                break;
        }
        $i++;
      }
      $magic_quotes_runtime=get_magic_quotes_runtime();
      // Disable magic_quotes_runtime
      set_magic_quotes_runtime(0);
      $data=mysql_fetch_array($result, $result_type);
      // Restore an original magic_quotes_runtime setting
      set_magic_quotes_runtime($magic_quotes_runtime);
      // Cast types
      foreach ($field_types as $key=>$val) {
        if (!is_null($data[$key]) && $val!='') {
          settype($data[$key], $val);
        }
      }
    }
    return $data;
  }


  /**
   * Escapes special characters in a string for use in a SQL statement.
   * WARNING! Always use this methode for creating queries!
   * @param   string    $string             String to escape
   * @param   boolean   $escape_wildcards   If TRUE, then wildcard chars "%" and "_" will be also escaped
   * @return  string  Escaped string
   */
  function _db_escapeStr($string='', $escape_wildcards=true) {
    $result='';
    if (false===$result=mysql_real_escape_string($string, $this->_db_conn)) {
      // An error occured
      $result='';
    } elseif ($escape_wildcards) {
      $result=str_replace('_', '\\_', str_replace('%', '\\%', $result));
    }
    return $result;
  }


  /**
   * Load databse table row into an object
   * @param   mixed     $id         Table row ID field value
   * @param   string    $id_field   Table row ID field name
   * @return  boolean   TRUE on success or FALSE on error. If an error occured,
   *                    then object property with name specified by $id_field will be set to null
   */
  function _db_loadObj($id='', $id_field='id') {
    $ok=false;
    if (!empty($id_field)) {
      // Read record from database
      if ($this->_db_getList($id_field.' =# '.$id, 1)) {
        // Record found
        $ok=true;
        $this->_db_setObject($this->_db_list[0]);
      }
    }
    if (!$ok) {
      // An error occured
      if (!empty($id_field) && isset($this->$id_field)) {
        $this->$id_field=null;
      }
    }
    return $ok;
  }


  /**
   * Apply associative array to the current object
   * @param   array   $data     Data to apply
   */
  function _db_setObject($data) {
    if (!empty($data) && is_array($data)) {
      foreach ($data as $key=>$val) {
        if ((is_scalar($val) || is_null($val)) && (isset($this->$key) || is_null($this->$key))) {
          $this->$key=$val;
        }
      }
    }
  }


  /**
   * Store object properties into assotiative array
   * @return  array
   */
  function _db_getFromObject() {
    $data=array();
    $this_class=get_class($this);
    $this_vars=array_keys(get_class_vars($this_class));
    $parent_class=get_parent_class(get_class($this));
    if (!empty($parent_class)) {
      $parent_vars=get_class_vars($parent_class);
    } else {
      $parent_vars=array();
    }
    foreach ($this_vars as $var) {
      if (!array_key_exists($var, $parent_vars)) {
        $data[$var]=$this->$var;
      }
    }
    return $data;
  }


  /**
   * Updates databse table row with object's data. ID field will not be updated.
   * @param   mixed     $id         Table row ID field value
   * @param   string    $id_field   Table row ID field name
   * @return  boolean   TRUE on success or FALSE on error
   */
  function _db_updateObj($id='', $id_field='id') {
    $ok=false;
    $tbl=$this->_db_getTbl();
    if (!empty($tbl) && !empty($id_field)) {
      // Get table information
      $tabledata=$this->_db_tableFields($tbl);
      if (!empty($tabledata)) {
        $parts=array();
        foreach ($tabledata as $field) {
          if (is_null($this->$field['Field']) || isset($this->$field['Field']) && $field['Field']!=$id_field) {
            if (is_null($this->$field['Field'])) {
              $parts[]='`'.$field['Field'].'` = NULL';
            } elseif ($this->$field['Field']=='' && array_key_exists('Default', $field)) {
              $parts[]='`'.$field['Field'].'` = "'.$this->_db_escapeStr($field['Default'], false).'"';
            } else {
              $parts[]='`'.$field['Field'].'` = "'.$this->_db_escapeStr($this->$field['Field'], false).'"';
            }
          }
        }
        if (!empty($parts)) {
          // Create and execute query
          $query='UPDATE `'.$tbl.'` SET '.implode(', ', $parts).' WHERE `'.$id_field.'` = BINARY "'.$this->_db_escapeStr($id, false).'" LIMIT 1';
          if ($result=$this->_db_query($query)) {
            $this->_db_freeResult($result);
            $ok=true;
          }
        }
      }
    }
    return $ok;
  }


  /**
   * Updates databse table row using suplied data. ID field will not be updated.
   * @param   mixed     $id         Table row ID field value
   * @param   string    $id_field   Table row ID field name
   * @param   array     $data       Data to apply as array with field name as KEY and field value as VAL
   * @return  boolean   TRUE on success or FALSE on error
   */
  function _db_updateRow($id='', $id_field='id', $data=null) {
    $ok=false;
    $tbl=$this->_db_getTbl();
    if (!empty($tbl) && !empty($id_field) && !empty($data) && is_array($data)) {
      // Get table information
      $tabledata=$this->_db_tableFields($tbl);
      if (!empty($tabledata)) {
        $parts=array();
        foreach ($tabledata as $field) {
          if (array_key_exists($field['Field'], $data) && (is_scalar($data[$field['Field']]) || is_null($data[$field['Field']]))) {
            if (is_null($data[$field['Field']])) {
              $parts[]='`'.$field['Field'].'` = NULL';
            } elseif ($data[$field['Field']]=='' && array_key_exists('Default', $field)) {
              $parts[]='`'.$field['Field'].'` = "'.$this->_db_escapeStr($field['Default'], false).'"';
            } else {
              $parts[]='`'.$field['Field'].'` = "'.$this->_db_escapeStr($data[$field['Field']], false).'"';
            }
          }
        }
        if (!empty($parts)) {
          // Create and execute query
          $query='UPDATE `'.$tbl.'` SET '.implode(', ', $parts).' WHERE `'.$id_field.'` = BINARY "'.$this->_db_escapeStr($id, false).'" LIMIT 1';
          if ($result=$this->_db_query($query)) {
            $this->_db_freeResult($result);
            $ok=true;
          }
        }
      }
    }
    return $ok;
  }


  /**
   * Delete a row from the databse table
   * @param   mixed     $id         Table row ID field value
   * @param   string    $id_field   Table row ID field name
   * @param   boolean   $no_limit   If TRUE, then no LIMIT will be used in query, otherwise: LIMIT 1
   * @return  boolean   TRUE on success or FALSE on error
   */
  function _db_deleteRow($id='', $id_field='id', $no_limit=false) {
    $ok=false;
    $tbl=$this->_db_getTbl();
    if (!empty($tbl) && !empty($id_field)) {
      // Get table information
      $tabledata=$this->_db_tableFields($tbl);
      foreach ($tabledata as $field) {
        if ($field['Field']==$id_field) {
          // Create and execute query
          $limit=($no_limit)? '' : ' LIMIT 1';
          $query='DELETE FROM `'.$tbl.'` WHERE `'.$id_field.'` = BINARY "'.$this->_db_escapeStr($id, false).'" '.$limit;
          if ($result=$this->_db_query($query)) {
            $this->_db_freeResult($result);
            $ok=true;
          }
          break;
        }
      }
    }
    return $ok;
  }


  /**
   * Delete a row from the databse table using multiple `<field>` [= BINARY "<val>" | IS NULL] 'AND' arguments
   * @param   array     $cond       An array with multiple WHERE conditions (field name as KEY and value as VAL)
   * @param   boolean   $no_limit   If TRUE, then no LIMIT will be used in query, otherwize: LIMIT 1
   * @return  boolean   TRUE on success or FALSE on error
   */
  function _db_deleteRowMultiCond($cond=null, $no_limit=false) {
    $ok=false;
    $cond_ok=true;
    $tbl=$this->_db_getTbl();
    if (!empty($tbl) && !empty($cond) && is_array($cond)) {
      // Get table information
      $tabledata=$this->_db_tableFields($tbl);
      $where=array();
      foreach ($tabledata as $field) {
        if (array_key_exists($field['Field'], $cond)) {
          if (is_null($cond[$field['Field']])) {
            $where[]='`'.$field['Field'].'` IS NULL';
          } elseif (is_scalar($cond[$field['Field']])) {
            $where[]='`'.$field['Field'].'` = BINARY "'.$this->_db_escapeStr($cond[$field['Field']], false).'"';
          } else {
            $cond_ok=false;
            break;
          }
        }
      }
      if ($cond_ok && !empty($where)) {
        // Create and execute query
        $limit=($no_limit)? '' : ' LIMIT 1';
        $query='DELETE FROM `'.$tbl.'` WHERE '.implode(' AND ', $where).' '.$limit;
        if ($result=$this->_db_query($query)) {
          $this->_db_freeResult($result);
          $ok=true;
        }
      }
    }
    return $ok;
  }


  /**
   * Insert a row into the databse table using object's data
   * @return  boolean   TRUE on success or FALSE on error
   */
  function _db_insertObj() {
    $ok=false;
    // Get table information
    $tbl=$this->_db_getTbl();
    $tabledata=$this->_db_tableFields($tbl);
    if (!empty($tabledata)) {
      $fields=array();
      $values=array();
      foreach ($tabledata as $field) {
        if (isset($this->$field['Field'])) {
          $fields[]='`'.$field['Field'].'`';
          $values[]=(is_null($this->$field['Field']))? 'NULL' : '"'.$this->_db_escapeStr($this->$field['Field'], false).'"';
        }
      }
      if (!empty($fields)) {
        // Create and execute query
        $query='INSERT INTO `'.$tbl.'` ('.implode(', ', $fields).') VALUES ('.implode(', ', $values).')';
        if ($result=$this->_db_query($query)) {
          $this->_db_freeResult($result);
          $ok=true;
        }
      }
    }
    return $ok;
  }


  /**
   * List MySQL table fields
   * @param    string   $tbl_name   Database table name
   * @return   mixed  Array with table fields as returned by PHP's mysql_list_fields()
   *                  function or false on error
   */
  function _db_tableFields($tbl_name='') {
    $fields=false;
    if ($tbl_name!='') {
      // Get table information
      if (!isset($this->_cache['_db_tabledata'][$tbl_name])) {
        // Table fields are not read yet. Do it.
        if ($result=$this->_db_query('SHOW COLUMNS FROM `'.$tbl_name.'`')) {
          $table=array();
          while ($data=$this->_db_fetch($result, MYSQL_ASSOC)) {
            $table[]=$data;
          }
          $this->_db_freeResult($result);
          if (!empty($table)) {
            // Store table fields data into cache
            $this->_cache['_db_tabledata'][$tbl_name]=$table;
            $fields=$this->_cache['_db_tabledata'][$tbl_name];
          }
        }
      } else {
        // Get table fields from cache
        $fields=$this->_cache['_db_tabledata'][$tbl_name];
      }
    }
    return $fields;
  }


 /**
  * List rows of single table into object property $_db_list using simple filter.
  * Listed rows will be saved into $this->_db_list array and their count will be saved
  * into $this->_db_list_count property.
  * @param    mixed   ...  Unlimited query parameters
  * @return   int     Listed rows count
  */
  function _db_getList() {
    $this->_db_freeList();
    $tbl=$this->_db_getTbl();
    if (!empty($tbl)) {
      $count=false;
      $select='*';
      $param=array();
      $where='';
      $order=array();
      $orderby='';
      $limitstart=null;
      $limitlength=null;
      $limit='';
      // Get function arguments
      $argv=func_get_args();
      // Process arguments
      foreach ($argv as $arg_key=>$arg) {
        if ($arg_key==0 && $arg=='COUNT') {
          // There will be a COUNT(*) query. An empty array will be saved into _db_list property
          // and the count will be saved into _db_list_count
          $count=true;
          $select='COUNT(*)';
        } else {
          $pos=array('>=#'     =>  strpos($arg, '>=#'),
                     '>='      =>  strpos($arg, '>='),
                     '<=#'     =>  strpos($arg, '<=#'),
                     '<='      =>  strpos($arg, '<='),
                     '!=#'     =>  strpos($arg, '!=#'),
                     '!='      =>  strpos($arg, '!='),
                     '>#'      =>  strpos($arg, '>#'),
                     '>'       =>  strpos($arg, '>'),
                     '<#'      =>  strpos($arg, '<#'),
                     '<'       =>  strpos($arg, '<'),
                     '=#'      =>  strpos($arg, '=#'),
                     '='       =>  strpos($arg, '='),
                     '!NULL'   =>  strpos($arg, '!NULL'),
                     'NULL'    =>  strpos($arg, 'NULL'),
                     '!_LIKE#' =>  strpos($arg, '!_LIKE#'),
                     '!LIKE#' =>  strpos($arg, '!LIKE#'),
                     '_LIKE#'  =>  strpos($arg, '_LIKE#'),
                     'LIKE#'  =>  strpos($arg, 'LIKE#'),
                     '!_LIKE'   =>  strpos($arg, '!_LIKE'),
                     '!LIKE'   =>  strpos($arg, '!LIKE'),
                     '_LIKE'    =>  strpos($arg, '_LIKE'),
                     'LIKE'    =>  strpos($arg, 'LIKE'),
                     'IN'      =>  strpos($arg, 'IN'),
                     '!IN'     =>  strpos($arg, '!IN')
                     );
          // Looking for the first comparison sign (longest signs first)
          asort($pos);
          $pos_=array();
          $val_start=null;
          foreach ($pos as $key=>$val) {
            if (false!==($val)) {
              if (is_null($val_start) || $val==$val_start) {
                $val_start=$val;
                $pos_[$key]=strlen($key);
              } else {
                break;
              }
            }
          }
          if ($arg_key==0 && empty($pos_) && false===strpos($arg, ' ASC') && false===strpos($arg, ' DESC') && false===pcpin_ctype_digit($arg)) {
            // Only specified fields will be selected
            $select_array=explode(',', $arg);
            $select_arr=array();
            foreach ($select_array as $key=>$val) {
              $val=trim(str_replace('`', '', $val));
              if ($val!='') {
                if (substr($val, 0, 2)!='x0') {
                  // Select values "as is"
                  $select_arr[$key]='`'.$val.'`';
                } else {
                  // Select HEX-encoded values
                  $val=substr($val, 2);
                  $select_arr[$key]='HEX(`'.$val.'`) AS `'.$val.'`';
                }
              }
            }
            $select=!empty($select_arr)? implode(',', $select_arr) : '*';
            unset($select_arr);
            unset($select_array);
          } elseif (!empty($pos_)) {
            // Argument contains two parts
            arsort($pos_);
            reset($pos_);
            $key=key($pos_);
            $val=$val_start;
            $part1=trim(substr($arg, 0, $val));
            $key_length=strlen($key);
            switch ($key) {
              case 'NULL':
                // 'IS NULL' operator
                $param[]='`'.$part1.'` IS NULL';
              break;
              case '!NULL':
                // 'IS NOT NULL' operator
                $param[]='`'.$part1.'` IS NOT NULL';
              break;
              case 'LIKE':
              case '_LIKE':
                // 'LIKE' operator with or without escaped wildcards
                $part2=(substr($arg, $val+$key_length, 1)==' ')? substr($arg, $val+$key_length+1) : substr($arg, $val+$key_length);
                $param[]='`'.$part1.'` LIKE "'.$this->_db_escapeStr($part2, '_'!=substr($key, 0, 1)).'"';
              break;
              case 'LIKE#':
              case '_LIKE#':
                // 'LIKE BINARY' operator with or without escaped wildcards
                $part2=(substr($arg, $val+$key_length, 1)==' ')? substr($arg, $val+$key_length+1) : substr($arg, $val+$key_length);
                $param[]='`'.$part1.'` LIKE BINARY "'.$this->_db_escapeStr($part2, '_'!=substr($key, 0, 1)).'"';
              break;
              case '!LIKE':
              case '!_LIKE':
                // 'NOT LIKE' operator with or without escaped wildcards
                $part2=(substr($arg, $val+$key_length, 1)==' ')? substr($arg, $val+$key_length+1) : substr($arg, $val+$key_length);
                $param[]='`'.$part1.'` NOT LIKE "'.$this->_db_escapeStr($part2, '_'!=substr($key, 0, 1)).'"';
              break;
              case '!LIKE#':
              case '!_LIKE#':
                // 'NOT LIKE BINARY' operator with or without escaped wildcards
                $part2=(substr($arg, $val+$key_length, 1)==' ')? substr($arg, $val+$key_length+1) : substr($arg, $val+$key_length);
                $param[]='`'.$part1.'` NOT LIKE BINARY "'.$this->_db_escapeStr($part2, '_'!=substr($key, 0, 1)).'"';
              break;
              case 'IN':
                // 'IN()' operator
                $part2=(substr($arg, $val+$key_length, 1)==' ')? substr($arg, $val+$key_length+1) : substr($arg, $val+$key_length);
                $this->_db_prepareList($part2);
                $param[]='`'.$part1.'` IN('.$part2.')';
              break;
              case '!IN':
                // 'NOT IN()' operator
                $part2=(substr($arg, $val+$key_length, 1)==' ')? substr($arg, $val+$key_length+1) : substr($arg, $val+$key_length);
                $this->_db_prepareList($part2);
                $param[]='`'.$part1.'` IN('.$part2.')';
              break;
              case '>'  :
              case '<'  :
              case '>=' :
              case '<=' :
              case '='  :
              case '!=' :
              case '>#'  :
              case '<#'  :
              case '>=#' :
              case '<=#' :
              case '=#'  :
              case '!=#' :
                // '>' '<' '>=' '<=' '=' '!=' [BINARY] operators
                $part2=(substr($arg, $val+$key_length, 1)==' ')? substr($arg, $val+$key_length+1) : substr($arg, $val+$key_length);
                $key=str_replace('#', ' BINARY', $key);
                $param[]='`'.$part1.'` '.$key.' "'.$this->_db_escapeStr($part2, false).'"';
              break;
            }
          } else {
            // Argument contains one part only (can be 'ORDER BY' or 'LIMIT')
            $arg=trim($arg);
            if (pcpin_ctype_digit($arg)) {
              // Argument contains digits only (one of LIMIT arguments)
              if (is_null($limitlength)) {
                $limitlength=$arg;
              } elseif (is_null($limitstart)) {
                $limitstart=$limitlength;
                $limitlength=$arg;
              }
            } else {
              // ORDER BY parameter
              while (false!==strpos($arg, '  ')) {
                $arg=str_replace('  ', ' ', $arg);
              }
              $parts=explode(' ', $arg);
              if (isset($parts[1]) && strtolower(trim($parts[1]))=='desc') {
                // DESC order
                $order[]='`'.trim($parts[0]).'` DESC';
              } else {
                // ASC order
                $order[]='`'.trim($parts[0]).'` ASC';
              }
            }
          }
        }
      }
      // Prepare WHERE, ORDER BY and LIMIT arguments
      $where=(!empty($param))? (' WHERE '.implode(' AND ', $param)) : '';
      $orderby=(!empty($order))? (' ORDER BY '.implode(', ', $order)) : '';
      $limit=(!is_null($limitstart))? (" LIMIT $limitstart, $limitlength") : ((!is_null($limitlength))? (" LIMIT $limitlength") : '');
      // Create query
      $query='SELECT '.$select.' FROM `'.$tbl.'` '.$where.$orderby.$limit;
#echo $query." <br />\n";
      $result=$this->_db_query($query);
      if ($count) {
        if ($data=$this->_db_fetch($result, MYSQL_NUM)) {
          $this->_db_list_count=$data[0];
        }
        $this->_db_freeResult($result);
      } else {
        while ($data=$this->_db_fetch($result, MYSQL_ASSOC)) {
          $this->_db_list[]=$data;
          $this->_db_list_count++;
        }
        $this->_db_freeResult($result);
      }
    }
    return $this->_db_list_count;
  }


 /**
  * Clear loaded list and list count
  */
  function _db_freeList() {
    $this->_db_list=array();
    $this->_db_list_count=0;
  }


 /**
  * Get the unique ID for the last inserted row
  * @return   int
  */
  function _db_lastInsertID() {
    $last_id=0;
    $result=$this->_db_query('SELECT LAST_INSERT_ID()');
    if ($data=$this->_db_fetch($result, MYSQL_NUM)) {
      $last_id=$data[0];
    }
    $this->_db_freeResult($result);
    return $last_id;
  }


 /**
  * Get database tables list
  * @return   array  Array with table names
  */
  function _db_listTables() {
    $tables=array();
    $query=$this->_db_makeQuery(113);
    if ($result=$this->_db_query($query)) {
      while ($data=$this->_db_fetch($result, MYSQL_NUM)) {
        $tables[]=$data[0];
      }
      $this->_db_freeResult($result);
    }
    return $tables;
  }


 /**
  * Get database table information
  * @param    string    $table    Table name
  * @return   array  Array with table information
  */
  function _db_readTableData($table='') {
    $info=array();
    if ($table!='') {
      $query=$this->_db_makeQuery(114, $table);
      if ($result=$this->_db_query($query)) {
        while ($data=$this->_db_fetch($result)) {
          $info[]=$data;
        }
        $this->_db_freeResult($result);
      }
    }
    return $info;
  }


 /**
  * Get database table indexes
  * @param    string    $table    Table name
  * @return   array  Array with table indexes
  */
  function _db_readTableIndexes($table='') {
    $indexes=array();
    if ($table!='') {
      $query=$this->_db_makeQuery(115, $table);
      if ($result=$this->_db_query($query)) {
        while ($data=$this->_db_fetch($result)) {
          $indexes[]=$data;
        }
        $this->_db_freeResult($result);
      }
    }
    return $indexes;
  }


 /**
  * Prepare list of values to be used by MySQL IN() operator
  * @param    string    &$list            List string
  * @param    boolean   $string_elements  List contains string elements
  */
  function _db_prepareList(&$list, $string_elements=false) {
    if ($list!='') {
      if (!$string_elements) {
        // List may contain digits only
        if (!pcpin_ctype_digit(str_replace(' ', '', str_replace(',', '', $list)))) {
          // ERROR: List contains string elements
          $list='';
        }
      } else {
        // List may contain string elements
        $list=explode(',', $list);
        foreach ($list as $key=>$val) {
          // Escaping string list elements
          $list[$key]='"'.$this->_db_escapeStr($val, false).'"';
        }
        $list=implode(',', $list);
      }
    }
  }


  /**
   * Get database table name
   * @return  string
   */
  function _db_getTbl() {
    return PCPIN_DB_PREFIX.strtolower(substr(get_class($this), 6)); // "PCPIN_" prefix removed
  }


  /**
   * Reference properties' values from one object to another
   * @param   object    $src_obj    Object to pass variables from
   * @param   object    $tgt_obj    Object to pass variables to
   */
  function _db_pass_vars(&$src_obj, &$tgt_obj) {
    if (is_object($tgt_obj)) {
      // Get source vars
      $src_vars=get_object_vars($src_obj);
      $keys=array_keys($src_vars);
      foreach ($keys as $key) {
        if ($key!='_db_list' && $key!='_db_list_count' && '_'==substr($key, 0, 1) && '_s_'!=substr($key, 0, 2)) {
          $tgt_obj->$key=&$src_obj->$key;
        }
      }
    }
  }


 /**
  * Check and repair/optimize database tables
  */
  function _db_cure() {
    $tables=$this->_db_listTables();
    foreach ($tables as $table) {
      // Check table
      if ($result=$this->_db_query($this->_db_makeQuery(120, $table))) {
        if ($data=$this->_db_fetch($result, MYSQL_ASSOC)) {
          if (!isset($data['Msg_text']) || strtolower($data['Msg_text'])!='ok') {
            // Repair table
            $this->_db_query($this->_db_makeQuery(125, $table));
          }
        }
        $this->_db_freeResult($result);
      }
      // Check overhead
      if ($result=$this->_db_query($this->_db_makeQuery(121, $table))) {
        if ($data=$this->_db_fetch($result, MYSQL_ASSOC)) {
          if (!empty($data['Data_free'])) {
            // Optimize table
            $this->_db_query($this->_db_makeQuery(126, $table));
          }
        }
        $this->_db_freeResult($result);
      }
    }
  }


 /**
  * Create SQL query using a template
  * @param    int     $nr   Query template number
  * @param    mixed   ...   Unlimited query parameters. WARNING: scalar data types only!!!
  * @return   string  Created query
  */
  function _db_makeQuery($nr) {
    // Get method arguments
    $argv=func_get_args();
    // First argument is query template number (not needed)
    unset($argv[0]);
    // Load requested template
    $query='';
    switch($nr) {
      default   :   // Invalid query template number
                    // No query will be returned (empty string)
                    $query='';
      break;

      case  113 :   // Get tables list
                    // Used in: PCPIN_DB->_db_listTables()
                    $query='SHOW TABLES LIKE "'.PCPIN_DB_PREFIX.'_%"';
      break;

      case  114 :   // Retrieve table data
                    // Used in: PCPIN_DB->_db_readTableData()
                    $query='SHOW TABLE STATUS LIKE "\\_ARG1_\\"';
      break;

      case  115 :   // Retrieve table indexes
                    // Used in: PCPIN_DB->_db_readTableIndexes()
                    $query='SHOW INDEX FROM `\\_ARG1_\\`';
      break;

      case  120 :   // Check table
                    // Used in: PCPIN_DB->_db_cure()
                    $query='CHECK TABLE `\\_ARG1_\\`';
      break;

      case  121 :   // Get table status
                    // Used in: PCPIN_DB->_db_cure()
                    $query='SHOW TABLE STATUS LIKE "\\_arg1_\\"';
      break;

      case  125 :   // Repair table
                    // Used in: PCPIN_DB->_db_cure()
                    $query='REPAIR TABLE `\\_ARG1_\\`';
      break;

      case  126 :   // Optimize table
                    // Used in: PCPIN_DB->_db_cure()
                    $query='OPTIMIZE TABLE `\\_ARG1_\\`';
      break;

      case 1100 :   // Check wether email address already in use or not
                    // Used in: PCPIN_User->checkEmailUnique()
                    $where='';
                    if (!empty($argv[1])) {
                      $where.=' AND `us`.`id` != "\\_arg1_\\"';
                    }
                    $query='SELECT 1 FROM `'.PCPIN_DB_PREFIX.'user` `us`
                                    WHERE 1
                                          AND (`us`.`email` LIKE "\\_arg2_\\" OR `us`.`email_new` LIKE "\\_arg2_\\")
                                          LIMIT 1';
      break;

      case 1200 :   // Get chat rooms list grouped in categories
                    // Used in: PCPIN_Category->getTree()
                    $where='';
                    if (!empty($argv[2])) {
                      $where.=' AND `ca`.`id` = BINARY "\\_ARG2_\\"';
                    }
                    if (!empty($argv[3])) {
                      $where.=' AND `ro`.`id` = BINARY "\\_ARG3_\\"';
                    }
                    $query='SELECT `ca`.`id` AS `category_id`,
                                   `ca`.`parent_id` AS `category_parent_id`,
                                   `ca`.`name` AS `category_name`,
                                   `ca`.`description` AS `category_description`,
                                   IF( `ca`.`creatable_rooms` = "g" OR `ca`.`creatable_rooms` = "r" AND `curr_us`.`is_guest` = "n", 1, 0 ) AS `creatable_rooms`,
                                   `ca`.`creatable_rooms` AS `creatable_rooms_flag`,
                                   `ro`.`id` AS `room_id`,
                                   `ro`.`name` AS `room_name`,
                                   `ro`.`description` AS `room_description`,
                                   `ro`.`default_message_color`,
                                   IF( `ro`.`password` != "", 1, 0) AS `password_protected`,
                                   IF( `ro`.`password` = "" OR `curr_us`.`is_admin` = "y" OR `curr_se`.`_s_room_id` = `ro`.`id`, `ro`.`background_image`, 0) AS `background_image`,
                                   IF( `ro`.`password` = "" OR `curr_us`.`is_admin` = "y" OR `curr_se`.`_s_room_id` = `ro`.`id`, `bf`.`width`, 0) AS `background_image_width`,
                                   IF( `ro`.`password` = "" OR `curr_us`.`is_admin` = "y" OR `curr_se`.`_s_room_id` = `ro`.`id`, `bf`.`height`, 0) AS `background_image_height`,
                                   `se`.`_s_user_id` AS `user_id`,
                                   `se`.`_s_ip` AS `ip_address`,
                                   `ud`.`gender` AS `gender`,
                                   COALESCE( `nn`.`nickname`, `us`.`login` ) AS `nickname`,
                                   COALESCE( `nn`.`nickname_plain`, `us`.`login` ) AS `nickname_plain`,
                                   COALESCE( `av`.`binaryfile_id`, `av_def`.`binaryfile_id` ) AS `avatar_bid`,
                                   `se`.`_s_online_status` AS `online_status`,
                                   `se`.`_s_online_status_message` AS `online_status_message`,
                                   `us`.`global_muted_permanently`,
                                   IF( `us`.`is_admin` = "y", 1, 0) AS `is_admin`,
                                   IF( `us`.`is_guest` = "y", 1, 0) AS `is_guest`,
                                   IF( `curr_se`.`_s_room_id` > 0 AND `curr_se`.`_s_room_id` = `se`.`_s_room_id` AND FIND_IN_SET( `se`.`_s_room_id`, `us`.`moderated_rooms` ) > 0, 1, 0) AS `is_moderator`,
                                   IF( `us`.`global_muted_until` > CURDATE() OR `us`.`global_muted_permanently` = "y", `us`.`global_muted_by`, 0) AS `global_muted_by`,
                                   IF( `us`.`global_muted_until` > CURDATE() OR `us`.`global_muted_permanently` = "y", `us`.`global_muted_by_username`, "") AS `global_muted_by_username`,
                                   IF( `us`.`global_muted_until` > CURDATE(), `us`.`global_muted_until`, "0000-00-00 00:00:00") AS `global_muted_until`,
                                   IF( `us`.`global_muted_until` > CURDATE() OR `us`.`global_muted_permanently` = "y", `us`.`global_muted_reason`, "") AS `global_muted_reason`
                              FROM `'.PCPIN_DB_PREFIX.'category` `ca`
                                   LEFT JOIN `'.PCPIN_DB_PREFIX.'user` `curr_us` ON `curr_us`.`id` = BINARY "\\_ARG1_\\"
                                   LEFT JOIN `'.PCPIN_DB_PREFIX.'session` `curr_se` ON `curr_se`.`_s_user_id` = `curr_us`.`id`
                                   LEFT JOIN `'.PCPIN_DB_PREFIX.'room` `ro` ON `ro`.`category_id` = `ca`.`id`
                                   LEFT JOIN `'.PCPIN_DB_PREFIX.'session` `se` ON (`se`.`_s_room_id` = `ro`.`id` AND (`se`.`_s_stealth_mode` = "n" OR `curr_us`.`is_admin` = "y" OR `curr_se`.`_s_room_id` != 0 AND FIND_IN_SET( `se`.`_s_room_id`, `curr_us`.`moderated_rooms` )))
                                   LEFT JOIN `'.PCPIN_DB_PREFIX.'nickname` `nn` ON (`nn`.`user_id` = `se`.`_s_user_id` AND `nn`.`default` = "y")
                                   LEFT JOIN `'.PCPIN_DB_PREFIX.'user` `us` ON `us`.`id` = `se`.`_s_user_id`
                                   LEFT JOIN `'.PCPIN_DB_PREFIX.'userdata` `ud` ON `ud`.`user_id` = `us`.`id`
                                   LEFT JOIN `'.PCPIN_DB_PREFIX.'avatar` `av` ON `av`.`user_id` = `us`.`id` AND `av`.`primary` = "y"
                                   LEFT JOIN `'.PCPIN_DB_PREFIX.'avatar` `av_def` ON `av_def`.`user_id` = 0 AND `av_def`.`primary` = "y"
                                   LEFT JOIN `'.PCPIN_DB_PREFIX.'binaryfile` `bf` ON `bf`.`id` = `ro`.`background_image`
                             WHERE 1
                                   '.$where.'
                          ORDER BY `ca`.`listpos` ASC,
                                   `ro`.`listpos` ASC,
                                   `nn`.`nickname_plain` ASC';
      break;

      case 1300 :   // Get new online messages for user
                    // Used in: PCPIN_Message->getNewMessages()
                    // Used in: PCPIN_Message->getLastMessages()
                    $where=' 1 ';
                    $orderby='`me`.`id` ASC';
                    $limit='';
                    if (empty($argv[2])) {
                      $where.=' AND ( `me`.`id` > `se`.`_s_last_message_id` AND `me`.`date` > `_s_room_date` )';
                    } else {
                      $orderby='`me`.`id` DESC';
                      $limit='LIMIT \\_ARG2_\\';
                    }
                    if (!empty($argv[3])) {
                      // Return messages of specified type
                      $where.=' AND `me`.`type` = "\\_ARG3_\\" ';
                    }
                    $query='SELECT `me`.*,
                                   IF( `at`.`id` IS NOT NULL, 1, 0 ) AS `has_attachments`
                              FROM `'.PCPIN_DB_PREFIX.'message` `me`
                         LEFT JOIN `'.PCPIN_DB_PREFIX.'attachment` `at` ON `at`.`message_id` = `me`.`id`
                         LEFT JOIN `'.PCPIN_DB_PREFIX.'session` `se` ON `se`.`_s_user_id` = "\\_ARG1_\\"
                             WHERE  '.$where.'
                                    AND `me`.`offline` = "n"
                                    AND ( `me`.`target_room_id` = `se`.`_s_room_id` OR `me`.`target_room_id` = 0 )
                                    AND ( `me`.`target_user_id` = `se`.`_s_user_id` OR `me`.`author_id` = `se`.`_s_user_id` OR `me`.`privacy` = 0 )
                                    AND ( `me`.`privacy` != 2 || `me`.`id` > `se`.`_s_last_message_id` ) /* do not show own sent PMs */
                          GROUP BY `me`.`id`
                          ORDER BY '.$orderby.'
                         '.$limit;
      break;

      case 1400 :   // Check wether IP address allowed/denied via IP filter or not
                    // Used in: PCPIN_IPFilter->isBlocked()
                    $query='SELECT `id`, `action`, `description`, `expires`
                              FROM `'.PCPIN_DB_PREFIX.'ipfilter`
                             WHERE `id` != "\\_ARG2_\\"
                                   AND ( `expires` = "0000-00-00 00:00:00" OR `expires` > NOW() )
                                   AND "\\_ARG1_\\" LIKE REPLACE( REPLACE( `address`, "?", "_" ), "*", "%" )
                          GROUP BY `action`
                          ORDER BY `id` DESC';
      break;

      case 1410 :   // Get IP addresses list from "ipfilter" table
                    // Used in: PCPIN_IPFilter->readAddresses()
                    $orderby='';
                    $orderdir='';
                    if (!empty($argv[2])) {
                      $orderdir='DESC';
                    } else {
                      $orderdir='ASC';
                    }
                    if (!isset($argv[1])) {
                      $argv[1]=0;
                    }
                    switch($argv[1]) {
                      default   :
                      case  0   :   $orderby= '  `ip_part1` '.$orderdir
                                             .', `ip_part2` '.$orderdir
                                             .', `ip_part3` '.$orderdir
                                             .', `ip_part4` '.$orderdir;
                                    break;
                      case  1   :   $orderby= '  `action` '.$orderdir
                                             .', `ip_part1` '.$orderdir
                                             .', `ip_part2` '.$orderdir
                                             .', `ip_part3` '.$orderdir
                                             .', `ip_part4` '.$orderdir;
                                    break;
                      case  2   :   $orderby= '  `expires` '.$orderdir
                                             .', `ip_part1` '.$orderdir
                                             .', `ip_part2` '.$orderdir
                                             .', `ip_part3` '.$orderdir
                                             .', `ip_part4` '.$orderdir;
                                    break;
                      case  3   :   $orderby= '  `description` '.$orderdir
                                             .', `ip_part1` '.$orderdir
                                             .', `ip_part2` '.$orderdir
                                             .', `ip_part3` '.$orderdir
                                             .', `ip_part4` '.$orderdir;
                                    break;
                      case  4   :   $orderby= '  `added_on` '.$orderdir
                                             .', `ip_part1` '.$orderdir
                                             .', `ip_part2` '.$orderdir
                                             .', `ip_part3` '.$orderdir
                                             .', `ip_part4` '.$orderdir;

                    }
                    $query='SELECT *,
                                   CONVERT( SUBSTRING_INDEX( `address`, ".", 1 ), UNSIGNED ) AS `ip_part1`,
                                   CONVERT( SUBSTRING_INDEX( SUBSTRING_INDEX( `address`, ".", 2), ".", -1 ), UNSIGNED ) AS `ip_part2`,
                                   CONVERT( SUBSTRING_INDEX( SUBSTRING_INDEX( `address`, ".", -2), ".", 1 ), UNSIGNED ) AS `ip_part3`,
                                   CONVERT( SUBSTRING_INDEX( `address`, ".", -1 ), UNSIGNED ) AS `ip_part4`
                              FROM `'.PCPIN_DB_PREFIX.'ipfilter`
                          ORDER BY '.$orderby;
                    break;

      case 1500 :   // Add an invitation
                    // Used in: PCPIN_Invitation->addNewInvitation()
                    $query='INSERT INTO `'.PCPIN_DB_PREFIX.'invitation`
                                        (`author_id`, `author_nickname`, `target_user_id`, `room_id`, `room_name`)
                                        SELECT "\\_ARG1_\\" AS `author_id`,
                                               `nn`.`nickname` AS `author_nickname`,
                                               "\\_ARG2_\\" AS `target_user_id`,
                                               "\\_ARG3_\\" AS `room_id`,
                                               `ro`.`name` AS `room_name`
                                          FROM `'.PCPIN_DB_PREFIX.'nickname` `nn`
                                               LEFT JOIN `'.PCPIN_DB_PREFIX.'room` `ro` ON `ro`.`id` = "\\_ARG3_\\"
                                         WHERE `nn`.`user_id` = "\\_ARG1_\\"
                                               AND `nn`.`default` = "y"
                                               AND `ro`.`id` IS NOT NULL
                                               LIMIT 1';
      break;

      case 1600 :   // Get room moderators
                    // Used in: PCPIN_Room->getModerators()
                    $query='SELECT `us`.`id`,
                                   IF( `us`.`hide_email` = "0", `us`.`email`, "" ) AS `email`,
                                   `us`.`moderated_categories`,
                                   `us`.`moderated_rooms`,
                                   IF ( `se`.`_s_id` IS NOT NULL, 1, 0 ) AS `is_online`
                              FROM `'.PCPIN_DB_PREFIX.'user` `us`
                                   LEFT JOIN `'.PCPIN_DB_PREFIX.'session` `se` ON `se`.`_s_user_id` = `us`.`id`
                             WHERE `us`.`moderated_rooms` != ""
                                   AND FIND_IN_SET( "\\_ARG1_\\", `us`.`moderated_rooms` )';
      break;

      case 1610 :   // Get category moderators
                    // Used in: PCPIN_Category->getModerators()
                    $query='SELECT `us`.`id`,
                                   IF( `us`.`hide_email` = "0", `us`.`email`, "" ) AS `email`,
                                   `us`.`moderated_categories`,
                                   `us`.`moderated_rooms`,
                                   IF ( `se`.`_s_id` IS NOT NULL, 1, 0 ) AS `is_online`
                              FROM `'.PCPIN_DB_PREFIX.'user` `us`
                                   LEFT JOIN `'.PCPIN_DB_PREFIX.'session` `se` ON `se`.`_s_user_id` = `us`.`id`
                             WHERE `us`.`moderated_categories` != ""
                                   AND FIND_IN_SET( "\\_ARG1_\\", `us`.`moderated_categories` )';
      break;

      case 1620 :   // Get chat admins
                    // Used in: PCPIN_User->getAdmins()
                    $query='SELECT `us`.`id`,
                                   IF( `us`.`hide_email` = "0", `us`.`email`, "" ) AS `email`,
                                   `us`.`moderated_categories`,
                                   `us`.`moderated_rooms`,
                                   IF ( `se`.`_s_id` IS NOT NULL, 1, 0 ) AS `is_online`
                              FROM `'.PCPIN_DB_PREFIX.'user` `us`
                                   LEFT JOIN `'.PCPIN_DB_PREFIX.'session` `se` ON `se`.`_s_user_id` = `us`.`id`
                             WHERE `us`.`is_admin` = "y"';
      break;

      case 1700 :   // Update version data
                    // Used in: PCPIN_Verion->setVersion()
                    //          PCPIN_Verion->setLastVersionCheckTime()
                    //          PCPIN_Verion->setNewestAvailableVersion()
                    //          PCPIN_Verion->setVersionCheckKey()
                    //          PCPIN_Verion->setNewVersionDownloadUrl()
                    $set=array();
                    if (!empty($argv[1])) {
                      // Version
                      $set[]='`version` = "\\_ARG1_\\"';
                    }
                    if (!empty($argv[2])) {
                      // Last version check time
                      $set[]='`last_version_check` = "\\_ARG2_\\"';
                    }
                    if (!empty($argv[3])) {
                      // Newest available version
                      $set[]='`new_version_available` = "\\_ARG3_\\"';
                    }
                    if (!empty($argv[4])) {
                      // Version check security key
                      $set[]='`version_check_key` = "\\_ARG4_\\"';
                    }
                    if (!empty($argv[5])) {
                      // New version download URL
                      $set[]='`new_version_url` = "\\_ARG5_\\"';
                    }
                    if (!empty($set)) {
                      $query='UPDATE `'.PCPIN_DB_PREFIX.'version` SET '.implode(',', $set).' LIMIT 1';
                    } else {
                      $query='';
                    }
                    break;

      case 1800 :   // Update chat setting
                    // Used in: PCPIN_Config->_conf_updateSettings()
                    $query='UPDATE `'.PCPIN_DB_PREFIX.'config` SET `_conf_value` = "\\_ARG2_\\" WHERE `_conf_name` = BINARY "\\_ARG1_\\" LIMIT 1';
      break;

      case 1900 :   // Get memberlist
                    // Used in: PCPIN_User->getMemberlist()
                    $select='';
                    $where='';
                    $groupby='';
                    $orderby='';
                    $orderdir='';
                    $limit='';
                    if (!empty($argv[1])) {
                      // Count only
                      $select=' COUNT( DISTINCT `us`.`id` ) AS `members` ';
                      $argv[3]=0; // No limit needed
                      $argv[4]=-1; // No sort needed
                      $groupby='';
                    } else {
                      // Full data
                      $select='`us`.`id` AS `id`,
                                UNIX_TIMESTAMP( `us`.`joined`) AS `joined`,
                                UNIX_TIMESTAMP( `us`.`last_login`) AS `last_login`,
                                `us`.`time_online` + IF( `se`.`_s_id` IS NOT NULL, UNIX_TIMESTAMP() - UNIX_TIMESTAMP( `se`.`_s_created` ), 0 ) AS `time_online`,
                                IF( `curr_us`.`is_admin` = "y", `se`.`_s_ip`, "" ) AS `ip_address`,
                                COALESCE( `se`.`_s_online_status`, 0 ) AS `online_status`,
                                COALESCE( `se`.`_s_online_status_message`, "" ) AS `online_status_message`,
                                COALESCE( `nn`.`nickname`, `us`.`login` ) AS `nickname`,
                                COALESCE( `nn`.`nickname_plain`, `us`.`login` ) AS `nickname_plain`,
                                IF( `us`.`hide_email` = "0" OR `curr_us`.`is_admin` = "y", `us`.`email`, "" ) AS `email`,
                                IF( `us`.`is_admin` = "y", 1, 0 ) AS `is_admin`,
                                IF( `us`.`moderated_rooms` != "" OR `us`.`moderated_categories` != "", 1, 0 ) AS `is_moderator`,
                                IF( `curr_us`.`is_admin` = "y", `us`.`moderated_rooms`, "" ) AS `moderated_rooms`,
                                IF( `curr_us`.`is_admin` = "y", `us`.`moderated_categories`, "" ) AS `moderated_categories`,
                                COALESCE( `av`.`binaryfile_id`, `av_def`.`binaryfile_id` ) AS `avatar_bid`,
                                IF( FIND_IN_SET( `us`.`id`, `curr_us`.`muted_users` )>0, 1, 0 ) AS `muted_locally`,
                                IF( `us`.`global_muted_until` > CURDATE() OR `us`.`global_muted_permanently` = "y", 1, 0 ) AS `global_muted`,
                                IF( `us`.`global_muted_until` > CURDATE(), UNIX_TIMESTAMP( `us`.`global_muted_until` ), 0 ) AS `global_muted_until`,
                                `us`.`global_muted_reason` AS `global_muted_reason`,
                                IF( `curr_us`.`is_admin` = "y", `us`.`global_muted_by`, "" ) AS `global_muted_by`,
                                IF( `curr_us`.`is_admin` = "y", `us`.`global_muted_by_username`, "" ) AS `global_muted_by_username`,
                                IF( `us`.`banned_until` > CURDATE() OR `us`.`banned_permanently` = "y", 1, 0 ) AS `banned`,
                                IF( `us`.`banned_until` > CURDATE(), UNIX_TIMESTAMP( `us`.`banned_until` ), 0 ) AS `banned_until`,
                                `us`.`ban_reason` AS `ban_reason`,
                                IF( `curr_us`.`is_admin` = "y", `us`.`banned_by`, "" ) AS `banned_by`,
                                IF( `curr_us`.`is_admin` = "y", `us`.`banned_by_username`, "" ) AS `banned_by_username`,
                                `ud`.`gender` AS `gender`,
                                IF( `us`.`is_guest` = "y", 1, 0 ) AS `is_guest`
                                ';
                      $groupby=' GROUP BY `us`.`id` ';
                    }
                    if (!empty($argv[3])) {
                      // LIMIT
                      if (!empty($argv[2])) {
                        $limit=' LIMIT \\_ARG2_\\, \\_ARG3_\\';
                      } else {
                        $limit=' LIMIT \\_ARG3_\\';
                      }
                    }
                    if (!empty($argv[4])) {
                      $orderdir=!empty($argv[5])? ' DESC ' : ' ASC ';
                      // Sort by
                      switch ($argv[4]) {

                        case 1 :
                          // Nickname
                          $orderby=' ORDER BY `nickname_plain` '.$orderdir;
                        break;

                        case 2 :
                          // Join date
                          $orderby=' ORDER BY `joined` '.$orderdir.', `nickname_plain` ASC';
                        break;

                        case 3 :
                          // Last login date
                          $orderby=' ORDER BY `last_login` '.$orderdir.', `nickname_plain` ASC';
                        break;

                        case 4 :
                          // Online stats
                          $orderby=' ORDER BY `online_status` '.$orderdir.', `nickname_plain` ASC';
                        break;

                        case 5 :
                          // Time spent online
                          $orderby=' ORDER BY `time_online` '.$orderdir.', `nickname_plain` ASC';
                        break;

                      }
                    }
                    if (!empty($argv[6])) {
                      // Nickname
                      $where.=' AND ( `nn`.`nickname_plain` LIKE "%\\_arg6_\\%" OR `nn`.`nickname_plain` IS NULL AND `us`.`login` LIKE "%\\_arg6_\\%" )';
                    }
                    if (isset($argv[7])) {
                      // ID of current user
                      $argv[7]*=1;
                    } else {
                      $argv[7]=0;
                    }
                    if (isset($argv[8]) && true===$argv[8]) {
                      // Banned users only
                      $where.=' AND (`us`.`banned_until` > CURDATE() OR `us`.`banned_permanently` = "y")';
                    }
                    if (isset($argv[9]) && true===$argv[9]) {
                      // Muted users only
                      $where.=' AND (`us`.`global_muted_until` > CURDATE() OR `us`.`global_muted_permanently` = "y")';
                    }
                    if (isset($argv[10]) && true===$argv[10]) {
                      // Moderators only
                      $where.=' AND ( `us`.`moderated_rooms` != "" OR `us`.`moderated_categories` != "" )';
                    }
                    if (isset($argv[11]) && true===$argv[11]) {
                      // Moderators only
                      $where.=' AND `us`.`is_admin` = "y"';
                    }
                    if (isset($argv[12]) && true===$argv[12]) {
                      // Not activated only
                      $where.=' AND `us`.`activated` = "n"';
                    } else {
                      // Activated only
                      $where.=' AND `us`.`activated` = "y"';
                    }
                    $query='SELECT '.$select.'
                              FROM `'.PCPIN_DB_PREFIX.'user` `us`
                                   LEFT JOIN `'.PCPIN_DB_PREFIX.'nickname` `nn` ON (`nn`.`user_id` = `us`.`id` AND `nn`.`default` = "y")
                                   LEFT JOIN `'.PCPIN_DB_PREFIX.'user` `curr_us` ON `curr_us`.`id` = BINARY "\\_ARG7_\\"
                                   LEFT JOIN `'.PCPIN_DB_PREFIX.'session` `se` ON `se`.`_s_user_id` = `us`.`id`
                                   LEFT JOIN `'.PCPIN_DB_PREFIX.'avatar` `av` ON `av`.`user_id` = `us`.`id` AND `av`.`primary` = "y"
                                   LEFT JOIN `'.PCPIN_DB_PREFIX.'avatar` `av_def` ON `av_def`.`user_id` = 0 AND `av_def`.`primary` = "y"
                                   LEFT JOIN `'.PCPIN_DB_PREFIX.'userdata` `ud` ON `ud`.`user_id` = `us`.`id`
                             WHERE 1
                                   '.$where.'
                                   '.$groupby.'
                                   '.$orderby.'
                                   '.$limit;
      break;

      case 2000 :   // Get display types of displayable banners
                    // Used in: PCPIN_Banner->checktRoomBanners()
                    $query='SELECT DISTINCT `display_position` AS `pos`
                                       FROM `'.PCPIN_DB_PREFIX.'banner`
                                      WHERE `active` = "y"
                                            AND `start_date` <= NOW()
                                            AND ( `expiration_date` >= NOW() OR `expiration_date` = "0000-00-00 00:00:00" )
                                            AND ( `max_views` = 0 OR `max_views` < `views` )
                                            ';
      break;

      case 2010 :   // Get random displayable banner of specified display position
                    // Used in: PCPIN_Banner->getRandomBanner()
                    $query='SELECT * FROM `'.PCPIN_DB_PREFIX.'banner`
                                    WHERE `active` = "y"
                                          AND `start_date` <= NOW()
                                          AND ( `expiration_date` >= NOW() OR `expiration_date` = "0000-00-00 00:00:00" )
                                          AND ( `max_views` = 0 OR `max_views` < `views` )
                                          AND `display_position` = "\\_ARG1_\\"
                                     ORDER BY RAND()
                                        LIMIT 1';
      break;

      case 2020 :   // Calculates total online time (in seconds), including current session
                    // Used in: PCPIN_User->calculateOnlineTime()
                    $query='SELECT `us`.`time_online` + COALESCE( UNIX_TIMESTAMP() - UNIX_TIMESTAMP(`se`.`_s_created`), 0 ) AS `time_online_total`
                              FROM `'.PCPIN_DB_PREFIX.'user` `us`
                                   LEFT JOIN `'.PCPIN_DB_PREFIX.'session` `se` ON `se`.`_s_user_id` = `us`.`id`
                             WHERE `us`.`id` = "\\_ARG1_\\"
                                   LIMIT 1';
      break;

      case 2030 :   // Collect full message data for logging
                    // Used in: PCPIN_Message_Log->addLogRecord()
                    $query='SELECT `me`.*,
                                   COALESCE( `src_cat`.`id`, 0 ) AS `category_id`,
                                   COALESCE( `src_cat`.`name`, "" ) AS `category_name`,
                                   COALESCE( `src_room`.`id`, 0 ) AS `room_id`,
                                   COALESCE( `src_room`.`name`, "" ) AS `room_name`,
                                   COALESCE( `tgt_cat`.`id`, 0 ) AS `target_category_id`,
                                   COALESCE( `tgt_cat`.`name`, "" ) AS `target_category_name`,
                                   COALESCE( `tgt_room`.`name`, "" ) AS `target_room_name`,
                                   COALESCE( `tgt_nn`.`nickname`, "" ) AS `target_user_nickname`
                              FROM `'.PCPIN_DB_PREFIX.'message` `me`
                                   LEFT JOIN `'.PCPIN_DB_PREFIX.'session` `src_se` ON `src_se`.`_s_user_id` = `me`.`author_id`
                                   LEFT JOIN `'.PCPIN_DB_PREFIX.'room` `src_room` ON `src_room`.`id` = `src_se`.`_s_room_id`
                                   LEFT JOIN `'.PCPIN_DB_PREFIX.'category` `src_cat` ON `src_cat`.`id` = `src_room`.`category_id`
                                   LEFT JOIN `'.PCPIN_DB_PREFIX.'room` `tgt_room` ON `tgt_room`.`id` = `me`.`target_room_id`
                                   LEFT JOIN `'.PCPIN_DB_PREFIX.'category` `tgt_cat` ON `tgt_cat`.`id` = `tgt_room`.`category_id`
                                   LEFT JOIN `'.PCPIN_DB_PREFIX.'nickname` `tgt_nn` ON ( `tgt_nn`.`user_id` = `me`.`target_user_id` AND `tgt_nn`.`default` = "y" )
                                   LEFT JOIN `'.PCPIN_DB_PREFIX.'user` `tgt_us` ON `tgt_us`.`id` = `me`.`target_user_id`
                             WHERE `me`.`id` = "\\_ARG1_\\"
                                   LIMIT 1';
      break;

      case 2040 :   // Delete old message logs
                    // Used in: PCPIN_Message_Log->cleanUp()
                    $query='DELETE `ml`, `la`
                              FROM `'.PCPIN_DB_PREFIX.'message_log` `ml`
                                   LEFT JOIN `'.PCPIN_DB_PREFIX.'message_log_attachment` `la` ON `la`.`message_id` = `ml`.`message_id`
                             WHERE `ml`.`date` <= "\\_ARG1_\\"';
      break;

      case 2050 :   // Update all users' ignore list after deleting a user
                    // Used in: PCPIN_User->deleteUser()
                    $query='UPDATE `'.PCPIN_DB_PREFIX.'user`
                              SET `muted_users` = TRIM( BOTH "," FROM REPLACE( CONCAT( ",", `muted_users`, "," ), ",\\_ARG1_\\,", "," ) )
                             WHERE FIND_IN_SET( "\\_ARG1_\\", `muted_users` )';
      break;

      case 2060 :   // List users for pruning: idle users and timed-out not activated users
                    // Used in: PCPIN_Session->_s_cleanUp()
                    $where_not_activated=' 0 ';
                    $where_idle=' 0 ';
                    if (!empty($argv[1])) {
                      $where_not_activated='`activated` = "n" AND `joined` < FROM_UNIXTIME( "\\_ARG1_\\" )';
                    }
                    if (!empty($argv[1])) {
                      $where_idle='`activated` = "y" AND `is_admin` = "n" AND `moderated_rooms` = "" AND `moderated_categories` = "" AND ( `last_login` != "0000-00-00 00:00:00" AND `last_login` < FROM_UNIXTIME( "\\_ARG2_\\" ) OR `last_login` = "0000-00-00 00:00:00" AND `joined` < FROM_UNIXTIME( "\\_ARG2_\\" ) )';
                    }
                    $query='SELECT `id` FROM `'.PCPIN_DB_PREFIX.'user` WHERE '.$where_not_activated.' OR '.$where_idle;
      break;

      case 2070 :   // Update language expression
                    // Used in: PCPIN_Language_Expression->updateExpression()
                    $query='UPDATE `'.PCPIN_DB_PREFIX.'language_expression`
                               SET `value` = "\\_ARG3_\\"
                             WHERE `language_id` = "\\_ARG1_\\"
                                   AND `code` = BINARY "\\_ARG2_\\"
                             LIMIT 1';
      break;

      case 2080 :   // Clean unbanned users
                    // Used in: PCPIN_Session->_s_CleanUp()
                    $query='UPDATE `'.PCPIN_DB_PREFIX.'user`
                               SET `banned_by` = 0,
                                   `banned_by_username` = "",
                                   `banned_until` = "0000-00-00 00:00:00",
                                   `ban_reason` = ""
                             WHERE `banned_until` > "0000-00-00 00:00:00"
                                   AND `banned_until` < NOW()
                                   AND `banned_permanently` != "y"';
      break;

    }
    if ($query!='') {
      $query=str_replace('\\_', PCPIN_SQLQUERY_ARG_SEPARATOR_START, str_replace('_\\', PCPIN_SQLQUERY_ARG_SEPARATOR_END, $query));
      foreach ($argv as $key=>$arg) {
        // Escape dangerous characters from query parameters
        if (is_scalar($arg)) {
          // Scalar argument
          $arg_with_wildcards=$this->_db_escapeStr($arg, false);
          $arg_no_wildcards=$this->_db_escapeStr($arg);
          // Pass parameters to query template
          $query=str_replace(PCPIN_SQLQUERY_ARG_SEPARATOR_START.'ARG'.$key.PCPIN_SQLQUERY_ARG_SEPARATOR_END, $arg_with_wildcards, $query);
          $query=str_replace(PCPIN_SQLQUERY_ARG_SEPARATOR_START.'arg'.$key.PCPIN_SQLQUERY_ARG_SEPARATOR_END, $arg_no_wildcards, $query);
        } else {
          // Invalid argument type. An empty string will be assumed.
          $query=str_replace(PCPIN_SQLQUERY_ARG_SEPARATOR_START.'ARG'.$key.PCPIN_SQLQUERY_ARG_SEPARATOR_END, '""', $query);
          $query=str_replace(PCPIN_SQLQUERY_ARG_SEPARATOR_START.'arg'.$key.PCPIN_SQLQUERY_ARG_SEPARATOR_END, '""', $query);
        }
      }
    }
    return $query;
  }

}
?>