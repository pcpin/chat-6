<PCPIN:TPL name="main">
<form action="#" method="post" onsubmit="return false">
  <PCPIN:TPL name="user_fields" type="simplecondition" requiredvars="ID">
    <input type="hidden" id="{ID}" value="{VALUE}" />
  </PCPIN:TPL>
</form>
<form action="#" method="post" onsubmit="return false">
  <table class="tbl" border="0" cellspacing="0" cellpadding="0" width="100%">
    <tr>
      <td colspan="2" class="tbl_header_main" id="other_profile_header_tbl_title">{HEADER_TEXT}</td>
    </tr>
    <tr>
      <td class="tbl_row" style="width:200px;border-right: solid 1px #000000;padding:0px;vertical-align:top;">
        <table class="tbl" border="0" cellspacing="0" cellpadding="0" width="100%" style="border:0px">
          <tr id="navigation_link_profile" style="cursor:pointer" onclick="showProfileContents(this.id.substr(16))">
            <td class="tbl_row" title="{LNG_PROFILE}"><a href="#" onclick="return false;">{LNG_PROFILE}</a></td>
          </tr>
          <tr id="navigation_link_avatars" style="cursor:pointer" onclick="showProfileContents(this.id.substr(16))">
            <td class="tbl_row" title="{LNG_AVATARS}"><a href="#" onclick="return false;">{LNG_AVATARS}</a></td>
          </tr>
          <tr id="navigation_link_nicknames" style="cursor:pointer" onclick="showProfileContents(this.id.substr(16))">
            <td class="tbl_row" title="{LNG_NICKNAMES}"><a href="#" onclick="return false;">{LNG_NICKNAMES}</a></td>
          </tr>
          <tr id="navigation_link_ignore_list" style="cursor:pointer" onclick="showProfileContents(this.id.substr(16))">
            <td class="tbl_row" title="{LNG_IGNORE_LIST}"><a href="#" onclick="return false;">{LNG_IGNORE_LIST}</a></td>
          </tr>
          <tr id="navigation_link_level" style="cursor:pointer;display:none;" onclick="showProfileContents(this.id.substr(16))">
            <td class="tbl_row" title="{LNG_LEVEL}"><a href="#" onclick="return false;">{LNG_LEVEL}</a></td>
          </tr>
          <tr id="navigation_link_password" style="cursor:pointer;display:none;" onclick="showProfileContents(this.id.substr(16))">
            <td class="tbl_row" title="{LNG_CHANGE_PASSWORD}"><a href="#" onclick="return false;">{LNG_CHANGE_PASSWORD}</a></td>
          </tr>
          <tr id="navigation_link_public_profile" style="cursor:pointer" onclick="showUserProfile(profileUserId)">
            <td class="tbl_row" title="{LNG_SHOW_PUBLIC_PROFILE}"><a href="#" onclick="return false;">{LNG_SHOW_PUBLIC_PROFILE}</a></td>
          </tr>
          <tr>
            <td class="tbl_row"></td>
          </tr>
        </table>
      </td>
      <td class="tbl_row" id="contents_profile_data" style="vertical-align:top">
        <div id="account_activation_required" style="display:none;width:100%;text-align:center;margin:10px;border:0px;">
          <b>{LNG_ACCOUNT_NOT_ACTIVATED}</b>
          &nbsp;&nbsp;&nbsp;
          <button type="button" onclick="activateUser()" title="{LNG_ACTIVATE_THIS_ACCOUNT}">{LNG_ACTIVATE_THIS_ACCOUNT}</button>
          <br />
        </div>
        <div id="contents_data_profile" style="display:none">
          <table id="profile_fields_tbl" class="tbl" border="0" cellspacing="1" cellpadding="0" style="width:100%">
            <tbody>
              <tr>
                <td class="tbl_header_main" colspan="2">
                  {LNG_PROFILE}
                </td>
              </tr>
              <tr>
                <td class="tbl_row">
                  <b>{LNG_USERNAME}:</b>
                </td>
                <td class="tbl_row" id="contents_profile_data_username"></td>
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
              <tr id="contents_profile_data_email_row0" style="display:none">
                <td class="tbl_row">
                  <b>{LNG_EMAIL_ADDRESS}:</b>
                </td>
                <td class="tbl_row">
                  <input id="contents_profile_data_email" type="text" title="{LNG_EMAIL_ADDRESS}" size="64" maxlength="255" />
                </td>
              </tr>
              <tr id="contents_profile_data_email_row1" style="display:none">
                <td class="tbl_row">
                  <b>{LNG_HIDE_EMAIL}:</b>
                </td>
                <td class="tbl_row">
                  <select title="{LNG_HIDE_EMAIL}" id="contents_profile_data_hide_email">
                    <option value="1" title="{LNG_HIDE_EMAIL}: {LNG_YES}">{LNG_YES}</option>
                    <option value="0" title="{LNG_HIDE_EMAIL}: {LNG_NO}">{LNG_NO}</option>
                  </select>
                </td>
              </tr>
              <tr id="contents_profile_data_custom_field_tr_tpl" style="display:none">
                <td class="tbl_row" style="font-weight:bold;vertical-align:top;"></td>
                <td class="tbl_row"></td>
              </tr>
              <PCPIN:TPL name="language_selection" type="simplecondition" requiredvars="DISPLAY">
                <tr>
                  <td class="tbl_row">
                    <b>{LNG_LANGUAGE}:</b>
                  </td>
                  <td class="tbl_row">
                    <select title="{LNG_LANGUAGE}" id="contents_profile_data_language_id">
                      <PCPIN:TPL name="language_selection_option">
                        <option value="{ID}" {SELECTED}>{LOCAL_NAME}</option>
                      </PCPIN:TPL>
                    </select>
                  </td>
                </tr>
              </PCPIN:TPL>
              <tr id="contents_profile_data_delete_own_account_row" style="display:none">
                <td class="tbl_row" colspan="2">
                  <a href="#" onclick="return deleteOwnAccount()" title="{LNG_DELETE_MY_ACCOUNT}">{LNG_DELETE_MY_ACCOUNT}</a>
                </td>
              </tr>
              <tr>
                <td class="tbl_row" colspan="2" style="text-align:center">
                  <button type="button" style="margin:3px" onclick="saveProfileChanges()" title="{LNG_SAVE_CHANGES}">{LNG_SAVE_CHANGES}</button>
                  <button type="button" style="margin:3px" onclick="showProfileContents('profile')" title="{LNG_RESET_FORM}">{LNG_RESET_FORM}</button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        <div id="contents_data_avatars" style="display:none">
          <table class="tbl" id="avatars_tbl" border="0" cellspacing="1" cellpadding="0" style="width:100%">
            <tr>
              <td class="tbl_header_main" id="avatars_header_cell" colspan="3" id="user_avatars_data_header">
                {LNG_AVATARS}
              </td>
            </tr>
            <tr>
              <td class="tbl_row" id="avatars_footer_cell" colspan="3" nowrap="nowrap" style="text-align:center">
                <br />
                <button id="upload_avatar_btn" style="display:none" title="{LNG_UPLOAD_NEW_AVATAR}" onclick="showNewAvatarForm()">{LNG_UPLOAD_NEW_AVATAR}</button>
                &nbsp;&nbsp;
                <button id="avatar_gallery_btn" style="display:none" title="{LNG_AVATAR_GALLERY}" onclick="showAvatarGallery()">{LNG_AVATAR_GALLERY}</button>
              </td>
            </tr>
          </table>
        </div>
        <div id="contents_data_password" style="display:none">
          <table class="tbl" border="0" cellspacing="1" cellpadding="0" style="width:100%">
            <tr>
              <td class="tbl_header_main" colspan="2" nowrap="nowrap">
                {LNG_CHANGE_PASSWORD}
              </td>
            </tr>
            <tr>
              <td class="tbl_row">
                <b>{LNG_ENTER_PASSWORD}:</b>
              </td>
              <td class="tbl_row">
                <input id="new_password_0" title="{LNG_PASSWORD}" type="password" size="32" maxlength="255" />
              </td>
            </tr>
            <tr>
              <td class="tbl_row">
                <b>{LNG_CONFIRM_PASSWORD}:</b>
              </td>
              <td class="tbl_row">
                <input id="new_password_1" title="{LNG_CONFIRM_PASSWORD}" type="password" size="32" maxlength="255" />
              </td>
            </tr>
            <tr>
              <td class="tbl_row" colspan="2" style="text-align:center">
                <button type="button" onclick="changePassword()" title="{LNG_CHANGE_PASSWORD}">{LNG_CHANGE_PASSWORD}</button>
              </td>
            </tr>
          </table>
        </div>
        <div id="contents_data_nicknames" style="display:none">
          <table id="nicknames_table" class="tbl" border="0" cellspacing="1" cellpadding="0" style="width:310px;">
            <tr>
              <td class="tbl_header_main" colspan="3" nowrap="nowrap">
                {LNG_NICKNAMES}
              </td>
            </tr>
            <tr id="no_nicknames" style="display:none">
              <td class="tbl_row" colspan="3" style="text-align:center">
                {LNG_NO_NICKNAMES_YET}
              </td>
            </tr>
            <tr id="nicknames_tbl_header2" style="display:none">
              <td class="tbl_header_sub"style="text-align:center">
                {LNG_ACTIVE}
              </td>
              <td class="tbl_header_sub"style="text-align:center">
                {LNG_ACTION}
              </td>
              <td class="tbl_header_sub"style="text-align:left">
                {LNG_NICKNAME}
              </td>
            </tr>
            <tr id="new_nickname_link_row" style="display:none">
              <td class="tbl_row" colspan="3" nowrap="nowrap" style="text-align:center">
                <button title="{LNG_ADD_NICKNAME}" onclick="showNicknameForm()">{LNG_ADD_NICKNAME}</button>
              </td>
            </tr>
          </table>
          <table id="nickname_colorizer_table" class="tbl" border="0" cellspacing="1" cellpadding="0" style="display:none;width:310px;">
            <tr>
              <td class="tbl_header_main" nowrap="nowrap">
                {LNG_NICKNAME_EDITOR}
              </td>
            </tr>
            <tr>
              <td class="tbl_row" style="text-align:center">
                {LNG_NICKNAME_COLORIZER_RULES}
              </td>
            </tr>
            <tr>
              <td class="tbl_row" nowrap="nowrap" style="text-align:center">
                <span id="nickname_preview" title="{LNG_NICKNAME}"></span>
              </td>
            </tr>
            <tr>
              <td class="tbl_row" style="text-align:center">
                <input id="nickname_text_input" type="text" size="32" maxlength="255" value="" title="{LNG_NICKNAME}" style="text-align: center" autocomplete="off" />
                <br />
                <button id="save_nickname_color_btn" type="button" value="{LNG_SAVE}" title="{LNG_SAVE}">{LNG_SAVE}</button>
                &nbsp;
                <button type="button" onclick="flushDisplay()" title="{LNG_CANCEL}">{LNG_CANCEL}</button>
              </td>
            </tr>
            <tr>
              <td class="tbl_row" style="text-align:center">
                <div id="nickname_colorbox_container" style="height:220px;padding:0px;"></div>
              </td>
            </tr>
          </table>
        </div>
        <div id="contents_data_level" style="display:none">
          <table id="nicknames_table" class="tbl" border="0" cellspacing="1" cellpadding="0" style="width:100%;">
            <tr>
              <td class="tbl_header_main" colspan="3" nowrap="nowrap">
                {LNG_LEVEL}
              </td>
            </tr>
            <tr>
              <td class="tbl_row">
                <span id="member_level">
                  <input type="hidden" name="member_level_id" id="member_level_id" value="" />
                  <span id="member_level_name"></span>
                  &nbsp;
                  <img src="./pic/edit_13x13.gif" title="{LNG_EDIT}" alt="{LNG_EDIT}" onclick="showMemberLevelForm()" style="cursor:pointer" />
                </span>
                <span id="member_level_options" style="display:none">
                  <label for="member_level_option_g" title="{LNG_GUEST}">
                    <input type="radio" name="member_level_option" id="member_level_option_g" title="{LNG_GUEST}" />
                    {LNG_GUEST}
                  </label>
                  &nbsp;&nbsp;
                  <label for="member_level_option_r" title="{LNG_REGISTERED}">
                    <input type="radio" name="member_level_option" id="member_level_option_r" title="{LNG_REGISTERED}" />
                    {LNG_REGISTERED}
                  </label>
                  &nbsp;&nbsp;
                  <label for="member_level_option_a" title="{LNG_ADMIN}">
                    <input type="radio" name="member_level_option" id="member_level_option_a" title="{LNG_ADMIN}" />
                    {LNG_ADMIN}
                  </label>
                  &nbsp;&nbsp;&nbsp;&nbsp;
                  <button type="button" onclick="setMemberLevel()" title="{LNG_SAVE_CHANGES}">{LNG_SAVE_CHANGES}</button>
                </span>
              </td>
            </tr>
          </table>
        </div>
        <div id="contents_data_ignore_list" style="display:none">
          <table class="tbl" id="ignore_list_table" border="0" cellspacing="1" cellpadding="0" style="width:100%">
            <tr>
              <td class="tbl_header_main" nowrap="nowrap">
                {LNG_IGNORE_LIST}
              </td>
            </tr>
            <tr id="no_members_found" style="display:none">
              <td class="tbl_row" style="text-align:center">
                <b>{LNG_NO_MEMBERS_FOUND}</b>
              </td>
            </tr>
          </table>
        </div>
      </td>
    </tr>
  </table>
</form>
<div style="width:99%;text-align:center;margin-top:20px;margin-bottom:20px;">
  <button type="button" title="{LNG_CLOSE_WINDOW}" onclick="window.close()">{LNG_CLOSE_WINDOW}</button>
</div>
</PCPIN:TPL>