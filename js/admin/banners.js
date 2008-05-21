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
 * Value of top_banner_height configuration setting
 * @var int
 */
var top_banner_height=0;

/**
 * Value of bottom_banner_height configuration setting
 * @var int
 */
var bottom_banner_height=0;

/**
 * Banners array
 * @var object
 */
var Banners=new Array();

/**
 * Init window
 */
function initBannersWindow(top_banner_height_, bottom_banner_height_) {
  top_banner_height=top_banner_height_;
  bottom_banner_height=bottom_banner_height_;
  // Get banners
  getBanners();
}


/**
 * Get banners
 */
function getBanners() {
  sendData('_CALLBACK_getBanners()', formlink, 'POST', 'ajax=get_banners&s_id='+urlencode(s_id));
}
function _CALLBACK_getBanners() {
//debug(actionHandler.getResponseString()); return false;

  var banner=null;
  var banner_array=null;

  if (actionHandler.status==-1) {
    // Session is invalid
    window.parent.document.location.href=formlink+'?session_timeout&ts='+unixTimeStamp();
    return false;
  } else {
    if (actionHandler.message=='OK') {
      // OK
      Banners=new Array();
      if (typeof(actionHandler.data['banner'])!='undefined') {
        for (var i=0; i<actionHandler.data['banner'].length; i++) {
          banner=actionHandler.data['banner'][i];
          banner_array=new Array();
          banner_array['id']=stringToNumber(banner['id'][0]);
          banner_array['name']=banner['name'][0];
          banner_array['active']=banner['active'][0];
          banner_array['source_type']=banner['source_type'][0];
          banner_array['source']=banner['source'][0];
          banner_array['display_position']=banner['display_position'][0];
          banner_array['views']=banner['views'][0];
          banner_array['max_views']=banner['max_views'][0];
          banner_array['start_date']=banner['start_date'][0];
          banner_array['expiration_date']=banner['expiration_date'][0];
          banner_array['width']=banner['width'][0];
          banner_array['height']=banner['height'][0];
          Banners[banner_array['id']]=banner_array;
        }
      }
    } else {
      alert(actionHandler.message);
    }
  }

  // Display banners
  showBanners();
  toggleProgressBar(false);
}


/**
 * Display banners
 */
function showBanners() {

  var banners_tbl=$('banners_tbl');
  var tr=null;
  var td=null;

  // Hide "Add new banner" form
  hideNewBannerForm();
  // Clear table
  for (var i=banners_tbl.rows.length-2; i>1; i--) {
    banners_tbl.deleteRow(i);
  }

  // Display banners
  if (Banners.length==0) {
    tr=banners_tbl.insertRow(banners_tbl.rows.length-1);
    td=tr.insertCell(-1);
    td.innerHTML='<br />'+htmlspecialchars(getLng('no_banners_yet'))+'<br /><br />';
    td.colSpan=8;
    setCssClass(td, '.tbl_row');
    td.style.textAlign='center';
    $('banners_list_header').style.display='none';
  } else {
    $('banners_list_header').style.display='';
    for (var i in Banners) {
      tr=banners_tbl.insertRow(banners_tbl.rows.length-1);
  
      // Name
      td=tr.insertCell(-1);
      td.innerHTML='<img src="./pic/edit_13x13.gif" title="'+htmlspecialchars(getLng('edit'))+'" alt="'+htmlspecialchars(getLng('edit'))+'" style="cursor:pointer" onclick="showEditBannerForm('+htmlspecialchars(i)+')" />'
                  +'&nbsp;'
                  +'<img src="./pic/delete_13x13.gif" title="'+htmlspecialchars(getLng('delete'))+'" alt="'+htmlspecialchars(getLng('delete'))+'" style="cursor:pointer" onclick="deleteBanner('+htmlspecialchars(i)+')" />'
                  +'&nbsp;&nbsp;'
                  +'<a href=":" onclick="showBannerPreview(\'banners\', '+htmlspecialchars(i)+'); return false;" title="'+htmlspecialchars(getLng('preview'))+'">'
                  +htmlspecialchars(Banners[i]['name'])
                  +'</a>'
                  ;
      setCssClass(td, '.tbl_row');

      // Active
      td=tr.insertCell(-1);
      td.innerHTML=htmlspecialchars(Banners[i]['active']=='y'? getLng('yes') : getLng('no'));
      setCssClass(td, '.tbl_row');
      td.style.textAlign='center';

      // Source
      td=tr.insertCell(-1);
      td.innerHTML=htmlspecialchars(Banners[i]['source_type']=='u'? getLng('url') : getLng('custom'));
      setCssClass(td, '.tbl_row');
      td.style.textAlign='center';

      // Display position
      td=tr.insertCell(-1);
      switch (Banners[i]['display_position']) {

        case 't':
          td.innerHTML=htmlspecialchars(getLng('at_window_top'));
        break;

        case 'b':
          td.innerHTML=htmlspecialchars(getLng('at_window_bottom'));
        break;

        case 'p':
          td.innerHTML=htmlspecialchars(getLng('in_popup_window'));
        break;

        case 'm':
          td.innerHTML=htmlspecialchars(getLng('between_messages'));
        break;

      }
      setCssClass(td, '.tbl_row');
      td.style.textAlign='center';

      // Views / Max. views
      td=tr.insertCell(-1);
      td.innerHTML=htmlspecialchars(Banners[i]['views']+' / '+(Banners[i]['max_views']=='0'? getLng('unlimited') : Banners[i]['max_views']));
      setCssClass(td, '.tbl_row');
      td.style.textAlign='center';

      // Start date
      td=tr.insertCell(-1);
      td.innerHTML=htmlspecialchars(date(dateFormat, Banners[i]['start_date']));
      setCssClass(td, '.tbl_row');
      td.style.textAlign='center';

      // Expiration date
      td=tr.insertCell(-1);
      if (stringToNumber(Banners[i]['expiration_date'])>0) {
        td.innerHTML=htmlspecialchars(date(dateFormat, Banners[i]['expiration_date']));
      } else {
        td.innerHTML=htmlspecialchars(getLng('never'));
      }
      setCssClass(td, '.tbl_row');
      td.style.textAlign='center';
    }
  }
}


