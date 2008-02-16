<PCPIN:TPL name="main">
<form action="#" onsubmit="sendModeratorCall(); return false;">
  <table class="tbl" border="0" cellspacing="1" cellpadding="0" width="100%">
    <tr>
      <td colspan="2" class="tbl_header_main">
        SOS &bull; {LNG_CALL_MODERATOR}
      </td>
    </tr>
    <tr>
      <td class="tbl_row" colspan="2">
        <b>{LNG_READ_BEFORE_FILLING_FORM}:</b>
        <br />
        {LNG_CALL_MODERATOR_RULES}
        <br /><br />
      </td>
    </tr>
    <tr>
      <td class="tbl_row">
        <b>{LNG_ROOM_NAME}:</b>
      </td>
      <td class="tbl_row">
        <b>{ROOM_NAME}</b>
      </td>
    </tr>
    <tr>
      <td class="tbl_row">
        <b>{LNG_YOUR_NICKNAME}:</b>
      </td>
      <td class="tbl_row">
        <b>{NICKNAME}</b>
      </td>
    </tr>
    <tr>
      <td class="tbl_row">
        <b>{LNG_VIOLATION_CATEGORY}:</b>
      </td>
      <td class="tbl_row">
        <select id="abuse_category" title="{LNG_VIOLATION_CATEGORY}">
          <option value="">--- {LNG_PLEASE_SELECT} ---</option>
          <option value="1">{LNG_SPAM}</option>
          <option value="2">{LNG_INSULT}</option>
          <option value="3">{LNG_ADULT_CONTENT}</option>
          <option value="4">{LNG_ILLEGAL_CONTENT}</option>
          <option value="5">{LNG_HARASSMENT}</option>
          <option value="6">{LNG_FRAUD}</option>
          <option value="7">{LNG_OTHER}</option>
        </select>
      </td>
    </tr>
    <tr>
      <td class="tbl_row">
        <b>{LNG_ABUSER_NICKNAME}:</b>
      </td>
      <td class="tbl_row">
        <input type="text" id="abuse_nickname" title="{LNG_ABUSER_NICKNAME}" size="32" maxlength="255" autocomplete="off" />
      </td>
    </tr>
    <tr>
      <td class="tbl_row" style="vertical-align:top">
        <b>{LNG_VIOLATION_DESCRIPTION}:</b>
      </td>
      <td class="tbl_row">
        <textarea id="abuse_description" cols="32" rows="8" title="{LNG_VIOLATION_DESCRIPTION}"></textarea>
      </td>
    </tr>
    <tr>
      <td class="tbl_row" colspan="2" style="text-align:center">
        <button type="submit" title="{LNG_SEND}">{LNG_SEND}</button>
        &nbsp;
        <button type="submit" onclick="window.close(); return false;" title="{LNG_CANCEL}">{LNG_CANCEL}</button>
      </td>
    </tr>
  </table>
</form>
</PCPIN:TPL>