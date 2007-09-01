<PCPIN:TPL name="main">
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Frameset//EN"  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-frameset.dtd">
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
<frameset rows="90,*" border="0" frameborder="0" style="overflow-x:hidden;" onload="{FRAMESET_ONLOAD}">
  <frame src="{FORMLINK}?s_id={S_ID}&amp;ainc=header_frame" name="header_frame" marginwidth="0" marginheight="0" scrolling="no" frameborder="0" />
  <frameset cols="210,*">
    <frame src="{FORMLINK}?s_id={S_ID}&amp;ainc=navigation_frame" name="navigation_frame" marginwidth="0" marginheight="0" scrolling="yes" frameborder="0" />
    <frame src="{FORMLINK}?s_id={S_ID}&amp;ainc=" name="main_frame" marginwidth="0" marginheight="0" scrolling="auto" frameborder="0" />
  </frameset>
</frameset>
</html>
</PCPIN:TPL>