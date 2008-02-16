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
 * name of the root XML element
 * @var string
 */
var rootXmlElementName='pcpin_xml';

/**
 * XMLHttpRequest handler array
 * @var object
 */
var reqHandler=new Array();
var reqHandlerLen=0;

/**
 * HTTP response status code
 * @var object
 */
var reqHttpStatusCode=new Array();

/**
 * HTTP response status text
 * @var object
 */
var reqHttpStatusText=new Array();

/**
 * HTTP response headers
 * @var object
 */
var reqHttpHeaders=new Array();

/**
 * HTTP response data as complete string
 * @var object
 */
var reqResponseText=new Array();

/**
 * HTTP response data as XML DOM object
 * @var object
 */
var reqResponseXML=new Array();

/**
 * The function name to call after request is completed
 * @var object
 */
var reqCallBackFunc=new Array();


/**
 * XMLHttpRequest.readyState change handler
 * Stores the HTTP response code returned by last transaction
 * @param   int       rn          Number of XMLHttpRequest handler in global reqHandler array
 * @param   boolean   useState    If false, no action will be performed but status monitor change
 */
function reqStateHandlerTpl(rn, useState) {
  var rh=reqHandler[rn];
  switch (rh.readyState) {
    case 0 :  // Uninitialised
              break;
    case 1 :  // Loading
              // Update connection status
//              updateConnectionStatus(2, useState);
              break;
    case 2 :  // Headers loaded
              break;
    case 3 :  // Interactive
              break;
    case 4 :  // Completed
              // Update connection status
//              updateConnectionStatus(0, useState);
              if (useState) {
                reqHttpStatusCode[rn]=rh.status;
                reqHttpStatusText[rn]=rh.statusText;
                reqHttpHeaders[rn]=rh.getAllResponseHeaders();
                reqResponseText[rn]=rh.responseText;
                // Is there already parsed DOMXML object?
                try {
                  if (rh.responseXML.firstChild==null) {
                    // XML object is not created or not parsed
                    // Throw an exception
                    throw(1);
                  } else {
                    // Success
                    reqResponseXML[rn]=rh.responseXML;
                  }
                }catch (e) {
                  try {
                    // Trying to create "MSXML2.DOMDocument" ActiveX object
                    reqResponseXML[rn]=new ActiveXObject('MSXML2.DOMDocument');
                    reqResponseXML[rn].async='false';
                    reqResponseXML[rn].loadXML(reqResponseText[rn]);
                  } catch (e) {
                    try {
                      // Trying "Microsoft.XMLDOM" ActiveX object
                      reqResponseXML[rn]=new ActiveXObject('Microsoft.XMLDOM');
                      reqResponseXML[rn].async='false';
                      reqResponseXML[rn].loadXML(reqResponseText[rn]);
                    } catch (e) {
                      // Failed to initialize DOMXML object
                      reqResponseXML[rn]=null;
                    }
                  }
                }
                if (typeof(reqCallBackFunc[rn])=='string' && reqCallBackFunc[rn]!='') {
                  // Execute callback function
                  eval(reqCallBackFunc[rn]);
                }
              }
              break;
  }
}






