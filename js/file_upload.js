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

/**
 * Init upload form
 * @param   string    Upload file type (target)
 */
function initUploadForm(f_target) {
  window.focus();
  try {
    opener.uploadWindow=window;
  } catch (e) {}
  // Assign onClose event
  window.onclose=function() {
    try {
      opener.uploadWindow=null;
      opener.onfocus=opener.onfocus_orig;
    } catch (e) {}
  }
  // Assign onKeyUp event
  document.onkeyup=function(e) {
    if (getKC(e)==27) {
      window.close();
    }
  };
  // Set session ID
  $('uploaded_file_form').s_id.value=s_id;
  // Set file target (passed by opener)
  $('uploaded_file_form').f_target.value=f_target;
  window.onblur=function() {
    window.focus;
  }
  opener.onfocus_orig=opener.onfocus;
  opener.onfocus=function() {
    try {
      window.blur();
      uploadWindow.focus();
    } catch (e) {}
  };
  // Resize window
  setTimeout('resizeForDocumentHeight(10)', 3);
}


/**
 * Parse upload results
 * @param   int       code            Status code
 * @param   string    message         Status message
 * @param   int       binaryfile_id   Binaryfile ID
 * @param   int       width           If file was an image: width
 * @param   int       height          If file was an image: height
 * @param   string    filename        Filename
 */
function parseUploadResponse(code, message, binaryfile_id, width, height, filename) {
  if (typeof(code)!='undefined' && typeof(message)!='undefined') {
    try {
      // Respond to opener
      if (opener && opener.parseUploadResponse) {
        opener.parseUploadResponse(code, message, binaryfile_id, width, height, filename);
      }
    } catch (e) {}
  }
  // And close window
  window.close();
}


/**
 * onSubmit() event handler for an upload form
 */
function uploadStarted() {
  // Show progress bar
  toggleProgressBar(true);
  // Hide form
  $('uploaded_file_div').style.display='none';
  return true;
}