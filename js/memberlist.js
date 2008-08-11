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
 * Current page number
 * @var int
 */
var CurrentPage=1;

/**
 * Current sort criteria
 * @var int
 */
var CurrentSortBy=1;

/**
 * Current sort direction
 * @var int
 */
var CurrentSortDir=0;

/**
 * Current search criteria
 * @var string
 */
var CurrentSearch='';

/**
 * How many page numbers (max) display at once
 * @var int
 */
var MaxPageNumbers=10;

/**
 * Flag: if TRUE, then gender icons will be displayed in userlist
 * @var boolean
 */
var userlistGender=false;

/**
 * Flag: if TRUE, then avatar thumbs will be displayed in userlist
 * @var boolean
 */
var userlistAvatar=false;

/**
 * Avatar thumb width
 * @var int
 */
var userlistAvatarWidth=10;

/**
 * Avatar thumb height
 * @var int
 */
var userlistAvatarHeight=10;

/**
 * Flag: if TRUE, then "Admin" and "Moderator" flags will be displayed in userlist
 * @var boolean
 */
var userlistPrivileged=false;

/**
 * Moderated categories (keyed by user ID)
 * @var object
 */
var ModeratedCategories=new Array();

/**
 * Moderated rooms (keyed by user ID)
 * @var object
 */
var ModeratedRooms=new Array();


/**
 * Initialise
 * @param   boolean   userlist_gender       Flag: if TRUE, then gender icons will be displayed in userlist
 * @param   boolean   userlist_avatar       Flag: if TRUE, then avatar thumbs will be displayed in userlist
 * @param   boolean   userlist_privileged   Flag: if TRUE, then "Admin" and "Moderator" flags will be displayed in userlist
 */
function initMemberlist(userlist_gender, userlist_avatar, userlist_privileged) {
  userlistGender=userlist_gender;
  userlistAvatar=userlist_avatar;
  userlistPrivileged=userlist_privileged;
  // Define callback function for user options context menu
  CallBackContextMenuFunc='getMemberlist()';
  // Get memberlist
  getMemberlist();
  // Get focus
  window.focus();
}


/**
 * Get memberlist
 * @param   int     page        Optional. Requested page number.
 * @param   int     sort_by     Optional. Sort criteria.
 * @param   int     sort_dir    Optional. Sort order.
 * @param   string  search      Optional. Search criteria.
 */
