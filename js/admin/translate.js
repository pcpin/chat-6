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
 * Array with currently loaded language expression objects
 * @var object
 */
var LngExpressions=new Array();

/**
 * Language expression object
 * @var object
 */
var LngExpressionObject=function(code, value, multi_row) {
  this.Code=code;
  this.Value=value;
  this.Multi_Row=multi_row;
}

/**
 * Current value of language expressions limit index
 * @var int
 */
var currentStartFrom=0;

/**
 * How many language expressions shall be displayed per page?
 * @var int
 */
var maxExpressions=20;

/**
 * Total language expressions count
 * @var int
 */
var totalExpressions=0;

/**
 * ID of currently edited language
 * @var int
 */
var currentLanguageId=0;

/**
 * How many page numbers (max) display at once
 * @var int
 */
var MaxPageNumbers=20;

/**
 * Flag: if TRUE, then some changes were made at the current page
 * @var boolean
 */
var currentPageChanges=false;



/**
 * Init window
 * @param   boolean   new_translation   If TRUE, then "Create new translation" form will be displayed
 */
function initTranslationPage(new_translation) {
  // Get languages
  getAvailableLanguages(new_translation? 'showNewTranslationPage()' : 'showStartPage()', true, true);
}


/**
 * Display start page
 */
function showStartPage() {
  hideEditPage();
  hideLanguageSelectionPage();
  hideNewTranslationPage();
  $('start_tbl').style.display='';
}


/**
 * Hide hide table
 */
function hideStartPage() {
  $('start_tbl').style.display='none';
}


/**
 * Display language selection page
 */
function showLanguageSelectionPage() {
  hideStartPage();
  var lng_sel=$('translation_select_language');
  lng_sel.options.length=0;
  for (var i in AvailableLanguages) {
    lng_sel.options[lng_sel.options.length]=new Option(AvailableLanguages[i].Name+' ('+AvailableLanguages[i].LocalName+')', AvailableLanguages[i].ID);
  }
  $('select_language_tbl').style.display='';
}


/**
 * Hide language selection table
 */
function hideLanguageSelectionPage() {
  $('select_language_tbl').style.display='none';
}


/**
 * Load language expressions of selected language and display first page
 * @param   int       language_id   Language ID
 * @param   int       start_from    Start from N-th expression
 * @param   boolean   confirmed     Optional. If TRUE: no confirmation will be displayed. Default: FALSE.
 */
function loadLngExpressions(language_id, start_from, confirmed) {
  if (typeof(language_id)=='undefined') {
    language_id=currentLanguageId;
  } else {
    language_id=stringToNumber(language_id);
  }
  if (typeof(start_from)!='number') {
    start_from=currentStartFrom;
  }
  if (currentPageChanges && (typeof(confirmed)!='boolean' || !confirmed)) {
    confirm(getLng('discard_changes_continue'), 0, 0, 'loadLngExpressions('+language_id+', '+start_from+', true)');
    return false;
  }
  currentLanguageId=language_id;
  currentStartFrom=start_from;
  $('edit_expressions_tbl').style.display='none';
  sendData('_CALLBACK_loadLngExpressions()', formlink, 'POST', 'ajax=manage_language_expressions'
                                                              +'&s_id='+urlencode(s_id)
                                                              +'&language_id='+urlencode(currentLanguageId)
                                                              +'&start_from='+urlencode(currentStartFrom)
                                                              +'&max_results='+urlencode(maxExpressions)
                                                              );
}
function _CALLBACK_loadLngExpressions() {
//debug(actionHandler.getResponseString());// return false;
  var expr=null;
  var expr_nr=0;
  var code='';

  toggleProgressBar(false);
  if (actionHandler.status==-1) {
    // Session is invalid
    window.parent.document.location.href=formlink+'?session_timeout&ts='+unixTimeStamp();
    return false;
  } else if (actionHandler.status==0) {
    LngExpressions=new Array();
    for (expr_nr=0; expr_nr<actionHandler.data['expression'].length; expr_nr++) {
      expr=actionHandler.data['expression'][expr_nr];
      code=expr['code'][0];
      if (code!='') {
        LngExpressions[code]=new LngExpressionObject(code,
                                                     expr['value'][0],
                                                     expr['multi_row'][0]
                                                     );
      }
    }
    totalExpressions=stringToNumber(actionHandler.data['expressions_total'][0]);
    showEditPage();
  } else {
    alert(actionHandler.message);
  }
}


