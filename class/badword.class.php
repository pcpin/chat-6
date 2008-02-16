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
 * Class PCPIN_Badword
 * Manage word blacklist
 * @author Konstantin Reznichak <k.reznichak@pcpin.com>
 * @copyright Copyright &copy; 2007, Konstantin Reznichak
 */
class PCPIN_Badword extends PCPIN_Session {

  /**
   * Word ID
   * @var   int
   */
  var $id=0;

  /**
   * Word
   * @var   string
   */
  var $word='';

  /**
   * Replacement
   * @var   string
   */
  var $replacement='';

  /**
   * Bad words array as returned by $this->getWords()
   * @var   string
   */
  var $words_cache=array();




  /**
   * Constructor
   * @param   object  &$sessionhandler  Session handler
   */
  function PCPIN_Badword(&$sessionhandler) {
    // Init object
    $this->_s_init($sessionhandler, $this);
  }

  /**
   * Get words
   * @return  array
   */
  function getWords() {
    if ($this->_db_getList('word ASC')) {
      $this->words_cache=$this->_db_list;
      $this->_db_freeList();
    }
    return $this->words_cache;
  }

  /**
   * Change word
   * @param   int       $id           Word ID
   * @param   string    $word         Word
   * @param   string    $replacement  Replacement
   * @return  boolean   TRUE on success or false on error
   */
  function updateWord($id=0, $word='', $replacement='') {
    $ok=false;
    if (!empty($id)) {
      $this->id=$id;
      $this->word=$word;
      $this->replacement=$replacement;
      $ok=$this->_db_updateObj($id);
    }
    return $ok;
  }

  /**
   * Delete word from database
   * @param   int       $id     Word ID
   * @return  boolean TRUE on success or false on error
   */
  function deleteWord ($id=0) {
    $ok=false;
    if (!empty($id)) {
      $ok=$this->_db_deleteRow($id);
    }
    return $ok;
  }

  /**
   * Add new word
   * @param   string    $word         Word
   * @param   string    $replacement  Replacement
   * @return  boolean   TRUE on success or false on error
   */
  function addWord($word='', $replacement='') {
    $ok=false;
    $this->id=0;
    $this->word=$word;
    $this->replacement=$replacement;
    if ($ok=$this->_db_insertObj()) {
      $this->id=$this->_db_lastInsertID();
    }
    return $ok;
  }

  /**
   * Replace bad words in a string
   * @param   string    $string         String to filter
   * @return  string
   */
  function filterString($string='') {
    if ($string!='') {
      if (empty($this->words_cache)) {
        $this->getWords();
      }
      if (!empty($this->words_cache)) {
        foreach ($this->words_cache as $badword_data) {
          $string=preg_replace('/('.$badword_data['word'].')/i', $badword_data['replacement'], $string);
        }
      }
    }
    return $string;
  }

  /**
   * Check string for containing bad words
   * @param   string    $string         String to check
   * @return  boolean TRUE if strong does not contains bad words, FALSE otherwise
   */
  function checkString($string='') {
    $result=true;
    if ($string!='') {
      if (empty($this->words_cache)) {
        $this->getWords();
      }
      if (!empty($this->words_cache)) {
        $string=_pcpin_strtolower($string);
        foreach ($this->words_cache as $badword_data) {
          if (false!==_pcpin_strpos($string, _pcpin_strtolower($badword_data['word']))) {
            $result=false;
            break;
          }
        }
      }
    }
    return $result;
  }

}
?>