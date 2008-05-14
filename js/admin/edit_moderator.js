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
 * Nicknames returned by last search, keyed by user ID
 * @var object
 */
var Users=new Array();

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
 * Categories as an Array with first element is tree structure
 * and other elements as references to categories in tree structure.
 * @var object
 */
var CategoryTree=new Array();

/**
 * Flag: if TRUE, then "Edit moderator" form will be displayed
 * automatically. if only one user will be found
 * @var boolean
 */
var autoShowSingleUserForm=true;


/**
 * Init window
 */
function initEditModeratorWindow() {
  showUserSearchForm();
}


/**
 * Get chat rooms list grouped in categories and sorted by name
 */
function getRoomStructure() {
  CategoryTree=new Array();
  $('categories_and_rooms').innerHTML='&nbsp;';
  sendData('_CALLBACK_getRoomStructure()', formlink, 'POST', 'ajax=get_room_structure&s_id='+urlencode(s_id), true);
}
function _CALLBACK_getRoomStructure() {
//debug(actionHandler.getResponseString()); return false;
  switch (actionHandler.status) {
    case  -1:
      // Session is invalid
      document.location.href=formlink+'?session_timeout';
      return false;
    break;
    case 0:
      // Success
      // Make category tree
      makeCategoryTree(actionHandler.data['category']);
      // Display category tree
      displayCategories();
    break;
  }
  toggleProgressBar(false);
}

/**
 * Create category tree from Categories XML object
 * @param   object    cats        Categories as XML object
 * @return  array
 */
function makeCategoryTree(cats) {
  var cat_id=0;
  var cat_parent_id=0;
  var room=null;
  var room_nr=0;
  var room_id=0;
  var user_nr=0;
  var user=null;
  var user_id=0;
  if (cats) {
    for (var cat_nr=0; cat_nr<cats.length; cat_nr++) {
      cat_id=stringToNumber(cats[cat_nr]['id'][0]);
      cat_parent_id=stringToNumber(cats[cat_nr]['parent_id'][0]);
      // Add category to the global array
      CategoryTree[cat_id]=new Array();
      CategoryTree[cat_id]['id']=cat_id;
      CategoryTree[cat_id]['parent_id']=cat_parent_id;
      CategoryTree[cat_id]['name']=cats[cat_nr]['name'][0];
      CategoryTree[cat_id]['description']=cats[cat_nr]['description'][0];
      CategoryTree[cat_id]['creatable_rooms']=cats[cat_nr]['creatable_rooms'][0]=='1';
      CategoryTree[cat_id]['children']=new Array();
      CategoryTree[cat_id]['child_ids']=new Array();
      CategoryTree[cat_id]['rooms']=new Array();
      CategoryTree[cat_id]['rooms_local']=0;
      CategoryTree[cat_id]['rooms_total']=0;
      CategoryTree[cat_id]['users_total']=0;
      if (cat_parent_id!=-1) {
        // Make a reference
        CategoryTree[cat_parent_id]['children'][cat_id]=CategoryTree[cat_id];
      }
      // Get child categories
      if (cats[cat_nr]['category'].length) {
        makeCategoryTree(cats[cat_nr]['category']);
      }
      // Get rooms
      for (room_nr=0; room_nr<cats[cat_nr]['room'].length; room_nr++) {
        room=cats[cat_nr]['room'][room_nr];
        room_id=stringToNumber(room['id'][0]);
        CategoryTree[cat_id]['rooms'][room_id]=new Array();
        CategoryTree[cat_id]['rooms'][room_id]['id']=room_id;
        CategoryTree[cat_id]['rooms'][room_id]['password_protected']='0'!=room['password_protected'][0];
        CategoryTree[cat_id]['rooms'][room_id]['name']=room['name'][0];
        CategoryTree[cat_id]['rooms'][room_id]['description']=room['description'][0];
        CategoryTree[cat_id]['rooms'][room_id]['moderated_by_me']='1'==room['moderated_by_me'][0];
        CategoryTree[cat_id]['rooms'][room_id]['users']=new Array();
        CategoryTree[cat_id]['rooms'][room_id]['users_total']=0;
        CategoryTree[cat_id]['rooms_local']++;
        CategoryTree[cat_id]['rooms_total']++;
      }
      // Save child categories' IDs and rooms/users counters
      for (var i in CategoryTree[cat_id]['children']) {
        CategoryTree[cat_id]['child_ids'][CategoryTree[cat_id]['child_ids'].length]=i;
        CategoryTree[cat_id]['rooms_total']+=CategoryTree[cat_id]['children'][i]['rooms_total'];
        CategoryTree[cat_id]['users_total']+=CategoryTree[cat_id]['children'][i]['users_total'];
        if (CategoryTree[cat_id]['parent_id']!=-1) {
          CategoryTree[CategoryTree[cat_id]['parent_id']]['child_ids'][CategoryTree[CategoryTree[cat_id]['parent_id']]['child_ids'].length]=i;
          CategoryTree[cat_id]['parent_id']['rooms_total']+=CategoryTree[cat_id]['rooms_total'];
          CategoryTree[cat_id]['parent_id']['users_total']+=CategoryTree[cat_id]['users_total'];
        }
      }
    }
  }
}

