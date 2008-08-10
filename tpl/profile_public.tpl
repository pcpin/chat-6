<PCPIN:TPL name="main">
<table class="tbl" border="0" cellspacing="1" cellpadding="0" width="100%" id="profile_table" style="display:none">
  <tbody>
    <tr>
      <td colspan="2" class="tbl_header_main">
        <span id="profile_header"></span>
      </td>
    </tr>
    <tr id="avatars_row" style="display:none">
      <td colspan="2" class="tbl_row" style="text-align:center">
        <table border="0" cellspacing="0" cellpadding="3">
          <tr valign="top">
            <td align="right" id="avatar_image"></td>
            <td align="left" id="avatar_thumbs"></td>
          </tr>
        </table>
      </td>
    </tr>
    <tr>
      <td class="tbl_row">
        <b>{LNG_NICKNAME}:</b>
      </td>
      <td class="tbl_row" id="contents_profile_data_nickname"></td>
    </tr>
    <tr id="contents_profile_data_regdate_row" style="display:none">
      <td class="tbl_row">
        <b>{LNG_REGISTRATION_DATE}:</b>
      </td>
      <td class="tbl_row" id="contents_profile_data_registration_date"></td>
    </tr>
    <tr>
      <td class="tbl_row">
        <b>{LNG_TIME_SPENT_ONLINE}:</b>
      </td>
      <td class="tbl_row" id="contents_profile_data_online_time"></td>
    </tr>
    <tr id="contents_profile_data_email_row" style="display:none">
      <td class="tbl_row">
        <b>{LNG_EMAIL_ADDRESS}:</b>
      </td>
      <td class="tbl_row" id="contents_profile_data_email">
      </td>
    </tr>
    <tr id="contents_profile_data_custom_field_tr_tpl" style="display:none">
      <td class="tbl_row" style="font-weight:bold;vertical-align:top;"></td>
      <td class="tbl_row"></td>
    </tr>
    <tr id="profile_fields_tbl_last_row">
      <td class="tbl_row">
        <b>{LNG_ONLINE_STATUS}:</b>
      </td>
      <td class="tbl_row">
        <span id="profile_online_status"></span>
      </td>
    </tr>
    <tr id="invite_user" style="display:none">
      <td class="tbl_row" colspan="2" style="text-align:center">
        <button id="invite_button" type="button"></button>
      </td>
    </tr>
  </tbody>
</table>
<div style="width:100%;text-align:center;margin-top:20px;margin-bottom:20px;" id="close_window_btn_div" style="display:none">
  <button type="button" title="{LNG_CLOSE_WINDOW}" onclick="window.close()">{LNG_CLOSE_WINDOW}</button>
</div>
</PCPIN:TPL>