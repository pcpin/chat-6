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

/**
 * This file contains static configuration.
 */

/**
 *
 * !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
 *              YOU DON'T NEED TO EDIT THIS FILE !
 * !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
 *
 */

/**
 * Whether to use persistent database connection or not (function "mysql_pconnect()" instead of "mysql_connect()").
 * Persistent database connection may considerably improve performance of this script.
 * Please read IMPORTANT information regarding persistent connections here:
 *  http://www.php.net/manual/en/features.persistent-connections.php
 * NOTE: If your server does not supports persistent database connections, then this setting will be ignored.
 */
define('PCPIN_DB_PERSISTENT', false);

/**
 * Path to the main index.php file as called by browser.
 * If chat works, you do not need to change this constant value.
 */
define('PCPIN_FORMLINK', './index.php');


/**
 * Path to the main admin.php file as called by browser.
 * If chat works, you do not need to change this constant value.
 */
define('PCPIN_ADMIN_FORMLINK', './admin.php');


/**
 * Activate Debug mode.
 * WARNING: Use for Development only!!!
 * If TRUE, then error_reporting will be set to 'E_ALL' and display_errors will be set to 'On'.
 */
define('PCPIN_DEBUGMODE', false);


/**
 * Filename to log PHP errors into. If not empty, then errors WILL BE NOT DISPLAYED and strored into that file.
 * NOTE: Requires activated PCPIN_DEBUGMODE
 */
define('PCPIN_ERRORLOG', './PHP_ERRORS.log');


/**
 * Activate STRICT Debug mode (PHP5 only).
 * NOTE: Requires activated PCPIN_DEBUGMODE
 * If TRUE, then error_reporting will be set to 'E_ALL|E_STRICT'.
 */
define('PCPIN_DEBUGMODE_STRICT', false);


/**
 * Calculate usage times (PHP and MySQL)?
 * NOTE: Requires activated PCPIN_DEBUGMODE
 */
define('PCPIN_LOG_TIMER', true);


/**
 * Log MySQL errors?
 * NOTE: Requires activated PCPIN_DEBUGMODE
 */
define('PCPIN_SHOW_MYSQL_ERRORS', true);


/**
 * Display/log slow MySQL queries?
 * All MySQL queries with execution time that is higher than specified
 * here value (in seconds), will be displayed/logged.
 * If value is empty, then no queries will be displayed/logged.
 * NOTE: Requires activated PCPIN_DEBUGMODE
 */
define('PCPIN_SHOW_SLOW_QUERIES', 0.1);


/**
 * Name of the file where to put SQL debug messages and errors
 * Value "*" will redirect output to client's browser
 * NOTE: If value is empty, then NO logging will be performed
 */
define('PCPIN_SQL_LOGFILE', './SQL.log');


/**
 * Session ID string length in characters
 * WARNING: Maximum length: 32 characters
 */
define('PCPIN_SID_LENGTH', 32);


/**
 * Maximum allowed room name length
 * WARNING: Maximum length: 32 characters
 */
define('PCPIN_ROOM_NAME_LENGTH_MAX', 12);


/**
 * Maximum allowed category name length
 * WARNING: Maximum length: 32 characters
 */
define('PCPIN_CATEGORY_NAME_LENGTH_MAX', 16);


/**
 * URL of version checker server
 * You don't need to change this value!
 */
define('PCPIN_VERSIONCHECKER_URL', 'https://https.pcpin.com/versions/chat/');


/**
 * Query separator for use in "Backup database" and "Restore database".
 * DO NOT CHANGE!!!
 */
define('PCPIN_SQL_QUERY_SEPARATOR', "\n\n/* \"'_PCPIN_SQL_'\" */\n\n");


/**
 * ISO-639 language codes and names
 * DO NOT CHANGE!!!
 */
