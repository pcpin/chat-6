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
 * Class PCPIN_Banner
 * Manage banners
 * @author Konstantin Reznichak <k.reznichak@pcpin.com>
 * @copyright Copyright &copy; 2007, Konstantin Reznichak
 */
class PCPIN_Banner extends PCPIN_Session {

  /**
   * Banner ID
   * @var   int
   */
  var $id=0;

  /**
   * Banner name
   * @var   string
   */
  var $name='';

  /**
   * Flag: banner active "y" or not "n"
   * @var   string
   */
  var $active='';

  /**
   * Banner source type (URL: "u" or custom text: "c")
   * @var   string
   */
  var $source_type='';

  /**
   * Banner source URL or HTML code
   * @var   string
   */
  var $source='';

  /**
   * Display position. Possible values:
   *    t     Banner will be displayed at the top of the window
   *    b     Banner will be displayed at the bottom of the window
   *    p     Banner will be displayed in new pop-up window
   *    m     Banner will be displayed between chat messages
   * @var   string
   */
  var $display_position='';

  /**
   * Views counter
   * @var   int
   */
  var $views=0;

  /**
   * After how many vews disable banner. 0: never
   * @var   int
   */
  var $max_views=0;

  /**
   * Banner start date (MySQL DATETIME)
   * @var   string
   */
  var $start_date='';

  /**
   * Banner expiration date (MySQL DATETIME). '0000-00-00 00:00:00' means that banner will never expire.
   * @var   string
   */
  var $expiration_date='';

  /**
   * Banner width in pixels
   * @var   int
   */
  var $width=0;

  /**
   * Banner height in pixels
   * @var   int
   */
  var $height=0;





  /**
   * Constructor
   * @param   object  &$sessionhandler  Session handler
   */
  function PCPIN_Banner(&$sessionhandler) {
    // Init object
    $this->_s_init($sessionhandler, $this);
  }


  /**
   * Add new banner
   * @param   string    $name               Banner name
   * @param   string    $active             Flag: banner active "y" or not "n"
   * @param   string    $source_type        Banner source type (URL: "u" or custom text: "c")
   * @param   string    $source             Banner source URL or HTML code
   * @param   string    $display_position   Banner display position
   * @param   string    $max_views          Views limit
   * @param   string    $start_date         Start date
   * @param   string    $expiration_date    Expiration date
   * @param   string    $width              Banner width
   * @param   string    $height             Banner height
   * @return  boolean TRUE on success or FALSE on error
   */
  function addBanner($name='', $active='', $source_type='', $source='', $display_position='', $max_views=0, $start_date=0, $expiration_date=0, $width=0, $height=0) {
    $result=false;
    if (   !empty($name)
        && $source_type!=''
        && $display_position!=''
        && $height>0
        && $width>0) {
      $this->id=0;
      $this->name=$name;
      $this->active=$active;
      $this->source=$source;
      $this->source_type=$source_type;
      $this->display_position=$display_position;
      $this->max_views=$max_views;
      $this->start_date=$start_date;
      $this->expiration_date=$expiration_date;
      $this->width=$width;
      $this->height=$height;
      if ($this->_db_insertObj()) {
        $result=true;
        $this->id=$this->_db_lastInsertID();
      }
    }
    return $result;
  }


  /**
   * Get banners list
   * @return  array
   */
  function getBanners() {
    $banners=array();
    if ($this->_db_getList('display_position DESC, name ASC')) {
      foreach ($this->_db_list as $data) {
        $data['start_date']=PCPIN_Common::datetimeToTimestamp($data['start_date']);
        $data['expiration_date']=$data['expiration_date']>'0000-00-00 00:00:00'? PCPIN_Common::datetimeToTimestamp($data['expiration_date']) : 0;
        $banners[]=$data;
      }
      $this->_db_freeList();
    }
    return $banners;
  }


  /**
   * Check if there are displayable banners of all types
   * Returns an array with display positions of displayable banners
   * @return  array
   */
  function checktRoomBanners() {
    $display_positions=array();
    $query=$this->_db_makeQuery(2000, date('Y-m-d H:i:s'));
    if ($result=$this->_db_query($query)) {
      while ($data=$this->_db_fetch($result, MYSQL_ASSOC)) {
        $display_positions[]=$data['pos'];
      }
      $this->_db_freeResult($result);
    }
    return $display_positions;
  }