/**
 * Create simplified category tree HTML
 * @param   array     cats          Array with category tree
 * @return  string
 */
function makeSimpleCategoryTreeHtml(cats) {
  var html='';
  var user_id=stringToNumber($('moderator_user_id').value);
  var cat_chkbox=false;
  var room_chkbox=false;
  if (cats.length) {
    for (var i in cats) {
      // Categories
      cat_chkbox=-1!=(','+ModeratedCategories[user_id]+',').indexOf(','+i+',');
      html+='<label for="category_selector_'+htmlspecialchars(i)+'">'
          + '<img src="./pic/clearpixel_1x1.gif" border="0" width="3" height="1" />'
          + '<input type="checkbox" '+(cat_chkbox? 'checked="checked"' : '')+' onclick="categorySelector('+htmlspecialchars(i)+', this.checked);" id="category_selector_'+htmlspecialchars(i)+'" />'
          + '<img src="./pic/clearpixel_1x1.gif" border="0" width="3" height="1" />'
          + '<span title="'+htmlspecialchars(getLng('chat_category')+': '+cats[i]['name']+"\n"+cats[i]['description'])+'" class="div_selection_scrollable_link">'
          + '<img src="./pic/clearpixel_1x1.gif" border="0" width="5" height="12" />'
          + '<b>'+htmlspecialchars(cats[i]['name'])+'</b>'
          + '</span>'
          + '</label>'
          + '<br />';
      // Rooms
      if (cats[i]['rooms'].length) {
        for (var ii in cats[i]['rooms']) {
          room_chkbox=cat_chkbox || (-1!=(','+ModeratedRooms[user_id]+',').indexOf(','+ii+','));
          room_title=getLng('chat_room')+': '+cats[i]['rooms'][ii]['name']+"\n"+cats[i]['rooms'][ii]['description'];
          html+='<label for="room_selector_'+htmlspecialchars(ii)+'">'
              + '<span title="'+htmlspecialchars(room_title)+'">'
              + '<img src="./pic/clearpixel_1x1.gif" border="0" width="25" height="12" />'
              + '<input type="checkbox" '+(room_chkbox? 'checked="checked"' : '')+' onclick="roomSelector('+htmlspecialchars(i)+');" id="room_selector_'+htmlspecialchars(ii)+'" />'
              + '<img src="./pic/clearpixel_1x1.gif" border="0" width="5" height="12" />'
              + '<span class="div_selection_scrollable_inactive">'
              + htmlspecialchars(cats[i]['rooms'][ii]['name'])
              + '</span>'
              + '</span>'
              + '</label>'
              + '<br />';
        }
      } else {
        html+='<img src="./pic/clearpixel_1x1.gif" border="0" width="30" height="12" />'
            + getLng('category_has_no_rooms')
            + '<br />';
      }
      if (cats[i]['children'].length) {
        html+=makeSimpleCategoryTreeHtml(cats[i]['children']);
      }
    }
  }
  return html;
}

/**
 * This function is called on each category checkbox click
 * @param     int       category_id       Category ID
 * @param     boolean   selector_active   TRUE, if checkbox is checked
 */
function categorySelector(category_id, selector_active) {
  if (selector_active) {
    for (var i in CategoryTree[category_id]['rooms']) {
      $('room_selector_'+i).checked=true;
    }
  }
}


/**
 * This function is called on each room checkbox click
 * @param     int       category_id       Parent category ID
 */
function roomSelector(category_id) {
  $('category_selector_'+category_id).checked=false;
}


/**
 * Display "Search for user" form
 */
function showUserSearchForm() {
  $('search_user_row').style.display='';
  $('user_name_row').style.display='none';
  $('categories_rooms_row').style.display='none';
  $('nickname_search').focus();
  $('nickname_search').select();
}


/**
 * Hide "Search for user" form
 */
function hideUserSearchForm() {
  $('search_user_row').style.display='none';
  $('user_name_row').style.display='';
  $('categories_rooms_row').style.display='';
}


/**
 * Search for user
 */
