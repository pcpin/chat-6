<PCPIN:TPL name="main">
<form action="{FORMLINK}" method="post" onsubmit="doLogin(); return false;">
  <table id="login_table" class="tbl" border="0" cellspacing="1" cellpadding="0" style="position:absolute;display:none;">
    <tr>
      <td nowrap="nowrap" class="tbl_header_main" colspan="2">
        {LNG_LOG_IN}
      </td>
    </tr>
    <tr>
      <td nowrap="nowrap" class="tbl_row">
        <b>{LNG_USERNAME}:</b>
      </td>
      <td nowrap="nowrap" class="tbl_row">
        <input id="login_username" value="{LOGIN_USERNAME}" type="text" size="16" maxlength="255" title="{LNG_USERNAME}" />
      </td>
    </tr>
    <tr>
      <td nowrap="nowrap" class="tbl_row">
        <b>{LNG_PASSWORD}:</b>
      </td>
      <td nowrap="nowrap" class="tbl_row">
        <input id="login_password" value="{LOGIN_PASSWORD}" type="password" size="16" maxlength="255" title="{LNG_PASSWORD}" />
      </td>
    </tr>
    <tr>
      <td nowrap="nowrap" class="tbl_row" colspan="2" style="text-align:center">
        <button type="submit" title="{LNG_LOG_IN}">{LNG_LOG_IN}</button>
        <PCPIN:TPL name="guest_login" type="simplecondition" requiredvars="DISPLAY">
          &nbsp;&nbsp;
          <button type="button" title="{LNG_LOG_IN_AS_GUEST}" onclick="doGuestLogin()">{LNG_LOG_IN_AS_GUEST}</button>
        </PCPIN:TPL>
      </td>
    </tr>
    <PCPIN:TPL name="account_options" type="simplecondition" requiredvars="DISPLAY">
      <tr>
        <td nowrap="nowrap" class="tbl_row" colspan="2" style="text-align:center">
          <PCPIN:TPL name="account_options_register" type="simplecondition" requiredvars="DISPLAY">
            <a href=":" title="{LNG_REGISTER}" onclick="showRegisterForm(); return false;">{LNG_REGISTER}</a>
            <br />
          </PCPIN:TPL>
          <a href=":" title="{LNG_FORGOT_PASSWORD}" onclick="showResetPasswordForm(); return false;">{LNG_FORGOT_PASSWORD}</a>
        </td>
      </tr>
    </PCPIN:TPL>
    <PCPIN:TPL name="language_selection" type="simplecondition" requiredvars="DISPLAY">
      <tr>
        <td nowrap="nowrap" class="tbl_row" colspan="2" style="text-align:center">
          <b>{LNG_LANGUAGE}:</b>
          <select id="language_selection" title="{LNG_LANGUAGES}" onchange="$('dummyform').language_id.value=this.value; $('dummyform').submit();">
            <PCPIN:TPL name="language_selection_option">
              <option value="{ID}" {SELECTED}>{LOCAL_NAME}</option>
            </PCPIN:TPL>
          </select>
        </td>
      </tr>
    </PCPIN:TPL>
  </table>
</form>
<PCPIN:TPL name="chat_summary" type="simplecondition" requiredvars="DISPLAY">
  <div id="chat_summary" style="height:{HEIGHT}px;display:none;">
    <iframe name="chat_summary_frame" src="./info.php" style="border:0;height:{HEIGHT}px;width:100%;" frameborder="0"></iframe>
  </div>
</PCPIN:TPL>

<div id="pbl" style="position:absolute;font-size:smaller;">
<!--
  PLEASE KEEP THIS UNCHANGED. THANK YOU.
-->
  Powered by <a href="http://www.pcpin.com/" target="_blank" title="Powered by PCPIN Chat">PCPIN Chat</a>
</div>

<form action="{FORMLINK}" method="post" onsubmit="doRegister(); return false;">
  <table id="register_table" class="tbl" border="0" cellspacing="1" cellpadding="0" style="position:absolute;display:none;">
    <tr>
      <td nowrap="nowrap" class="tbl_header_main" colspan="2">
        {LNG_REGISTRATION}
      </td>
    </tr>
    <tr>
      <td nowrap="nowrap" class="tbl_row">
        <b>{LNG_USERNAME}:</b>
      </td>
      <td nowrap="nowrap" class="tbl_row">
        <input id="register_username" value="" type="text" size="24" maxlength="{LOGIN_MAXLENGTH}" title="{LNG_USERNAME}" autocomplete="off" />
      </td>
    </tr>
    <tr>
      <td nowrap="nowrap" class="tbl_row">
        <b>{LNG_EMAIL_ADDRESS}:</b>
      </td>
      <td nowrap="nowrap" class="tbl_row">
        <input id="register_email" value="" type="text" size="24" maxlength="255" title="{LNG_EMAIL_ADDRESS}" autocomplete="off" />
      </td>
    </tr>
    <tr>
      <td nowrap="nowrap" class="tbl_row">
        <b>{LNG_PASSWORD}:</b>
      </td>
      <td nowrap="nowrap" class="tbl_row">
        <input id="register_password1" value="" type="password" size="24" maxlength="255" title="{LNG_PASSWORD}" autocomplete="off" />
      </td>
    </tr>
    <tr>
      <td nowrap="nowrap" class="tbl_row">
        <b>{LNG_CONFIRM_PASSWORD}:</b>
      </td>
      <td nowrap="nowrap" class="tbl_row">
        <input id="register_password2" value="" type="password" size="24" maxlength="255" title="{LNG_CONFIRM_PASSWORD}" autocomplete="off" />
      </td>
    </tr>
    <tr>
      <td nowrap="nowrap" class="tbl_row" colspan="2" style="text-align:center">
        <button type="submit" title="{LNG_REGISTER}">{LNG_REGISTER}</button>
        &nbsp;&nbsp;
        <button type="button" title="{LNG_CANCEL}" onclick="showLoginForm()">{LNG_CANCEL}</button>
      </td>
    </tr>
  </table>
</form>

<form action="{FORMLINK}" method="post" onsubmit="doResetPassword(); return false;">
  <table id="reset_pw_table" class="tbl" border="0" cellspacing="1" cellpadding="0" style="position:absolute;display:none;">
    <tr>
      <td nowrap="nowrap" class="tbl_header_main" colspan="2">
        {LNG_PASSWORD_RESET}
      </td>
    </tr>
    <tr>
      <td nowrap="nowrap" class="tbl_row">
        <b>{LNG_USERNAME}:</b>
      </td>
      <td nowrap="nowrap" class="tbl_row">
        <input id="reset_pw_username" value="" type="text" size="24" maxlength="255" title="{LNG_USERNAME}" autocomplete="off" />
      </td>
    </tr>
    <tr>
      <td nowrap="nowrap" class="tbl_row">
        <b>{LNG_EMAIL_ADDRESS}:</b>
      </td>
      <td nowrap="nowrap" class="tbl_row">
        <input id="reset_pw_email" value="" type="text" size="24" maxlength="255" title="{LNG_EMAIL_ADDRESS}" autocomplete="off" />
      </td>
    </tr>
    <tr>
      <td nowrap="nowrap" class="tbl_row" colspan="2" style="text-align:center">
        <button type="submit" title="{LNG_RESET_MY_PASSWORD}">{LNG_RESET_MY_PASSWORD}</button>
        &nbsp;&nbsp;
        <button type="button" title="{LNG_CANCEL}" onclick="showLoginForm()">{LNG_CANCEL}</button>
      </td>
    </tr>
  </table>
</form>
</PCPIN:TPL>