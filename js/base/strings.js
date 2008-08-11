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
 * Delete all whitespaces from the beginning and the end of the string.
 * Optional: collapse all double whitespaces within the string.
 * Optional: delete all line breaks (ASCII 0A and 0D) from the beginning and the end of the string.
 * @param   string    inputString   String to trim
 * @param   boolean   doDouble      TRUE: collapse all double whitespaces within the string
 *                                  FALSE: (default) do not collapse all double whitespaces within the string
 * @param   int       trimType      Trim type (1: Left trim, 2: Right trim, 0: Both)
 */
function trimString(inputString, doDouble, doLineBreaks, trimType) {
  if (typeof(doLineBreaks)!='boolean') {
    doLineBreaks=true;
  }
  if (typeof(trimType)!='number') {
    trimType=0;
  }
  var retValue='';
  if (typeof(inputString)=='string') {
    retValue=inputString;
    // Processing line start
    if (trimType==0 || trimType==1) {
      var oneChar=retValue.charCodeAt(0);
      while (retValue.length && (oneChar==32 || oneChar==9) || doLineBreaks && (oneChar==10 || oneChar==13)) {
        retValue=retValue.substring(1, retValue.length);
        oneChar=retValue.charCodeAt(0);
      }
    }
    // Processing line end
    if (trimType==0 || trimType==2) {
      oneChar=retValue.charCodeAt(retValue.length-1);
      while (retValue.length && (oneChar==32 || oneChar==9) || doLineBreaks && (oneChar==10 || oneChar==13)) {
        // Deleting all whitespaces at the end of the string
        retValue=retValue.substring(0, retValue.length-1);
        oneChar=retValue.charCodeAt(retValue.length-1);
      }
    }
    if (typeof(doDouble)=='boolean' && doDouble) {
      // Deleting all double whitespaces within the string
      while (retValue.indexOf('  ')>0) {
        retValue=retValue.substring(0, retValue.indexOf('  '))+retValue.substring(retValue.indexOf('  ')+1, retValue.length);
      }
    }
  }
  return retValue;
}


/**
 * BASE64 string encoder
 * @param   string    plainString     String to encode
 * @param   string    BASE64-encoded string
 */
function base64encode(plainString) {
  var encodedString='';
  var chr1='';
  var chr2='';
  var chr3='';
  var enc1='';
  var enc2='';
  var enc3='';
  var enc4='';
  var i=0;
  var base64chars='ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=';
  if (typeof(plainString)=='number') {
    plainString=numberToString(plainString);
  }
  if (plainString!='') {
    do {
      chr1=plainString.charCodeAt(i++);
      chr2=plainString.charCodeAt(i++);
      chr3=plainString.charCodeAt(i++);
      enc1=chr1 >>2;
      enc2=((chr1 &3) <<4) | (chr2 >>4);
      enc3=((chr2 &15) <<2) | (chr3 >>6);
      enc4=chr3 &63;
      if (isNaN(chr2)) {
        enc3=enc4=64;
      } else if (isNaN(chr3)) {
         enc4=64;
      }
      encodedString+=base64chars.charAt(enc1)+base64chars.charAt(enc2)+base64chars.charAt(enc3)+base64chars.charAt(enc4);
    } while (i<plainString.length);
  }
  return encodedString;
}



/**
 * Decoder for BASE64-encoded strings
 * @param   string    encodedString   BASE64-encoded string
 * @param   string    decodedString   Decoded string
 */
function base64decode(encodedString) {
  var decodedString='';
  if (typeof(encodedString)=='string' && encodedString!='') {
    encodedString=trimString(encodedString);
    var base64pattern=/[^A-Za-z0-9\+\/\=]/g;
    var base64chars='ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=';
    var chr1='';
    var chr2='';
    var chr3='';
    var enc1='';
    var enc2='';
    var enc3='';
    var enc4='';
    var i=0;
    if (!base64pattern.exec(encodedString)) {
      do {
        enc1=base64chars.indexOf(encodedString.charAt(i++));
        enc2=base64chars.indexOf(encodedString.charAt(i++));
        enc3=base64chars.indexOf(encodedString.charAt(i++));
        enc4=base64chars.indexOf(encodedString.charAt(i++));
        chr1=(enc1 <<2) |(enc2 >>4);
        chr2=((enc2 &15) <<4) |(enc3 >>2);
        chr3=((enc3 &3) <<6) |enc4;
        decodedString=decodedString+String.fromCharCode(chr1);
        if (enc3!=64) {
          decodedString=decodedString+String.fromCharCode(chr2);
        }
        if (enc4!=64) {
          decodedString=decodedString+String.fromCharCode(chr3);
        }
      } while (i<encodedString.length);
    }
  }
  return decodedString;
}


/**
 * Convert special characters to HTML entities
 * @param   string    string        Input string
 */