/**
 * Display "Add new banner" form
 */
function showNewBannerForm() {
  $('banners_tbl').style.display='none';
  $('new_banner_btn_row').style.display='none';
  $('new_banner_tbl').style.display='';

  $('new_banner_name').value='';
  $('new_banner_active_y').click();

  $('new_banner_source_u').onclick=function() {
    $('new_banner_source_url').style.display='';
    $('new_banner_source_custom').style.display='none';
  }
  $('new_banner_source_c').onclick=function() {
    $('new_banner_source_url').style.display='none';
    $('new_banner_source_custom').style.display='';
  }
  $('new_banner_source_c').click();

  $('new_banner_source_url_text').value='http://www.pcpin.com/';
  $('new_banner_source_custom_text').value='<html>\n<body>\n<table bgcolor=\"#DDDDDD\" border=\"0\" width=\"100%\">\n  <tr>\n    <td width=\"100%\" align=\"center\">\n      Hello, I am your banner. Pleased to meet you :)\n    </td>\n  </tr>\n</table>\n</body>\n</html>';

  $('new_banner_display_position_t').onclick=function() {
    $('new_banner_width_row').style.display='none';
    $('new_banner_height_row').style.display='none';
  }
  $('new_banner_display_position_b').onclick=function() {
    $('new_banner_width_row').style.display='none';
    $('new_banner_height_row').style.display='none';
  }
  $('new_banner_display_position_p').onclick=function() {
    $('new_banner_width_row').style.display='';
    $('new_banner_height_row').style.display='';
  }
  $('new_banner_display_position_m').onclick=function() {
    $('new_banner_width_row').style.display='';
    $('new_banner_height_row').style.display='';
  }
  $('new_banner_display_position_t').click();

  $('new_banner_max_views').value='0';

  $('new_banner_start_date_year').value=date('Y');
  $('new_banner_start_date_month').value=date('m');
  $('new_banner_start_date_day').value=date('d');
  $('new_banner_start_date_hour').value=date('H');
  $('new_banner_start_date_minute').value=date('i');

  $('new_banner_expiration_date_year').value=stringToNumber(date('Y'))+1;
  $('new_banner_expiration_date_month').value=date('m');
  $('new_banner_expiration_date_day').value=date('d');
  $('new_banner_expiration_date_hour').value=date('H');
  $('new_banner_expiration_date_minute').value=date('i');
  $('new_banner_expiration_date_never').checked=false;
  $('new_banner_expiration_date_never').onclick=function() {
    $('new_banner_expiration_date_year').disabled=this.checked;
    $('new_banner_expiration_date_month').disabled=this.checked;
    $('new_banner_expiration_date_day').disabled=this.checked;
    $('new_banner_expiration_date_hour').disabled=this.checked;
    $('new_banner_expiration_date_minute').disabled=this.checked;
  };
  $('new_banner_expiration_date_never').click();

  $('new_banner_width').value='400';
  $('new_banner_height').value='300';

  $('new_banner_name').focus();
}


