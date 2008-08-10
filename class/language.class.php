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
 * Class PCPIN_Language
 * Manage languages
 * @author Konstantin Reznichak <k.reznichak@pcpin.com>
 * @copyright Copyright &copy; 2007, Konstantin Reznichak
 */
class PCPIN_Language extends PCPIN_Session {

  /**
   * Language ID
   * @var   int
   */
  var $id=0;

  /**
   * Accept-Language two-letter code according to ISO-639 standard
   * Codes are here: http://www.oasis-open.org/cover/iso639a.html
   * @var   string
   */
  var $iso_name='';

  /**
   * Language name
   * @var   string
   */
  var $name='';

  /**
   * Language name in its's own language
   * @var   string
   */
  var $local_name='';

  /**
   * Flag: if "y", then language is active
   * @var   string
   */
  var $active='';




  /**
   * Constructor
   * @param   object  &$sessionhandler  Session handler
   */
  function PCPIN_Language(&$sessionhandler) {
    // Init object
    $this->_s_init($sessionhandler, $this);
    // Cached language expressions
    $this->cache['language']=array();
  }


  /**
   * Set language
   * @param   mixed     $language_id    Language ID or ISO name
   * @return  boolean   TRUE on success or FALSE on error
   */
  function setLanguage($language_id=0) {
    $ok=false;
    // Clear cache
    $this->_cache['language']=array();
    if (empty($this->_conf_all['allow_language_selection']) && $language_id!=$this->_conf_all['default_language']) {
      // Language selection is disallowed. Using default language.
      $language_id=$this->_conf_all['default_language'];
    } else {
      if (!pcpin_ctype_digit($language_id)) {
        // ISO name submitted, get ID for this language
        if ($this->_db_getList('id', 'iso_name = '.$language_id, 1)) {
          // Specified language exists
          $language_id=$this->_db_list[0]['id'];
          $this->_db_freeList();
        } else {
          // Specified language not found, using default language
          $language_id=$this->_conf_all['default_language'];
        }
      }
    }
    // Load language data
    if (empty($language_id) || !$this->_db_getList('id = '.$language_id, 'active = y', 1)) {
      // No language requested or requested language does not exists or not active
      // Trying to get default language
      if (!$this->_db_getList('id = '.$this->_conf_all['default_language'], 'active = y', 1)) {
        // Trying to get English language
        if (!$this->_db_getList('iso_name = en', 'active = y', 1)) {
          // Get first available language
          $this->_db_getList('active = y', 'id ASC', 1);
        }
      }
    }
    if (!empty($this->_db_list)) {
      $this->_db_setObject($this->_db_list[0]);
      $this->_db_freeList();
      $ok=true;
    }
    return $ok;
  }


  /**
   * Get language expression value from cache.
   * @param   string    $code     Expression code
   * @return  string  Expression or expression code if no expression found
   */
  function g($code) {
    $expr=$code;
    if (!empty($this->id) && $code!='') {
      if (empty($this->_cache['language'])) {
        // Language expressions are not loaded yet - load them now
        _pcpin_loadClass('language_expression'); $language_expression=new PCPIN_Language_Expression($this);
        if ($language_expression->_db_getList('code,value', 'language_id = '.$this->id)) {
          if (!empty($language_expression->_db_list)) {
            foreach ($language_expression->_db_list as $data) {
              $this->_cache['language'][$data['code']]=$data['value'];
            }
            $language_expression->_db_freeList();
          }
        }
      }
      if (isset($this->_cache['language'][$code]) && $this->_cache['language'][$code]!='') {
        $expr=$this->_cache['language'][$code];
      }
    }
    return $expr;
  }