/**
 * Display edit page
 */
function showEditPage() {
  var expr_tbl=$('edit_expressions_tbl');
  var tr=null;
  var td=null;
  var page=0;
  var total_pages=0;

  currentPageChanges=false;
  hideLanguageSelectionPage();

  // Language name
  for (var i in AvailableLanguages) {
    if (AvailableLanguages[i].ID==currentLanguageId) {
      $('edit_expressions_lng_name').innerHTML=htmlspecialchars(AvailableLanguages[i].Name+' ('+AvailableLanguages[i].LocalName+')');
      break;
    }
  }

  // Cleanup table
  while (expr_tbl.rows.length>5) {
    expr_tbl.deleteRow(expr_tbl.rows.length-3);
  }

  // Show fields
  for (var i in LngExpressions) {
    tr=expr_tbl.insertRow(expr_tbl.rows.length-2);

    // Code
    td=tr.insertCell(-1);
    td.innerHTML=htmlspecialchars(i);
    setCssClass(td, 'tbl_row');
    td.style.width='1%';
    td.style.fontSize='11px';

    // Value
    td=tr.insertCell(-1);
    if (LngExpressions[i].Multi_Row=='y') {
      // Display textarea
      td.innerHTML='<textarea id="lng_expr_'+htmlspecialchars(i)+'" rows="8" title="'+htmlspecialchars(getLng('code'))+': '+htmlspecialchars(i)+'" style="width:100%;font-size:12px;" onchange="currentPageChanges=true">'
                  +htmlspecialchars(LngExpressions[i].Value)
                  +'</textarea>';
    } else {
      // Display text input field
      td.innerHTML='<input type="text" id="lng_expr_'+htmlspecialchars(i)+'" value="'+htmlspecialchars(LngExpressions[i].Value)+'" title="'+htmlspecialchars(getLng('code'))+': '+htmlspecialchars(i)+'" style="width:100%;font-size:12px;" onchange="currentPageChanges=true" />';
    }
    setCssClass(td, 'tbl_row');

  }

  // Display page numbers
  page=Math.floor(currentStartFrom/maxExpressions)+1;
  total_pages=Math.ceil(totalExpressions/maxExpressions);
  $('page_numbers').innerHTML='';
  pages_html='<b>'+htmlspecialchars(getLng('pages'))+' ('+htmlspecialchars(total_pages)+'):</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
  // Calculate page numbers range
  pn_start=(page>=MaxPageNumbers)? page-Math.floor(MaxPageNumbers/2) : 1;
  pn_end=(pn_start+MaxPageNumbers<=total_pages)? pn_start+MaxPageNumbers : total_pages+1;
  if (pn_start>1 && pn_end-pn_start<MaxPageNumbers) {
    pn_start--;
  }
  pages_html+='<a href=":" onclick="loadLngExpressions(currentLanguageId, 0); return false;" title="'+htmlspecialchars(getLng('goto_first_page'))+'">&laquo; '+htmlspecialchars(getLng('first'))+'</a>&nbsp;&nbsp;&nbsp;&nbsp;';
  if (page>1) {
    pages_html+='<a href=":" onclick="loadLngExpressions(currentLanguageId, '+htmlspecialchars((page-2)*maxExpressions)+'); return false;" title="'+htmlspecialchars(getLng('goto_previous_page'))+'">&laquo;</a>&nbsp;&nbsp;&nbsp;&nbsp;';
  }
  for (var i=pn_start; i<pn_end; i++) {
    if (i==page) {
      pages_html+='<b title="'+htmlspecialchars(getLng('page')+' '+i)+'">['+i+']</b>&nbsp;&nbsp;&nbsp;&nbsp;';
    } else {
      pages_html+='<a href=":" onclick="loadLngExpressions(currentLanguageId, '+htmlspecialchars((i-1)*maxExpressions)+'); return false;" title="'+htmlspecialchars(getLng('page')+' '+i)+'">'+htmlspecialchars(i)+'</a>&nbsp;&nbsp;&nbsp;&nbsp;';
    }
  }
  if (page<total_pages) {
    pages_html+='<a href=":" onclick="loadLngExpressions(currentLanguageId, '+htmlspecialchars(page*maxExpressions)+'); return false;" title="'+htmlspecialchars(getLng('goto_next_page'))+'">&raquo;</a>&nbsp;&nbsp;&nbsp;&nbsp;';
    $('save_and_next_page_btn').disabled=false;
  } else {
    $('save_and_next_page_btn').disabled=true;
  }
  pages_html+='<a href=":" onclick="loadLngExpressions(currentLanguageId, '+htmlspecialchars((total_pages-1)*maxExpressions)+'); return false;" title="'+htmlspecialchars(getLng('goto_last_page'))+'">'+htmlspecialchars(getLng('last'))+' &raquo;</a>';
  $('page_numbers').innerHTML=pages_html;

  expr_tbl.style.display='';
  toggleProgressBar(false);
}