/**
 * Hide "Add new banner" form
 */
function hideNewBannerForm() {
  $('banners_tbl').style.display='';
  $('new_banner_btn_row').style.display='';
  $('new_banner_tbl').style.display='none';
}


/**
 * Preview the banner
 * @param   string    src         Where are banner data stored at?
 * @param   int       banner_id   Banner ID
 */
function showBannerPreview(src, banner_id) {
  var wh=null;
  var source_code='';
  var source_url='';
  var width=0;
  var height=0;
  var top=Math.round((getWinHeight(window.parent)-height)/2);
  var left=null;

  switch (src) {

    case 'banners':
      // Banners Array
      if (typeof(banner_id)=='number' && Banners[banner_id]) {
        if (Banners[banner_id]['source_type']=='u') {
          source_url=Banners[banner_id]['source'];
        } else {
          source_code=Banners[banner_id]['source'];
        }
        if (Banners[banner_id]['display_position']=='t') {
          // Top
          left=1;
          width=getWinWidth(window.parent);
          height=top_banner_height;
        } else if (Banners[banner_id]['display_position']=='b') {
          // Bottom
          left=1;
          width=getWinWidth(window.parent);
          height=bottom_banner_height;
        } else {
          // Popup or between messages
          width=Banners[banner_id]['width'];
          height=Banners[banner_id]['height'];
        }
      }
    break;

    case 'edit_banner':
      // "Edit banner" form
      if ($('edit_banner_source_u').checked) {
        source_url=$('edit_banner_source_url_text').value;
      } else {
        source_code=$('edit_banner_source_custom_text').value;
      }
      if ($('edit_banner_display_position_t').checked) {
        width=winWidth;
        height=top_banner_height;
      } else if ($('edit_banner_display_position_b').checked) {
        width=winWidth;
        height=bottom_banner_height;
      } else {
        width=$('edit_banner_width').value;
        height=$('edit_banner_height').value;
      }
    break;

    case 'new_banner':
      // "New banner" form
      if ($('new_banner_source_u').checked) {
        source_url=$('new_banner_source_url_text').value;
      } else {
        source_code=$('new_banner_source_custom_text').value;
      }
      if ($('new_banner_display_position_t').checked) {
        width=winWidth;
        height=top_banner_height;
      } else if ($('new_banner_display_position_b').checked) {
        width=winWidth;
        height=bottom_banner_height;
      } else {
        width=$('new_banner_width').value;
        height=$('new_banner_height').value;
      }
    break;

  }

  if (source_url!='') {
    // Preview URL
    wh=openWindow(formlink+'?external_url='+urlencode(source_url),
                  'banner_preview',
                  stringToNumber(width),
                  stringToNumber(height),
                  false,
                  false,
                  false,
                  false,
                  true,
                  false,
                  false,
                  false,
                  false,
                  false,
                  left,
                  top);
  } else if (source_code!='') {
    // Preview HTML
    wh=openWindow('dummy.html',
                  'banner_preview',
                  stringToNumber(width),
                  stringToNumber(height),
                  false,
                  false,
                  false,
                  false,
                  true,
                  false,
                  false,
                  false,
                  false,
                  false,
                  left,
                  top);
    wh.document.open();
    wh.document.write(source_code);
    wh.document.close();
  }
  wh.focus();
}


/**
 * Add new banner
 */
