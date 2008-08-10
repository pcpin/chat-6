<PCPIN:TPL name="main">
<table id="fields_tbl" class="tbl" border="0" cellspacing="1" cellpadding="0" width="100%" style="display:none">
  <tr>
    <td colspan="9" class="tbl_header_main">
      <b>{LNG_CUSTOM_PROFILE_FIELDS}</b>
    </td>
  </tr>
  <tr>
    <td class="tbl_header_sub" style="width:1px">
    </td>
    <td class="tbl_header_sub" nowrap="nowrap" style="text-align:center;width:1px;">
      <b>{LNG_ACTIVE}</b>
    </td>
    <td class="tbl_header_sub" style="text-align:center">
      <b>{LNG_NAME}</b>
    </td>
    <td class="tbl_header_sub" style="text-align:center">
      <b>{LNG_TYPE}</b>
    </td>
    <td class="tbl_header_sub" style="text-align:center">
      <b>{LNG_CHOICES}</b>
    </td>
    <td class="tbl_header_sub" style="text-align:center">
      <b>{LNG_DEFAULT_VALUE}</b>
    </td>
    <td class="tbl_header_sub" style="text-align:center">
      <b>{LNG_VISIBILITY}</b>
    </td>
    <td class="tbl_header_sub" style="text-align:center">
      <b>{LNG_WRITEABLE}</b>
    </td>
    <td class="tbl_header_sub" style="width:1px">
  </tr>
  <tr>
    <td colspan="9" class="tbl_row">
      <button type="button" title="{LNG_CREATE_NEW_FIELD}" onclick="createNewField()">{LNG_CREATE_NEW_FIELD}</button>
    </td>
  </tr>
</table>
<form id="field_form" method="post" action="#" onsubmit="return false">
  <input type="hidden" id="field_form_id" value="" />
  <table id="field_form_tbl" class="tbl" border="0" cellspacing="1" cellpadding="0" style="display:none">
    <tr id="field_form_create_header" style="display:none">
      <td colspan="2" class="tbl_header_main">
        <b>{LNG_CREATE_NEW_FIELD}</b>
      </td>
    </tr>
    <tr id="field_form_edit_header" style="display:none">
      <td colspan="2" class="tbl_header_main">
        <b>{LNG_EDIT_FIELD}</b>
      </td>
    </tr>
    <tr>
      <td class="tbl_row">
        <b>{LNG_NAME}:</b>
      </td>
      <td class="tbl_row">
        <input type="text" title="{LNG_NAME}" id="field_form_name" size="42" maxlength="255" />
      </td>
    </tr>
    <tr>
      <td class="tbl_row">
        <b>{LNG_TYPE}:</b>
      </td>
      <td class="tbl_row">
        <select title="{LNG_TYPE}" id="field_form_type" onchange="formatFields()">
          <option value="string">{LNG_SINGLE_TEXT_FIELD}</option>
          <option value="text">{LNG_TEXTAREA}</option>
          <option value="url">{LNG_URL}</option>
          <option value="email">{LNG_EMAIL_ADDRESS}</option>
          <option value="choice">{LNG_SIMPLE_CHOICE}</option>
          <option value="multichoice">{LNG_MULTIPLE_CHOICE}</option>
        </select>
      </td>
    </tr>
    <tr id="field_form_choices_row" style="display:none">
      <td class="tbl_row" style="vertical-align:top">
        <b>{LNG_CHOICES}:</b>
        <br />
        {LNG_FIELD_CHOICES_DESCRIPTION}
      </td>
      <td class="tbl_row">
        <textarea title="{LNG_CHOICES}" id="field_form_choices" rows="5" cols="40"></textarea>
      </td>
    </tr>
    <tr>
      <td class="tbl_row" style="vertical-align:top">
        <b>{LNG_DEFAULT_VALUE}:</b>
      </td>
      <td class="tbl_row">
        <input type="hidden" id="field_form_default_value_id" value="" />
        <input type="text" title="{LNG_DEFAULT_VALUE}" id="field_form_default_value_string" size="42" maxlength="255" style="display:none" />
        <textarea title="{LNG_DEFAULT_VALUE}" id="field_form_default_value_text" rows="5" cols="40" style="display:none"></textarea>
        <select title="{LNG_DEFAULT_VALUE}" id="field_form_default_value_choice" style="display:none"></select>
        <select title="{LNG_DEFAULT_VALUE}" id="field_form_default_value_multichoice" size="8" multiple="multiple" style="display:none"></select>
      </td>
    </tr>
    <tr>
      <td class="tbl_row">
        <b>{LNG_VISIBILITY}:</b>
      </td>
      <td class="tbl_row">
        <select id="field_form_visibility" title="{LNG_VISIBILITY}">
          <option value="public">{LNG_EVERYBODY}</option>
          <option value="registered">{LNG_REGISTERED_USERS_ONLY}</option>
          <option value="moderator">{LNG_MODERATORS_ONLY}</option>
          <option value="admin">{LNG_ADMINS_ONLY}</option>
        </select>
      </td>
    </tr>
    <tr>
      <td class="tbl_row">
        <b>{LNG_WRITEABLE}:</b>
      </td>
      <td class="tbl_row">
        <select id="field_form_writeable" title="{LNG_WRITEABLE}">
          <option value="user">{LNG_PROFILE_OWNER}</option>
          <option value="admin">{LNG_ADMINS_ONLY}</option>
        </select>
      </td>
    </tr>
    <tr>
      <td class="tbl_row">
        <b>{LNG_ACTIVE}:</b>
      </td>
      <td class="tbl_row">
        <select id="field_form_disabled" title="{LNG_ACTIVE}">
          <option value="n">{LNG_YES}</option>
          <option value="y">{LNG_NO}</option>
        </select>
      </td>
    </tr>
    <tr id="field_form_create_footer" style="display:none">
      <td class="tbl_row" colspan="2" style="text-align:center">
        <button type="submit" title="{LNG_CREATE_NEW_FIELD}" onclick="createNewField(true)">{LNG_CREATE_NEW_FIELD}</button>
        &nbsp;
        <button type="button" title="{LNG_CANCEL}" onclick="hideFieldForm()">{LNG_CANCEL}</button>
      </td>
    </tr>
    <tr id="field_form_edit_footer" style="display:none">
      <td class="tbl_row" colspan="2" style="text-align:center">
        <button type="submit" title="{LNG_SAVE_CHANGES}" onclick="editField(0, true)">{LNG_SAVE_CHANGES}</button>
        &nbsp;
        <button type="button" title="{LNG_CANCEL}" onclick="hideFieldForm()">{LNG_CANCEL}</button>
      </td>
    </tr>
  </table>
</form>
</PCPIN:TPL>