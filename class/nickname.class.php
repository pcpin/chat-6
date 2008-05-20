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
 * Class PCPIN_Nickname
 * Manage nicknames
 * @author Konstantin Reznichak <k.reznichak@pcpin.com>
 * @copyright Copyright &copy; 2007, Konstantin Reznichak
 */
class PCPIN_Nickname extends PCPIN_Session {

  /**
   * Nickname ID
   * @var   int
   */
  var $id=0;

  /**
   * User ID
   * @var   int
   */
  var $user_id=0;

  /**
   * Nickname (colored)
   * @var   string
   */
  var $nickname='';

  /**
   * Nickname (without color codes)
   * @var   string
   */
  var $nickname_plain='';

  /**
   * Default (flag). Values: "y" or "n"
   * @var   string
   */
  var $default='';



  /**
   * Constructor
   * @param   object  &$sessionhandler  Session handler
   */
  function PCPIN_Nickname(&$sessionhandler) {
    // Init object
    $this->_s_init($sessionhandler, $this);
  }


  /**
   * Add new nickname
   * @param   int     $user_id      User ID
   * @param   string  $nickname     Nickname (colored)
   * @return  int  Nickname ID on success or 0 on error
   */
  function addNickname($user_id=0, $nickname='') {
    $this->id=0;
    $nickname=trim($nickname);
    if (!empty($user_id) && $nickname!='') {
      $this->user_id=$user_id;
      $this->nickname=$nickname;
      $this->nickname_plain=$this->coloredToPlain($nickname, false);
      // Check for default flag
      if ($this->_db_getList('id', 'user_id = '.$user_id, 'default = y', 1)) {
        $this->default='n';
      } else {
        $this->default='y';
      }
      if ($this->_db_insertObj()) {
        $this->id=$this->_db_lastInsertID();
      }
    }
    return $this->id;
  }


  /**
   * Delete nickname
   * @param   int     $user_id      User ID
   * @param   int     $id           Nickname ID
   * @return  boolean   TRUE on success or FALSE on error
   */
  function deleteNickname($user_id=0, $id=0) {
    $result=false;
    if (!empty($user_id) && !empty($id) && $this->_db_getList('id,default,user_id', 'id = '.$id)) {
      $data=$this->_db_list[0];
      $this->_db_freeList();
      if ($data['user_id']==$user_id) {
        $this->_db_deleteRow($id);
        if ($data['default']=='y') {
          // Default nickname was deleted. Set "default" flag to the next available nickname.
          if ($this->_db_getList('user_id = '.$user_id, 'id ASC', 1)) {
            $this->_db_setObject($this->_db_list[0]);
            $this->default='y';
            $this->_db_updateObj($this->id);
          }
        }
      }
    }
    return $result;
  }


  /**
   * Update nickname
   * @param   int       $user_id      User ID
   * @param   int       $id           Nickname ID
   * @param   string    $nickname     New nickname
   * @return  boolean   TRUE on success or FALSE on error
   */
  function updateNickname($user_id=0, $id=0, $nickname='') {
    $result=false;
    if (!empty($user_id) && !empty($id) && $nickname!='' && $this->_db_getList('nickname', 'id = '.$id, 'user_id = '.$user_id, 1)) {
      $nickname=$this->optimizeColored($nickname);
      if ($this->_db_list[0]['nickname']==$nickname) {
        // Nothing to change
        $result=true;
      } else {
        $this->_db_freeList();
        $result=$this->_db_updateRow($id, 'id', array('nickname'=>$nickname, 'nickname_plain'=>$this->coloredToPlain($nickname, false)));
      }
    }
    return $result;
  }


  /**
   * Delete all user's nicknames
   * @param   int     $user_id      User ID
   * @return  boolean   TRUE on success or FALSE on error
   */
  function deleteAllNickname($user_id=0) {
    $result=false;
    if (!empty($user_id)) {
      $result=$this->_db_deleteRowMultiCond(array('user_id'=>$user_id), true);
    }
    return $result;
  }