function addNewBanner() {
  var errors=new Array();

  // Validate form
  $('new_banner_name').value=trimString($('new_banner_name').value);
  $('new_banner_source_url_text').value=trimString($('new_banner_source_url_text').value);
  $('new_banner_source_custom_text').value=trimString($('new_banner_source_custom_text').value);
  $('new_banner_max_views').value=trimString($('new_banner_max_views').value);
  $('new_banner_width').value=trimString($('new_banner_width').value);
  $('new_banner_height').value=trimString($('new_banner_height').value);

  // Name
  if ($('new_banner_name').value=='') {
    errors.push(getLng('banner_name_empty_error'));
  }

  // Width
  if (!$('new_banner_display_position_t').checked && !$('new_banner_display_position_b').checked && 0==stringToNumber($('new_banner_width').value)) {
    errors.push(getLng('width_invalid'));
  }

  // Height
  if (!$('new_banner_display_position_t').checked && !$('new_banner_display_position_b').checked && 0==stringToNumber($('new_banner_height').value)) {
    errors.push(getLng('height_invalid'));
  }

  // Max. views
  if (!isDigitString($('new_banner_max_views').value)) {
    $('new_banner_max_views').value='0';
  }

  // Start date
  $('new_banner_start_date_year').value=trimString($('new_banner_start_date_year').value);
  $('new_banner_start_date_month').value=trimString($('new_banner_start_date_month').value);
  $('new_banner_start_date_day').value=trimString($('new_banner_start_date_day').value);
  $('new_banner_start_date_hour').value=trimString($('new_banner_start_date_hour').value);
  $('new_banner_start_date_minute').value=trimString($('new_banner_start_date_minute').value);
  if (   $('new_banner_start_date_year').value=='' || !isDigitString($('new_banner_start_date_year').value)
      || $('new_banner_start_date_month').value=='' || !isDigitString($('new_banner_start_date_month').value)
      || $('new_banner_start_date_day').value=='' || !isDigitString($('new_banner_start_date_day').value)
      || $('new_banner_start_date_hour').value=='' || !isDigitString($('new_banner_start_date_hour').value)
      || $('new_banner_start_date_minute').value=='' || !isDigitString($('new_banner_start_date_minute').value)
      ) {
    errors.push(getLng('start_date_invalid'));
  }

  // Expiration date
  $('new_banner_expiration_date_year').value=trimString($('new_banner_expiration_date_year').value);
  $('new_banner_expiration_date_month').value=trimString($('new_banner_expiration_date_month').value);
  $('new_banner_expiration_date_day').value=trimString($('new_banner_expiration_date_day').value);
  $('new_banner_expiration_date_hour').value=trimString($('new_banner_expiration_date_hour').value);
  $('new_banner_expiration_date_minute').value=trimString($('new_banner_expiration_date_minute').value);
  if (   !$('new_banner_expiration_date_never').checked
      && (   $('new_banner_expiration_date_year').value=='' || !isDigitString($('new_banner_expiration_date_year').value)
          || $('new_banner_expiration_date_month').value=='' || !isDigitString($('new_banner_expiration_date_month').value)
          || $('new_banner_expiration_date_day').value=='' || !isDigitString($('new_banner_expiration_date_day').value)
          || $('new_banner_expiration_date_hour').value=='' || !isDigitString($('new_banner_expiration_date_hour').value)
          || $('new_banner_expiration_date_minute').value=='' || !isDigitString($('new_banner_expiration_date_minute').value)
          )
      ) {
    errors.push(getLng('expiration_date_invalid'));
  }

  if (errors.length>0) {
    // There are some errors
    alert('- '+errors.join("\n- "));
  } else {
    // Send data to server
    var display_position='';
    if ($('new_banner_display_position_t').checked) {
      display_position='t';
    } else if ($('new_banner_display_position_b').checked) {
      display_position='b';
    } else if ($('new_banner_display_position_p').checked) {
      display_position='p';
    } else if ($('new_banner_display_position_m').checked) {
      display_position='m';
    }
    sendData('_CALLBACK_addNewBanner()', formlink, 'POST', 'ajax=add_banner&s_id='+urlencode(s_id)
             +'&name='+urlencode($('new_banner_name').value)
             +'&active='+urlencode($('new_banner_active_y').checked? 'y' : 'n')
             +'&source_type='+urlencode($('new_banner_source_u').checked? 'u' : 'c')
             +'&source='+urlencode($('new_banner_source_u').checked? $('new_banner_source_url_text').value : $('new_banner_source_custom_text').value)
             +'&display_position='+urlencode(display_position)
             +'&max_views='+urlencode($('new_banner_max_views').value)
             +'&start_year='+urlencode($('new_banner_start_date_year').value)
             +'&start_month='+urlencode($('new_banner_start_date_month').value)
             +'&start_day='+urlencode($('new_banner_start_date_day').value)
             +'&start_hour='+urlencode($('new_banner_start_date_hour').value)
             +'&start_minute='+urlencode($('new_banner_start_date_minute').value)
             +'&expires_year='+urlencode($('new_banner_expiration_date_year').value)
             +'&expires_month='+urlencode($('new_banner_expiration_date_month').value)
             +'&expires_day='+urlencode($('new_banner_expiration_date_day').value)
             +'&expires_hour='+urlencode($('new_banner_expiration_date_hour').value)
             +'&expires_minute='+urlencode($('new_banner_expiration_date_minute').value)
             +($('new_banner_expiration_date_never').checked? '&expires_never=1' : '')
             +'&width='+urlencode($('new_banner_width').value)
             +'&height='+urlencode($('new_banner_height').value)
             );
  }
}
function _CALLBACK_addNewBanner() {
//alert(actionHandler.getResponseString()); return false;
  alert(actionHandler.message);
  toggleProgressBar(false);
  if (actionHandler.status==0) {
    hideNewBannerForm();
    getBanners();
  }
}