/**
 * Save contents of "Edit translation" page
 */
function saveEditTranslationPage() {
  hideEditPage();

  var req=new Array();

  for (var i in LngExpressions) {
    $('lng_expr_'+i).value=trimString($('lng_expr_'+i).value);
    req.push('update_lng_expr['+i+']='+urlencode($('lng_expr_'+i).value));
  }
  // Send data to server
  sendData('_CALLBACK_loadLngExpressions()', formlink, 'POST', 'ajax=manage_language_expressions'
                                                               +'&s_id='+urlencode(s_id)
                                                               +'&language_id='+urlencode(currentLanguageId)
                                                               +'&start_from='+urlencode(currentStartFrom)
                                                               +'&max_results='+urlencode(maxExpressions)
                                                               +'&'+req.join('&')
                                                               );
}


/**
 * Hide "Edit translation" page
 */
function hideEditPage() {
  $('edit_expressions_tbl').style.display='none';
}


/**
 * Show "Create new translation" page
 */
function showNewTranslationPage() {
  var translated_iso_names=new Array();
  var from=null;
  var to=null;

  hideStartPage();
  // Fill "Translate from" dropdown
  from=$('start_translation_translate_from');
  from.options.length=0;
  for (var i in AvailableLanguages) {
    from.options[from.options.length]=new Option(AvailableLanguages[i].Name+' ('+AvailableLanguages[i].LocalName+')', AvailableLanguages[i].ISO_Name);
    translated_iso_names[AvailableLanguages[i].ISO_Name]=1;
  }

  // Fill "Translate from" dropdown
  to=$('start_translation_translate_to');
  to.options.length=0;
  to.options[0]=new Option('--- '+getLng('please_select')+' ---', '');
  for (var i in AvailableLanguageNames) {
    if (typeof(translated_iso_names[i])=='undefined') {
      to.options[to.options.length]=new Option(AvailableLanguageNames[i].Name, AvailableLanguageNames[i].ISO_Name);
    }
  }
  
  $('start_translation_tbl').style.display='';
}


/**
 * Hide "Create new translation" page
 */
function hideNewTranslationPage() {
  $('start_translation_tbl').style.display='none';
}


/**
 * Create a language copy
 * @param   string    from    ISO name of source language
 * @param   string    to      ISO name of destination language
 */
function copyLanguage(from, to) {
  if (to=='') {
    alert(getLng('please_select_language'));
  } else {
    sendData('_CALLBACK_copyLanguage()', formlink, 'POST', 'ajax=copy_language'
                                                          +'&s_id='+urlencode(s_id)
                                                          +'&src_language='+urlencode(from)
                                                          +'&dst_language='+urlencode(to)
                                                          );
  }
}
function _CALLBACK_copyLanguage() {
//debug(actionHandler.getResponseString());// return false;
  toggleProgressBar(false);
  if (actionHandler.status==-1) {
    // Session is invalid
    window.parent.document.location.href=formlink+'?session_timeout&ts='+unixTimeStamp();
    return false;
  } else if (actionHandler.status==0) {
    // OK
    hideNewTranslationPage();
    getAvailableLanguages('loadLngExpressions('+actionHandler.data['language_id'][0]+')', true, true);
  } else {
    // An error. Should not happen...
    alert(actionHandler.message);
  }
}