function htmlspecialchars(string) {
  var result='';
  if (typeof(string)=='number') {
    string=numberToString(string);
  }
  if (typeof(string)=='string' && string!='') {
    result=string.split('&').join('&amp;');
    result=result.split('"').join('&quot;');
    result=result.split("'").join('&#039;');
    result=result.split('<').join('&lt;');
    result=result.split('>').join('&gt;');
  }
  return result;
}


/**
 * Convert HTML entities into their plain text values
 * @param   string    string        Input string
 */
function htmlspecialchars_decode(string) {
  var result='';
  if (typeof(string)=='string' && string!='') {
    result=string.split('&gt;').join('>');
    result=result.split('&lt;').join('<');
    result=result.split('&#039;').join("'");
    result=result.split('&quot;').join('"');
    result=result.split('&amp;').join('&');
  }
  return result;
}


/**
 * Inserts HTML line breaks before all newlines in a string
 * @param   string    string        Input string
 * @return  string
 */
function nl2br(string) {
  var result='';
  if (typeof(string)=='string' && string!='') {
    result=string.split('\r\n').join('<br />');
    result=result.split('\r').join('<br />');
    result=result.split('\n').join('<br />');
  }
  return result;
}


/**
 * Check wether string contains digits only
 * @param   string    str       Input string
 * @return  boolean
 */
function isDigitString(str) {
  var result=false;
  var digits='0123456789';
  if (typeof(str)!='undefined') {
    if (typeof(str)!='string') {
      str=str.toString();
    }
    try {
      if (str!='') {
        result=true;
        for (var i=0; i<str.length; i++) {
          if (-1==digits.indexOf(str.charAt(i))) {
            // Invalid character
            result=false;
            break;
          }
        }
      }
    } catch(e) { }
  }
  return result;
}


/**
 * Check wether string contains alphanumeric characters only
 * @param   string    str       Input string
 * @return  boolean
 */
function isAlphaNumString(str) {
  var reg=new RegExp(/^[A-Za-z0-9]+$/);
  return null!=str.match(reg);
}


/**
 * Convert string into number
 * The string will be recognized as number if it has following format:
 *      (+|-)*(digit)*[.](digit)*
 * If string has an incorrect format, then 0 will be returned.
 *
 * @param   string    str       Input string
 * @return  number
 */
function stringToNumber(str) {
  if (typeof(str)=='string') {
    var result=0;
    var reg=new RegExp(/^[+-]*([0-9]+(\.)?[0-9]*)|([0-9]*(\.)?[0-9]+)$/);
    str=trimString(str);
    if (null!=str.match(reg)) {
      try {
        eval('result='+str+';');
      } catch (e) {
        result=0;
      }
    }
    return result;
  } else if (typeof(str)=='number') {
    // It is number
    return !isNaN(str)? str : 0;
  } else {
    return 0;
  }
}


/**
 * Convert number into string using specified decimal comma
 * @param   number    Number to convert
 * @param   string    decComma  Decimal comma. Default: '.'
 * @return  string
 */
function numberToString(num, decComma) {
  var result=num;
  if (typeof(decComma)!='string' || decComma=='') {
    decComma='.';
  }
  if (typeof(num)=='number') {
    result=num.toString().split('.').join(decComma);
  }
  return result;
}


/**
 * URL-encodes a string
 * @param   string    plainString     String to encode
 * @return  string    URL-encoded string
 */
function urlencode(plainString) {
  var safeChars="0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz-_.!~*'()";
  var encodedString='';
  var c='';
  var hc='';
  var hcc='';
  if (typeof(plainString)=='number') {
    plainString=numberToString(plainString);
  }
  if (typeof(plainString)=='string' && plainString!='') {
    plainString=utf8encode(plainString);
    for (var i=0; i<plainString.length; i++) {
      c=plainString.charAt(i);
      if (-1==safeChars.indexOf(c)) {
        hc=decHex(c.charCodeAt(0)).toUpperCase();
        if (hc.length==1) {
          hc='0'+hc;
        }
        encodedString+='%'+hc.toUpperCase();
      } else {
        encodedString+=c;
      }
    }
  }
  return encodedString;
}

/**
 * URL-decodes a string
 * @param   string    encoded     String to decode
 * @return  string    Decoded string
 */
function urldecode(encoded) {
  return unescape(encoded);
}


/**
 * This function encodes the string data to UTF-8, and returns the encoded version
 * @param   string  str   String to encode
 * @return  string  Encoded string
 */
function utf8encode(str) {
  var encoded='';
  var cc=0;
  for (var i=0; i<str.length; i++) {
    cc=str.charCodeAt(i);
    if (cc<128) {
      // An ASCII char
      // Does not to be encoded
      encoded+=String.fromCharCode(cc);
    } else if (cc>127 && cc<2048) {
      // 7-11 bits
      encoded+=String.fromCharCode((cc >>6) |192);
      encoded+=String.fromCharCode((cc &63) |128);
    } else {
      // 16-21 bits
      encoded+=String.fromCharCode((cc >>12) | 224);
      encoded+=String.fromCharCode(((cc >>6) &63) |128);
      encoded+=String.fromCharCode((cc &63) |128);
    }
  }
  return encoded;
}