/**
 * Delete banner
 * @param   int       banner_id   Banner ID
 * @param   boolean   confirmed   Optional. If TRUE: no confirmation will be displayed. Default: FALSE.
 */
function deleteBanner(banner_id, confirmed) {
  if (Banners[banner_id]) {
    if (typeof(confirmed)!='boolean' || !confirmed) {
      confirm(getLng('confirm_delete_banner').split('[NAME]').join(Banners[banner_id]['name']), null, null, 'deleteBanner('+banner_id+', true)');
    } else {
      sendData('_CALLBACK_deleteBanner()', formlink, 'POST', 'ajax=delete_banner&s_id='+urlencode(s_id)+'&banner_id='+urlencode(banner_id));
    }
  }
}
function _CALLBACK_deleteBanner() {
//debug(actionHandler.getResponseString()); return false;
  if (actionHandler.status==-1) {
    // Session is invalid
    window.parent.document.location.href=formlink+'?session_timeout&ts='+unixTimeStamp();
    return false;
  } else {
    alert(actionHandler.message);
    getBanners();
  }
}


/**
 * Display "Edit banner" form
 * @param   int   id    Banner ID
 */
function showEditBannerForm(id) {
  if (Banners[id]) {
    $('banners_tbl').style.display='none';
    $('new_banner_btn_row').style.display='none';
    $('edit_banner_tbl').style.display='';
    $('edit_banner_name_title').innerHTML=htmlspecialchars(getLng('edit_banner').split('[NAME]').join(Banners[id]['name']));

    $('edit_banner_id').value=id;
    $('edit_banner_name').value=Banners[id]['name'];
    $('edit_banner_active_'+Banners[id]['active']).click();

    $('edit_banner_source_u').onclick=function() {
      $('edit_banner_source_url').style.display='';
      $('edit_banner_source_custom').style.display='none';
    }
    $('edit_banner_source_c').onclick=function() {
      $('edit_banner_source_url').style.display='none';
      $('edit_banner_source_custom').style.display='';
    }
    $('edit_banner_source_'+Banners[id]['source_type']).click();

    if (Banners[id]['source_type']=='u') {
      $('edit_banner_source_url_text').value=Banners[id]['source'];
      $('edit_banner_source_custom_text').value='';
    } else {
      $('edit_banner_source_url_text').value='';
      $('edit_banner_source_custom_text').value=Banners[id]['source'];
    }

    $('edit_banner_display_position_t').onclick=function() {
      $('edit_banner_width_row').style.display='none';
      $('edit_banner_height_row').style.display='none';
    }
    $('edit_banner_display_position_b').onclick=function() {
      $('edit_banner_width_row').style.display='none';
      $('edit_banner_height_row').style.display='none';
    }
    $('edit_banner_display_position_p').onclick=function() {
      $('edit_banner_width_row').style.display='';
      $('edit_banner_height_row').style.display='';
    }
    $('edit_banner_display_position_m').onclick=function() {
      $('edit_banner_width_row').style.display='';
      $('edit_banner_height_row').style.display='';
    }
    $('edit_banner_display_position_'+Banners[id]['display_position']).click();

    $('edit_banner_max_views').value=Banners[id]['max_views'];

    $('edit_banner_start_date_year').value=date('Y', Banners[id]['start_date']);
    $('edit_banner_start_date_month').value=date('m', Banners[id]['start_date']);
    $('edit_banner_start_date_day').value=date('d', Banners[id]['start_date']);
    $('edit_banner_start_date_hour').value=date('H', Banners[id]['start_date']);
    $('edit_banner_start_date_minute').value=date('i', Banners[id]['start_date']);

    $('edit_banner_expiration_date_never').checked=false;
    $('edit_banner_expiration_date_never').onclick=function() {
      $('edit_banner_expiration_date_year').disabled=this.checked;
      $('edit_banner_expiration_date_month').disabled=this.checked;
      $('edit_banner_expiration_date_day').disabled=this.checked;
      $('edit_banner_expiration_date_hour').disabled=this.checked;
      $('edit_banner_expiration_date_minute').disabled=this.checked;
    };
    if (Banners[id]['expiration_date']=='0') {
      $('edit_banner_expiration_date_year').value=stringToNumber(date('Y'))+1;
      $('edit_banner_expiration_date_month').value=date('m');
      $('edit_banner_expiration_date_day').value=date('d');
      $('edit_banner_expiration_date_hour').value=date('H');
      $('edit_banner_expiration_date_minute').value=date('i');
      $('edit_banner_expiration_date_never').click();
    } else {
      $('edit_banner_expiration_date_year').value=date('Y', Banners[id]['expiration_date']);
      $('edit_banner_expiration_date_month').value=date('m', Banners[id]['expiration_date']);
      $('edit_banner_expiration_date_day').value=date('d', Banners[id]['expiration_date']);
      $('edit_banner_expiration_date_hour').value=date('H', Banners[id]['expiration_date']);
      $('edit_banner_expiration_date_minute').value=date('i', Banners[id]['expiration_date']);
    }

    $('edit_banner_width').value=Banners[id]['width'];
    $('edit_banner_height').value=Banners[id]['height'];

  }
}