function moderatorSearchUser() {
  autoShowSingleUserForm=true;
  Users=new Array();
  $('nickname_search').value=trimString($('nickname_search').value);
  sendData('_CALLBACK_getMemberlist()', formlink, 'POST', 'ajax=get_memberlist'
                                                         +'&s_id='+urlencode(s_id)
                                                         +'&sort_by=1'
                                                         +'&sort_dir=0'
                                                         +'&nickname='+urlencode($('nickname_search').value)
                                                         );
}
function _CALLBACK_getMemberlist() {
//debug(actionHandler.getResponseString()); return false;
  var members=null;
  var member_nr=0;
  var user_id=0;
  var nickname='';
  var users_count=0;

  if (actionHandler.status==-1) {
    // Session is invalid
    window.close();
    opener.document.location.href=formlink+'?session_timeout&ts='+unixTimeStamp();
    return false;
  } else {
    if (actionHandler.status==0 && typeof(actionHandler.data['member'])!='undefined' && actionHandler.data['member'].length) {
      for (member_nr=0; member_nr<actionHandler.data['member'].length; member_nr++) {
        member=actionHandler.data['member'][member_nr];
        users_count++;
        user_id=stringToNumber(member['id'][0]);
        nickname=member['nickname'][0];
        Users[user_id]=nickname;
        ModeratedCategories[user_id]=member['moderated_categories'][0];
        ModeratedRooms[user_id]=member['moderated_rooms'][0];
      }
    }
  }
  // Display users
  if (users_count==0) {
    // No members found
    $('search_results').style.display='none';
    alert(getLng('no_members_found'));
  } else {
    showSearchResults();
  }
  toggleProgressBar(false);
  showUserSearchForm();
}


/**
 * Display search results
 */
function showSearchResults() {
  var users_count=0;
  var col_length=0;
  var col_length_max=0;
  var col_nr=0;
  var col_html='';
  var cols_html=new Array();
  var last_user_id=0;
  for (var i in Users) {
    users_count++;
  }

  if (Users.length>0) {
    $('search_results').style.display='';
    col_length_max=Math.ceil(users_count/3);
    col_html='';
    col_length=0;
    for (var i in Users) {
      col_length++;
      col_html+='<a href=":" onclick="showModeratorForm('+htmlspecialchars(i)+'); return false;" title="'+htmlspecialchars(getLng('edit_moderator')+': '+coloredToPlain(Users[i], false))+'">'
              + coloredToHTML(Users[i])
              + '</a>'
              + '<br />';
      if (col_length==col_length_max) {
        cols_html[col_nr]=col_html;
        col_html='';
        col_nr++;
        col_length=0;
      }
      last_user_id=i;
    }
    if (col_html!='') {
      cols_html[col_nr]=col_html;
    }
    for (var i=0; i<3; i++) {
      if (typeof(cols_html[i])=='string') {
        $('search_results_col_'+i).innerHTML=cols_html[i];
      } else {
        $('search_results_col_'+i).innerHTML='&nbsp;';
      }
    }
  }
  if (users_count==1 && autoShowSingleUserForm) {
    setTimeout("showModeratorForm("+last_user_id+");", 300);
    autoShowSingleUserForm=false;
  }
}


/**
 * Hide search results
 */
function hideSearchResults() {
  $('search_results').style.display='none';
}


/**
 * Display categories and rooms
 */
function displayCategories() {
  $('categories_rooms_row').style.display='';
  $('categories_and_rooms').innerHTML=makeSimpleCategoryTreeHtml(CategoryTree[0]['children']);
}


/**
 * Hide categories and rooms
 */
function hideCategories() {
  $('categories_rooms_row').style.display='none';
}


/**
 * Display moderator form
 * @param     int     user_id     User ID
 */
function showModeratorForm(user_id) {
  hideUserSearchForm();
  hideSearchResults();
  $('user_name_row').style.display='';
  $('moderator_user_id').value=user_id;
  $('moderator_user_nick').innerHTML=coloredToHTML(Users[user_id]);
  // Get room structure
  getRoomStructure();
}


/**
 * Hide moderator form
 * @param     int     user_id     User ID
 */
function hideModeratorForm() {
  $('user_name_row').style.display='none';
  hideCategories();
  showUserSearchForm();
  showSearchResults();
}


/**
 * Save new moderator data
 */
function saveModerator(cats, cat_ids, room_ids, level) {
  if (typeof(cats)=='undefined') {
    var cats=CategoryTree[0]['children'];
  }
  if (typeof(cat_ids)=='undefined') {
    var cat_ids=new Array();
  }
  if (typeof(room_ids)=='undefined') {
    var room_ids=new Array();
  }
  if (typeof(level)=='undefined') {
    var level=0;
  }
  for (var i in cats) {
    if ($('category_selector_'+i).checked) {
      // Category is selected
      cat_ids.push(i);
    } else {
      // Check rooms
      for (var ii in cats[i]['rooms']) {
        if ($('room_selector_'+ii).checked) {
          // Room is selected
          room_ids.push(ii);
        }
      }
    }
  }
  sendData('_CALLBACK_saveModerator()', formlink, 'POST', 'ajax=update_moderator'
                                                         +'&s_id='+urlencode(s_id)
                                                         +'&moderator_user_id='+urlencode($('moderator_user_id').value)
                                                         +'&categories='+urlencode(cat_ids.join(','))
                                                         +'&rooms='+urlencode(room_ids.join(',')),
                                                         true);
}
function _CALLBACK_saveModerator() {
//debug(actionHandler.getResponseString()); return false;
  if (actionHandler.status==-1) {
    // Session is invalid
    document.location.href=formlink+'?session_timeout';
    return false;
  }
  alert(actionHandler.message);
  moderatorSearchUser();
}