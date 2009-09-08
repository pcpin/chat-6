<PCPIN:TPL name="main">
  <form action="#" onsubmit="updateSettingsACP(); return false;" method="post">
    <table id="settings_tbl" class="tbl" border="0" cellspacing="1" cellpadding="0" width="100%" style="display:none">
      <tr>
        <td colspan="3" class="tbl_header_main">
          <b>{LNG_SETTINGS} &bull; {TITLE}</b>
        </td>
      </tr>
      <tr>
        <td class="tbl_header_sub" style="width:1%;text-align:center;">
          <b>#</b>
        </td>
        <td class="tbl_header_sub">
          <b>{LNG_DESCRIPTION}</b>
        </td>
        <td class="tbl_header_sub">
          <b>{LNG_VALUE}</b>
        </td>
      </tr>
      <tr>
        <td colspan="3" class="tbl_row" style="text-align:center">
          <button type="submit" title="{LNG_SAVE_CHANGES}">{LNG_SAVE_CHANGES}</button>
          &nbsp;
          <button type="button" onclick="getSettings()" title="{LNG_RESET_FORM}">{LNG_RESET_FORM}</button>
        </td>
      </tr>
    </table>
  </form>
</PCPIN:TPL>