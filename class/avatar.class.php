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
 * Class PCPIN_Avatar
 * Manage avatars
 * @author Konstantin Reznichak <k.reznichak@pcpin.com>
 * @copyright Copyright &copy; 2007, Konstantin Reznichak
 */
class PCPIN_Avatar extends PCPIN_Session {

  /**
   * Avatar ID
   * @var int
   */
  var $id=0;

  /**
   * Avatar owner' user ID. If empty: default avatar
   * @var int
   */
  var $user_id=0;

  /**
   * Flag, "y", if avatar is primary, "n" if not
   * @var string
   */
  var $primary='';

  /**
   * ID of avatar image file in "binaryfile" table
   * @var int
   */
  var $binaryfile_id=0;




  /**
   * Constructor. Initialize Avatar class.
   * @param   object  &$sessionhandler  Session handler
   */
  function PCPIN_Avatar(&$sessionhandler) {
    // Init object
    $this->_s_init($sessionhandler, $this);
  }


  /**
   * Add new avatar
   * @param   int     $binaryfile_id  ID of avatar image binary file
   * @param   int     $user_if        User ID
   * @return  boolean TRUE on success or FALSE on error
   */
  function addAvatar($binaryfile_id, $user_id=0) {
    $result=false;
    $this->id=0;
    if (!empty($binaryfile_id)) {
      $this->binaryfile_id=$binaryfile_id;
      $this->user_id=$user_id;
      $this->primary='n';
      if ($result=$this->_db_insertObj()) {
        $this->id=$this->_db_lastInsertID();
        $this->setPrimaryAvatar($user_id, $this->id);
      }
    }
    return $result;
  }


  /**
   * Delete avatar(s) owned by user
   * @param   int   $user_id    Avatar owner' User ID
   * @param   int   $id         Optional. Avatar ID. If empty, then *ALL* avatars owned by user will be deleted.
   */
  function deleteAvatar($user_id=0, $id=0) {
    _pcpin_loadClass('binaryfile'); $binaryfile=new PCPIN_BinaryFile($this);
    if (!empty($user_id)) {
      if (!empty($id)) {
        $this->_db_getList('id,binaryfile_id', 'id = '.$id, 'user_id = '.$user_id);
      } else {
        $this->_db_getList('id,binaryfile_id', 'user_id = '.$user_id);
      }
      $list=$this->_db_list;
      $this->_db_freeList();
      foreach ($list as $data) {
        if ($this->_db_getList('id', 'user_id = 0', 'binaryfile_id = '.$data['binaryfile_id'], 1)) {
          // Avatar has been picked from the Gallery. Do not delete binary file.
          $this->_db_freeList();
        } else {
          // Delete binary file
          $binaryfile->deleteBinaryFile($data['binaryfile_id']);
        }
        // Delete avatar
        $this->_db_deleteRow($data['id']);
      }
      if (!empty($id) && !$this->_db_getList('user_id = '.$user_id, 'primary = y', 1)) {
        // User has no primary avatars. Set one.
        $this->setPrimaryAvatar($user_id);
      }
    }
  }


  /**
   * Delete avatar from Avatar Gallery
   * @param   int     $avatar_id      Avatar ID
   * @return  boolean TRUE on success or FALSE on error
   */
  function deleteGalleryAvatar($avatar_id=0) {
    $result=false;
    _pcpin_loadClass('binaryfile'); $binaryfile=new PCPIN_BinaryFile($this);
    if (!empty($avatar_id) && $this->_db_getList('binaryfile_id,primary', 'id = '.$avatar_id, 'user_id = 0', 1)) {
      // Avatar exists
      $binaryfile_id=$this->_db_list[0]['binaryfile_id'];
      $default='y'===$this->_db_list[0]['primary'];
      $this->_db_freeList();
      if ($default) {
        // Default avatar will be deleted. Check wether there are not default avatars.
        if ($this->_db_getList('id,binaryfile_id', 'user_id = 0', 'primary = n', 1)) {
          $new_default_id=$this->_db_list[0]['id'];
          $new_default_binaryfile_id=$this->_db_list[0]['binaryfile_id'];
          $this->_db_freeList();
        } else {
          // Last gallery avatar cannot be deleted!
          return false;
        }
      }
      // Check wether avatar is linked to other users
      $linked_avatars=array();
      if ($this->_db_getList('id,user_id,primary', 'binaryfile_id = '.$binaryfile_id, 'user_id > 0')) {
        // There are some linked avatars
        $linked_avatars=$this->_db_list;
        $this->_db_freeList();
        if (empty($default)) {
          // Get default avatar
          $this->_db_getList('binaryfile_id', 'user_id = 0', 'primary = y', 1);
          $new_default_binaryfile_id=$this->_db_list[0]['binaryfile_id'];
        }
      }
      if ($default) {
        // Set new default avatar
        $this->setDefaultAvatarGallery($new_default_id);
      }
      // Update linked avatars
      foreach ($linked_avatars as $data) {
        // Check wether user has other avatars
        $old_id=$data['id'];
        if ($data['primary']=='y') {
          // That was user's primary avatar
          if ($this->_db_getList('id', 'user_id = '.$data['user_id'], 'id != '.$old_id, 1)) {
            // Set new primary avatar
            $this->setPrimaryAvatar($data['user_id'], $this->_db_list[0]['id']);
          }
        }
        $this->_db_deleteRow($old_id);
      }
      // Delete binary file
      $binaryfile->deleteBinaryFile($binaryfile_id);
      // Delete avatar
      $this->_db_deleteRow($avatar_id);
      $result=true;
    }
    return $result;
  }


