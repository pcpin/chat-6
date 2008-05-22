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


$errortext=array();
$statustext=array();

if (!empty($do_upload)) {
  if (   !empty($dump)
      && is_array($dump)
      && isset($dump['error'])
      && empty($dump['error'])
      && !empty($dump['size'])
      && is_uploaded_file($dump['tmp_name'])
      ) {
    $queries_count=0;
    if ($h=fopen($dump['tmp_name'], 'rb')) {
      $query='';
      do {
        $data=fread($h, 4096);
        if (false!==$data && $data!='') {
          $query.=$data;
          if (false!==strpos($query, PCPIN_SQL_QUERY_SEPARATOR)) {
            // Query separator found. Execute query.
            $parts=explode(PCPIN_SQL_QUERY_SEPARATOR, $query);
            $parts_count=count($parts);
            do {
              execQuery(array_shift($parts));
              $parts_count--;
            } while ($parts_count>1);
            $query=array_shift($parts);
          }
        }
      } while(!feof($h));
      if ($query!='') {
        execQuery($query);
      }
      fclose($h);
      $statustext[]=str_replace('[COUNT]', $queries_count, $l->g('count_queries_executed'));
    } else {
      $errortext[]=$l->g('failed_opening_uploaded_file');
    }
  } else {
    $errortext[]=$l->g('file_upload_error');
  }
}



// Initialize template handler
_pcpin_loadClass('pcpintpl'); $tpl=new PcpinTpl();
$tpl->setBasedir('./tpl');
$tpl->readTemplatesFromFile('./admin/db_restore.tpl');

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

// Display errors
if (!empty($errortext)) {
  $tpl->addVar('error', 'error', nl2br(htmlspecialchars(implode("\n", $errortext))));
}
// Display status messages
if (!empty($statustext)) {
  $tpl->addVar('status', 'status', nl2br(htmlspecialchars(implode("\n", $statustext))));
}


/**
 * Execute a query parsed out from dump file
 * @param   string    $query        Query
 */
function execQuery($query) {
  $query=trim($query);
  if($query!='') {
    global $session;
    global $errortext;
    global $queries_count;
    global $l;
    if(false===$result=$session->_db_query($query)) {
      $errortext[]="\n".$l->g('following_query_caused_error').":\n".$query."\n---------------------------------------------------";
    } else {
      $session->_db_freeResult($result);
    }
    $queries_count++;
  }
}
?>