  /**
   * Get all nicknames of specified user
   * @param   int     $user_id      User ID
   * @return  array
   */
  function getNicknames($user_id=0) {
    $nicknames=array();
    if (!empty($user_id) && $this->_db_getList('id,nickname,nickname_plain,default', 'user_id = '.$user_id, 'nickname_plain ASC')) {
      foreach ($this->_db_list as $nickname_data) {
        $nicknames[]=$nickname_data;
      }
    }
    return $nicknames;
  }


  /**
   * Mark nickname as "default"
   * @param   int     $id         Nickname ID
   * @param   int     $user_id    User ID
   * @return  boolean TRUE on success or FALSE on error
   */
  function setDefault($id=0, $user_id=0) {
    $result=false;
    if (!empty($id) && !empty($user_id)) {
      $change_needed=true;
      // Get ID of currently default nickname
      if ($this->_db_getList('id', 'default = y', 'user_id = '.$user_id, 1)) {
        if ($this->_db_list[0]['id']!=$id) {
          $this->_db_updateRow($this->_db_list[0]['id'], 'id', array('default'=>'n'));
          $this->_db_freeList();
        } else {
          $change_needed=false;
          $result=true;
        }
      }
      if ($change_needed) {
        $result=$this->_db_updateRow($id, 'id', array('default'=>'y'));
      }
    }
    return $result;
  }


  /**
   * Extract plain text from (colored) nickname
   * @param   string    $nickname           Colored nickname
   * @return  boolean   $escape_html_chars  If TRUE (default), then HTML chars will be escaped
   */
  function coloredToPlain($nickname='', $escape_html_chars=true) {
    $plain='';
    if ($nickname!='') {
      $parts=explode('^', $nickname);
      if (!isset($parts[1])) {
        $plain=$parts[0];
      } else {
        foreach ($parts as $part) {
          if (_pcpin_strlen($part)>6) {
            $plain.=substr($part, 6);
          } elseif (_pcpin_strlen($part)<6) {
            $plain.=$part;
          }
        }
      }
    }
    if ($escape_html_chars) {
      $plain=htmlspecialchars($plain);
    }
    return $plain;
  }


  /**
   * Get default nickname. If user has no nicknames, his username will be returned.
   * @param   int   $user_id    User ID
   * @return  string
   */
  function getDefaultNickname($user_id) {
    $nickname='';
    if (!empty($user_id)) {
      if (!$this->_db_getList('nickname', 'user_id = '.$user_id, 'default = y', 1)) {
        $this->_db_getList('nickname', 'user_id = '.$user_id, 1);
      }
      if (!empty($this->_db_list)) {
        $nickname=$this->_db_list[0]['nickname'];
        $this->_db_freeList();
      }
      if ($nickname=='') {
        // User has no nicknames, get username
        $usr=new PCPIN_User($this);
        if ($usr->_db_getList('login', 'id =# '.$user_id, 1)) {
          $nickname=$usr->_db_list[0]['login'];
          $usr->_db_freeList();
        }
      }
    }
    return $nickname;
  }


  /**
   * Remove double color codes from the string
   * @param   string    $colored        String with color codes
   * @return  string
   */
  function optimizeColored($colored='') {
    $optimized='';
    if ($colored!='') {
      $parts=explode('^', $colored);
      $optimized.=array_shift($parts);
      foreach ($parts as $part) {
        if (_pcpin_strlen($part)>6) {
          if (trim($part)!='') {
            $optimized.='^'.$part;
          } else {
            $optimized.=substr($part, 6);
          }
        }
      }
    }
    return $optimized;
  }

  /**
   * Convert string with color codes into HTML colored string
   * @param   string    colored     String with color codes
   * @param   string    tag         HTML tag to use (default: SPAN)
   * @return  string
   */
  function coloredToHTML($colored='', $tag='span') {
    $html='';
    $parts=explode('^', $colored);
    if (count($parts)==1) {
      $html=str_replace(' ', '&nbsp;', htmlspecialchars($parts[0]));
    } else {
      foreach ($parts as $part) {
        if (_pcpin_strlen($part)>6) {
          $html.='<'.$tag.' style="color:#'.substr($part, 0, 6).'">'
               .str_replace(' ', '&nbsp;', htmlspecialchars(substr($part, 6)))
               .'</'.$tag.'>';
        }
      }
    }
    return $html;
  }

}
?>