  /**
   * Set new default Gallery avatar
   * @param   int   $avatar_id    Avatar ID
   */
  function setDefaultAvatarGallery($avatar_id=0) {
    if (!empty($avatar_id) && $this->_db_getList('id', 'id = '.$avatar_id, 'user_id = 0', 1)) {
      // Avatar exists
      // Check for another default avatar
      if ($this->_db_getList('id', 'user_id = 0', 'primary = y', 1)) {
        // Remove "default" flag
        $this->_db_updateRow($this->_db_list[0]['id'], 'id', array('primary'=>'n'));
        $this->_db_freeList();
      }
      // Set "default" flag
      $this->_db_updateRow($avatar_id, 'id', array('primary'=>'y'));
    }
  }


  /**
   * Get list of avatars owned by specified user
   * @param   int       $user_id        User ID
   * @param   int       $limit          Optional. If not empty, limit number of returned avatars.
   * @return  array   If user has no avatars, then default avatar (ID=0) will be returned.
   */
  function getAvatars($user_id=0, $limit=0) {
    $avatars=array();
    _pcpin_loadClass('binaryfile'); $binaryfile=new PCPIN_BinaryFile($this);
    if (!empty($user_id)) {
      if (   empty($limit) && $this->_db_getList('id, primary, binaryfile_id', 'user_id = '.$user_id, 'primary ASC', 'id ASC')
          || !empty($limit) && $this->_db_getList('id, primary, binaryfile_id', 'user_id = '.$user_id, 'primary ASC', 'id ASC', $limit)) {
        foreach ($this->_db_list as $data) {
          if ($binaryfile->_db_getList('width, height', 'id = '.$data['binaryfile_id'])) {
            $data['width']=$binaryfile->_db_list[0]['width'];
            $data['height']=$binaryfile->_db_list[0]['height'];
            $avatars[]=$data;
          }
        }
      } else {
        // User has no avatars
        if ($this->_db_getList('binaryfile_id', 'user_id = 0', 1)) {
          if ($binaryfile->_db_getList('width, height', 'id = '.$this->_db_list[0]['binaryfile_id'])) {
            $this->_db_list[0]['id']=0;
            $this->_db_list[0]['width']=$binaryfile->_db_list[0]['width'];
            $this->_db_list[0]['height']=$binaryfile->_db_list[0]['height'];
            $this->_db_list[0]['primary']='n';
            $avatars[]=$this->_db_list[0];
          }
        }
      }
      $this->_db_freeList();
    }
    return $avatars;
  }


  /**
   * Get list of gallery avatars
   * @return  array
   */
  function getGalleryAvatars() {
    $avatars=array();
    _pcpin_loadClass('binaryfile'); $binaryfile=new PCPIN_BinaryFile($this);
    if ($this->_db_getList('id, primary, binaryfile_id', 'user_id = 0', 'id ASC')) {
      foreach ($this->_db_list as $data) {
        if ($binaryfile->_db_getList('width, height', 'id = '.$data['binaryfile_id'])) {
          $data['width']=$binaryfile->_db_list[0]['width'];
          $data['height']=$binaryfile->_db_list[0]['height'];
          $avatars[]=$data;
        }
      }
      $this->_db_freeList();
    }
    return $avatars;
  }


  /**
   * Set new primary avatar
   * @param   int   $user_id    User ID
   * @param   int   $avatar_id  Optional. Avatar ID. If empty, then avatar with smallest ID, if any, will be set as primary.
   */
  function setPrimaryAvatar($user_id=0, $avatar_id=0) {
    if (!empty($user_id)) {
      // Check avatar
      if (   !empty($avatar_id) && $this->_db_getList('id,primary', 'id = '.$avatar_id, 'user_id = '.$user_id, 1)
          || $this->_db_getList('id,primary', 'user_id = '.$user_id, 'id DESC', 1)) {
        // Avatar exists and belongs to user
        if ($this->_db_list[0]['primary']!='y') {
          // Clear "primary flag from all user's avatars
          $this->_db_updateRow($user_id, 'user_id', array('primary'=>'n'), true);
          // Set new flag
          $this->_db_updateRow($this->_db_list[0]['id'], 'id', array('primary'=>'y'));
        }
        $this->_db_freeList();
      }
    }
  }


  /**
   * Set user avatar from Avatar Gallery
   * @param   int   $user_id      User ID
   * @param   int   $avatar_id    Avatar ID
   * @return  boolean TRUE on success or FALSE on error
   */
  function setAvatarFromGallery($user_id=0, $avatar_id=0) {
    $result=false;
    if (!empty($user_id) && !empty($avatar_id) && $this->_db_getList('binaryfile_id', 'id = '.$avatar_id, 'user_id = 0', 1)) {
      // Avatar exists
      $binaryfile_id=$this->_db_list[0]['binaryfile_id'];
      _pcpin_loadClass('user'); $user=new PCPIN_User($this);
      if ($user->_db_getList('id', 'id = '.$user_id, 1)) {
        // User exists
        $user->_db_freeList();
        // Does user has primary avatar
        if ($this->_db_getList('id', 'user_id = '.$user_id, 'primary = y', 1)) {
          // There is already one primary avatar
          $this->primary='n';
          $this->_db_freeList();
        } else {
          // This avatar will be primary
          $this->primary='y';
        }
        // Insert avatar
        $this->id=0;
        $this->user_id=$user_id;
        $this->binaryfile_id=$binaryfile_id;
        $result=$this->_db_insertObj();
      }
    }
    return $result;
  }


}
?>