  /**
   * Get language expressions (from cache) that code begins with a supplied prefix.
   * @param   string    $prefix   Expression code prefix
   * @return  array
   */
  function getExpressions($prefix='') {
    $list=array();
    if ($prefix=='') {
      // All expressions
      $list=$this->_cache['language'];
    } else {
      foreach ($this->_cache['language'] as $code=>$expr) {
        if (0===strpos($code, $prefix)) {
          $list[$code]=$expr;
        }
      }
    }
    return $list;
  }


  /**
   * Get available languages list
   * @param   boolean   $all    Optional. If TRUE, then inactive languages will be also listed.
   * @return  array
   */
  function getLanguages($all=false) {
    if (empty($all)) {
      $this->_db_getList('active = y', 'name ASC');
    } else {
      $this->_db_getList('name ASC');
    }
    $list=$this->_db_list;
    $this->_db_freeList();
    return $list;
  }


  /**
   * Check the language for availability
   * @param   mixed     $language_id    Language ID or ISO name
   * @return  int   Language ID, if language is available or 0, if not
   */
  function checkLanguage($id) {
    $available_id=0;
    if (!empty($id)) {
      if (   !pcpin_ctype_digit($id) && $this->_db_getList('id', 'iso_name = '.$id, 'active = y', 1)
          || pcpin_ctype_digit($id) && $this->_db_getList('id', 'id = '.$id, 'active = y', 1)) {
        $available_id=$this->_db_list[0]['id'];
        $this->_db_freeList();
      }
    }
    return $available_id;
  }


  /**
   * Delete language
   * @param   int   $language_id    Language ID
   * @return  boolean TRUE on success or FALSE on error
   */
  function deleteLanguage($language_id=0) {
    $result=false;
    $languages=$this->getLanguages(true);
    $language_found=false;
    $active_language_needed=false;
    $active_language_found=false;
    foreach ($languages as $language_data) {
      if ($language_data['id']==$language_id) {
        $language_found=true;
        $active_language_needed=$language_data['active']=='y';
      } elseif ($language_data['active']=='y') {
        $active_language_found=true;
      }
      if ($language_found && $active_language_needed && $active_language_found) {
        break;
      }
    }
    if ($language_found) {
      if (!$active_language_needed || !empty($active_language_found)) {
        // Delete language
        if ($result=$this->_db_deleteRow($language_id)) {
          // Delete all language expressions
          _pcpin_loadClass('language_expression'); $language_expression=new PCPIN_Language_Expression($this);
          $language_expression->_db_deleteRowMultiCond(array('language_id'=>$language_id), true);
        }
      }
    }
    return $result;
  }


  /**
   * Replace all occurances of language wildcard "{LNG_...}" in a string with an appropriate language expression
   * @param   string    $str    String to process
   * @return  string
   */
  function addExpressionsString($str='') {
    if ($str!='') {
      $parts=null;
      preg_match_all('/{LNG_[^{}]+}/', $str, $parts);
      foreach ($parts as $part) {
        if (!empty($part)) {
          foreach ($part as $lng_expr) {
            $str=str_replace($lng_expr, $this->g(strtolower(substr($lng_expr, 5, -1))), $str);
          }
        }
      }
    }
    return $str;
  }


