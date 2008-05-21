<PCPIN:TPL name="main">
<table id="other_profile_header_tbl" class="tbl" border="0" cellspacing="0" cellpadding="0" style="display:none;margin:5px;">
  <tr>
    <td class="tbl_header_main" id="other_profile_header_tbl_title"></td>
  </tr>
  <tr id="member_not_activated_row" style="display:none">
    <td class="tbl_row">
      <b>{LNG_ACCOUNT_NOT_ACTIVATED}</b>
      &nbsp;
      <button type="button" onclick="activateUser()" title="{LNG_ACTIVATE_THIS_ACCOUNT}">{LNG_ACTIVATE_THIS_ACCOUNT}</button>
    </td>
  </tr>
  <tr>
    <td class="tbl_row">
      <b>{LNG_USERNAME}:</b>
      <span id="member_username"></span>
    </td>
  </tr>
  <tr>
    <td class="tbl_row">
      <b>{LNG_LEVEL}:</b>
      <span id="member_level">
        <input type="hidden" name="member_level_id" id="member_level_id" value="" />
        <span id="member_level_name"></span>
        &nbsp;
        <a href=":" onclick="showMemberLevelForm(); return false;" title="{LNG_EDIT}">
          <img src="./pic/edit_13x13.gif" title="{LNG_EDIT}" alt="{LNG_EDIT}" />
        </a>
      </span>
      <span id="member_level_options" style="display:none">
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
        &nbsp;
        <button type="button" onclick="hideMemberLevelForm()" title="{LNG_CANCEL}">{LNG_CANCEL}</button>
      </span>
    </td>
  </tr>
  <tr id="member_moderated_categories_row">
    <td class="tbl_row">
      <b>{LNG_MODERATED_CATEGORIES}:</b>
      <span id="member_moderated_categories"></span>
      &nbsp;&nbsp;
      <a href=":" onclick="openEditModeratorWindow(profileUserId); return false;" title="{LNG_EDIT}">
        <img src="./pic/edit_13x13.gif" title="{LNG_EDIT}" alt="{LNG_EDIT}" />
      </a>
    </td>
  </tr>
  <tr id="member_moderated_rooms_row">
    <td class="tbl_row">
      <b>{LNG_MODERATED_ROOMS}:</b>
      <span id="member_moderated_rooms"></span>
      &nbsp;&nbsp;
      <a href=":" onclick="openEditModeratorWindow(profileUserId); return false;" title="{LNG_EDIT}">
        <img src="./pic/edit_13x13.gif" title="{LNG_EDIT}" alt="{LNG_EDIT}" />
      </a>
    </td>
  </tr>
  <tr id="member_moderated_rooms_row">
    <td class="tbl_row">
      <a href=":" onclick="deleteUser(); return false;" title="{LNG_DELETE_USER}">
        <img src="./pic/delete_13x13.gif" title="{LNG_DELETE_USER}" alt="{LNG_DELETE_USER}" />
        {LNG_DELETE_USER}
      </a>
    </td>
  </tr>
</table>

