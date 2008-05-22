<PCPIN:TPL name="main">
<!-- TOP BANNER AREA -->
<iframe id="chatroom_top_banner" name="chatroom_top_banner" src="dummy.html" scrolling="No" frameborder="0"></iframe>

<!-- BOTTOM BANNER AREA -->
<iframe id="chatroom_bottom_banner" name="chatroom_bottom_banner" src="dummy.html" scrolling="No" frameborder="0"></iframe>

<!-- USERLIST AREA -->
<div id="chatroom_userlist">
  <div id="chatroom_userlist_contents">
    {LNG_CHAT_ROOM} &quot;<span id="chatroom_userlist_room_name"></span>&quot;
    <br />
    <PCPIN:TPL name="room_selection" type="simplecondition" requiredvars="DISPLAY">
      <table border="0" cellspacing="0" cellpadding="2">
        <tr valign="middle">
          <td align="left">
            <select id="chatroom_userlist_room_selection"></select>
          </td>
          <td style="padding:0px; vertical-align:middle; text-align:left;">
            <button style="width:23px;background-image:url(./pic/arrow_left_11x9.gif);background-repeat:no-repeat;background-position:center center;" onclick="switchChatRoom($('chatroom_userlist_room_selection').value, $('chatroom_userlist_room_selection').options[$('chatroom_userlist_room_selection').selectedIndex].password_protect)" alt="{LNG_ENTER_THIS_CHAT_ROOM}" title="{LNG_ENTER_THIS_CHAT_ROOM}">&nbsp;</button>
          </td>
        </tr>
      </table>
    </PCPIN:TPL>
    <div id="chatroom_userlist_list">
      <table cellspacing="0" cellpadding="0" border="0">
        <tbody id="userlist_table_body"><!-- USERLIST GOES HERE --></tbody>
      </table>
    </div>
  </div>
</div>