function PCPIN_XmlHttpRequest() {

  /**
   * Number of XMLHttpRequest handler in global reqHandler array
   * @var int
   */
  this.reqHandlerNr=reqHandlerLen++;

  /**
   * Activate the connection and make the request
   * @param   string      callBackFunc    Function name to call after completing the request.
   *                                      If not empty, then request will be executed in asynchronous mode,
   *                                      otherwise, the synchronous mode will be used.
   * @param   string      method          HTTP request method (GET, POST ...)
   * @param   string      reqUrl          Request URL
   * @param   string      overrideMime    If not empty, then overrides the mime type returned by the server
   * @param   string      data            Data string to send
   * @param   boolean     doSync          If TRUE, then the request will be executed in synchronous mode,
   *                                      undependant on callBackFunc parameter. Default is FALSE
   */
  this.sendXmlHttpRequest=function(callBackFunc, method, reqUrl, overrideMime, data, doSync) {
    var http_user=HttpAuthUser;
    var http_pass=HttpAuthPass;
    var requestType;
    if (typeof(doSync)!='boolean') {
      doSync=false;
    }

    reqHandler[this.reqHandlerNr]=null;
    reqHttpStatusCode[this.reqHandlerNr]=0;
    reqHttpStatusText[this.reqHandlerNr]='';
    reqHttpHeaders[this.reqHandlerNr]='';
    reqResponseText[this.reqHandlerNr]='';
    reqResponseXML[this.reqHandlerNr]=null;

    if (typeof(callBackFunc)=='string' && callBackFunc!='') {
      requestType=true; // asynchronous request mode
      reqCallBackFunc[this.reqHandlerNr]=callBackFunc;
    } else {
      requestType=false; // synchronous request mode
      reqCallBackFunc[this.reqHandlerNr]='';
    }
    if (doSync) {
      requestType=false;
    }
    // Set defaults
    if (typeof(http_user)!='string') {
      http_user='';
    }
    if (typeof(http_pass)!='string') {
      http_pass='';
    }
    // Request type
    // Initialize XMLHttpRequest engine
    reqHandler[this.reqHandlerNr]=this.XMLHttpRequestInit();
    if (reqHandler[this.reqHandlerNr]!=null) {
      if (typeof(method)!='string') {
        method='GET';
      } else {
        method=method.toUpperCase();
      }
      if (typeof(data)!='string' || data=='') {
        data=null;
      }
      if (typeof(overrideMime)!='string') {
        overrideMime='';
      }
      if (this.openConnection(method, reqUrl, overrideMime, requestType, http_user, http_pass)) {
        // POST method requires "Content-Type" header to be set
        if (method=='POST') {
          reqHandler[this.reqHandlerNr].setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        }
        // Update connection status
//        updateConnectionStatus(1, true);
        // Send data
        reqHandler[this.reqHandlerNr].send(data);
      }
    }
  }


  /**
   * Initialize XMLHttpRequest object
   * @return  mixed XMLHttpRequest object on success or null on error
   */
  this.XMLHttpRequestInit=function(){
    var req=null;
    if (isIE) {
      // IE
      // Trying to initialize an ActiveX object
      try {
        // Newer Microsoft XMLHttpRequest object
        req=new ActiveXObject('Msxml2.XMLHTTP');
      } catch (e) {
        try {
          // Older Microsoft XMLHttpRequest object
          req=new ActiveXObject('Microsoft.XMLHTTP');
        } catch (e) {
          // Failed to initialize XMLHttpRequest object
          return null;
        }
      }
    } else {
      // Not IE
      try {
        // Trying to initialize native XMLHttpRequest object
        req=new XMLHttpRequest();
      } catch (e) { }
    }
    // Define ReadyState trigger handler
    if (isIE) {
      try {
        // Internet Explorer only
        eval('req.onreadystatechange=function() { reqStateHandlerTpl('+this.reqHandlerNr+', true); return true; }');
      } catch (e) { }
    } else {
      // Non-IE browsers
      try {
        eval('req.onprogress=function() { reqStateHandlerTpl('+this.reqHandlerNr+', true); return true; }');
        eval('req.onload=function() { reqStateHandlerTpl('+this.reqHandlerNr+', true); return true; }');
        eval('req.onreadystatechange=function() { reqStateHandlerTpl('+this.reqHandlerNr+', false); return true; }');
      } catch (e) {
        // Could not set event handler.
        req=null;
      }
    }
    return req;
  }


  /**
   * Initialise a connection
   * @param   string      method          Connection method (POST, GET ...)
   * @param   string      reqUrl          URL to connect (absolute or relative)
   * @param   string      overrideMime    If not empty, then overrides the mime type returned by the server
   * @param   boolean     request_type    Whether the request is made asynchronously (true, the default)
   *                                      or synchronously (false)
   * @param   string      http_user       Username to use in HTTP authentication
   * @param   string      http_pass       Password to use in HTTP authentication
   * @return  boolen      true on success or false on error
   */
  this.openConnection=function(method, reqUrl, overrideMime, request_type, http_user, http_pass) {
    // Set defaults
    if (typeof(request_type)=='undefined' || request_type!=true && request_type!=false) {
      request_type=true; // Asynchronous connection
    }
    if (typeof(http_user)!='string') {
      http_user='';
    }
    if (typeof(http_pass)!='string') {
      http_pass='';
    }
    // Initialize a connection
    try {
      if (http_user=='') {
        // Do not use HTTP authentication
        reqHandler[this.reqHandlerNr].open(method, reqUrl, request_type);
      } else {
        // Connect using HTTP authentication
        reqHandler[this.reqHandlerNr].open(method, reqUrl, request_type, http_user, http_pass);
      }
    } catch (e) {
      // An error occured
      return false;
    }
    if (typeof(overrideMime)=='string' && overrideMime!='') {
      try {
        // Override the mime type returned by the server
        reqHandler[this.reqHandlerNr].overrideMimeType(overrideMime);
      } catch (e) {}
    }
    return true;
  }


  /**
   * Send data
   * @param   string    callbackFunc    CallBack function
   * @param   string    method          HTTP-Method: "GET" or "POST". Default: "POST"
   * @param   string    url             URL (with GET parameters, if any)
   * @param   string    data            POST data: "var_name=value&var_name=value..."
   *                                    IMPORTANT: value must be urlencoded; use existing function urlencode()
   * @param   boolean   doSync          If TRUE, then the request will be executed in synchronous mode,
   *                                    undependant on callBackFunc parameter. Default is TRUE.
   */
  this.sendData=function(callbackFunc, method, url, data, doSync) {
    if (typeof(method)!='string' || method!='POST' || method!='GET') {
      method='POST';
    }
    if (typeof(data)!='string' || method=='GET') {
      data='';
    }
    if (typeof(doSync)!='boolean') {
      doSync=true;
    }
    if(typeof(url)=='string' && url!='') {
      this.sendXmlHttpRequest(callbackFunc, method, url, 'text/xml', data, doSync);
    }
  }


  /**
   * Get element
   * @param   string    elementName     Element name
   * @param   int       elementNr       Element number
   * @param   object    parentElement   Optional: parent element; if empty, then XML Response root element will be assumed
   * @return  object
   */
  this.getElement=function(elementName, elementNr, parentElement) {
    var Element=null;
    if (typeof(elementName)=='string' && elementName!='') {
      if (typeof(elementNr)!='number') {
        elementNr=0;
      }
      try {
        if (typeof(parentElement)!='object' || parentElement==null) {
          parentElement=reqResponseXML[this.reqHandlerNr].getElementsByTagName(rootXmlElementName)[0];
        }
        for (var i=0; i<parentElement.childNodes.length; i++) {
          if (parentElement.childNodes[i].nodeType==1 && parentElement.childNodes[i].nodeName==elementName) {
            if (elementNr==0) {
              Element=parentElement.childNodes[i];
              break;
            } else {
              elementNr--;
            }
          }
        }
      } catch (e) {
        Element=null;
      }
    }
    return Element;
  }


  /**
   * Get element's CDATA value
   * @param   string  elementName     Element name
   * @param   int     elementNr       Element number
   * @param   object  parentElement   Optional: parent element; if empty, then XML Response root element will be assumed
   * @param   string  defaultValue    Optional: if element does not exists or has no CDATA, then this value will be returned
   * @return  string  CDATA or NULL if requested element does not exists
   */
  this.getCdata=function(elementName, elementNr, parentElement, defaultValue) {
    var Cdata=null;
    if (typeof(elementName)=='string' && elementName!='') {
      if (typeof(elementNr)!='number') {
        elementNr=0;
      }
      try {
        if (typeof(parentElement)!='object' || parentElement==null) {
          parentElement=reqResponseXML[this.reqHandlerNr].getElementsByTagName(rootXmlElementName)[0];
        }
        var myElement=this.getElement(elementName, elementNr, parentElement);
        for (var i=0; i<myElement.childNodes.length; i++) {
          if (myElement.childNodes[i].nodeType==3 || myElement.childNodes[i].nodeType==4) {
            if (Cdata!=null) {
              Cdata+=myElement.childNodes[i].nodeValue;
            } else {
              Cdata=myElement.childNodes[i].nodeValue;
            }
          }
        }
      } catch (e) {
        Cdata=null;
      }
    }
    if (Cdata==null && typeof(defaultValue)=='string') {
      Cdata=defaultValue;
    }
    return Cdata;
  }

  /**
   * Count elements
   * @param   string  elementName     Element name
   * @param   object  parentElement   Optional: parent element; if empty, then XML Response root element will be assumed
   * @return  int
   */
  this.countElements=function(elementName, parentElement) {
    elementsCount=0;
    try {
      if (typeof(parentElement)!='object' || parentElement==null) {
        parentElement=reqResponseXML[this.reqHandlerNr].getElementsByTagName(rootXmlElementName)[0];
      }
      for (var i=0; i<parentElement.childNodes.length; i++) {
        if (parentElement.childNodes[i].nodeType==1 && parentElement.childNodes[i].nodeName==elementName) {
          elementsCount++;
        }
      }
    } catch (e) {}
    return elementsCount;
  }

  /**
   * Get complete XML response object
   * @return   object   XML object
   */
  this.getXML=function() {
    try {
      return reqResponseXML[this.reqHandlerNr];
    } catch (e) {
      return null;
    }
  }


  /**
   * Get complete response as string
   * @return   string   Server response
   */
  this.getResponseString=function() {
    try {
      return reqResponseText[this.reqHandlerNr];
    } catch (e) {
      return '';
    }
  }



}