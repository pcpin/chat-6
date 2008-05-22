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

if (empty($current_user->id) || $current_user->is_admin!=='y') {
  header('Location: '.PCPIN_FORMLINK.'?'.md5(microtime()));
  die();
}

$headers_sent=false;


if (!empty($do_download)) {
  // Get database structure
  $db_structure=array();
  $tables=$session->_db_listTables();
  foreach ($tables as $table) {
    $db_structure[$table]['data']=reset($session->_db_readTableData($table));
    $db_structure[$table]['fields']=array();
    $fields=$session->_db_tableFields($table);
    foreach ($fields as $field) {
      $db_structure[$table]['fields'][$field['Field']]=$field;
    }
    $tbl_indexes=$session->_db_readTableIndexes($table);
    $indexes=array();
    foreach ($tbl_indexes as $index) {
      if (!isset($indexes[$index['Key_name']])) {
        $indexes[$index['Key_name']]=$index;
        $indexes[$index['Key_name']]['columns']=array();
      }
      $indexes[$index['Key_name']]['columns'][]=$index['Column_name'];
    }
    $db_structure[$table]['indexes']=$indexes;
  }
  /**
   * Create queries
   */
  // ... structure
  foreach ($db_structure as $table=>$tabledata) {
    $fields=array();
    foreach ($tabledata['fields'] as $key=>$field) {
      if (!empty($field['Null']) && strtolower($field['Null'])!='no' && strtolower($field['Null'])!='false') {
        $is_null='';
        $extra='';
      }else{
        $is_null='NOT NULL';
        $type_lowered=strtolower($field['Type']);
        $extra='';
      }
      $default='';
      if (!empty($field['Extra'])) {
        $extra=$field['Extra'];
      } else {
        // Check indexes; members of PRIMARY index can't have default value
        $default_allowed=true;
        foreach ($tabledata['indexes'] as $index_name=>$index_fields) {
          if ($index_name=='PRIMARY') {
            foreach ($index_fields['columns'] as $column) {
              if ($column==$field['Field']) {
                $default_allowed=false;
                break;
              }
            }
            break;
          }
        }
        if ($default_allowed) {
          if (false===strpos($type_lowered, 'blob') && false===strpos($type_lowered, 'text')) {
            if (!is_null($field['Default'])) {
              $default='default \''.$session->_db_escapeStr($field['Default']).'\'';
            }
          }
        }
      }
      $fields[]="  `".$field['Field']."` ".$field['Type']." $is_null $default $extra";
    }
    queryOut("DROP TABLE IF EXISTS `$table`");
    $query="CREATE TABLE `$table` (\r\n";
    foreach ($tabledata['indexes'] as $index_name=>$index_fields) {
      if ($index_name=='PRIMARY') {
        $fields[]="  PRIMARY KEY (`".implode('`,`', $index_fields['columns'])."`)";
      } elseif (empty($index_fields['Non_unique'])) {
        $fields[]="  UNIQUE KEY `$index_name` (`".implode('`,`', $index_fields['columns'])."`)";
      } else {
        $fields[]="  KEY `$index_name` (`".implode('`,`', $index_fields['columns'])."`)";
      }
    }
    $query.=implode(",\r\n", $fields)."\r\n";
    if (!empty($tabledata['data']['Create_options'])) {
      $create_options=$tabledata['data']['Create_options'];
    } else {
      $create_options='';
    }
    if (!empty($tabledata['data']['Auto_increment'])) {
      $auto_increment='AUTO_INCREMENT='.$tabledata['data']['Auto_increment'];
    } else {
      $auto_increment='';
    }
    // Charset
    if (!empty($tabledata['data']['Collation'])) {
      $default_charset='DEFAULT CHARSET='.substr($tabledata['data']['Collation'], 0, strpos($tabledata['data']['Collation'], '_'));
    } else {
      $default_charset='';
    }
    // TYPE/ENGINE
    if (isset($tabledata['data']['Engine'])) {
      $engine='ENGINE='.$tabledata['data']['Engine'];
    } elseif (isset($tabledata['data']['Type'])) {
      $engine='TYPE='.$tabledata['data']['Type'];
    }
    $query.=") $engine $create_options $auto_increment $default_charset";
    // Send query to client
    queryOut($query);
  }
  // ... data
  foreach ($db_structure as $table=>$tabledata) {
    $session->_db_table=$table;
    // Count rows
    $rows=$tabledata['data']['Rows'];
    $field_names=array_keys($tabledata['fields']);
    if (!empty($field_names)) {
      $select=array();
      foreach ($field_names as $field_name) {
        $select[]='HEX( `'.$field_name.'` ) AS `'.$field_name.'`';
      }
      $rows_per_call=100;
      for($i=0; $i<$rows; $i+=$rows_per_call) {
        $result=$session->_db_query('SELECT '.implode(',', $select).' FROM `'.$table.'` LIMIT '.$i.', '.$rows_per_call);
        while ($row=$session->_db_fetch($result, MYSQL_ASSOC)) {
          $values=array();
          foreach ($row as $val) {
            if (is_null($val)) {
              $values[]='NULL';
            } elseif ($val=='') {
              $values[]="''";
            } else {
              $values[]='0x'.$val;
            }
          }
          // Send query to client
          queryOut("INSERT INTO `$table` VALUES (".implode(', ', $values).')');
        }
        $session->_db_freeResult($result);
      }
    }
  }
  // Stop output here
  die();
}




// Initialize template handler
_pcpin_loadClass('pcpintpl'); $tpl=new PcpinTpl();
$tpl->setBasedir('./tpl');
$tpl->readTemplatesFromFile('./admin/db_backup.tpl');

// Add global vars to template
foreach ($global_tpl_vars as $key=>$val) {
  $tpl->addGlobalVar($key, htmlspecialchars($val));
}

// Add language expressions to template
foreach ($tpl->tpl_vars_plain as $var) {
  if (0===strpos($var, 'LNG_')) {
    $var=strtolower($var);
    $tpl->addGlobalVar($var, htmlspecialchars($l->g(substr($var, 4))));
  }
}

/**
 * Send query directly to client's browser
 * @param   string    $query    Query
 */
function queryOut($query='') {
  // Output directly to the client's browser
  global $headers_sent;
  if (!$headers_sent) {
    // Send headers
    header('Content-type: application/octet-stream');
    header('Expires: '.gmdate('D, d M Y H:i:s').' GMT');
    header('Content-Disposition: attachment; filename="sqldump.sql"');
    if (PCPIN_CLIENT_AGENT_NAME=='IE') {
      header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
      header('Pragma: public');
    } else {
      header('Pragma: no-cache');
    }
    $headers_sent=true;
  }
  echo $query.';'.PCPIN_SQL_QUERY_SEPARATOR."\r\n";
}


?>