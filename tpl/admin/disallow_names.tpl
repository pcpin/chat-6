<PCPIN:TPL name="main">
  <table id="names_tbl" class="tbl" border="0" cellspacing="1" cellpadding="0" width="100%">
    <tr>
      <td colspan="2" class="tbl_header_main">
        <b>{LNG_DISALLOW_NAMES}</b>
      </td>
    </tr>
    <tr>
      <td class="tbl_header_sub">
        {LNG_NAME}
      </td>
      <td class="tbl_header_sub">
        &nbsp;
      </td>
    </tr>
  </table>
  <br />
  <table class="tbl" border="0" cellspacing="1" cellpadding="0" width="100%">
    <tr>
      <td class="tbl_header_sub" colspan="2">
        <b>{LNG_ADD_NEW_DISALLOWED_NAME}</b>
      </td>
    </tr>
    <tr>
      <td class="tbl_row">
        <b>{LNG_NAME}:</b>
      </td>
      <td class="tbl_row">
        <input id="new_name_name" title="{LNG_NAME}" size="32" maxlength="255" />
      </td>
    </tr>
    <tr>
      <td class="tbl_row" style="text-align:center" colspan="2">
        <button type="button" onclick="addDisallowedName()" title="{LNG_ADD_NEW_DISALLOWED_NAME}">{LNG_ADD_NEW_DISALLOWED_NAME}</button>
      </td>
    </tr>
  </table>
</PCPIN:TPL>