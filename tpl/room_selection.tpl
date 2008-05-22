<PCPIN:TPL name="main">
<table id="profile_header_tbl" class="tbl" border="0" cellspacing="0" cellpadding="0" width="100%">
  <tr>
    <td class="tbl_header_main">
      {WELCOME_MESSAGE}
      ( <a class="tbl_header_main_link" href="#" onclick="logOut(); return false;" title="{LNG_LOG_OUT}">{LNG_LOG_OUT}</a> )
    </td>
    <PCPIN:TPL name="last_login" type="simplecondition" requiredvars="LAST_LOGIN">
      <td  class="tbl_header_main" style="text-align:right">
        <b>{LNG_YOUR_LAST_VISIT}:</b> {LAST_LOGIN}
      </td>
    </PCPIN:TPL>
  </tr>
</table>

<table id="room_selection_tbl" class="tbl" border="0" cellspacing="1" cellpadding="0" width="100%" style="margin-top:20px;margin-bottom:20px">
  <tr>
    <td colspan="2" class="tbl_header_main">
      {LNG_ROOM_SELECTION}
      <span id="simplified_view_link" style="display:none">
        ( <a class="tbl_header_main_link" href=":" title="{LNG_ADVANCED_VIEW}" onclick="setRoomSelectionDisplayType('s'); return false;">{LNG_ADVANCED_VIEW}</a> )
      </span>
      <span id="advanced_view_link" style="display:none">
        ( <a class="tbl_header_main_link" href=":" title="{LNG_SIMPLIFIED_VIEW}" onclick="setRoomSelectionDisplayType('a'); return false;">{LNG_SIMPLIFIED_VIEW}</a> )
      </span>
    </td>
  </tr>
  <tr>
    <td class="tbl_row" style="text-align:center">
      <table id="join_room_tbl" border="0" cellspacing="0" cellpadding="0" width="100%">
        <tr id="rooms_tree" style="display:none">
          <td class="tbl_row" width="35%">
            <b>{LNG_SELECT_CATEGORY}:</b>
            <br />
            <div id="chat_categories_list" class="div_selection_scrollable" title="{LNG_CHAT_CATEGORIES}"><br /></div>
          </td>
          <td class="tbl_row">
            <b>{LNG_SELECT_ROOM}:</b>
            <br />
            <div id="chat_rooms_list" class="div_selection_scrollable" title="{LNG_CHAT_ROOMS}"><br /></div>
          </td>
        </tr>
        <tr id="rooms_simplified" style="display:none" valign="top">
          <td class="tbl_row" colspan="2">
            <b>{LNG_SELECT_ROOM}:</b>
            <br />
            <div id="chat_rooms_list_simplified" class="div_selection_scrollable" title="{LNG_CHAT_ROOMS}"><br /></div>
          </td>
        </tr>
        <tr>
          <td colspan="2" class="tbl_row" nowrap="nowrap" style="text-align:left">
            <button type="button" id="enterChatRoom_btn" onclick="enterChatRoom(); return false;">{LNG_ENTER}</button>
            <span id="stealth_mode_chkbox_row" style="display:none">
              &nbsp;
              <label for="stealth_mode_chkbox" title="{LNG_STEALTH_MODE}">
                <input id="stealth_mode_chkbox" type="checkbox" /> {LNG_STEALTH_MODE}
              </label>
            </span>
          </td>
        </tr>
      </table>
    </td>
  </tr>
</table>
</PCPIN:TPL>