<PCPIN:TPL name="main">
<table class="tbl" border="0" cellspacing="0" cellpadding="0" width="100%">
  <tr>
    <td class="tbl_header_main">
      {LNG_INVITATION}
    </td>
  </tr>
  <tr>
    <td class="tbl_row" style="text-align:center">
      <br /><br /><br />
      {INVITATION_TEXT}
      <br /><br /><br />
      <button type="button" title="{LNG_ACCEPT_INVITATION}" onclick="acceptInvitation({ROOM_ID})">{LNG_ACCEPT_INVITATION}</button>
      &nbsp;&nbsp;
      <button type="button" title="{LNG_DECLINE_INVITATION}" onclick="window.close()">{LNG_DECLINE_INVITATION}</button>
      <br /><br />
      <button type="button" title="{LNG_SHOW_PROFILE}" onclick="opener.showUserProfile({USER_ID})">{LNG_SHOW_PROFILE}</button>
      &nbsp;&nbsp;
      <button type="button" title="{LNG_DECLINE_INVITATION_AND_MUTE_USER_LOCALLY}" onclick="opener.muteLocally({USER_ID}); window.close();">{MUTE_USER_LOCALLY}</button>
    </td>
  </tr>
</table>
</PCPIN:TPL>