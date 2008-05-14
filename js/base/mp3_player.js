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
 * PCPIN MP3 player handle
 * @var object
 */
var PCPIN_MP3_Player=null;

/**
 * Name of the file to play in "locked" mode after player finished initialization
 * @var string
 */
PCPIN_MP3_Player_PlayLockedAfterInit='';

/**
 * Default player volume (must be between 0 and 100)
 * @var int
 */
var PCPIN_MP3_PlayerDefaultVolume=75;

/**
 * This function called by player after it finished initialisation
 */
function PCPIN_MP3_Player_Init() {
  // Initialize JavaScript object
  PCPIN_MP3_Player=new PCPINPlayer();
  PCPIN_MP3_Player.init();
  if (typeof(PCPIN_MP3_Player_PlayLockedAfterInit)=='string' && PCPIN_MP3_Player_PlayLockedAfterInit!='') {
    PCPIN_MP3_Player.loadUrl(PCPIN_MP3_Player_PlayLockedAfterInit);
    PCPIN_MP3_Player.playTrackLocked();
  }
}


/**
 * This function called by player after it finished playing track
 */
function PCPIN_MP3_Player_PlayComplete() {
  PCPIN_MP3_Player.PlayingLocked=false;
}


/**
 * PCPIN MP3 flash player JavaScript API
 */
var PCPINPlayer=function() {

  /**
   * Player instance
   * @var object
   */
  this.PlayerInstance=null;

  /**
   * Flag: If TRUE, then player will ignore playTrack(), playTrackLocked() and loadUrl() calls until current track still being played
   * @var boolean
   */
  this.PlayingLocked=false;



  /**
   * Initialize player
   */
  this.init=function() {
    var obj=$('pcpin_mp3');
    if (obj.SetTrackVolume) {
      this.PlayerInstance=obj;
    } else {
      try {
        obj=document.pcpin_mp3;
        if (obj.SetTrackVolume) {
          this.PlayerInstance=obj;
          if (typeof(PCPIN_MP3_PlayerDefaultVolume)==number) {
            this.PlayerInstance.setVolume(PCPIN_MP3_PlayerDefaultVolume);
          }
        }
      } catch (e) {}
    }
  }


  /**
   * Load MP3 track from URL
   * @param   string    url   URL
   */
  this.loadUrl=function(url) {
    if (typeof(url)=='string' && url!='' && this.PlayerInstance && !this.PlayingLocked) {
      try {
        this.PlayerInstance.LoadTrackUrl(url);
      } catch (e) {}
    }
  }


  /**
   * Play loaded track
   */
  this.playTrack=function() {
    if (this.PlayerInstance && !this.PlayingLocked) {
      try {
        this.PlayerInstance.PlayTrack();
      } catch (e) {}
    }
  }


  /**
   * Play loaded track in "locked" mode. The player will ignore all other sounds until the track is playing.
   */
  this.playTrackLocked=function() {
    if (this.PlayerInstance && !this.PlayingLocked) {
      try {
        this.PlayingLocked=true;
        this.PlayerInstance.PlayTrack();
      } catch (e) {}
    }
  }


  /**
   * Stop currently played track
   */
  this.stopTrack=function() {
    if (this.PlayerInstance) {
      try {
        this.PlayerInstance.StopTrack();
      } catch (e) {}
    }
  }


  /**
   * Pause currently played track
   */
  this.pauseTrack=function() {
    if (this.PlayerInstance) {
      try {
        this.PlayerInstance.PauseTrack();
      } catch (e) {}
    }
  }


  /**
   * Set new volume
   * @param   int   volume    Volume, a number between 0 (quiet) and 100 (loud)
   */
  this.setVolume=function(volume) {
    if (typeof(volume)=='number' && volume>=0 && volume<=100 && this.PlayerInstance) {
      try {
        this.PlayerInstance.SetTrackVolume(volume);
      } catch (e) {}
    }
  }


}