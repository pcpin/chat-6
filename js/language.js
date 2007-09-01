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
 * Language objects array
 * @var object
 */
var AvailableLanguages=new Array();

/**
 * Language name objects array
 * @var object
 */
var AvailableLanguageNames=new Array();


/**
 * Language object
 * @var object
 */
var LanguageObject=function(id, iso_name, name, local_name, active) {
  this.ID=stringToNumber(id);
  this.ISO_Name=iso_name;
  this.Name=name;
  this.LocalName=local_name;
  this.Active=active;
}

/**
 * Language name object
 * @var object
 */
var LanguageNameObject=function(iso_name, name) {
  this.ISO_Name=iso_name;
  this.Name=name;
}

/**
 * Function name and args to call after languages were loaded
 * @var string
 */
var GetLanguagesCallback='';


/**
 * Get available languages
 * @param   string    callback    Optional. Callback function name and args.
 * @param   boolean   all         Optional. If TRUE and called by admin, then inactive languages wil be also listed
 * @param   boolean   names       Optional. If TRUE and called by admin, then all known language ISO-names wil be also listed
 */
function getAvailableLanguages(callback, all, names) {
  AvailableLanguages=new Array();
  AvailableLanguageNames=new Array();
  if (typeof(callback)=='string' && callback!='') {
    GetLanguagesCallback=callback;
  } else {
    GetLanguagesCallback='';
  }
  sendData('_CALLBACK_getAvailableLanguages()', formlink, 'POST', 'ajax='+urlencode('get_languages')+'&s_id='+urlencode(s_id)+((typeof(all)=='boolean' && all)? '&all_languages=1' : '')+((typeof(names)=='boolean' && names)? '&get_iso_names=1' : ''));
}
function _CALLBACK_getAvailableLanguages() {
//debug(actionHandler.getResponseString()); return false;
  var message=actionHandler.getCdata('message');
  var status=actionHandler.getCdata('status');

  var language=null;
  var language_nr=0;
  var lang_obj=null;

  var language_name=null;
  var language_name_nr=0;
  var language_name_obj=null;

  if (status=='-1') {
    // Session is invalid
    window.parent.document.location.href=formlink+'?session_timeout&ts='+unixTimeStamp();
    return false;
  } else {
    if (message=='OK') {
      while (null!=(language=actionHandler.getElement('language', language_nr++))) {
        lang_obj=new LanguageObject(actionHandler.getCdata('id', 0, language),
                                    actionHandler.getCdata('iso_name', 0, language),
                                    actionHandler.getCdata('name', 0, language),
                                    actionHandler.getCdata('local_name', 0, language),
                                    actionHandler.getCdata('active', 0, language)
                                    );
        AvailableLanguages.push(lang_obj);
      }
      while (null!=(language_name=actionHandler.getElement('language_name', language_name_nr++))) {
        lang_name_obj=new LanguageNameObject(actionHandler.getCdata('iso_name', 0, language_name),
                                             actionHandler.getCdata('name', 0, language_name)
                                            );
        AvailableLanguageNames[lang_name_obj.ISO_Name]=lang_name_obj;
      }
    }
  }
  toggleProgressBar(false);
  if (GetLanguagesCallback!='') {
    try {
      eval(GetLanguagesCallback);
    } catch (e) {}
  }
}


