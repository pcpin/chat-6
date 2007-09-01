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
 * Current sort field
 * @var int
 */
var sort_by=0;

/**
 * Current sort direction
 * @var int
 */
var sort_dir=0;


/**
 * Init window
 */
function initIPFilterForm() {
  // Get filtered addresses
  getFilteredIPAddresses();
  // Init "Add address" form
  initAddIpAddressForm();
}


/**
 * Get filtered IP addresses
 */
function getFilteredIPAddresses() {
  sendData('_CALLBACK_getFilteredIPAddresses()', formlink, 'POST', 'ajax='+urlencode('ip_filter_get_addresses')+'&s_id='+urlencode(s_id)+'&sort_by='+urlencode(sort_by)+'&sort_dir='+urlencode(sort_dir));
}
function _CALLBACK_getFilteredIPAddresses() {
//debug(actionHandler.getResponseString()); return false;
  var message=actionHandler.getCdata('message');
  var status=actionHandler.getCdata('status');

  var address=null;
  var address_nr=0;
  var address_id=0;

  var ip_tbl=null;
  var tr=null;
  var td=null;

  if (status=='-1') {
    // Session is invalid
    window.parent.document.location.href=formlink+'?session_timeout&ts='+unixTimeStamp();
    return false;
  } else {
    if (message=='OK') {
      // OK
      ip_tbl=$('ip_table');
      // Clear table
      for (var i=ip_tbl.rows.length-2; i>1; i--) {
        ip_tbl.deleteRow(i);
      }
      while (null!=(address=actionHandler.getElement('address', address_nr++))) {
        address_id=stringToNumber(actionHandler.getCdata('id', 0, address));
        tr=ip_tbl.insertRow(ip_tbl.rows.length-1);
        tr.checkbox_id='ip_selection_'+address_id;

        td=tr.insertCell(-1);
        td.innerHTML='<input type="checkbox" id="ip_selection_'+address_id+'" title="'+htmlspecialchars(getLng('delete'))+'" />';
        setCssClass(td, '.tbl_row');

        td=tr.insertCell(-1);
        td.innerHTML=htmlspecialchars(actionHandler.getCdata('mask', 0, address));
        setCssClass(td, '.tbl_row');

        td=tr.insertCell(-1);
        td.innerHTML=htmlspecialchars(actionHandler.getCdata('action', 0, address)=='a'? getLng('allow') : getLng('deny'));
        setCssClass(td, '.tbl_row');

        td=tr.insertCell(-1);
        td.innerHTML=htmlspecialchars(actionHandler.getCdata('added_on', 0, address));
        setCssClass(td, '.tbl_row');

        td=tr.insertCell(-1);
        td.innerHTML=htmlspecialchars(actionHandler.getCdata('expires', 0, address));
        setCssClass(td, '.tbl_row');

        td=tr.insertCell(-1);
        td.innerHTML=nl2br(htmlspecialchars(actionHandler.getCdata('description', 0, address)));
        setCssClass(td, '.tbl_row');

      }
    } else if (message!=null) {
      alert(message);
    }
  }
  toggleProgressBar(false);
}


/**
 * Init "Add new IP address" form
 */
function initAddIpAddressForm() {
  $('new_ip_expires_year').value=stringToNumber(date('Y'))+1;
  $('new_ip_expires_month').value=date('m');
  $('new_ip_expires_day').value=date('d');
  $('new_ip_expires_hour').value=date('H');
  $('new_ip_expires_minute').value=date('i');
  $('new_ip_expires_never').checked=false;
  $('new_ip_mask_0').value='';
  $('new_ip_mask_1').value='';
  $('new_ip_mask_2').value='';
  $('new_ip_mask_3').value='';
  $('new_ip_expires_never').onclick=function() {
    $('new_ip_expires_year').disabled=this.checked;
    $('new_ip_expires_month').disabled=this.checked;
    $('new_ip_expires_day').disabled=this.checked;
    $('new_ip_expires_hour').disabled=this.checked;
    $('new_ip_expires_minute').disabled=this.checked;
  };
  $('new_ip_expires_never').onclick();
}



/**
 * Add new IP address to the filter
 */