/**
 * Hide "Edit banner" form
 */
function hideEditBannerForm() {
  $('banners_tbl').style.display='';
  $('new_banner_btn_row').style.display='';
  $('edit_banner_tbl').style.display='none';
}


/**
 * Update banner
 */
function updateBanner() {
  var errors=new Array();

  // Validate form
  $('edit_banner_name').value=trimString($('edit_banner_name').value);
  $('edit_banner_source_url_text').value=trimString($('edit_banner_source_url_text').value);
  $('edit_banner_source_custom_text').value=trimString($('edit_banner_source_custom_text').value);
  $('edit_banner_max_views').value=trimString($('edit_banner_max_views').value);
  $('edit_banner_width').value=trimString($('edit_banner_width').value);
  $('edit_banner_height').value=trimString($('edit_banner_height').value);

  // Name
  if ($('edit_banner_name').value=='') {
    errors.push(getLng('banner_name_empty_error'));
  }

  // Width
  if (!$('edit_banner_display_position_t').checked && !$('edit_banner_display_position_b').checked && 0==stringToNumber($('edit_banner_width').value)) {
    errors.push(getLng('width_invalid'));
  }

  // Height
  if (!$('edit_banner_display_position_t').checked && !$('edit_banner_display_position_b').checked && 0==stringToNumber($('edit_banner_height').value)) {
    errors.push(getLng('height_invalid'));
  }

  // Max. views
  if (!isDigitString($('edit_banner_max_views').value)) {
    $('edit_banner_max_views').value='0';
  }

  // Start date
  $('edit_banner_start_date_year').value=trimString($('edit_banner_start_date_year').value);
  $('edit_banner_start_date_month').value=trimString($('edit_banner_start_date_month').value);
  $('edit_banner_start_date_day').value=trimString($('edit_banner_start_date_day').value);
  $('edit_banner_start_date_hour').value=trimString($('edit_banner_start_date_hour').value);
  $('edit_banner_start_date_minute').value=trimString($('edit_banner_start_date_minute').value);
  if (   $('edit_banner_start_date_year').value=='' || !isDigitString($('edit_banner_start_date_year').value)
      || $('edit_banner_start_date_month').value=='' || !isDigitString($('edit_banner_start_date_month').value)
      || $('edit_banner_start_date_day').value=='' || !isDigitString($('edit_banner_start_date_day').value)
      || $('edit_banner_start_date_hour').value=='' || !isDigitString($('edit_banner_start_date_hour').value)
      || $('edit_banner_start_date_minute').value=='' || !isDigitString($('edit_banner_start_date_minute').value)
      ) {
    errors.push(getLng('start_date_invalid'));
  }

  // Expiration date
  $('edit_banner_expiration_date_year').value=trimString($('edit_banner_expiration_date_year').value);
  $('edit_banner_expiration_date_month').value=trimString($('edit_banner_expiration_date_month').value);
  $('edit_banner_expiration_date_day').value=trimString($('edit_banner_expiration_date_day').value);
  $('edit_banner_expiration_date_hour').value=trimString($('edit_banner_expiration_date_hour').value);
  $('edit_banner_expiration_date_minute').value=trimString($('edit_banner_expiration_date_minute').value);
  if (   !$('edit_banner_expiration_date_never').checked
      && (   $('edit_banner_expiration_date_year').value=='' || !isDigitString($('edit_banner_expiration_date_year').value)
          || $('edit_banner_expiration_date_month').value=='' || !isDigitString($('edit_banner_expiration_date_month').value)
          || $('edit_banner_expiration_date_day').value=='' || !isDigitString($('edit_banner_expiration_date_day').value)
          || $('edit_banner_expiration_date_hour').value=='' || !isDigitString($('edit_banner_expiration_date_hour').value)
          || $('edit_banner_expiration_date_minute').value=='' || !isDigitString($('edit_banner_expiration_date_minute').value)
          )
      ) {
    errors.push(getLng('expiration_date_invalid'));
  }

  if (errors.length>0) {
    // There are some errors
    alert('- '+errors.join("\n- "));
  } else {
    // Send data to server
    var display_position='';
    if ($('edit_banner_display_position_t').checked) {
      display_position='t';
    } else if ($('edit_banner_display_position_b').checked) {
      display_position='b';
    } else if ($('edit_banner_display_position_p').checked) {
      display_position='p';
    } else if ($('edit_banner_display_position_m').checked) {
      display_position='m';
    }
    sendData('_CALLBACK_updateBanner()', formlink, 'POST', 'ajax=update_banner&s_id='+urlencode(s_id)
             +'&banner_id='+urlencode($('edit_banner_id').value)
             +'&name='+urlencode($('edit_banner_name').value)
             +'&active='+urlencode($('edit_banner_active_y').checked? 'y' : 'n')
             +'&source_type='+urlencode($('edit_banner_source_u').checked? 'u' : 'c')
             +'&source='+urlencode($('edit_banner_source_u').checked? $('edit_banner_source_url_text').value : $('edit_banner_source_custom_text').value)
             +'&display_position='+urlencode(display_position)
             +'&max_views='+urlencode($('edit_banner_max_views').value)
             +'&start_year='+urlencode($('edit_banner_start_date_year').value)
             +'&start_month='+urlencode($('edit_banner_start_date_month').value)
             +'&start_day='+urlencode($('edit_banner_start_date_day').value)
             +'&start_hour='+urlencode($('edit_banner_start_date_hour').value)
             +'&start_minute='+urlencode($('edit_banner_start_date_minute').value)
             +'&expires_year='+urlencode($('edit_banner_expiration_date_year').value)
             +'&expires_month='+urlencode($('edit_banner_expiration_date_month').value)
             +'&expires_day='+urlencode($('edit_banner_expiration_date_day').value)
             +'&expires_hour='+urlencode($('edit_banner_expiration_date_hour').value)
             +'&expires_minute='+urlencode($('edit_banner_expiration_date_minute').value)
             +($('edit_banner_expiration_date_never').checked? '&expires_never=1' : '')
             +'&width='+urlencode($('edit_banner_width').value)
             +'&height='+urlencode($('edit_banner_height').value)
             );
  }
}
function _CALLBACK_updateBanner() {
//alert(actionHandler.getResponseString()); return false;
  alert(actionHandler.message);
  toggleProgressBar(false);
  if (actionHandler.status==0) {
    hideEditBannerForm();
    getBanners();
  }
}