<!-- CONTROLS AREA -->
<div id="chatroom_controls">
  <div id="chatroom_controls_contents">
    <table border="0" cellspacing="0" cellpadding="0" width="100%">
      <tr>
        <td align="left" colspan="2">
          <table border="0" cellspacing="0" cellpadding="0" width="100%">
            <tr>
              <td><button type="button" id="msg_bold_btn" style="width:23px;font-weight:900;" title="{LNG_BOLD}" onclick="invertCssProperty('main_input_textarea', 'font-weight', '700/500', true, this.id, '{LNG_BOLD_SHORT}*/{LNG_BOLD_SHORT}')" onfocus="blur()">{LNG_BOLD_SHORT}</button></td>
              <td style="width:1px"><img src="./pic/clearpixel_1x1.gif" alt="" width="1" height="1" /></td>
              <td><button type="button" id="msg_italic_btn" style="width:23px;font-style:italic;" title="{LNG_ITALIC}" onclick="invertCssProperty('main_input_textarea', 'font-style', 'italic/normal', true, this.id, '{LNG_ITALIC_SHORT}*/{LNG_ITALIC_SHORT}')" onfocus="blur()">{LNG_ITALIC_SHORT}</button></td>
              <td style="width:1px"><img src="./pic/clearpixel_1x1.gif" alt="" width="1" height="1" /></td>
              <td><button type="button" id="msg_underlined_btn" style="width:23px;text-decoration:underline;" title="{LNG_UNDERLINED}" onclick="invertCssProperty('main_input_textarea', 'text-decoration', 'underline/none', true, this.id, '{LNG_UNDERLINED_SHORT}*/{LNG_UNDERLINED_SHORT}')" onfocus="blur()">{LNG_UNDERLINED_SHORT}</button></td>
              <PCPIN:TPL name="fonts" type="simplecondition" requiredvars="FONTS">
                <td style="width:1px"><img src="./pic/clearpixel_1x1.gif" alt="" width="5" height="1" /></td>
                <td>
                  <span id="available_fonts_list" style="display:none">{FONTS}</span>
                  <select id="message_font_select" title="{LNG_FONT_FAMILY}"></select>
                </td>
                <td style="width:1px"><img src="./pic/clearpixel_1x1.gif" alt="" width="1" height="1" /></td>
                <td id="message_fontsize_select_col" style="display:none">
                  <span id="available_font_sizes_list" style="display:none">{FONT_SIZES}</span>
                  <select id="message_fontsize_select" title="{LNG_FONT_SIZE}"></select>
                </td>
              </PCPIN:TPL>
              <td style="width:1px"><img src="./pic/clearpixel_1x1.gif" alt="" width="5" height="1" /></td>
              <td><button id="message_colors_btn" type="button" style="width:32px;background-image:none;" onclick="openColorBox('main_input_textarea', 'color', this, 'outgoingMessageColor', false, 'background-color')" title="{LNG_MESSAGE_COLOR}" onfocus="blur()"></button></td>
              <td style="width:1px"><img src="./pic/clearpixel_1x1.gif" alt="" width="5" height="1" /></td>
              <td><button type="button" id="invert_timestamp_btn" style="width:23px;background-repeat:no-repeat;background-position:center center;" onclick="invertTimeStampView()" onfocus="blur()"></button></td>
              <PCPIN:TPL name="invert_sounds_btn" type="simplecondition" requiredvars="DISPLAY">
                <td style="width:1px"><img src="./pic/clearpixel_1x1.gif" alt="" width="5" height="1" /></td>
                <td><button id="invert_sounds_btn" type="button" style="width:23px;background-image:url(./pic/sounds_active_15x15.gif);background-repeat:no-repeat;background-position:center center;" onclick="toggleSounds()" title="{LNG_SOUNDS}" onfocus="blur()"></button></td>
              </PCPIN:TPL>
              <PCPIN:TPL name="msg_attachment_btn" type="simplecondition" requiredvars="DISPLAY">
                <td style="width:1px"><img src="./pic/clearpixel_1x1.gif" alt="" width="5" height="1" /></td>
                <td><button id="msg_attachment_btn" type="button" style="width:23px;background-image:url(./pic/attachment_15x15.gif);background-repeat:no-repeat;background-position:center center;" onclick="addMsgAttachment()" title="{LNG_ATTACH_FILE}" onfocus="blur()"></button></td>
              </PCPIN:TPL>
              <td style="width:1px"><img src="./pic/clearpixel_1x1.gif" alt="" width="5" height="1" /></td>
              <td><button id="smilies_btn" type="button" style="width:23px;background-image:url(./pic/smilie_15x15.gif);background-repeat:no-repeat;background-position:center center;" onclick="openSmilieBox('main_input_textarea', null, this, false)" title="{LNG_SMILIES}" onfocus="blur()"></button></td>

              <td width="100%"><img src="./pic/clearpixel_1x1.gif" width="1" height="1" alt="" /></td>

              <td><button style="width:23px;background-image:url(./pic/members_15x15.gif);background-repeat:no-repeat;background-position:center center;" onclick="openMemberlistWindow()" title="{LNG_MEMBERLIST}" onfocus="blur()"></button></td>
              <PCPIN:TPL name="your_profile_button" type="simplecondition" requiredvars="DISPLAY">
                <td style="width:1px"><img src="./pic/clearpixel_1x1.gif" alt="" width="5" height="1" /></td>
                <td><button id="your_profile_button" style="width:23px;background-image:url({FORMLINK}?b_x=15&b_y=15&b_id={AVATAR_BID}&s_id={S_ID});background-repeat:no-repeat;background-position:center center;" onclick="openEditProfileWindow(currentUserId, 'own_profile')" title="{LNG_YOUR_PROFILE}" onfocus="blur()"></button></td>
              </PCPIN:TPL>
              <PCPIN:TPL name="admin_btn" type="simplecondition" requiredvars="DISPLAY">
                <td style="width:1px"><img src="./pic/clearpixel_1x1.gif" alt="" width="5" height="1" /></td>
                <td><button style="width:23px;background-image:url(./pic/admin_18x18.gif);background-repeat:no-repeat;background-position:center center;" onclick="openAdminWindow()" title="{LNG_ADMINISTRATION_AREA}" onfocus="blur()"></button></td>
              </PCPIN:TPL>
              <td style="width:1px"><img src="./pic/clearpixel_1x1.gif" alt="" width="5" height="1" /></td>
              <td><button id="online_status_pulldown" style="width:23px;background-repeat:no-repeat;background-position:center center;" onclick="openOnlineStatusBox(this)" onfocus="blur()"></button></td>
              <td style="width:1px"><img src="./pic/clearpixel_1x1.gif" alt="" width="5" height="1" /></td>
              <td><button type="button" style="width:23px;background-image:url(./pic/help_18x18.gif);background-repeat:no-repeat;background-position:center center;" onclick="showHelpBox(this)" title="{LNG_HELP}" onfocus="blur()"></button></td>
              <td style="width:1px"><img src="./pic/clearpixel_1x1.gif" alt="" width="5" height="1" /></td>
              <td><button type="button" style="width:23px;background-image:url(./pic/close_red_18x18.gif);background-repeat:no-repeat;background-position:center center;"  onclick="openExitBox(this)" title="{LNG_LEAVE_THIS_ROOM}" onfocus="blur()"></button></td>
              <td style="width:1px"><img src="./pic/clearpixel_1x1.gif" alt="" width="5" height="1" /></td>
              <td><button type="button" id="scroll_ctl_btn" style="width:11px;background-image:url(./pic/scroll_active_5x18.gif);background-repeat:no-repeat;background-position:center center;" onclick="setAutoScroll()" title="" onfocus="blur()"></button></td>
            </tr>
          </table>
        </td>
      </tr>
      <tr>
        <td width="1%" style="vertical-align:middle;padding-top:2px;">
          <div id="attached_files" style="height:18px;overflow:auto;padding:0px;display:none;"></div>
          <textarea id="main_input_textarea" cols="1" rows="1" title="{LNG_TYPE_MESSAGE_HERE}" style="margin:0px" autocomplete="off"></textarea>
        </td>
        <td width="100%" style="vertical-align:bottom;text-align:left;padding-left:5px;padding-top:2px;padding-bottom:3px;">
          <button id="mainSendMessageButton" type="button" style="width:50px; height:50px;" title="{LNG_SEND}" onfocus="blur()" onclick="postChatMessage($('main_input_textarea'))">{LNG_SEND}</button>
        </td>
      </tr>
    </table>
  </div>
