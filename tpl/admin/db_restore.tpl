<PCPIN:TPL name="main">
  <form action="{FORMLINK}" method="post" enctype="multipart/form-data">
    <input type="hidden" name="s_id" value="{S_ID}" />
    <input type="hidden" name="ainc" value="{AINC}" />
    <input type="hidden" name="do_upload" value="1" />
    <table border="0" cellspacing="1" cellpadding="0" width="100%" class="tbl">
      <tr>
        <td class="tbl_header_main">
          <b>{LNG_RESTORE_DATABASE}</b>
        </td>
      </tr>
      <PCPIN:TPL name="error" type="simplecondition" requiredvars="ERROR">
        <tr>
          <td class="tbl_row" colspan="2">
            <span class="statustext_error">{ERROR}</span>
          </td>
        </tr>
      </PCPIN:TPL>
      <PCPIN:TPL name="status" type="simplecondition" requiredvars="STATUS">
        <tr>
          <td class="tbl_row" colspan="2">
            <span class="statustext_success">{STATUS}</span>
          </td>
        </tr>
      </PCPIN:TPL>
      <tr valign="middle">
        <td class="tbl_row" style="text-align:center">
          <br />
          <b>{LNG_UPLOAD_SQL_DUMP}:</b> <input type="file" name="dump" title="{LNG_UPLOAD_SQL_DUMP}" />
          <br /><br />
          <button type="submit" title="{LNG_UPLOAD_SQL_DUMP}">{LNG_UPLOAD}</button>
          <br /><br />
        </td>
      </tr>
    </table>
  </form>
</PCPIN:TPL>