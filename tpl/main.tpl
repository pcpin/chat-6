<PCPIN:TPL name="main">
<PCPIN:TPL name="doctype" type="condition" conditionvar="HIDE">
<PCPIN:SUB condition="empty"><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "DTD/xhtml1-transitional.dtd"></PCPIN:SUB>
<PCPIN:SUB condition="default"></PCPIN:SUB>
</PCPIN:TPL>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="{ISO_LNG}" lang="{ISO_LNG}">
<head>
  <meta http-equiv="Content-Type" content="text/xml; charset=utf-8" />
  <link rel="shortcut icon" href="favicon.ico" />
  <title>{TITLE}</title>
  <PCPIN:TPL name="css_files" type="simplecondition" requiredvars="FILE">
    <link rel="stylesheet" type="text/css" href="{FILE}" />
  </PCPIN:TPL>
  <PCPIN:TPL name="js_files" type="simplecondition" requiredvars="FILE">
    <script type="text/javascript" src="{FILE}"></script>
  </PCPIN:TPL>
</head>
<body onload="{BODY_ONLOAD}" oncontextmenu="{BODY_ONCONTEXTMENU}">
<!-- MP3 PLAYER -->
<PCPIN:TPL name="mp3_player" type="simplecondition" requiredvars="PLAYER" src="mp3_player.tpl" />
<div id="body_contents">{CONTENTS}</div>
<div id="progressBar" style="display:none;">
  {LNG_PLEASE_WAIT}...
  <br />
  <img src="./pic/progress_bar_267x14.gif" title="{LNG_PLEASE_WAIT}" alt="{LNG_PLEASE_WAIT}" />
</div>
<div id="color_selection_box" style="display:none">
  <PCPIN:TPL name="colorbox" src="colorbox.tpl" type="simplecondition" requiredvars="DISPLAY" />
</div>
<PCPIN:TPL name="smiliebox_tpl" src="smiliebox.tpl" type="simplecondition" requiredvars="DISPLAY" />
<div style="display:none">
  <form id="dummyform" action="{FORMLINK}" method="post">
    <input type="hidden" name="s_id" value="" />
    <input type="hidden" name="inc" value="" />
    <input type="hidden" name="ts" value="" />
    <input type="hidden" name="just_logged_in" value="" />
    <input type="hidden" name="auto_message_container" value="" />
    <input type="hidden" name="language_id" value="" />
  </form>
</div>
<!-- PASSWORD PROMPT BOX -->
<PCPIN:TPL name="password_field_box" src="password_field_box_tpl.tpl" />
<!-- USERLIST/MEMBERLIST RECORD TEMPLATE -->
<PCPIN:TPL name="userlist_record_tpl" src="userlist_record_tpl.tpl" />
<!-- USER OPTIONS CONTEXT MENU -->
<PCPIN:TPL name="context_menu_user_options" src="context_menu_user_options.tpl" type="simplecondition" requiredvars="DISPLAY" />
<!-- alert() box -->
<PCPIN:TPL name="alert_tpl" src="alertbox.tpl" />
<!-- confirm() box -->
<PCPIN:TPL name="confirm_tpl" src="confirmbox.tpl" />
<!-- prompt() box -->
<PCPIN:TPL name="prompt_tpl" src="promptbox.tpl" />
<div id="last_element_dummy" style="display:none;margin:0px;padding:0px;border:0px;width:1px;height:1px;"></div>
<noscript>
  <br /><br /><br /><br /><br />
  <div style="text-align:center;width:100%;">
    <h1>Sorry, this chat needs a browser that supports JavaScript.</h1>
  </div>
</noscript>
{TIMERS}
</body>
</html>
</PCPIN:TPL>