</div>

<!-- MESSAGES AREA -->
<div id="chatroom_messages" style="background-image:url({ROOM_BACKGROUND_IMAGE_URL})">
  <div id="chatroom_messages_contents" style="position:absolute;top:0px;left:0px;"></div>
</div>

<!-- ONLINE STATUS SELECTION BOX -->
<div id="online_status_selection_box" style="display:none">
  <table border="0" cellspacing="0" cellpadding="2" width="1px" class="context_menu_table">
    <tr>
      <td align="left" class="context_menu_table_header" colspan="3" nowrap="nowrap">{LNG_ONLINE_STATUS}</td>
    </tr>
    <tr title="{LNG_ONLINE_STATUS}: {LNG_ONLINE_STATUS_1}" class="context_menu_table_row" onmouseover="setCssClass(this, '.context_menu_table_hrow')" onmouseout="setCssClass(this, '.context_menu_table_row')" onclick="closeOnlineStatusBox(1, '{LNG_ONLINE_STATUS_1}')">
      <td width="1%"><img id="online_status_1_pointer" src="./pic/clearpixel_1x1.gif" alt="" /></td>
      <td width="1%"><img src="./pic/online_status_1_10x10.gif" alt="" /></td>
      <td nowrap="nowrap">&nbsp;{LNG_ONLINE_STATUS_1}&nbsp;</td>
    </tr>
    <tr title="{LNG_ONLINE_STATUS}: {LNG_ONLINE_STATUS_2}" class="context_menu_table_row" onmouseover="setCssClass(this, '.context_menu_table_hrow')" onmouseout="setCssClass(this, '.context_menu_table_row')" onclick="closeOnlineStatusBox(2, '{LNG_ONLINE_STATUS_2}')">
      <td width="1%"><img id="online_status_2_pointer" src="./pic/clearpixel_1x1.gif" alt="" title="" /></td>
      <td width="1%"><img src="./pic/online_status_2_10x10.gif" alt="" /></td>
      <td nowrap="nowrap">&nbsp;{LNG_ONLINE_STATUS_2}&nbsp;</td>
    </tr>
    <tr title="{LNG_ONLINE_STATUS}: {LNG_ONLINE_STATUS_3}" class="context_menu_table_row" onmouseover="setCssClass(this, '.context_menu_table_hrow')" onmouseout="setCssClass(this, '.context_menu_table_row')" onclick="closeOnlineStatusBox(3, '{LNG_ONLINE_STATUS_3}')">
      <td width="1%"><img id="online_status_3_pointer" src="./pic/clearpixel_1x1.gif" alt="" title="" /></td>
      <td width="1%"><img src="./pic/online_status_3_10x10.gif" alt="" /></td>
      <td nowrap="nowrap">&nbsp;{LNG_ONLINE_STATUS_3}&nbsp;</td>
    </tr>
  </table>
</div>

