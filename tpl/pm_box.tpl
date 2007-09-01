<PCPIN:TPL name="main">
<!-- MESSAGES AREA -->
<div id="chatroom_messages">
  <div id="chatroom_messages_contents"></div>
</div>
<!-- CONTROLS AREA -->
<div id="chatroom_controls">
  <div id="chatroom_controls_contents">
    <table border="0" cellspacing="0" cellpadding="0" width="100%">
      <tr>
        <td align="left" colspan="2">
          <table border="0" cellspacing="0" cellpadding="0">
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
              <td><button id="message_colors_btn" type="button" style="width:32px;background-image:none;" onclick="openColorBox('main_input_textarea', 'color', this, 'outgoingMessageColor', true, 'background-color')" title="{LNG_MESSAGE_COLOR}" onfocus="blur()"></button></td>
              <td style="width:1px"><img src="./pic/clearpixel_1x1.gif" alt="" width="5" height="1" /></td>
              <td><button type="button" style="width:23px;background-image:url(./pic/smilie_15x15.gif);background-repeat:no-repeat;background-position:center center;" onclick="openSmilieBox('main_input_textarea', null, this, false)" title="{LNG_SMILIES}"></button></td>

              <td width="100%"><img src="./pic/clearpixel_1x1.gif" width="1" height="1" alt="" /></td>

              <td><button type="button" id="scroll_ctl_btn" style="width:11px;background-image:url(./pic/scroll_active_5x18.gif);background-repeat:no-repeat;background-position:center center;" onclick="window.opener.setAutoScroll(null, window)" title="" onfocus="blur()"></button></td>
            </tr>
          </table>
        </td>
      </tr>
      <tr>
        <td align="left" nowrap="nowrap">
          <table border="0" cellspacing="2" cellpadding="0" width="100%">
            <tr valign="middle">
              <td width="1%">
                <input type="text" id="main_input_textarea" title="{LNG_TYPE_MESSAGE_HERE}" autocomplete="off" />
              </td>
              <td align="left">
                <button id="mainSendMessageButton" type="button" title="{LNG_SEND}" onfocus="blur()" onclick="opener.postChatMessage($('main_input_textarea'), '3001', 'n', window.tgt_user_id, opener.currentRoomID, 2)">{LNG_SEND}</button>
              </td>
            </tr>
          </table>
        </td>
      </tr>
    </table>
  </div>
</div>

</PCPIN:TPL>