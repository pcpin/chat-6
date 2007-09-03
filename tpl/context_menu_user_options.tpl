<div id="user_options_box" style="display:none">
  <table border="0" cellspacing="0" cellpadding="2" width="1px" class="context_menu_table" width="100%">
    <tr>
      <td align="left" class="context_menu_table_header" id="user_options_box_header" nowrap="nowrap"></td>
    </tr>
    <tr title="{LNG_SHOW_PROFILE}" class="context_menu_table_row" onmouseover="setCssClass(this, '.context_menu_table_hrow')" onmouseout="setCssClass(this, '.context_menu_table_row')" onclick="hideUserOptionsBox(1)">
      <td nowrap="nowrap">&nbsp;{LNG_SHOW_PROFILE}&nbsp;</td>
    </tr>
    <tr id="context_menu_send_pm" title="{LNG_SEND_PRIVATE_MESSAGE}" class="context_menu_table_row" onmouseover="setCssClass(this, '.context_menu_table_hrow')" onmouseout="setCssClass(this, '.context_menu_table_row')" onclick="hideUserOptionsBox(2)">
      <td nowrap="nowrap">&nbsp;{LNG_SEND_PRIVATE_MESSAGE}&nbsp;</td>
    </tr>
    <tr id="context_menu_invite_user" title="{LNG_INVITE}" class="context_menu_table_row" onmouseover="setCssClass(this, '.context_menu_table_hrow')" onmouseout="setCssClass(this, '.context_menu_table_row')" onclick="hideUserOptionsBox(12)">
      <td nowrap="nowrap">&nbsp;{LNG_INVITE}&nbsp;</td>
    </tr>
    <tr id="context_menu_mute_locally" title="{LNG_IGNORE}" class="context_menu_table_row" onmouseover="setCssClass(this, '.context_menu_table_hrow')" onmouseout="setCssClass(this, '.context_menu_table_row')" onclick="hideUserOptionsBox(3)">
      <td nowrap="nowrap">&nbsp;{LNG_IGNORE}&nbsp;</td>
    </tr>
    <tr id="context_menu_unmute_locally" title="{LNG_STOP_IGNORING}" class="context_menu_table_row" onmouseover="setCssClass(this, '.context_menu_table_hrow')" onmouseout="setCssClass(this, '.context_menu_table_row')" onclick="hideUserOptionsBox(4)">
      <td nowrap="nowrap">&nbsp;{LNG_STOP_IGNORING}&nbsp;</td>
    </tr>
    <tr id="context_menu_cmd_say" title="/say ..." class="context_menu_table_row" onmouseover="setCssClass(this, '.context_menu_table_hrow')" onmouseout="setCssClass(this, '.context_menu_table_row')" onclick="hideUserOptionsBox(13)" style="display:none">
      <td nowrap="nowrap">&nbsp;/say ...&nbsp;</td>
    </tr>
    <tr id="context_menu_cmd_whisper" title="/whisper ..." class="context_menu_table_row" onmouseover="setCssClass(this, '.context_menu_table_hrow')" onmouseout="setCssClass(this, '.context_menu_table_row')" onclick="hideUserOptionsBox(14)" style="display:none">
      <td nowrap="nowrap">&nbsp;/whisper ...&nbsp;</td>
    </tr>
    <PCPIN:TPL name="moderator_user_options" type="simplecondition" requiredvars="DISPLAY">
      <tr>
        <td class="context_menu_table_separator_row"></td>
      </tr>
      <tr id="context_menu_kick" title="{LNG_KICK}" class="context_menu_table_row" onmouseover="setCssClass(this, '.context_menu_table_hrow')" onmouseout="setCssClass(this, '.context_menu_table_row')" onclick="hideUserOptionsBox(5)">
        <td nowrap="nowrap">&nbsp;{LNG_KICK}&nbsp;</td>
      </tr>
      <PCPIN:TPL name="admin_user_options" type="simplecondition" requiredvars="DISPLAY">
        <tr>
          <td class="context_menu_table_separator_row"></td>
        </tr>
        <tr id="context_menu_client_info" title="{LNG_CLIENT_INFO}" class="context_menu_table_row" onmouseover="setCssClass(this, '.context_menu_table_hrow')" onmouseout="setCssClass(this, '.context_menu_table_row')" onclick="hideUserOptionsBox(6)">
          <td nowrap="nowrap">&nbsp;{LNG_CLIENT_INFO}&nbsp;</td>
        </tr>
        <tr id="context_menu_mute_global" title="{LNG_MUTE_GLOBAL}" class="context_menu_table_row" onmouseover="setCssClass(this, '.context_menu_table_hrow')" onmouseout="setCssClass(this, '.context_menu_table_row')" onclick="hideUserOptionsBox(7)">
          <td nowrap="nowrap">&nbsp;{LNG_MUTE_GLOBAL}&nbsp;</td>
        </tr>
        <tr id="context_menu_unmute_global" title="{LNG_UNMUTE_GLOBAL}" class="context_menu_table_row" onmouseover="setCssClass(this, '.context_menu_table_hrow')" onmouseout="setCssClass(this, '.context_menu_table_row')" onclick="hideUserOptionsBox(8)">
          <td nowrap="nowrap">&nbsp;{LNG_UNMUTE_GLOBAL}&nbsp;</td>
        </tr>
        <tr id="context_menu_ban_user" title="{LNG_BAN_USER}" class="context_menu_table_row" onmouseover="setCssClass(this, '.context_menu_table_hrow')" onmouseout="setCssClass(this, '.context_menu_table_row')" onclick="hideUserOptionsBox(9)">
          <td nowrap="nowrap">&nbsp;{LNG_BAN_USER}&nbsp;</td>
        </tr>
        <tr id="context_menu_ipban" title="{LNG_BAN_USER_AND_IP}" class="context_menu_table_row" onmouseover="setCssClass(this, '.context_menu_table_hrow')" onmouseout="setCssClass(this, '.context_menu_table_row')" onclick="hideUserOptionsBox(10)">
          <td nowrap="nowrap">&nbsp;{LNG_BAN_USER_AND_IP}&nbsp;</td>
        </tr>
        <tr id="context_menu_unban_user" title="{LNG_UNBAN_USER}" class="context_menu_table_row" onmouseover="setCssClass(this, '.context_menu_table_hrow')" onmouseout="setCssClass(this, '.context_menu_table_row')" onclick="hideUserOptionsBox(11)">
          <td nowrap="nowrap">&nbsp;{LNG_UNBAN_USER}&nbsp;</td>
        </tr>
      </PCPIN:TPL>
    </PCPIN:TPL>
  </table>
</div>