  /**
   * Export language object as string.
   * Output string will have following format: <hash><data>
   *      <hash> - MD5 hash of the <data> (32 chars)
   *      <data> - Serialized and BASE64-encoded array in following format:
   *               array (
   *                       // Header data.
   *                       'data_type'      =>  'language' ,
   *                       'pcpin_version'  =>  'pcpin_chat_<version>' ,
   *                       'date_created'   =>  '<UNIX_TIMESTAMP>' ,
   *                       'rand'           =>  '<RANDOM_STRING_32_BYTES>' ,
   *                       // Main data block as serialized and BASE64-encoded array in following format (all values are hexadecial):
   *                       'data'           =>  array (
   *                                                   'iso_name'      =>  '<ISO_CODE>' ,
   *                                                   'local_name'    =>  '<LOCAL_NAME>' ,
   *                                                   'expressions'   =>  array (
   *                                                                              array (
   *                                                                                     'code'        =>  '<EXPRESSION_CODE>' ,
   *                                                                                     'value'       =>  '<EXPRESSION_VALUE>' ,
   *                                                                                     'multi_row'   =>  '<EXPRESSION_MULTI_ROW>'
   *                                                                                    ) ,
   *                                                                              ...
   *                                                                              )
   *                                                 )
   *                      )
   * @param   int   $language_id    Language ID to export
   * @return  mixed   (string) Language data string on success or (boolean) FALSE on error
   */
  function exportLanguage($language_id=0) {
    $out=false;
    if (!empty($language_id) && $this->_db_getList('x0iso_name, x0name, x0local_name', 'id = '.$language_id, 1)) {
      $lng=array(
                 'data_type'        =>  'language',
                 'pcpin_version'    =>  'pcpin_chat_'.PCPIN_VERSION,
                 'date_created'     =>  time(),
                 'rand'             =>  PCPIN_Common::randomString(32),
                 'data'             =>  array(
                                              'iso_name'    =>  $this->_db_list[0]['iso_name'],
                                              'local_name'  =>  $this->_db_list[0]['local_name'],
                                              'expressions' =>  array()
                                              )
                 );
      $this->_db_freeList();
      _pcpin_loadClass('language_expression'); $language_expression=new PCPIN_Language_Expression($this);
      if ($language_expression->_db_getList('x0code, x0value, x0multi_row', 'language_id = '.$language_id)) {
        while ($expr=array_pop($language_expression->_db_list)) {
          $lng['data']['expressions'][]=array('code'      =>  $expr['code'],
                                              'value'     =>  $expr['value'],
                                              'multi_row' =>  $expr['multi_row']);
        }
        $out=base64_encode(serialize($lng));
        unset($lng);
        // Get hash
        $out=strtoupper(md5($out)).$out;
      }
    }
    return $out;
  }

