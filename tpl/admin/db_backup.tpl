<PCPIN:TPL name="main">
  <form action="{FORMLINK}" method="post">
    <input type="hidden" name="s_id" value="{S_ID}" />
    <input type="hidden" name="ainc" value="{AINC}" />
    <input type="hidden" name="do_download" value="1" />
    <table border="0" cellspacing="1" cellpadding="0" width="100%" class="tbl">
      <tr>
        <td class="tbl_header_main">
          <b>{LNG_BACKUP_DATABASE}</b>
        </td>
      </tr>
      <tr valign="middle">
        <td class="tbl_row" style="text-align:center">
          <br />
          <b>{LNG_DOWNLOAD_SQL_DUMP}</b>
          <br /><br />
          <button type="submit" title="{LNG_DOWNLOAD_SQL_DUMP}">{LNG_DOWNLOAD}</button>
          <br /><br />
        </td>
      </tr>
    </table>
  </form>
</PCPIN:TPL>