<!-- EXIT OPTIONS SELECTION BOX -->
<div id="exit_selection_box" style="display:none">
  <table border="0" cellspacing="0" cellpadding="2" width="1px" class="context_menu_table">
    <tr>
      <td align="left" class="context_menu_table_header" colspan="3" nowrap="nowrap">{LNG_LEAVE_THIS_ROOM}</td>
    </tr>
    <PCPIN:TPL name="leave_room_link" type="simplecondition" requiredvars="DISPLAY">
      <tr title="{LNG_LEAVE_THIS_ROOM}" class="context_menu_table_row" onmouseover="setCssClass(this, '.context_menu_table_hrow')" onmouseout="setCssClass(this, '.context_menu_table_row')" onclick="closeExitBox(-1)">
        <td nowrap="nowrap" colspan="3" align="center">&nbsp;{LNG_LEAVE_THIS_ROOM}&nbsp;</td>
      </tr>
    </PCPIN:TPL>
    <tr title="{LNG_LOG_OUT_OF_CHAT}" class="context_menu_table_row" onmouseover="setCssClass(this, '.context_menu_table_hrow')" onmouseout="setCssClass(this, '.context_menu_table_row')" onclick="closeExitBox(-2)">
      <td nowrap="nowrap" colspan="3" align="center">&nbsp;{LNG_LOG_OUT_OF_CHAT}&nbsp;</td>
    </tr>
  </table>
</div>

<!-- HELP BOX -->
<div id="help_box" style="display:none">
  <table border="0" cellspacing="0" cellpadding="2" width="1px" class="context_menu_table">
    <tr>
      <td align="left" class="context_menu_table_header" colspan="3" nowrap="nowrap">{LNG_HELP}</td>
    </tr>
    <tr title="{LNG_CHAT_COMMANDS}" class="context_menu_table_row" onmouseover="setCssClass(this, '.context_menu_table_hrow')" onmouseout="setCssClass(this, '.context_menu_table_row')" onclick="closeHelpBox(1)">
      <td width="1%"><img src="./pic/commands_20x14.png" alt="" /></td>
      <td nowrap="nowrap">&nbsp;{LNG_CHAT_COMMANDS}&nbsp;</td>
    </tr>
    <tr title="{LNG_CALL_MODERATOR}" class="context_menu_table_row" onmouseover="setCssClass(this, '.context_menu_table_hrow')" onmouseout="setCssClass(this, '.context_menu_table_row')" onclick="closeHelpBox(2)">
      <td width="1%"><img src="./pic/sos_20x14.png" alt="" /></td>
      <td nowrap="nowrap">&nbsp;{LNG_CALL_MODERATOR}&nbsp;</td>
    </tr>
    <tr class="context_menu_table_row">
      <td colspan="2" class="context_menu_table_separator_row"></td>
    </tr>
    <tr title="{LNG_ABOUT}" class="context_menu_table_row" onmouseover="setCssClass(this, '.context_menu_table_hrow')" onmouseout="setCssClass(this, '.context_menu_table_row')" onclick="closeHelpBox(0)">
      <td colspan="2" nowrap="nowrap">&nbsp;{LNG_ABOUT}...&nbsp;</td>
    </tr>
  </table>
</div>

<!-- HELP MESSAGES -->
<div id="cmd_help_records" style="display:none">
  <PCPIN:TPL name="cmd_help_records" type="simplecondition" requiredvars="CMD">
    <span id="help_record_cmd_{CMD}">{TEXT}</span>
  </PCPIN:TPL>
</div>

<!-- COMMAND EXECUTION ERROR MESSAGES -->
<div id="cmd_err_records" style="display:none">
  <PCPIN:TPL name="cmd_err_records" type="simplecondition" requiredvars="CMD">
    <span id="error_record_cmd_{CMD}">{TEXT}</span>
  </PCPIN:TPL>
</div>

<!-- POPUP BANNER AREA -->
<div id="banner_popup" style="display:none">
  <img src="./pic/close_18x18.gif" alt="{LNG_CLOSE_WINDOW}" title="{LNG_CLOSE_WINDOW}" style="cursor:pointer" onclick="hidePopupBanner()" />
  <br />
  <iframe id="banner_popup_frame" name="banner_popup_frame" src="dummy.html" scrolling="No" frameborder="0"></iframe>
</div>

<!-- COMMUNICATION INDICATOR -->
<div id="CommunicationIndicator" style="position:absolute;display:none;"><img src="./pic/progress_16x16.gif" alt="" style="border:0px" id="CommunicationIndicatorImg" /></div>

</PCPIN:TPL>