  /**
   * Delete banner
   * @param   int   $id   Banner ID
   * @return  boolean   TRUE on success or FALSE on error
   */
  function deleteBanner($id=0) {
    $result=false;
    if ($this->_db_getList('id', 'id = '.$id, 1)) {
      $result=$this->_db_deleteRow($id);
    }
    return $result;
  }


  /**
   * Update banner
   * @param   int       $id                 Banner ID
   * @param   boolean   $obj                If TRUE, then object properties will be updated
   * @param   boolean   $db                 If TRUE, then database table will be updated
   * @param   string    $name               Banner name. NULL: do not change.
   * @param   string    $active             Flag: banner active "y" or not "n". NULL: do not change.
   * @param   string    $source_type        Banner source type (URL: "u" or custom text: "c"). NULL: do not change.
   * @param   string    $source             Banner source URL or HTML code. NULL: do not change.
   * @param   string    $display_position   Banner display position. NULL: do not change.
   * @param   string    $max_views          Views limit. NULL: do not change.
   * @param   string    $start_date         Start date. NULL: do not change.
   * @param   string    $expiration_date    Expiration date. NULL: do not change.
   * @param   string    $width              Banner width. NULL: do not change.
   * @param   string    $height             Banner height. NULL: do not change.
   * @return  boolean   TRUE on success or FALSE on error
   */
  function updateBanner($id, $obj=false, $db=false,
                        $name=null,
                        $active=null,
                        $source_type=null,
                        $source=null,
                        $display_position=null,
                        $max_views=null,
                        $start_date=null,
                        $expiration_date=null,
                        $width=null,
                        $height=null) {
    $result=false;
    if (!empty($id)) {
      if (true===$obj && $id==$this->id) {
        $result=true;
        if (!is_null($name)) $this->name=$name;
        if (!is_null($active)) $this->active=$active;
        if (!is_null($source_type)) $this->source_type=$source_type;
        if (!is_null($source)) $this->source=$source;
        if (!is_null($display_position)) $this->display_position=$display_position;
        if (!is_null($max_views)) $this->max_views=$max_views;
        if (!is_null($start_date)) $this->start_date=$start_date;
        if (!is_null($expiration_date)) $this->expiration_date=$expiration_date;
        if (!is_null($width)) $this->width=$width;
        if (!is_null($height)) $this->height=$height;
      }
      if (true===$db) {
        $param=array();
        if (!is_null($name)) $param['name']=$name;
        if (!is_null($active)) $param['active']=$active;
        if (!is_null($source_type)) $param['source_type']=$source_type;
        if (!is_null($source)) $param['source']=$source;
        if (!is_null($display_position)) $param['display_position']=$display_position;
        if (!is_null($max_views)) $param['max_views']=$max_views;
        if (!is_null($start_date)) $param['start_date']=$start_date;
        if (!is_null($expiration_date)) $param['expiration_date']=$expiration_date;
        if (!is_null($width)) $param['width']=$width;
        if (!is_null($height)) $param['height']=$height;
        $result=$this->_db_updateRow($id, 'id', $param);
      }
    }
    return $result;
  }


  /**
   * Get random viewable banner of specified display position.
   * Returns an array with banner source type as KEY and banner source as VAL.
   * @param   string    $display_position     Display position
   * @return  array
   */
  function getRandomBanner($display_position='') {
    $banner_data=array();
    if (!empty($display_position)) {
      $query=$this->_db_makeQuery(2010, $display_position, date('Y-m-d H:i:s'));
      if ($result=$this->_db_query($query)) {
        if ($data=$this->_db_fetch($result, MYSQL_ASSOC)) {
          $banner_data=$data;
          $this->_db_updateRow($data['id'], 'id', array('views'=>$data['views']+1));
        }
        $this->_db_freeResult($result);
      }
    }
    return $banner_data;
  }



}
?>