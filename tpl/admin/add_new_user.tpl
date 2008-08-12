<PCPIN:TPL name="main">
  <form method="post" target="#" onsubmit="addNewUser(); return false;">
    <table id="names_tbl" class="tbl" border="0" cellspacing="1" cellpadding="0" width="100%">
      <tr>
        <td colspan="2" class="tbl_header_main">
          <b>{LNG_ADD_NEW_USER}</b>
        </td>
      </tr>
      <tr>
        <td class="tbl_row">
          <b>{LNG_USERNAME}:</b>
        </td>
        <td class="tbl_row">
          <input type="text" id="new_user_name" title="{LNG_USERNAME}" size="36" maxlength="30" />
        </td>
      </tr>
      <tr>
        <td class="tbl_row">
          <b>{LNG_EMAIL_ADDRESS}:</b>
        </td>
        <td class="tbl_row">
          <input type="text" id="new_user_email" title="{LNG_EMAIL_ADDRESS}" size="36" maxlength="255" />
        </td>
      </tr>
      <tr>
        <td class="tbl_row">
          <b>{LNG_PASSWORD}:</b>
        </td>
        <td class="tbl_row">
          <input type="password" id="new_user_password0" title="{LNG_PASSWORD}" size="20" maxlength="255" />
        </td>
      </tr>
      <tr>
        <td class="tbl_row">
          <b>{LNG_CONFIRM_PASSWORD}:</b>
        </td>
        <td class="tbl_row">
          <input type="password" id="new_user_password1" title="{LNG_CONFIRM_PASSWORD}" size="20" maxlength="255" />
        </td>
      </tr>
      <tr>
        <td class="tbl_row" style="text-align:center" colspan="2">
          <button type="submit" title="{LNG_ADD_NEW_USER}">{LNG_ADD_NEW_USER}</button>
          &nbsp;&nbsp;
          <button type="reset" title="{LNG_RESET_FORM}">{LNG_RESET_FORM}</button>
        </td>
      </tr>
    </table>
  </form>
</PCPIN:TPL>