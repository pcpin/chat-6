<?php
/**
 *    This file is part of "PCPIN Chat 6".
 *
 *    "PCPIN Chat 6" is free software; you can redistribute it and/or modify
 *    it under the terms of the GNU General Public License as published by
 *    the Free Software Foundation; either version 3 of the License, or
 *    (at your option) any later version.
 *
 *    "PCPIN Chat 6" is distributed in the hope that it will be useful,
 *    but WITHOUT ANY WARRANTY; without even the implied warranty of
 *    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *    GNU General Public License for more details.
 *
 *    You should have received a copy of the GNU General Public License
 *    along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

if (!file_exists('./install/install.php')) {
  header('Location: ./index.php');
  die();
}

// Chat root directory
if (!defined('PCPIN_CHAT_ROOT_DIR')) define('PCPIN_CHAT_ROOT_DIR', str_replace('\\', '/', realpath(dirname(__FILE__))));

define('PCPIN_INSTALL_MODE', true);
require_once('./install/install.php');

if (!isset($step)) $step=0;

if (empty($step)) {
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN" "http://www.w3.org/TR/html4/frameset.dtd">
<html>
<head>
  <title>PCPIN Chat <?php echo htmlspecialchars(PCPIN_INSTALL_VERSION) ?> installation</title>
  <script type="text/javascript" src="./js/base/screen.js?<?php echo md5(filemtime('./js/base/screen.js')); ?>"></script>
  <script type="text/javascript" src="./js/base/strings.js?<?php echo md5(filemtime('./js/base/strings.js')); ?>"></script>
  <script type="text/javascript" src="./js/base/time.js?<?php echo md5(filemtime('./js/base/time.js')); ?>"></script>
  <script type="text/javascript" src="./js/base/xmlhttprequest.js?<?php echo md5(filemtime('./js/base/xmlhttprequest.js')); ?>"></script>
  <script type="text/javascript" src="./js/base/connectionstatus.js?<?php echo md5(filemtime('./js/base/connectionstatus.js')); ?>"></script>
  <script type="text/javascript" src="./js/base/global.js?<?php echo md5(filemtime('./js/base/global.js')); ?>"></script>
  <script type="text/javascript">
    var lastStep=0;
    function setStep(step) {
      step=parseInt(step);
      if (lastStep+1<step) {
        main_area.location.href='./install.php?step='+lastStep;
        return false;
      } else {
        lastStep=step;
      }
      for (var i=1; ; i++) {
        if ($('step_'+i, progress_area.document)) {
          if (i<step) {
            $('step_'+i, progress_area.document).style.color='#008800';
            $('step_'+i, progress_area.document).style.fontWeight='bold';
            $('step_'+i+'_prepend', progress_area.document).innerHTML='&nbsp;';
          } else if (i==step) {
            $('step_'+i, progress_area.document).style.color='#880000';
            $('step_'+i, progress_area.document).style.fontWeight='bold';
            $('step_'+i+'_prepend', progress_area.document).innerHTML='&gt;&gt;&nbsp;';
          } else {
            $('step_'+i, progress_area.document).style.color='#888888';
            $('step_'+i, progress_area.document).style.fontWeight='normal';
            $('step_'+i+'_prepend', progress_area.document).innerHTML='&nbsp;';
          }
        } else {
          break;
        }
      }
    }
  </script>
</head>
<frameset cols="220,*" framespacing="0" frameborder="0" marginwidth="0" marginheight="0">
  <frame name="progress_area" id="progress_area" src="./install.php?step=-1" scrolling="auto" noresize marginwidth="0" marginheight="0" border="0">
  <frame name="main_area" id="main_area" src="./install.php?step=1" scrolling="auto" noresize marginwidth="0" marginheight="0">
</frameset>
<noframes>
  Sorry, this chat needs a browser that understands framesets.
</noframes>
</html>
<?php
} else {
  $body_onload=array();
  $js_files=array();
  $body_onload[]='parent.setStep('.htmlspecialchars($step*1).')';
  $body_onload[]='$(\'contents_div\').style.display=\'\'';
  ob_start();

  switch ($step) {

    case -1:
      // Progress bar
      require_once('./install/progress.php');
    break;

    case 1:
      // Welcome page
      require_once('./install/step1.php');
    break;

    case 2:
      // License information
      require_once('./install/step2.php');
    break;

    case 3:
      // Server information
      require_once('./install/step3.php');
    break;

    case 4:
      // Database connection
      require_once('./install/step4.php');
    break;

    case 5:
      // Data import
      require_once('./install/step5.php');
    break;

    case 6:
      // Language files
      require_once('./install/step6.php');
    break;

    case 7:
      // Administrator account
      require_once('./install/step7.php');
    break;

    case 8:
      // Chat settings
      require_once('./install/step8.php');
    break;

  }

  $contents=ob_get_clean();
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
  <title>PCPIN Chat <?php echo htmlspecialchars(PCPIN_INSTALL_VERSION) ?> installation</title>
  <style type="text/css">
    .status_finished {
      color: #008800;
      font-weight: bold;
    }
    .status_current {
      color: #880000;
      font-weight: bold;
    }
    .status_open {
      color: #888888;
      font-weight: normal;
    }
  </style>
  <link rel="stylesheet" type="text/css" href="./main.css" />
  <script type="text/javascript" src="./js/base/screen.js?<?php echo md5(filemtime('./js/base/screen.js')); ?>"></script>
  <script type="text/javascript" src="./js/base/strings.js?<?php echo md5(filemtime('./js/base/strings.js')); ?>"></script>
  <script type="text/javascript" src="./js/base/time.js?<?php echo md5(filemtime('./js/base/time.js')); ?>"></script>
  <script type="text/javascript" src="./js/base/xmlhttprequest.js?<?php echo md5(filemtime('./js/base/xmlhttprequest.js')); ?>"></script>
  <script type="text/javascript" src="./js/base/connectionstatus.js?<?php echo md5(filemtime('./js/base/connectionstatus.js')); ?>"></script>
  <script type="text/javascript" src="./js/base/global.js?<?php echo md5(filemtime('./js/base/global.js')); ?>"></script>
<?php
foreach ($js_files as $file) {
?>
  <script type="text/javascript" src="./install/js/<?php echo $file.'?'.md5(filemtime('./install/js/'.$file)); ?>"></script>
<?php
}
?>
</head>
<body onload="<?php echo implode(' ; ', $body_onload) ?>">
<div id="contents_div" style="position:absolute; top: 0px; left: 0px; display: none; width:99%;">
<?php
  echo $contents;
?>
</div>
<div id="progressBar" style="display:none;">
  Please wait...
  <br />
  <img src="./pic/progress_bar_267x14.gif" title="{LNG_PLEASE_WAIT}" alt="{LNG_PLEASE_WAIT}" />
</div>
<noscript>
  Sorry, this chat needs a browser that supports JavaScript.
</noscript>
</body>
</html>
<?php
}
?>