function addIPAddress() {
  var errors=new Array();
  $('new_ip_expires_year').value=trimString($('new_ip_expires_year').value);
  $('new_ip_expires_month').value=trimString($('new_ip_expires_month').value);
  $('new_ip_expires_day').value=trimString($('new_ip_expires_day').value);
  $('new_ip_expires_hour').value=trimString($('new_ip_expires_hour').value);
  $('new_ip_expires_minute').value=trimString($('new_ip_expires_minute').value);
  if (   !$('new_ip_expires_never').checked
      && (   $('new_ip_expires_year').value=='' || !isDigitString($('new_ip_expires_year').value)
          || $('new_ip_expires_month').value=='' || !isDigitString($('new_ip_expires_month').value)
          || $('new_ip_expires_day').value=='' || !isDigitString($('new_ip_expires_day').value)
          || $('new_ip_expires_hour').value=='' || !isDigitString($('new_ip_expires_hour').value)
          || $('new_ip_expires_minute').value=='' || !isDigitString($('new_ip_expires_minute').value)
          )
      ) {
    errors.push(getLng('expiration_date_invalid'));
  }

  $('new_ip_mask_0').value=trimString($('new_ip_mask_0').value);
  $('new_ip_mask_1').value=trimString($('new_ip_mask_1').value);
  $('new_ip_mask_2').value=trimString($('new_ip_mask_2').value);
  $('new_ip_mask_3').value=trimString($('new_ip_mask_3').value);
  if (   $('new_ip_mask_0').value.length==0
      || $('new_ip_mask_1').value.length==0
      || $('new_ip_mask_2').value.length==0
      || $('new_ip_mask_3').value.length==0) {
    errors.push(getLng('ip_mask_invalid'));
  }
  $('new_ip_description').value=trimString($('new_ip_description').value);

  if (errors.length>0) {
    alert(errors.join("\n"));
  } else {
    // Send data to server
    sendData('_CALLBACK_addIPAddress()', formlink, 'POST', 'ajax='+urlencode('ip_filter_add_address')+'&s_id='+urlencode(s_id)
             +'&mask='+urlencode($('new_ip_mask_0').value+'.'+$('new_ip_mask_1').value+'.'+$('new_ip_mask_2').value+'.'+$('new_ip_mask_3').value)
             +'&expires_year='+urlencode($('new_ip_expires_year').value)
             +'&expires_month='+urlencode($('new_ip_expires_month').value)
             +'&expires_day='+urlencode($('new_ip_expires_day').value)
             +'&expires_hour='+urlencode($('new_ip_expires_hour').value)
             +'&expires_minute='+urlencode($('new_ip_expires_minute').value)
             +($('new_ip_expires_never').checked? '&expires_never=1' : '')
             +'&description='+urlencode($('new_ip_description').value)
             +'&action='+urlencode($('new_ip_action').value));
  }
  return false;
}
function _CALLBACK_addIPAddress() {
//alert(actionHandler.getResponseString()); return false;
  var message=actionHandler.getCdata('message');
  var status=actionHandler.getCdata('status');
  if (message!=null) {
    alert(message);
  }
  toggleProgressBar(false);
  if (status=='0') {
    getFilteredIPAddresses();
    initAddIpAddressForm();
  }
}

/**
 * Delete selected IP addresses
 */
function deleteSelectedAddresses() {
  var inputs=$$('INPUT');
  var ids=new Array();
  for (var i=0; i<inputs.length; i++) {
    if (inputs[i].type=='checkbox' && 0==inputs[i].id.indexOf('ip_selection_') && inputs[i].checked) {
      ids.push('ids[]='+urlencode(inputs[i].id.substring(13)));
    }
  }
  if (ids.length && confirm(getLng('confirm_delete_addresses'))) {
    sendData('_CALLBACK_deleteSelectedAddresses()', formlink, 'POST', 'ajax='+urlencode('ip_filter_delete_address')+'&s_id='+urlencode(s_id)+'&'+ids.join('&'));
  }
  return false;
}
function _CALLBACK_deleteSelectedAddresses() {
//alert(actionHandler.getResponseString()); return false;
  var message=actionHandler.getCdata('message');
  var status=actionHandler.getCdata('status');
  if (message!=null) {
    alert(message);
  }
  toggleProgressBar(false);
  getFilteredIPAddresses();
}
