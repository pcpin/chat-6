<PCPIN:TPL name="main">
<div id="uploaded_file_div" style="text-align:center;margin:0px;">
  <br />
  <b>{LNG_UPLOAD_HINT}</b>
  <br />
  <form id="uploaded_file_form" action="{FORMLINK}" method="post" enctype="multipart/form-data" onsubmit="uploadStarted()">
    <input type="hidden" name="s_id" value="" />
    <input type="hidden" name="inc" value="upload" />
    <input type="hidden" name="f_target" value="" />
    <input type="hidden" name="f_submitted" value="" />
    <input type="hidden" name="profile_user_id" value="{PROFILE_USER_ID}" />
    <input type="file" name="f_data" style="width:95%" />
    <br /><br />
    <button type="submit" title="{LNG_OK}">{LNG_OK}</button>
    &nbsp;
    <button type="button" onclick="window.close()" title="{LNG_CANCEL}">{LNG_CANCEL}</button>
    <br /><br />
  </form>
</div>
</PCPIN:TPL>