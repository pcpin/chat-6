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
 * This file contains message queue records and functions
 */

/**
 * Initialize MessageQueue object
 */
var MessageQueue=new MessageQueue();
MessageQueue.initializeOut();
MessageQueue.initializeIn();


/**
 * Message object
 * @param   string  type              Message type
 * @param   string  offline           Offline flag
 * @param   string  date              Message date (UNIX timestamp)
 * @param   string  author_id         ID of message author
 * @param   string  author_nickname   Nickname of message author
 * @param   string  target_user_id    ID of message target user
 * @param   string  target_room_id    ID of message target room
 * @param   string  privacy           Privacy level
 * @param   string  body              Message body
 * @param   array   css_properties    Message CSS properties
 * @param   string  actor_nickname    Actor nickname
 * @param   array   attachments       Attachments
 */
function Message(id, type, offline, date, author_id, author_nickname, target_user_id, target_room_id, privacy, body, css_properties, actor_nickname, attachments) {

  /**
   * Message ID
   * @var   int
   */
  this.id=stringToNumber(id);

  /**
   * Message type
   * @var   int
   */
  this.type=stringToNumber(type);

  /**
   * Offline flag
   * @var   string
   */
  this.offline=offline;

  /**
   * Message date in MySQL datetime format
   * @var   string
   */
  this.date=stringToNumber(date);

  /**
   * Author user ID
   * @var   int
   */
  this.author_id=stringToNumber(author_id);

  /**
   * Author nickname
   * @var   string
   */
  this.author_nickname=author_nickname;

  /**
   * Target user ID
   * @var   int
   */
  this.target_user_id=stringToNumber(target_user_id);

  /**
   * Target room ID
   * @var   int
   */
  this.target_room_id=stringToNumber(target_room_id);

  /**
   * Privacy level
   * @var   int
   */
  this.privacy=stringToNumber(privacy);

  /**
   * Message body
   * @var   string
   */
  this.body=body;

  /**
   * Message CSS properties
   * @var object
   */
  this.css_properties=css_properties;

  /**
   * Actor nickname
   * @var   string
   */
  this.actor_nickname=actor_nickname;

  /**
   * Attachments
   * @var object
   */
  this.attachments=attachments;

}


/**
 * MessageQueue object
 */
function MessageQueue() {

  /**
   * Outgoing messages queue
   * @var object
   */
  this.records_out=new Array();

  /**
   * Incoming messages queue
   * @var object
   */
  this.records_in=new Array();


  /**
   * Clear outgoing messages cueue
   */
  this.initializeOut=function() {
    this.records_out=new Array();
  }

  /**
   * Clear incoming messages cueue
   */
  this.initializeIn=function() {
    this.records_in=new Array();
  }


  /**
   * Add new message record to the outgoing list
   * @param   string  type              Message type
   * @param   string  offline           Offline flag
   * @param   string  date              Message date (UNIX timestamp)
   * @param   string  target_user_id    ID of message target user
   * @param   string  target_room_id    ID of message target room
   * @param   string  privacy           Privacy level
   * @param   string  body              Message body
   * @param   array   css_properties    Message CSS properties
   */
  this.addRecordOut=function(type, offline, date, target_user_id, target_room_id, privacy, body, css_properties) {
    this.records_out.push(new Message(0, type, offline, date, '', '', target_user_id, target_room_id, privacy, body, css_properties, ''));
  }

  /**
   * Add new message record to the incoming list
   * @param   string  id                Message ID
   * @param   string  type              Message type
   * @param   string  offline           Offline flag
   * @param   string  date              Message date (UNIX timestamp)
   * @param   string  author_id         ID of message author
   * @param   string  author_nickname   Nickname of message author
   * @param   string  target_user_id    ID of message target user
   * @param   string  target_room_id    ID of message target room
   * @param   string  privacy           Privacy level
   * @param   string  body              Message body
   * @param   array   css_properties    Message CSS properties
   * @param   string  actor_nickname    Actor nickname
   * @param   array   attachments       Attachments
   */
  this.addRecordIn=function(id, type, offline, date, author_id, author_nickname, target_user_id, target_room_id, privacy, body, css_properties, actor_nickname, attachments) {
    this.records_in.push(new Message(id, type, offline, date, author_id, author_nickname, target_user_id, target_room_id, privacy, body, css_properties, actor_nickname, attachments));
  }

  /**
   * Return all records from this.records_out list and clear it
   * @return  array
   */
  this.getAllRecordsOut=function() {
    var recs_orig=this.records_out;
    var recs=new Array();
    this.initializeOut();
    for (var i in recs_orig) {
      recs[i]=recs_orig[i];
    }
    return recs;
  }

  /**
   * Return all records from this.records_in list and clear it
   * @param     boolean   pm_only   If TRUE, then only private messages will be returned. Default: FALSE.
   * @return  array
   */
  this.getAllRecordsIn=function(pm_only) {
    var recs_orig=this.records_in;
    var recs=new Array();
    this.initializeIn();
    if (typeof(pm_only)!='boolean') {
      pm_only=false;
    }
    for (var i in recs_orig) {
      if (false==pm_only || recs_orig[i].privacy==2) {
        recs[i]=recs_orig[i];
      }
    }
    return recs;
  }


}