  /**
   * Import language from string. String format: see $this->() documentation.
   * On success, ID of created language will be returned.
   * Error codes:
   *          10:  Invalid / damaged file
   *          100: Language already exists
   * @param   string    $raw            Raw data
   * @param   int       $language_id    Language ID will be stored here
   * @return  int   0 (zero) on success or error number on error
   */
  function importLanguage($raw, &$language_id) {
    $status=10;
    $language_id=0;
    if ($raw!='') {
      $hash=substr($raw, 0, 32);
      $raw=substr($raw, 32);
      if (strlen($hash)==32 && $raw!='' && strtoupper(md5($raw))===$hash) {
        // Hash OK
        if ($raw=@base64_decode($raw)) {
          if ($lng=@unserialize($raw)) {
            unset($raw);
            if (   is_array($lng)
                && isset($lng['data_type']) && $lng['data_type']=='language'
                && isset($lng['pcpin_version']) && 0===strpos($lng['pcpin_version'], 'pcpin_chat_') && floor(PCPIN_VERSION*10)===floor(substr($lng['pcpin_version'], 11)*10)
                && !empty($lng['data']) && is_array($lng['data'])
                ) {
              $lng=$lng['data'];
              // Check ISO name
              if (!empty($lng['iso_name'])) {
                $this->iso_name=PCPIN_Common::hexToString($lng['iso_name']);
                if (_pcpin_strlen($this->iso_name)==2 && defined('PCPIN_ISO_LNG_'.strtoupper($this->iso_name))) {
                  if ($this->_db_getList('id', 'iso_name = '.$this->iso_name, 1)) {
                    // Language already exists
                    $status=100;
                    $language_id=$this->_db_list[0]['id'];
                    $this->_db_freeList();
                  } else {
                    // Name
                    $this->name=substr(constant('PCPIN_ISO_LNG_'.strtoupper($this->iso_name)), 3);
                    // Get local name
                    if (isset($lng['local_name'])) {
                      $this->local_name=PCPIN_Common::hexToString($lng['local_name']);
                      if ($this->local_name=='') {
                        $this->local_name=$this->name;
                      }
                      // Get expressions
                      if (!empty($lng['expressions']) && is_array($lng['expressions'])) {
                        $lng=$lng['expressions'];
                        // Insert new object
                        $this->id=0;
                        $this->active='n';
                        if ($this->_db_insertObj()) {
                          $language_id=$this->_db_lastInsertID();
                          $this->id=$language_id;
                          // Insert language expressions
                          _pcpin_loadClass('language_expression');
                          foreach ($lng as $expr) {
                            $language_expression=new PCPIN_Language_Expression($this);
                            $language_expression->language_id=$language_id;
                            $language_expression->code=PCPIN_Common::hexToString($expr['code']);
                            $language_expression->value=PCPIN_Common::hexToString($expr['value']);
                            $language_expression->multi_row=PCPIN_Common::hexToString($expr['multi_row']);
                            if ($language_expression->code!='' && is_scalar($language_expression->value)) {
                              $language_expression->_db_insertObj();
                            }
                          }
                          unset($lng);
                          $status=0;
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
    return $status;
  }


  /**
   * Create a copy of the language
   * @param   string  $src    Source language ISO name
   * @param   string  $dst    Destination language ISO name
   * @return  boolean TRUE on success or FALSE on error
   */
  function copyLanguage($from='', $to='') {
    $result=false;
    if ($from!='' && $to!='' && $this->_db_getList('id', 'iso_name = '.$from, 1)) {
      $src_id=$this->_db_list[0]['id'];
      $this->_db_freeList();
      $this->id=0;
      $this->iso_name=strtolower($to);
      $this->name=substr(constant('PCPIN_ISO_LNG_'.strtoupper($to)), 3);
      $this->local_name=$this->name;
      $this->active='n';
      if ($this->_db_insertObj()) {
        $result=true;
        $this->id=$this->_db_lastInsertID();
        // Copy language expressions
        _pcpin_loadClass('language_expression'); $language_expression=new PCPIN_Language_Expression($this);
        $language_expression->_db_getList('language_id = '.$src_id);
        $expressions=$language_expression->_db_list;
        $language_expression->_db_freeList();
        foreach ($expressions as $expr) {
          $language_expression=new PCPIN_Language_Expression($this);
          $language_expression->_db_setObject($expr);
          $language_expression->language_id=$this->id;
          $language_expression->_db_insertObj();
        }
      }
    }
    return $result;
  }


  /**
   * Get language file information
   * @param   string    $raw            Raw data
   * @param   string    &$lng_info      Language file info will be stored here
   * @return  boolean TRUE on success or FALSE on error
   */
  function getLanguageFileInfo($raw, &$lng_info) {
    $result=false;
    $lng_info=array();
    if ($raw!='') {
      $hash=substr($raw, 0, 32);
      $raw=substr($raw, 32);
      if (strlen($hash)==32 && $raw!='' && strtoupper(md5($raw))===$hash) {
        // Hash OK
        if ($raw=@base64_decode($raw)) {
          if ($lng=@unserialize($raw)) {
            unset($raw);
            if (   is_array($lng)
                && isset($lng['data_type']) && $lng['data_type']=='language'
                && isset($lng['pcpin_version'])
                && !empty($lng['data']) && is_array($lng['data'])
                ) {
              $result=true;
              $lng_info['pcpin_version']=$lng['pcpin_version'];
              $lng_info['date_created']=$lng['date_created'];
              $lng=$lng['data'];
              $lng_info['iso_name']=PCPIN_Common::hexToString($lng['iso_name']);
              $lng_info['local_name']=PCPIN_Common::hexToString($lng['local_name']);
              $lng_info['expressions_count']=count($lng['expressions']);
            }
          }
        }
      }
    }
    return $result;
  }
  
}
?>