/**
 * Decodes a string encoded with UTF-8
 * @param   string  encoded   UTF-8 encoded string
 * @return  string  Decoded string
 */
function utf8decode(encoded) {
  var decoded='';
  var i=0;
  var c0=0;
  var c1=0;
  var c2=0;
  while (i<encoded.length) {
    c0=encoded.charCodeAt(i);
    if (c0<128) {
      // ASCII char (not encoded)
      decoded+=String.fromCharCode(c0);
      i++;
    } else if (c0>191 && c0<224) {
      // Two-bytes UTF8 char
      c1=encoded.charCodeAt(i+1);
      decoded+=String.fromCharCode(((c0 &31) <<6) |(c1 &63));
      i+=2;
    } else {
      // Three-bytes UTF8 char
      c1=encoded.charCodeAt(i+1);
      c2=encoded.charCodeAt(i+2);
      decoded+=String.fromCharCode(((c0 &15) <<12) |((c1 &63) <<6) |(c2 &63));
      i+=3;
    }
  }
  return decoded;
}


/**
 * Pad a string to a certain length with another string.
 * This functions returns the input string padded on the left, the right,
 * or both sides to the specified padding length.
 * If the optional argument pad_string is not supplied, the input is padded with spaces,
 * otherwise it is padded with characters from pad_string up to the limit.
 *
 * Note: The pad_string may be truncated if the required number of padding
 * characters can't be evenly divided by the pad_string's length
 *
 * @param   string    input         Input string
 * @param   int       pad_length    Padding length
 * @param   string    pad_string    String to pad with. Default: ' ' (space)
 * @param   int       pad_type      Pad type (0: Pad left, 1: Pad right, 2: Pad both). Default: 1
 * @return  string    Formatted date/time
 */
var STR_PAD_LEFT  =0;
var STR_PAD_RIGHT =1;
var STR_PAD_BOTH  =2;
function str_pad(input, pad_length, pad_string, pad_type) {
  if (typeof(input)=='string' || typeof(input)=='number') {
    if (typeof(input)=='number') {
      input=input.toString();
    }
    if (typeof(pad_string)=='number') {
      pad_string=pad_string.toString();
    }
    if (input!='' && input.length<pad_length && typeof(pad_string)=='string' && pad_string!='') {
      var toAdd=pad_length-input.length;
      // Truncate pad_string?
      while (toAdd%pad_string.length!=0) {
        pad_string=pad_string.substring(0, pad_string.length-1);
      }
      if (typeof(pad_type)!='number') {
        pad_type=STR_PAD_RIGHT;
      }
      switch(pad_type) {
        case  0 :   // Left
                    do {
                      input=pad_string+input;
                    } while (input.length<pad_length);
                    break;
        default :
        case  1 :   // Right
                    do {
                      input+=pad_string;
                    } while (input.length<pad_length);
                    break;
        case  2 :   // Both
                    do {
                      input=pad_string+input+pad_string;
                    } while (input.length<pad_length);
                    break;
      }
    }
  }
  return input;
}


/**
 * Convert an integer to hex
 * @param   int       num         Number to convert
 * @param   int       pad_len     Desired string length
 * @return  string    Hex-representation
 */
function decHex(num, pad_len) {
  var hex='';
  if (typeof(num)=='string') {
    num=stringToNumber(num);
  }
  if (typeof(num)=='number') {
    var hexChars='0123456789ABCDEF';
    var hex=hexChars.substr(num &15, 1);
    while (num>15) {
      num>>=4;
      hex=hexChars.substr(num &15, 1)+hex;
    }
    if (typeof(pad_len)=='number' && pad_len>hex.length) {
      hex=str_pad(hex, pad_len, '0', STR_PAD_LEFT);
    }
  }
  return hex;
}


/**
 * Convert hex to an integer
 * @param   string    hex     Hex-number
 * @return  int
 */
function hexDec(hex) {
  var num=0;
  try {
    num=parseInt(hex, 16);
  } catch(e) {}
  return num;
}


/**
 * Returns a string with backslashes before characters that need to be quoted.
 * These characters are single quote ('), double quote (") and backslash (\)
 * @param   string    str   String to escape
 * @return  string
 */
function addSlashes(str) {
  str=str.split('\\').join('\\\\');
  str=str.split('\'').join('\\\'');
  str=str.split('"').join('\\"');
  return str;
}


/**
 * Check email address
 * @param   string    email     Email address to check
 * @return  boolean   TRUE, if email address is well-formed, FALSE if not.
 */
function checkEmail(email) {
  var result=false;
  var reg=new RegExp(/^([a-zA-Z0-9]+[\._-]?)+[a-zA-Z0-9]+@(((([a-zA-Z0-9]+-?)+[a-zA-Z0-9]+)|([a-zA-Z0-9]{2,}))+\.)+[a-zA-Z]{2,4}$/);
  if (typeof(email)=='string' && email!='') {
    result=(null!=email.match(reg));
  }
  return result;
}