function getMemberlist(page, sort_by, sort_dir, search) {
  if (typeof(page)!='undefined' && page!=null) {
    CurrentPage=stringToNumber(page);
  }
  if (typeof(sort_by)!='undefined' && sort_by!=null) {
    CurrentSortBy=stringToNumber(sort_by);
  }
  if (typeof(sort_dir)!='undefined' && sort_dir!=null) {
    CurrentSortDir=stringToNumber(sort_dir);
  }
  if (typeof(search)!='undefined' && search!=null) {
    CurrentSearch=trimString(search);
  }
  sendData('_CALLBACK_getMemberlist()', formlink, 'POST', 'ajax=get_memberlist'
                                                         +'&s_id='+urlencode(s_id)
                                                         +'&page='+urlencode(CurrentPage)
                                                         +'&sort_by='+urlencode(CurrentSortBy)
                                                         +'&sort_dir='+urlencode(CurrentSortDir)
                                                         +'&nickname='+urlencode(CurrentSearch)
                                                         +(($('banned_only') && $('banned_only').checked)? '&banned_only=1' : '')
                                                         +(($('muted_only') && $('muted_only').checked)? '&muted_only=1' : '')
                                                         +(($('moderators_only') && $('moderators_only').checked)? '&moderators_only=1' : '')
                                                         +(($('admins_only') && $('admins_only').checked)? '&admins_only=1' : '')
                                                         +(($('not_activated_only') && $('not_activated_only').checked)? '&not_activated_only=1' : '')
                                                         +'&load_custom_fields=1'
                                                         );
}
function _CALLBACK_getMemberlist() {
//debug(actionHandler.getResponseString()); return false;
  var member_nr=0;
  var members_tbl=null;
  var tr=null;
  var td=null;
  var urec_tpl=$('userlist_record_tpl').innerHTML;
  var urec='';
  var online_status='0';
  var online_status_message='';
  var online_seconds=0;
  var online_minutes=0;
  var online_hours=0;
  var online_days=0;
  var ignored_img_suffix='';
  var status_img='';
  var status_title='';
  var pages_html='';
  var page=0;
  var total_pages=0;
  var total_members=0;
  var pn_start=0;
  var pn_end=0;
  var category_nr=0;
  var category_name='';
  var room_nr=0;
  var room_name='';
  var gender='';
  var muted_until=0;

  if (actionHandler.status==-1) {
    // Session is invalid
    window.close();
    opener.document.location.href=formlink+'?session_timeout&ts='+unixTimeStamp();
    return false;
  } else {
    if (actionHandler.status==0) {
      total_members=actionHandler.data['total_members'][0];
      UserList.initialize();
      ModeratedCategories=new Array();
      ModeratedRooms=new Array();
      for (member_nr=0; member_nr<actionHandler.data['member'].length; member_nr++) {
        member=actionHandler.data['member'][member_nr];
        user_id=stringToNumber(member['id'][0]);
        online_status=member['online_status'][0];
        online_status_message=member['online_status_message'][0];
        if (online_status_message=='') online_status_message=getLng('online_status_'+online_status);
        for (var iii=0; iii<member['custom_field'].length; iii++) {
          if (member['custom_field'][iii]['name'][0]=='gender') {
            gender=member['custom_field'][iii]['field_value'][0];
            break;
          }
        }
        UserList.addRecord(user_id,
                           member['nickname'][0],
                           online_status,
                           online_status_message,
                           '1'==member['muted_locally'][0],
                           '1'==member['global_muted'][0],
                           member['global_muted_until'][0],
                           member['ip_address'][0],
                           gender,
                           member['avatar_bid'][0],
                           '1'==member['is_admin'][0],
                           '1'==member['is_moderator'][0],
                           member['joined'][0],
                           member['last_login'][0],
                           member['time_online'][0],
                           '1'==member['banned'][0],
                           member['banned_until'][0],
                           member['ban_reason'][0],
                           stringToNumber(member['banned_by'][0]),
                           member['banned_by_username'][0],
                           stringToNumber(member['global_muted_by'][0]),
                           member['global_muted_by_username'][0],
                           member['global_muted_reason'][0],
                           '1'==member['is_guest'][0]
                           );
        category_nr=0;
        for (category_nr=0; category_nr<member['moderated_category'].length; category_nr++) {
          category_name=member['moderated_category'][category_nr];
          if (ModeratedCategories[user_id]) {
            ModeratedCategories[user_id].push(category_name);
          } else {
            ModeratedCategories[user_id]=new Array(category_name);
          }
        }
        room_nr=0;
        for (room_nr=0; room_nr<member['moderated_room'].length; room_nr++) {
          room_name=member['moderated_room'][room_nr];
          if (ModeratedRooms[user_id]) {
            ModeratedRooms[user_id].push(room_name);
          } else {
            ModeratedRooms[user_id]=new Array(room_name);
          }
        }
      }
      // Display page numbers
      page=stringToNumber(actionHandler.data['page'][0]);
      total_pages=stringToNumber(actionHandler.data['total_pages'][0]);
      $('page_numbers').innerHTML='';
      if (total_pages>1) {
        pages_html='<b>'+htmlspecialchars(getLng('pages'))+' ('+htmlspecialchars(total_pages)+'):</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
        // Calculate page numbers range
        pn_start=(page>=MaxPageNumbers)? page-Math.floor(MaxPageNumbers/2) : 1;
        pn_end=(pn_start+MaxPageNumbers<=total_pages)? pn_start+MaxPageNumbers : total_pages+1;
        if (pn_start>1 && pn_end-pn_start<MaxPageNumbers) {
          pn_start--;
        }
        pages_html+='<a href=":" onclick="getMemberlist(1); return false;" title="'+htmlspecialchars(getLng('goto_first_page'))+'">&laquo; '+htmlspecialchars(getLng('first'))+'</a>&nbsp;&nbsp;&nbsp;&nbsp;';
        if (page>1) {
          pages_html+='<a href=":" onclick="getMemberlist('+htmlspecialchars(page-1)+'); return false;" title="'+htmlspecialchars(getLng('goto_previous_page'))+'">&laquo;</a>&nbsp;&nbsp;&nbsp;&nbsp;';
        }
        for (var i=pn_start; i<pn_end; i++) {
          if (i==page) {
            pages_html+='<b title="'+htmlspecialchars(getLng('page')+' '+i)+'">['+i+']</b>&nbsp;&nbsp;&nbsp;&nbsp;';
          } else {
            pages_html+='<a href=":" onclick="getMemberlist('+htmlspecialchars(i)+'); return false;" title="'+htmlspecialchars(getLng('page')+' '+i)+'">'+htmlspecialchars(i)+'</a>&nbsp;&nbsp;&nbsp;&nbsp;';
          }
        }
        if (page<total_pages) {
          pages_html+='<a href=":" onclick="getMemberlist('+htmlspecialchars(page+1)+'); return false;" title="'+htmlspecialchars(getLng('goto_next_page'))+'">&raquo;</a>&nbsp;&nbsp;&nbsp;&nbsp;';
        }
        pages_html+='<a href=":" onclick="getMemberlist('+htmlspecialchars(total_pages)+'); return false;" title="'+htmlspecialchars(getLng('goto_last_page'))+'">'+htmlspecialchars(getLng('last'))+' &raquo;</a>';
        $('page_numbers').innerHTML=pages_html;
        $('page_numbers').style.display='';
      } else {
        $('page_numbers').style.display='none';
      }
    }
  }

  members_tbl=$('members_tbl');
  members_tbl.style.display='';
  while (members_tbl.rows.length>3) {
    members_tbl.deleteRow(members_tbl.rows.length-2);
  }
  if ($('banned_only') && $('banned_only').checked) {
    $('title_postfix').innerHTML=getLng('banned_only');
  } else if ($('muted_only') && $('muted_only').checked) {
    $('title_postfix').innerHTML=getLng('muted_only');
  } else if ($('admins_only') && $('admins_only').checked) {
    $('title_postfix').innerHTML=getLng('admins_only');
  } else if ($('moderators_only') && $('moderators_only').checked) {
    $('title_postfix').innerHTML=getLng('moderators_only');
  } else if ($('not_activated_only') && $('not_activated_only').checked) {
    $('title_postfix').innerHTML=getLng('not_activated_accounts');
  } else {
    $('title_postfix').innerHTML=getLng('all_members');
  }
  if (total_members==0) {
    // No members found
    tr=members_tbl.insertRow(members_tbl.rows.length-1);
    td=tr.insertCell(-1);
    td.colSpan=4;
    td.innerHTML='<br />'+htmlspecialchars(getLng('no_members_found'))+'<br /><br />';
    setCssClass(td, 'tbl_row');
    td.style.textAlign='center';
  } else {
    // Display userlist
    if ($('banned_only') && $('banned_only').checked) {
      $('registration_date_col').style.display='none';
      $('last_visit_col').style.display='none';
      $('time_online_col').style.display='none';
      $('banned_by_col').style.display='';
      $('banned_until_col').style.display='';
      $('banned_reason_col').style.display='';
      $('muted_by_col').style.display='none';
      $('muted_until_col').style.display='none';
      $('muted_reason_col').style.display='none';
      $('moderated_rooms_col').style.display='none';
      $('moderated_categories_col').style.display='none';
      $('moderator_edit_col').style.display='none';
      $('not_activated_empty_col').style.display='none';
    } else if ($('muted_only') && $('muted_only').checked) {
      $('registration_date_col').style.display='none';
      $('last_visit_col').style.display='none';
      $('time_online_col').style.display='none';
      $('banned_by_col').style.display='none';
      $('banned_until_col').style.display='none';
      $('banned_reason_col').style.display='none';
      $('muted_by_col').style.display='';
      $('muted_until_col').style.display='';
      $('muted_reason_col').style.display='';
      $('moderated_rooms_col').style.display='none';
      $('moderated_categories_col').style.display='none';
      $('moderator_edit_col').style.display='none';
      $('not_activated_empty_col').style.display='none';
    } else if ($('admins_only') && $('admins_only').checked) {
      $('not_activated_empty_col').style.display='none';
    } else if ($('moderators_only') && $('moderators_only').checked) {
      $('registration_date_col').style.display='none';
      $('last_visit_col').style.display='none';
      $('time_online_col').style.display='none';
      $('banned_by_col').style.display='none';
      $('banned_until_col').style.display='none';
      $('banned_reason_col').style.display='none';
      $('muted_by_col').style.display='none';
      $('muted_until_col').style.display='none';
      $('muted_reason_col').style.display='none';
      $('moderated_rooms_col').style.display='';
      $('moderated_categories_col').style.display='';
      $('moderator_edit_col').style.display='';
      $('not_activated_empty_col').style.display='none';
    } else if ($('not_activated_only') && $('not_activated_only').checked) {
      $('registration_date_col').style.display='none';
      $('last_visit_col').style.display='none';
      $('time_online_col').style.display='none';
      $('banned_by_col').style.display='none';
      $('banned_until_col').style.display='none';
      $('banned_reason_col').style.display='none';
      $('muted_by_col').style.display='none';
      $('muted_until_col').style.display='none';
      $('muted_reason_col').style.display='none';
      $('moderated_rooms_col').style.display='none';
      $('moderated_categories_col').style.display='none';
      $('moderator_edit_col').style.display='none';
      $('not_activated_empty_col').style.display='';
    } else {
      $('registration_date_col').style.display='';
      $('last_visit_col').style.display='';
      $('time_online_col').style.display='';
      $('banned_by_col').style.display='none';
      $('banned_until_col').style.display='none';
      $('banned_reason_col').style.display='none';
      $('muted_by_col').style.display='none';
      $('muted_until_col').style.display='none';
      $('muted_reason_col').style.display='none';
      $('moderated_rooms_col').style.display='none';
      $('moderated_categories_col').style.display='none';
      $('moderator_edit_col').style.display='none';
      $('not_activated_empty_col').style.display='none';
    }
    members=UserList.getAllRecords();
    for (var i in members) {
  
      // Calculate time spent online
      online_seconds=members[i].TimeOnline;
      online_days=Math.floor(online_seconds/86400);
      online_seconds-=online_days*86400;
      online_hours=Math.floor(online_seconds/3600);
      online_seconds-=online_hours*3600;
      online_minutes=Math.floor(online_seconds/60);
      online_seconds-=online_minutes*60;
  
      tr=members_tbl.insertRow(members_tbl.rows.length-1);
      td=tr.insertCell(-1);
      td.noWrap=true;
  
      // Online status
      if (true==members[i].MutedLocally) {
        ignored_img_suffix='ignored_';
      } else {
        ignored_img_suffix='';
      }
      if (true==members[i].Banned) {
        status_img='./pic/banned_'+ignored_img_suffix+'10x10.gif';
        if (members[i].BannedUntil==0) {
          status_title=getLng('permanently_banned')+(ignored_img_suffix!=''? (' + '+getLng('ignored')) : '');
        } else {
          status_title=getLng('banned_until').split('[EXPIRATION_DATE]').join(date(dateFormat, members[i].BannedUntil))+(ignored_img_suffix!=''? (' + '+getLng('ignored')) : '');
        }
      } else {
        if (true==members[i].GlobalMuted) {
          muted_until=members[i].getGlobalMutedUntil();
          status_img='./pic/online_status_muted_'+ignored_img_suffix+'10x10.gif';
          if (muted_until==0) {
            status_title=getLng('permanently_globalmuted')+(ignored_img_suffix!=''? (' + '+getLng('ignored')) : '');
          } else {
            status_title=getLng('globalmuted_until').split('[EXPIRATION_DATE]').join(date(dateFormat, muted_until))+(ignored_img_suffix!=''? (' + '+getLng('ignored')) : '');
          }
        } else {
          status_img='./pic/online_status_'+members[i].OnlineStatus+'_'+ignored_img_suffix+'10x10.gif';
          status_title=members[i].OnlineStatusMessage+(ignored_img_suffix!=''? (' + '+getLng('ignored')) : '');
        }
      }
      status_title=htmlspecialchars(status_title);

      urec=urec_tpl;
      urec=urec.split('[ID]').join(i);
      urec=urec.split('[ONLINE_STATUS_ICON]').join('<img id="user_status_image_'+i+'" src="'+status_img+'" alt="'+status_title+'" title="'+status_title+'" />');
      // Gender
      if (userlistGender) {
        urec=urec.split('[GENDER_ICON]').join('<img src="./pic/gender_'+members[i].Gender+'_10x10.gif" alt="'+htmlspecialchars(getLng('gender')+': '+getLng('gender_'+members[i].Gender))+'" title="'+htmlspecialchars(getLng('gender')+': '+getLng('gender_'+members[i].Gender))+'" border="0" />');
      } else {
        urec=urec.split('[GENDER_ICON]').join('');
      }
      // Avatar
      if (userlistAvatar) {
        if (members[i].AvatarBID>0) {
          urec=urec.split('[AVATAR_THUMB]').join('<img style="cursor:pointer" onclick="showUserProfile('+i+'); return false;" src="'+htmlspecialchars(formlink)+'?b_x='+htmlspecialchars(userlistAvatarHeight)+'&amp;b_y='+htmlspecialchars(userlistAvatarWidth)+'&amp;b_id='+htmlspecialchars(members[i].AvatarBID)+'&amp;s_id='+htmlspecialchars(s_id)+'" onmouseover="showUserlistAvatarThumb(this, '+htmlspecialchars(members[i].AvatarBID)+')" onmouseout="hideUserlistAvatarThumb()" onclick="hideUserlistAvatarThumb()" alt="'+htmlspecialchars(getLng('avatar'))+'" title="'+htmlspecialchars(getLng('avatar'))+'" border="0" />');
        } else {
          urec=urec.split('[AVATAR_THUMB]').join('<img src="./pic/clearpixel_1x1.gif" width="'+htmlspecialchars(userlistAvatarWidth)+'" height="'+htmlspecialchars(userlistAvatarHeight)+'" alt="" title="" border="0" />');
        }
      } else {
        urec=urec.split('[AVATAR_THUMB]').join('');
      }
      // Nickname
      urec=urec.split('[NICKNAME_PLAIN]').join('"'+coloredToPlain(members[i].Nickname, true)+'"');
      urec=urec.split('[NICKNAME_COLORED]').join(coloredToHTML(members[i].Nickname)+(members[i].IsGuest? htmlspecialchars(' ('+getLng('guest')+')') : ''));
      // Admin
      if (userlistPrivileged && members[i].IsAdmin) {
        urec=urec.split('_admin_section').join(' onclick="alert(getLng(\'user_is_admin\').split(\'[USER]\').join(\''+htmlspecialchars(coloredToPlain(members[i].Nickname, false))+'\')); return false;" ');
      } else {
        urec=urec.split('_admin_section').join(' style="display:none" ');
      }
      // Moderator
      if (userlistPrivileged && members[i].IsModerator) {
        urec=urec.split('_moderator_section').join(' onclick="alert(getLng(\'user_is_moderator\').split(\'[USER]\').join(\''+htmlspecialchars(coloredToPlain(members[i].Nickname, false))+'\')); return false;" ');
      } else {
        urec=urec.split('_moderator_section').join(' style="display:none" ');
      }
      // "Edit" button (admins only)
      if (isAdmin && !SlaveMode) {
        urec='<img src="./pic/edit_13x13.gif" alt="'+htmlspecialchars(getLng('edit_profile'))+'" title="'+htmlspecialchars(getLng('edit_profile'))+'" style="cursor:pointer" onclick="openEditProfileWindow('+htmlspecialchars(i)+'); return false;" />'
            +'&nbsp;'
            +'<img src="./pic/delete_13x13.gif" alt="'+htmlspecialchars(getLng('delete_user'))+'" title="'+htmlspecialchars(getLng('delete_user'))+'" style="cursor:pointer" onclick="deleteUser('+htmlspecialchars(i)+'); return false;" />'
            +urec;
      }
      td.innerHTML=urec;
      setCssClass(td, 'tbl_row');

      if ($('banned_only') && $('banned_only').checked) {
        // Banned by
        td=tr.insertCell(-1);
        if (members[i].BannedBy>0) {
          td.innerHTML='<a href=":" title="'+coloredToPlain(members[i].BannedByUsername, true)+'" onclick="showUserOptionsBox('+htmlspecialchars(members[i].BannedBy)+'); return false;" oncontextmenu="showUserOptionsBox('+htmlspecialchars(members[i].BannedBy)+'); return false;">'
                       +coloredToHTML(members[i].BannedByUsername)
                       +'</a>';
        } else {
          td.innerHTML=htmlspecialchars('- '+getLng('server')+' -');
        }
        setCssClass(td, '.tbl_row');
        td.style.textAlign='center';
        // Ban expiration date
        td=tr.insertCell(-1);
        td.innerHTML=htmlspecialchars(members[i].BannedUntil>0? date(dateFormat, members[i].BannedUntil) : getLng('permanently'));
        setCssClass(td, '.tbl_row');
        td.style.textAlign='center';
        // Ban reason
        td=tr.insertCell(-1);
        td.innerHTML=nl2br(htmlspecialchars(members[i].BanReason!=''? members[i].BanReason : '-'));
        setCssClass(td, '.tbl_row');
      } else if ($('muted_only') && $('muted_only').checked) {
        // Muted by
        td=tr.insertCell(-1);
        if (members[i].GlobalMutedBy>0) {
          td.innerHTML='<a href=":" title="'+coloredToPlain(members[i].GlobalMutedByUsername, true)+'" onclick="showUserOptionsBox('+htmlspecialchars(members[i].GlobalMutedBy)+'); return false;" oncontextmenu="showUserOptionsBox('+htmlspecialchars(members[i].GlobalMutedBy)+'); return false;">'
                       +coloredToHTML(members[i].GlobalMutedByUsername)
                       +'</a>';
        } else {
          td.innerHTML=htmlspecialchars('- '+getLng('server')+' -');
        }
        setCssClass(td, '.tbl_row');
        td.style.textAlign='center';
        // Mute expiration date
        td=tr.insertCell(-1);
        td.innerHTML=htmlspecialchars(members[i].GlobalMutedUntil>0? date(dateFormat, members[i].GlobalMuted) : getLng('permanently'));
        setCssClass(td, '.tbl_row');
        td.style.textAlign='center';
        // Mute reason
        td=tr.insertCell(-1);
        td.innerHTML=nl2br(htmlspecialchars(members[i].GlobalMutedReason!=''? members[i].GlobalMutedReason : '-'));
        setCssClass(td, '.tbl_row');
      } else if ($('moderators_only') && $('moderators_only').checked) {
        // Moderated categories
        td=tr.insertCell(-1);
        td.innerHTML=nl2br(htmlspecialchars(ModeratedCategories[i]? ModeratedCategories[i].join("\n") : '-'));
        setCssClass(td, '.tbl_row');
        td.style.textAlign='center';
        // Moderated rooms
        td=tr.insertCell(-1);
        td.innerHTML=nl2br(htmlspecialchars(ModeratedRooms[i]? ModeratedRooms[i].join("\n") : '-'));
        setCssClass(td, '.tbl_row');
        td.style.textAlign='center';
        // "Edit moderator" button
        td=tr.insertCell(-1);
        td.innerHTML=SlaveMode? '&nbsp;' : '<button type="button" onclick="openEditModeratorWindow('+htmlspecialchars(i)+'); return false;" title="'+htmlspecialchars(getLng('edit_moderator'))+'">'+htmlspecialchars(getLng('edit_moderator'))+'</button>';
        setCssClass(td, '.tbl_row');
        td.style.textAlign='center';
      } else if ($('not_activated_only') && $('not_activated_only').checked) {
        td=tr.insertCell(-1);
        td.innerHTML='&nbsp;';
        setCssClass(td, '.tbl_row');
        td.style.textAlign='center';
        td.colSpan=2;
      } else {
        // Registration date
        td=tr.insertCell(-1);
        td.innerHTML=htmlspecialchars(date(dateFormat, members[i].Joined));
        setCssClass(td, '.tbl_row');
        td.style.textAlign='center';
        // Last visit date
        td=tr.insertCell(-1);
        td.innerHTML=htmlspecialchars(members[i].LastVisit>0? date(dateFormat, members[i].LastVisit) : getLng('never'));
        setCssClass(td, '.tbl_row');
        td.style.textAlign='center';
        // Time spent online
        td=tr.insertCell(-1);
        if (online_days>0) {
          td.innerHTML=htmlspecialchars( online_days+' '+getLng('days')+', '
                                        +online_hours+' '+getLng('hours')+', '
                                        +online_minutes+' '+getLng('minutes')+', '
                                        +online_seconds+' '+getLng('seconds')
                                        );
        } else if (online_hours>0) {
          td.innerHTML=htmlspecialchars( online_hours+' '+getLng('hours')+', '
                                        +online_minutes+' '+getLng('minutes')+', '
                                        +online_seconds+' '+getLng('seconds')
                                        );
        } else if (online_minutes>0) {
          td.innerHTML=htmlspecialchars( online_minutes+' '+getLng('minutes')+', '
                                        +online_seconds+' '+getLng('seconds')
                                        );
        } else {
          td.innerHTML=htmlspecialchars(online_seconds+' '+getLng('seconds'));
        }
        setCssClass(td, '.tbl_row');
        td.style.textAlign='center';
      }

    }
  }
  toggleProgressBar(false);
}


