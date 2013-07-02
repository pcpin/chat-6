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
   * Cache for field types
   * @var array
   */
  var $_db_field_types_cache = array();

  /**
   * Resources for field types cache
   * @var array
   */
  var $_db_field_types_cache_resources = array();



  /**
   * Constructor.
   * Connect to database.
   * @param   object  &$caller        Caller object
   * @param   array   $db_conndata    Database connection data
   */
  function PCPIN_DB(&$caller, $db_conndata) {
    // Connect to database
    $connected=false;
    if (empty($this->_db_conn)) {
      if (!function_exists('mysql_connect')) {
        // MySQL extension is not loaded
        PCPIN_Common::dieWithError(1, '<b>Fatal error</b>: MySQL extension is not loaded');
      } elseif (PCPIN_DB_PERSISTENT && $this->_db_conn=@mysql_pconnect($db_conndata['server'], $db_conndata['user'], $db_conndata['password'])) {
        // Database server connected using mysql_pconnect() function
        $connected=true;
      } elseif ($this->_db_conn=mysql_connect($db_conndata['server'], $db_conndata['user'], $db_conndata['password'])) {
        // Database server connected using mysql_connect() function
        $connected=true;
      }
      if (!$connected) {
        PCPIN_Common::dieWithError(1, '<b>Fatal error</b>: Failed to connect database server');
      } else {
        // Set UTF-8 character set for client-server communication
        $this->_db_setCharsets();
        // Disable MySQL strict mode
        $this->_db_query('SET SESSION sql_mode=""');
        // Trying do select database
        if (!mysql_select_db($db_conndata['database'], $this->_db_conn)) {
          // Failed to select database
          $this->_db_close();
          PCPIN_Common::dieWithError(1, '<b>Fatal error</b>: Failed to select database');
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
   * @param   resource    $result    Result identifier
   */
  function _db_freeResult($result) {
    if (is_resource($result)) {
      mysql_free_result($result);
    }
    unset($this->_db_field_types_cache[$result], $this->_db_field_types_cache_resources[$result]);
  }


  /**
   * Close database connection
   */
  function _db_close() {
    if (is_resource($this->_db_conn)) {
      mysql_close($this->_db_conn);
    }
    $this->_db_conn=null;
    $this->_db_field_types_cache = array();
    $this->_db_field_types_cache_resources = array();
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
#echo "$query\r\n\r\n";
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
            echo '<b>'.implode('.', $diff_str).":</b> ".str_replace("\n", "\r\n", $query)." <br />\r\n\r\n";
            flush();
          } elseif (PCPIN_SQL_LOGFILE!='' && $lfh=@fopen(PCPIN_SQL_LOGFILE, 'a')) {
            @fwrite($lfh, implode('.', $diff_str).': '.str_replace("\n", "\r\n", $query)."\r\n\r\n");
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
            echo nl2br("Query:\r\n$query\r\nERROR ($errno): $errstr\r\n---------------------------------------\r\n\r\n");
            flush();
          } elseif (PCPIN_SQL_LOGFILE!='' && $lfh=@fopen(PCPIN_SQL_LOGFILE, 'a')) {
            @fwrite($lfh, "Query:\r\n".str_replace("\n", "\r\n", $query)."\r\nERROR ($errno): $errstr\r\n---------------------------------------\r\n\r\n");
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
      $result_id = (int) $result;
      if (isset($this->_db_field_types_cache[$result_id])) {
        $field_types = $this->_db_field_types_cache[$result_id];
      } else {
        // Determine field types
        $field_types = array();
        $field_nr = mysql_num_fields($result);
        while ($field_nr > 0) {
          $field_nr --;
          if ($result_type !== MYSQL_NUM) {
            $field_name = mysql_field_name($result, $field_nr);
          } else {
            $field_name = $field_nr;
          }
          if (!array_key_exists($field_name, $field_types) || $result_type===MYSQL_BOTH) {
            // Map data types
            $type = strtolower(mysql_field_type($result, $field_nr));
            if (false !== strpos($type, 'int')) {
              $type = 'int';
            } elseif (   false !== strpos($type, 'dec')
                      || false !== strpos($type, 'numeric')
                      || false !== strpos($type, 'float')
                      || false !== strpos($type, 'real')
                      || false !== strpos($type, 'double')
                      ) {
              $type = 'float';
            } else {
              $type = null;
            }
            if ($result_type !== MYSQL_BOTH) {
              $field_types[$field_name] = $type;
            } else {
              $field_types[$field_nr] = $type;
              if (!array_key_exists($field_name, $field_types)) {
                $field_types[$field_name] = $type;
              }
            }
          }
        }
        $this->_db_field_types_cache[$result_id] = $field_types;
        $this->_db_field_types_cache_resources[$result_id] = $result;
      }
      if (false !== ($data = mysql_fetch_array($result, $result_type))) {
        // Cast types
        foreach ($field_types as $key => $val) {
          if (!is_null($data[$key]) && $val !== '' && $val !== null) {
            settype($data[$key], $val);
          }
        }
      } else {
        unset($this->_db_field_types_cache[$result_id], $this->_db_field_types_cache_resources[$result_id]);
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
   * @param   boolean   $omit_limit Optional. If TRUE: no LIMIT statement will be used in SQL query. Default is FALSE
   * @return  boolean   TRUE on success or FALSE on error
   */
  function _db_updateRow($id='', $id_field='id', $data=null, $omit_limit=false) {
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
          $query='UPDATE `'.$tbl.'` SET '.implode(', ', $parts).' WHERE `'.$id_field.'` = BINARY "'.$this->_db_escapeStr($id, false).'"'.(true===$omit_limit? '' : ' LIMIT 1');
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
        // Trying to get cached table data.
        $cache=$this->_db_getCacheRecord('_db_tabledata_'.$tbl_name);
        if (!is_null($cache)) {
          // Cached record loaded.
          $fields=unserialize($cache);
        } else {
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
              // Store cache record
              $this->_db_addCacheRecord('_db_tabledata_'.$tbl_name, serialize($fields));
            }
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
#echo $query." <br />\r\n\r\n";
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
  * Repair/optimize database tables, if needed
  */
  function _db_cure() {
    $tables=$this->_db_listTables();
    foreach ($tables as $table) {
      // Repair table
      $this->_db_query($this->_db_makeQuery(127, $table));
      // Optimize table
      $this->_db_query($this->_db_makeQuery(126, $table));
    }
  }


 /**
  * Create SQL query using a template
  * @param    int     $nr   Query template number
  * @param    mixed   ...   Unlimited query parameters. WARNING: scalar data types only!!!
  * @return   string  Created query
  */
  function _db_makeQuery($nr) {
    $query='';
    // Get method arguments
    $argv=func_get_args();
    // First argument is query template number (not needed)
    unset($argv[0]);
    // Load requested template
    if (pcpin_ctype_digit($nr)) {
      require('./class/dbtpl/'.$nr.'.tpl.php');
      $trans=array();
      foreach ($argv as $key=>$arg) {
        $trans['\\_ARG'.$key.'_\\']=is_scalar($arg)? $this->_db_escapeStr($arg, false) : '';
        $trans['\\_arg'.$key.'_\\']=is_scalar($arg)? $this->_db_escapeStr($arg) : '';
      }
      $query=strtr($query, $trans);
    }
    return $query;
  }


  /**
   * Add new record to the cache table
   * @param   string    $id         Record ID (max. 255 chars)
   * @param   string    $contents   Contents
   */
  function _db_addCacheRecord($id, $contents) {
    if (!defined('PCPIN_INSTALL_MODE') || !PCPIN_INSTALL_MODE) {
      $result=$this->_db_query($this->_db_makeQuery(2110, $id, $contents));
      $this->_db_freeResult($result);
    }
  }


  /**
   * Get record
   * @param   string  $id     Record ID
   * @return  mixed   (string) cache contents or NULL if no record found
   */
  function _db_getCacheRecord($id) {
    $cache=null;
    if (!defined('PCPIN_INSTALL_MODE') || !PCPIN_INSTALL_MODE) {
      if ($result=$this->_db_query($this->_db_makeQuery(2120, $id))) {
        if ($data=$this->_db_fetch($result, MYSQL_NUM)) {
          $cache=$data[0];
        }
        $this->_db_freeResult($result);
      }
    }
    return $cache;
  }



}
?>