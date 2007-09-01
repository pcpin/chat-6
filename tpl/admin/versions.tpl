<PCPIN:TPL name="main">
  <form action="{FORMLINK}" method="post">
    <input type="hidden" name="s_id" value="{S_ID}" />
    <input type="hidden" name="ainc" value="{AINC}" />
    <input type="hidden" name="do_check" value="1" />
    <table class="tbl" border="0" cellspacing="1" cellpadding="0" width="100%">
      <tr>
        <td colspan="2" class="tbl_header_main">
          <b>{LNG_VERSION_INFORMATION}</b>
        </td>
      </tr>
      <PCPIN:TPL name="newer_version" type="simplecondition" requiredvars="DISPLAY">
        <tr>
          <td colspan="2" class="tbl_row" style="text-align:center">
            <br />
            <a href="{URL}" target="new_version" style="color:#FF0000; font-weight:bold;" title="{NEWVERSIONAVAILABLE}">{NEWVERSIONAVAILABLE}</a>
            <br /><br />
          </td>
        </tr>
      </PCPIN:TPL>
      <PCPIN:TPL name="no_new_version" type="simplecondition" requiredvars="DISPLAY">
        <tr>
          <td colspan="2" class="tbl_row" style="text-align:center">
            <br />
            <b>{LNG_YOUR_VERSION_IS_UP_TO_DATE}</b>
            <br /><br />
          </td>
        </tr>
      </PCPIN:TPL>
      <tr>
        <td class="tbl_row">
          <b>{LNG_CURRENT_VERSION}:</b>
        </td>
        <td class="tbl_row">
          {CURRENT_VERSION}
        </td>
      </tr>
      <tr>
        <td class="tbl_row">
          <b>{LNG_LAST_VERSION_CHECK}:</b>
        </td>
        <td class="tbl_row">
          {LAST_CHECK}
        </td>
      </tr>
      <tr>
        <td colspan="2" class="tbl_row" style="text-align:center">
          <br />
          <button type="submit" title="{LNG_CLICK_CHECK_FOR_NEW_VERSION}">{LNG_CLICK_CHECK_FOR_NEW_VERSION}</button>
          <br /><br />
        </td>
      </tr>
    </table>
  </form>
</PCPIN:TPL>