/**
 * Delete user
 * @param   boolean   confirmed     First confirmation
 * @param   boolean   confirmed2    Second confirmation
 */
function deleteUser(user_id, confirmed, confirmed2) {
  if (isAdmin && !SlaveMode && typeof(user_id)=='number' && user_id>0 && currentUserId!=user_id) {
    if (typeof(confirmed)!='boolean' || !confirmed) {
      confirm(getLng('sure_delete_user'), null, null, 'deleteUser('+user_id+', true)');
    } else if (typeof(confirmed2)!='boolean' || !confirmed2) {
      confirm(getLng('really_sure'), null, null, 'deleteUser('+user_id+', true, true)');
    } else {
      toggleProgressBar(true);
      sendData('_CALLBACK_deleteUser()',
               formlink,
               'POST',
               'ajax=delete_user'
               +'&s_id='+urlencode(s_id)
               +'&profile_user_id='+urlencode(user_id)
               );
    }
  }
}
function _CALLBACK_deleteUser() {
  toggleProgressBar(false);
  switch (actionHandler.status) {
    case  -1:
      // Session is invalid
      document.location.href=formlink+'?session_timeout';
      return false;
    break;
    case  0:
      // User deleted
      alert(actionHandler.message, null, null, "$('memberlist_search_button').click()");
    break;
    default:
      alert(actionHandler.message);
    break;
  }
}