<table id="profile_data_table" border="0" cellspacing="5" cellpadding="0" width="1%" style="display:none">
  <tr valign="top">
    <td align="center">
      <table class="tbl" border="0" cellspacing="1" cellpadding="0" width="460px">
        <tbody>
          <tr>
            <td class="tbl_header_main" colspan="2" id="user_profile_data_header">
              {LNG_YOUR_PROFILE}
            </td>
          </tr>
          <tr>
            <td class="tbl_row">
              <b>{LNG_REGISTRATION_DATE}:</b>
            </td>
            <td class="tbl_row">
              {REGISTRATION_DATE}
            </td>
          </tr>
          <tr>
            <td class="tbl_row">
              <b>{LNG_TIME_SPENT_ONLINE}:</b>
            </td>
            <td class="tbl_row">
              <PCPIN:TPL name="online_minutes" type="simplecondition" requiredvars="MINUTES">
                <PCPIN:TPL name="online_hours" type="simplecondition" requiredvars="HOURS">
                  <PCPIN:TPL name="online_days" type="simplecondition" requiredvars="DAYS">
                    <b>{DAYS}</b> {LNG_DAYS},
                  </PCPIN:TPL>
                  <b>{HOURS}</b> {LNG_HOURS},
                </PCPIN:TPL>
                <b>{MINUTES}</b> {LNG_MINUTES},
              </PCPIN:TPL>
              <b>{ONLINE_SECONDS}</b> {LNG_SECONDS}
            </td>
          </tr>
          <tr>
            <td class="tbl_row">
              <b>{LNG_EMAIL_ADDRESS}:</b>
            </td>
            <td class="tbl_row">
              <a href="#" onclick="changeEmailAddress()" title="{LNG_CHANGE}"><img src="./pic/edit_13x13.gif" title="{LNG_CHANGE}" alt="{LNG_CHANGE}" /></a>
              &nbsp;&nbsp;
              <span id="email_address_span">{EMAIL_ADDRESS}</span>
            </td>
          </tr>
          <tr>
            <td class="tbl_row">
              <b>{LNG_HIDE_EMAIL}:</b>
            </td>
            <td class="tbl_row">
              <a href="#" onclick="changeEmailVisibility()" title="{LNG_CHANGE}"><img src="./pic/edit_13x13.gif" title="{LNG_CHANGE}" alt="{LNG_CHANGE}" /></a>
              &nbsp;&nbsp;
              <span id="hide_email_span">{HIDE_EMAIL}</span>
            </td>
          </tr>
          <tr>
            <td class="tbl_row">
              <b>{LNG_HOMEPAGE}:</b>
            </td>
            <td class="tbl_row">
              <a href="#" onclick="updateUserdataField('homepage', currentProfileHomepage)" title="{LNG_CHANGE}"><img src="./pic/edit_13x13.gif" title="{LNG_CHANGE}" alt="{LNG_CHANGE}" /></a>
              &nbsp;&nbsp;
              <span id="homepage_span"><a href="{FORMLINK}?external_url={HOMEPAGE_URLENCODED}" target="_blank" title="{HOMEPAGE}">{HOMEPAGE}</a></span>
            </td>
          </tr>
          <tr>
            <td class="tbl_row">
              <b>{LNG_GENDER}:</b>
            </td>
            <td class="tbl_row">
              <span id="gender_span">
                <a href="#" onclick="showChangeGenderForm()" title="{LNG_CHANGE}"><img src="./pic/edit_13x13.gif" title="{LNG_CHANGE}" alt="{LNG_CHANGE}" /></a>
                &nbsp;&nbsp;
                <img src="./pic/clearpixel_1x1.gif" id="gender_image" border="0" />
              </span>
              <span id="gender_input_span" style="display:none">
                <table border="0" cellspacing="0" cellpadding="0">
                  <tr>
                    <td>
                      <select id="gender">
                        <option value="-">{LNG_GENDER_-}</option>
                        <option value="m">{LNG_GENDER_M}</option>
                        <option value="f">{LNG_GENDER_F}</option>
                      </select>
                    </td>
                    <td>
                      &nbsp;<button type="button" onclick="updateGender($('gender').value)" title="{LNG_OK}">{LNG_OK}</button>
                    </td>
                  </tr>
                </table>
              </span>
            </td>
          </tr>
          <tr>
            <td class="tbl_row">
              <b>{LNG_AGE}:</b>
            </td>
            <td class="tbl_row">
              <a href="#" onclick="updateUserdataField('age')" title="{LNG_CHANGE}"><img src="./pic/edit_13x13.gif" title="{LNG_CHANGE}" alt="{LNG_CHANGE}" /></a>
              &nbsp;&nbsp;
              <span id="age_span">{AGE}</span>
            </td>
          </tr>
          <tr>
            <td class="tbl_row">
              <b>{LNG_MESSENGER_ICQ}:</b>
            </td>
            <td class="tbl_row">
              <a href="#" onclick="updateUserdataField('icq')" title="{LNG_CHANGE}"><img src="./pic/edit_13x13.gif" title="{LNG_CHANGE}" alt="{LNG_CHANGE}" /></a>
              &nbsp;&nbsp;
              <span id="icq_span">{ICQ}</span>
            </td>
          </tr>
          <tr>
            <td class="tbl_row">
              <b>{LNG_MESSENGER_MSN}:</b>
            </td>
            <td class="tbl_row">
              <a href="#" onclick="updateUserdataField('msn')" title="{LNG_CHANGE}"><img src="./pic/edit_13x13.gif" title="{LNG_CHANGE}" alt="{LNG_CHANGE}" /></a>
              &nbsp;&nbsp;
              <span id="msn_span">{MSN}</span>
            </td>
          </tr>
          <tr>
            <td class="tbl_row">
              <b>{LNG_MESSENGER_AIM}:</b>
            </td>
            <td class="tbl_row">
              <a href="#" onclick="updateUserdataField('aim')" title="{LNG_CHANGE}"><img src="./pic/edit_13x13.gif" title="{LNG_CHANGE}" alt="{LNG_CHANGE}" /></a>
              &nbsp;&nbsp;
              <span id="aim_span">{AIM}</span>
            </td>
          </tr>
          <tr>
            <td class="tbl_row">
              <b>{LNG_MESSENGER_YIM}:</b>
            </td>
            <td class="tbl_row">
              <a href="#" onclick="updateUserdataField('yim')" title="{LNG_CHANGE}"><img src="./pic/edit_13x13.gif" title="{LNG_CHANGE}" alt="{LNG_CHANGE}" /></a>
              &nbsp;&nbsp;
              <span id="yim_span">{YIM}</span>
            </td>
          </tr>
          <tr>
            <td class="tbl_row">
              <b>{LNG_LOCATION}:</b>
            </td>
            <td class="tbl_row">
              <a href="#" onclick="updateUserdataField('location')" title="{LNG_CHANGE}"><img src="./pic/edit_13x13.gif" title="{LNG_CHANGE}" alt="{LNG_CHANGE}" /></a>
              &nbsp;&nbsp;
              <span id="location_span">{LOCATION}</span>
            </td>
          </tr>
          <tr>
            <td class="tbl_row">
              <b>{LNG_OCCUPATION}:</b>
            </td>
            <td class="tbl_row">
              <a href="#" onclick="updateUserdataField('occupation')" title="{LNG_CHANGE}"><img src="./pic/edit_13x13.gif" title="{LNG_CHANGE}" alt="{LNG_CHANGE}" /></a>
              &nbsp;&nbsp;
              <span id="occupation_span">{OCCUPATION}</span>
            </td>
          </tr>
          <tr>
            <td class="tbl_row">
              <b>{LNG_INTERESTS}:</b>
            </td>
            <td class="tbl_row">
              <a href="#" onclick="updateUserdataField('interests')" title="{LNG_CHANGE}"><img src="./pic/edit_13x13.gif" title="{LNG_CHANGE}" alt="{LNG_CHANGE}" /></a>
              &nbsp;&nbsp;
              <span id="interests_span">{INTERESTS}</span>
            </td>
          </tr>
          <PCPIN:TPL name="change_password" type="simplecondition" requiredvars="DISPLAY">
            <tr>
              <td class="tbl_row">
                <b>{LNG_PASSWORD}:</b>
              </td>
              <td class="tbl_row">
                <span id="change_password_link_span">
                  <a href="#" onclick="changePassword()" title="{LNG_CHANGE_PASSWORD}">{LNG_CHANGE}</a>
                </span>
              </td>
            </tr>
          </PCPIN:TPL>
          <PCPIN:TPL name="language_selection" type="simplecondition" requiredvars="DISPLAY">
            <tr>
              <td class="tbl_row">
                <b>{LNG_LANGUAGE}:</b>
              </td>
              <td class="tbl_row">
                <select id="language_selection" title="{LNG_LANGUAGES}" onchange="SkipPageUnloadedMsg=true;$('dummyform').action=window.document.location.href; $('dummyform').s_id.value=s_id; $('dummyform').language_id.value=this.value; $('dummyform').submit();">
                  <PCPIN:TPL name="language_selection_option">
                    <option value="{ID}" {SELECTED}>{LOCAL_NAME}</option>
                  </PCPIN:TPL>
                </select>
              </td>
            </tr>
          </PCPIN:TPL>
          <tr>
            <td class="tbl_row" colspan="2" style="text-align:center">
              <button type="button" onclick="showUserProfile(profileUserId)" title="{LNG_SHOW_PUBLIC_PROFILE}">{LNG_SHOW_PUBLIC_PROFILE}</button>
            </td>
          </tr>
        </tbody>
      </table>
    </td>
    <td align="center" id="nicknames_area" width="1%" nowrap="nowrap">
      <table id="nicknames_table" class="tbl" border="0" cellspacing="1" cellpadding="0">
        <tr>
          <td class="tbl_header_main" colspan="2" nowrap="nowrap" id="user_nicknames_data_header">
            {LNG_YOUR_NICKNAMES}
          </td>
        </tr>
        <tr id="no_nicknames" nowrap="nowrap" style="display:none">
          <td class="tbl_row" colspan="2" style="text-align:center">
            {LNG_NO_NICKNAMES_YET}
          </td>
        </tr>
        <tr id="new_nickname_link_row" style="display:none">
          <td class="tbl_row" colspan="2" nowrap="nowrap" style="text-align:center">
            <a href="#" title="{LNG_ADD_NICKNAME}" onclick="showNicknameForm()"><img src="./pic/plus_13x13.gif" name="img_hover" alt="{LNG_ADD_NICKNAME}" title="{LNG_ADD_NICKNAME}" border="0" />&nbsp;{LNG_ADD_NICKNAME}</a>
          </td>
        </tr>
      </table>
      <table id="nickname_colorizer_table" class="tbl" border="0" cellspacing="1" cellpadding="0" style="display:none">
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
            <button type="button" onclick="flushDisplay()" title="{LNG_CANCEL}">{LNG_CANCEL}</button>
          </td>
        </tr>
        <tr>
          <td class="tbl_row" style="text-align:center">
            <PCPIN:TPL name="colorbox" src="colorbox.tpl" />
          </td>
        </tr>
      </table>
    </td>
    <td>
      <table class="tbl" id="avatars_tbl" border="0" cellspacing="1" cellpadding="0">
        <tr>
          <td class="tbl_header_main" colspan="2" id="user_avatars_data_header">
            {LNG_YOUR_AVATARS}
          </td>
        </tr>
        <tr id="upload_avatar_row" style="display:none">
          <td class="tbl_row" colspan="2" nowrap="nowrap" style="text-align:center">
            <a href="#" title="{LNG_UPLOAD_NEW_AVATAR}" onclick="showNewAvatarForm(); return false;">{LNG_UPLOAD_NEW_AVATAR}</a>
          </td>
        </tr>
        <tr id="avatar_gallery_row" style="display:none">
          <td class="tbl_row" colspan="2" nowrap="nowrap" style="text-align:center">
            <a href="#" title="{LNG_AVATAR_GALLERY}" onclick="showAvatarGallery(); return false;">{LNG_AVATAR_GALLERY}</a>
          </td>
        </tr>
      </table>
    </td>
  </tr>
</table>
<input type="hidden" id="profile_username_hidden" value="{PROFILE_USERNAME_HIDDEN}" />
<br /><br />
<div style="width:99%;text-align:center;">
  <button type="button" title="{LNG_CLOSE_WINDOW}" onclick="window.close()">{LNG_CLOSE_WINDOW}</button>
</div>
</PCPIN:TPL>