define('PCPIN_ISO_LNG_AB', 'ab=Abkhazian');
define('PCPIN_ISO_LNG_OM', 'om=Afan (oromo)');
define('PCPIN_ISO_LNG_AA', 'aa=Afar');
define('PCPIN_ISO_LNG_AF', 'af=Afrikaans');
define('PCPIN_ISO_LNG_SQ', 'sq=Albanian');
define('PCPIN_ISO_LNG_AM', 'am=Amharic');
define('PCPIN_ISO_LNG_AR', 'ar=Arabic');
define('PCPIN_ISO_LNG_HY', 'hy=Armenian');
define('PCPIN_ISO_LNG_AS', 'as=Assamese');
define('PCPIN_ISO_LNG_AY', 'ay=Aymara');
define('PCPIN_ISO_LNG_AZ', 'az=Azerbaijani');
define('PCPIN_ISO_LNG_BA', 'ba=Bashkir');
define('PCPIN_ISO_LNG_EU', 'eu=Basque');
define('PCPIN_ISO_LNG_BN', 'bn=Bengali;bangla');
define('PCPIN_ISO_LNG_DZ', 'dz=Bhutani');
define('PCPIN_ISO_LNG_BH', 'bh=Bihari');
define('PCPIN_ISO_LNG_BI', 'bi=Bislama');
define('PCPIN_ISO_LNG_BR', 'br=Breton');
define('PCPIN_ISO_LNG_BG', 'bg=Bulgarian');
define('PCPIN_ISO_LNG_MY', 'my=Burmese');
define('PCPIN_ISO_LNG_BE', 'be=Byelorussian');
define('PCPIN_ISO_LNG_KM', 'km=Cambodian');
define('PCPIN_ISO_LNG_CA', 'ca=Catalan');
define('PCPIN_ISO_LNG_ZH', 'zh=Chinese');
define('PCPIN_ISO_LNG_CO', 'co=Corsican');
define('PCPIN_ISO_LNG_HR', 'hr=Croatian');
define('PCPIN_ISO_LNG_CS', 'cs=Czech');
define('PCPIN_ISO_LNG_DA', 'da=Danish');
define('PCPIN_ISO_LNG_NL', 'nl=Dutch');
define('PCPIN_ISO_LNG_EN', 'en=English');
define('PCPIN_ISO_LNG_EO', 'eo=Esperanto');
define('PCPIN_ISO_LNG_ET', 'et=Estonian');
define('PCPIN_ISO_LNG_FO', 'fo=Faroese');
define('PCPIN_ISO_LNG_FJ', 'fj=Fiji');
define('PCPIN_ISO_LNG_FI', 'fi=Finnish');
define('PCPIN_ISO_LNG_FR', 'fr=French');
define('PCPIN_ISO_LNG_FY', 'fy=Frisian');
define('PCPIN_ISO_LNG_GL', 'gl=Galician');
define('PCPIN_ISO_LNG_KA', 'ka=Georgian');
define('PCPIN_ISO_LNG_DE', 'de=German');
define('PCPIN_ISO_LNG_EL', 'el=Greek');
define('PCPIN_ISO_LNG_KL', 'kl=Greenlandic');
define('PCPIN_ISO_LNG_GN', 'gn=Guarani');
define('PCPIN_ISO_LNG_GU', 'gu=Gujarati');
define('PCPIN_ISO_LNG_HA', 'ha=Hausa');
define('PCPIN_ISO_LNG_HE', 'he=Hebrew');
define('PCPIN_ISO_LNG_HI', 'hi=Hindi');
define('PCPIN_ISO_LNG_HU', 'hu=Hungarian');
define('PCPIN_ISO_LNG_IS', 'is=Icelandic');
define('PCPIN_ISO_LNG_ID', 'id=Indonesian');
define('PCPIN_ISO_LNG_IA', 'ia=Interlingua');
define('PCPIN_ISO_LNG_IE', 'ie=Interlingue');
define('PCPIN_ISO_LNG_IU', 'iu=Inuktitut');
define('PCPIN_ISO_LNG_IK', 'ik=Inupiak');
define('PCPIN_ISO_LNG_GA', 'ga=Irish');
define('PCPIN_ISO_LNG_IT', 'it=Italian');
define('PCPIN_ISO_LNG_JA', 'ja=Japanese');
define('PCPIN_ISO_LNG_JV', 'jv=Javanese');
define('PCPIN_ISO_LNG_KN', 'kn=Kannada');
define('PCPIN_ISO_LNG_KS', 'ks=Kashmiri');
define('PCPIN_ISO_LNG_KK', 'kk=Kazakh');
define('PCPIN_ISO_LNG_RW', 'rw=Kinyarwanda');
define('PCPIN_ISO_LNG_KY', 'ky=Kirghiz');
define('PCPIN_ISO_LNG_RN', 'rn=Kurundi');
define('PCPIN_ISO_LNG_KO', 'ko=Korean');
define('PCPIN_ISO_LNG_KU', 'ku=Kurdish');
define('PCPIN_ISO_LNG_LO', 'lo=Laothian');
define('PCPIN_ISO_LNG_LA', 'la=Latin');
define('PCPIN_ISO_LNG_LV', 'lv=Latvian;lettish');
define('PCPIN_ISO_LNG_LN', 'ln=Lingala');
define('PCPIN_ISO_LNG_LT', 'lt=Lithuanian');
define('PCPIN_ISO_LNG_MK', 'mk=Macedonian');
define('PCPIN_ISO_LNG_MG', 'mg=Malagasy');
define('PCPIN_ISO_LNG_MS', 'ms=Malay');
define('PCPIN_ISO_LNG_ML', 'ml=Malayalam');
define('PCPIN_ISO_LNG_MT', 'mt=Maltese');
define('PCPIN_ISO_LNG_MI', 'mi=Maori');
define('PCPIN_ISO_LNG_MR', 'mr=Marathi');
define('PCPIN_ISO_LNG_MO', 'mo=Moldavian');
define('PCPIN_ISO_LNG_MN', 'mn=Mongolian');
define('PCPIN_ISO_LNG_NA', 'na=Nauru');
define('PCPIN_ISO_LNG_NE', 'ne=Nepali');
define('PCPIN_ISO_LNG_NO', 'no=Norwegian');
define('PCPIN_ISO_LNG_OC', 'oc=Occitan');
define('PCPIN_ISO_LNG_OR', 'or=Oriya');
define('PCPIN_ISO_LNG_PS', 'ps=Pashto;pushto');
define('PCPIN_ISO_LNG_FA', 'fa=Persian (farsi)');
define('PCPIN_ISO_LNG_PL', 'pl=Polish');
define('PCPIN_ISO_LNG_PT', 'pt=Portuguese');
define('PCPIN_ISO_LNG_PA', 'pa=Punjabi');
define('PCPIN_ISO_LNG_QU', 'qu=Quechua');
define('PCPIN_ISO_LNG_RM', 'rm=Rhaeto-romance');
define('PCPIN_ISO_LNG_RO', 'ro=Romanian');
define('PCPIN_ISO_LNG_RU', 'ru=Russian');
define('PCPIN_ISO_LNG_SM', 'sm=Samoan');
define('PCPIN_ISO_LNG_SG', 'sg=Sangho');
define('PCPIN_ISO_LNG_SA', 'sa=Sanskrit');
define('PCPIN_ISO_LNG_GD', 'gd=Scots gaelic');
define('PCPIN_ISO_LNG_SR', 'sr=Serbian');
define('PCPIN_ISO_LNG_SH', 'sh=Serbo-croatian');
define('PCPIN_ISO_LNG_ST', 'st=Sesotho');
define('PCPIN_ISO_LNG_TN', 'tn=Setswana');
define('PCPIN_ISO_LNG_SN', 'sn=Shona');
define('PCPIN_ISO_LNG_SD', 'sd=Sindhi');
define('PCPIN_ISO_LNG_SI', 'si=Singhalese');
define('PCPIN_ISO_LNG_SS', 'ss=Siswati');
define('PCPIN_ISO_LNG_SK', 'sk=Slovak');
define('PCPIN_ISO_LNG_SL', 'sl=Slovenian');
define('PCPIN_ISO_LNG_SO', 'so=Somali');
define('PCPIN_ISO_LNG_ES', 'es=Spanish');
define('PCPIN_ISO_LNG_SU', 'su=Sundanese');
define('PCPIN_ISO_LNG_SW', 'sw=Swahili');
define('PCPIN_ISO_LNG_SV', 'sv=Swedish');
define('PCPIN_ISO_LNG_TL', 'tl=Tagalog');
define('PCPIN_ISO_LNG_TG', 'tg=Tajik');
define('PCPIN_ISO_LNG_TA', 'ta=Tamil');
define('PCPIN_ISO_LNG_TT', 'tt=Tatar');
define('PCPIN_ISO_LNG_TE', 'te=Telugu');
define('PCPIN_ISO_LNG_TH', 'th=Thai');
define('PCPIN_ISO_LNG_BO', 'bo=Tibetan');
define('PCPIN_ISO_LNG_TI', 'ti=Tigrinya');
define('PCPIN_ISO_LNG_TO', 'to=Tonga');
define('PCPIN_ISO_LNG_TS', 'ts=Tsonga');
define('PCPIN_ISO_LNG_TR', 'tr=Turkish');
define('PCPIN_ISO_LNG_TK', 'tk=Turkmen');
define('PCPIN_ISO_LNG_TW', 'tw=Twi');
define('PCPIN_ISO_LNG_UG', 'ug=Uigur');
define('PCPIN_ISO_LNG_UK', 'uk=Ukrainian');
define('PCPIN_ISO_LNG_UR', 'ur=Urdu');
define('PCPIN_ISO_LNG_UZ', 'uz=Uzbek');
define('PCPIN_ISO_LNG_VI', 'vi=Vietnamese');
define('PCPIN_ISO_LNG_VO', 'vo=Volapuk');
define('PCPIN_ISO_LNG_CY', 'cy=Welsh');
define('PCPIN_ISO_LNG_WO', 'wo=Wolof');
define('PCPIN_ISO_LNG_XH', 'xh=Xhosa');
define('PCPIN_ISO_LNG_YI', 'yi=Yiddish');
define('PCPIN_ISO_LNG_YO', 'yo=Yoruba');
define('PCPIN_ISO_LNG_ZA', 'za=Zhuang');
define('PCPIN_ISO_LNG_ZU', 'zu=Zulu');

/**
 * Separator char for window title
 */
define('PCPIN_WINDOW_TITLE_SEPARATOR', chr(226).chr(128).chr(162));


/**
 * PCPIN XML: Character encoding
 */
define('PCPIN_XMLDOC_ENCODING', 'UTF-8');

/**
 * PCPIN XML: Root element name
 */
define('PCPIN_XMLDOC_ROOT_NAME', 'pcpin');

/**
 * PCPIN XML: Whether to use indentation in XML or not. Makes XML more readable, should be disabled in production environments
 */
define('PCPIN_XMLDOC_INDENT', PCPIN_DEBUGMODE);

/**
 * PCPIN XML: Indentation string
 */
define('PCPIN_XMLDOC_INDENT_STRING', '    ');


?>