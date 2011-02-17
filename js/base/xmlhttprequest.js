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
var rootXmlElementName='pcpin';


/**
 * Array containing XmlHttpRequest objects
 * @var array
 */
var XmlHttpRequestObjects=new Array();

/**
 * Array containing PCPIN XmlHttpRequest objects
 * @var array
 */
var PCPIN_XmlHttpRequestObjects=new Array();



/**
 * PCPIN XmlHttpRequest object core
 * @param   string      http_user       Optional. Username to use in HTTP authentication
 * @param   string      http_pass       Optional. Password to use in HTTP authentication
 */
function PCPIN_XmlHttpRequest(http_user, http_pass) {

  /**
   * Index for XmlHttpRequestObjects and PCPIN_XmlHttpRequestObjects arrays
   * @var int
   */
  PCPIN_XmlHttpRequestObjects.push(this);
  this.index=PCPIN_XmlHttpRequestObjects.length-1;

  /**
   * XMLHttpRequest handler
   * @var object
   */
  XmlHttpRequestObjects[this.index]=null;

  /**
   * HTTP authentication username
   * @var string
   */
  this.HttpAuthUser=typeof(http_user)=='string'? http_user : '';

  /**
   * HTTP authentication username
   * @var string
   */
  this.HttpAuthPass=typeof(http_pass)=='string'? http_pass : '';

  /**
   * HTTP response status code
   * @var int
   */
  this.HttpStatusCode=0;

  /**
   * HTTP response status text
   * @var string
   */
  this.HttpStatusText='';

  /**
   * HTTP response headers
   * @var string
   */
  this.HttpHeaders='';

  /**
   * HTTP response data as complete string
   * @var string
   */
  this.ResponseText='';

  /**
   * HTTP response data as XML DOM object
   * @var object
   */
  this.ResponseXML=null;

  /**
   * The function name to call after request is completed
   * @var string
   */
  this.CallBackFunc='';

  /**
   * Service name parsed from XML response
   * @var string
   */
  this.service='';

  /**
   * Status parsed from XML response
   * @var int
   */
  this.status=0;

  /**
   * Status message parsed from XML response
   * @var string
   */
  this.message='';

  /**
   * Data parsed from XML response
   * @var array
   */
  this.data=new Array();


  /**
   * Reset XML response data
   */
  this.resetResponseData=function() {
    if (XmlHttpRequestObjects[this.index]!=null) {
      try {
        XmlHttpRequestObjects[this.index].abort();
      } catch (e) {}
    }
    this.createXMLHttpRequestObject();
    this.HttpStatusCode=0;
    this.HttpStatusText='';
    this.HttpHeaders='';
    this.ResponseText='';
    this.ResponseXML=null;
    this.service='';
    this.status=-1;
    this.message='Error: Invalid XML received!';
    this.data=new Array();
  }


  /**
   * Create new XMLHttpRequest object
   */
  this.createXMLHttpRequestObject=function() {
    // Initialize XMLHttpRequest engine
    XmlHttpRequestObjects[this.index]=null;
    // Trying to initialize an ActiveX object
    try {
      // Newer Microsoft XMLHttpRequest object
      XmlHttpRequestObjects[this.index]=new ActiveXObject('Msxml2.XMLHTTP');
    } catch (e) {
      XmlHttpRequestObjects[this.index]=null;
      try {
        // Older Microsoft XMLHttpRequest object
        XmlHttpRequestObjects[this.index]=new ActiveXObject('Microsoft.XMLHTTP');
      } catch (e) {
        XmlHttpRequestObjects[this.index]=null;
        try {
          // Trying to initialize native XMLHttpRequest object
          XmlHttpRequestObjects[this.index]=new XMLHttpRequest();
        } catch (e) {
          XmlHttpRequestObjects[this.index]=null;
        }
      }
    }
    // Define ReadyState trigger handler
    if (XmlHttpRequestObjects[this.index]!=null) {
      if (isIE) {
        try {
          // Internet Explorer only
          eval('XmlHttpRequestObjects['+this.index+'].onreadystatechange=function() { PCPIN_XmlHttpRequestObjects['+this.index+'].readyStateHandler('+this.index+', true); return true; }');
        } catch (e) {
          XmlHttpRequestObjects[this.index]=null;
        }
      } else {
        // Non-IE browsers
        try {
          eval('XmlHttpRequestObjects['+this.index+'].onprogress=function() { PCPIN_XmlHttpRequestObjects['+this.index+'].readyStateHandler('+this.index+', true); return true; }');
          eval('XmlHttpRequestObjects['+this.index+'].onload=function() { PCPIN_XmlHttpRequestObjects['+this.index+'].readyStateHandler('+this.index+', true); return true; }');
          eval('XmlHttpRequestObjects['+this.index+'].onreadystatechange=function() { PCPIN_XmlHttpRequestObjects['+this.index+'].readyStateHandler('+this.index+', false); return true; }');
        } catch (e) {
          // Could not set event handler.
          XmlHttpRequestObjects[this.index]=null;
        }
      }
    }
  }


  /**
   * Activate the connection and make the request
   * @param   string      callBackFunc    Function name to call after completing the request.
   *                                      If not empty, then request will be executed in asynchronous mode (doSync argument will be ignored),
   *                                      otherwise, the mode will be set based on doSync argument value.
   * @param   string      method          HTTP request method (GET, POST ...). Default is GET.
   * @param   string      reqUrl          Request URL
   * @param   string      overrideMime    If not empty, then overrides the mime type returned by the server
   * @param   string      data            Data string to send
   * @param   boolean     doSync          If TRUE, then the request will be executed in synchronous mode,
   *                                      undependant on callBackFunc parameter. Default is FALSE
   */
  this.sendXmlHttpRequest=function(callBackFunc, method, reqUrl, overrideMime, data, doSync) {
    // Reset response data
    this.resetResponseData();
    var requestType;
    if (typeof(doSync)!='boolean') {
      doSync=false;
    }
    if (typeof(callBackFunc)=='string' && callBackFunc!='') {
      requestType=true; // asynchronous request mode
      this.CallBackFunc=callBackFunc;
    } else {
      requestType=false; // synchronous request mode
      this.CallBackFunc='';
    }
    if (doSync) {
      requestType=false;
    }
    if (XmlHttpRequestObjects[this.index]!=null) {
      if (typeof(method)!='string' || method=='') {
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
      if (this.openConnection(method, reqUrl, overrideMime, requestType)) {
        // POST method requires "Content-Type" header to be set
        if (method=='POST') {
          XmlHttpRequestObjects[this.index].setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        }
        // Update connection status
        updateConnectionStatus(1, true);
        // Send data
        XmlHttpRequestObjects[this.index].send(data);
      }
    }
  }


  /**
   * Initialise a connection
   * @param   string      method          Connection method (POST, GET ...)
   * @param   string      reqUrl          URL to connect (absolute or relative)
   * @param   string      overrideMime    If not empty, then overrides the mime type returned by the server
   * @param   boolean     request_type    Whether the request is made asynchronously (true, the default)
   *                                      or synchronously (false)
   * @return  boolen      true on success or false on error
   */
  this.openConnection=function(method, reqUrl, overrideMime, request_type) {
    // Set defaults
    if (typeof(request_type)!='boolean') {
      var request_type=true; // Asynchronous connection
    }
    // Initialize a connection
    try {
      if (typeof(this.HttpAuthPass)!='string' || this.HttpAuthUser=='') {
        // Do not use HTTP authentication
        XmlHttpRequestObjects[this.index].open(method, reqUrl, request_type);
      } else {
        // Connect using HTTP authentication
        XmlHttpRequestObjects[this.index].open(method, reqUrl, request_type, this.HttpAuthUser, this.HttpAuthPass);
      }
    } catch (e) {
      // An error occured
      return false;
    }
    if (typeof(overrideMime)=='string' && overrideMime!='') {
      try {
        // Override the mime type returned by the server
        XmlHttpRequestObjects[this.index].overrideMimeType(overrideMime);
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
   * XMLHttpRequest.readyState change handler
   * Stores the HTTP response code returned by last transaction
   * @param   boolean   useState    Optional. Default: TRUE. If FALSE, no action will be performed but status monitor change
   */
  this.readyStateHandler=function(index, useState) {
    var rh=XmlHttpRequestObjects[index];
    var prh=null;
    var parser=null;
    if (useState) {
      prh=PCPIN_XmlHttpRequestObjects[index];
    }
    if (typeof(useState)!='boolean') {
      var useState=false;
    }
    switch (rh.readyState) {
      case 0 :  // Uninitialised
                break;
      case 1 :  // Loading
                // Update connection status
                updateConnectionStatus(2, useState);
                break;
      case 2 :  // Headers loaded
                break;
      case 3 :  // Interactive
                break;
      case 4 :  // Completed
                // Update connection status
                updateConnectionStatus(0, useState);
                if (useState) {
                  prh.HttpStatusCode=rh.status;
                  prh.HttpStatusText=rh.statusText;
                  prh.HttpHeaders=rh.getAllResponseHeaders();
                  prh.ResponseText=rh.responseText;
                  // Is there already parsed DOMXML object?
                  try {
                    if (rh.responseXML.childNodes.length==0) {
                      // XML object is not created or not parsed
                      // Throw an exception
                      throw(1);
                    } else {
                      // Check for parser errors
                      for (var i=0; i<rh.responseXML.childNodes.length; i++) {
                        if (typeof(rh.responseXML.childNodes[i].tagName)=='string' && rh.responseXML.childNodes[i].tagName=='parsererror') {
                          // There is an error
                          prh.message+='\n'+rh.responseXML.childNodes[i].textContent;
                          throw(1);
                        }
                      }
                      // Success
                      prh.ResponseXML=rh.responseXML;
                    }
                  } catch (e) {
                    try {
                      parser=new DOMParser();
                      prh.ResponseXML=parser.parseFromString(trimString(prh.ResponseText), 'text/xml');
                    } catch (e) {
                      try {
                        // Trying to create "MSXML2.DOMDocument" ActiveX object
                        prh.ResponseXML=new ActiveXObject('MSXML2.DOMDocument');
                        prh.ResponseXML.async='false';
                        prh.ResponseXML.loadXML(trimString(prh.ResponseText));
                      } catch (e) {
                        try {
                          // Trying "Microsoft.XMLDOM" ActiveX object
                          prh.ResponseXML=new ActiveXObject('Microsoft.XMLDOM');
                          prh.ResponseXML.async='false';
                          prh.ResponseXML.loadXML(trimString(prh.ResponseText));
                        } catch (e) {
                          // Failed to initialize DOMXML object
                          prh.ResponseXML=null;
                        }
                      }
                    }
                  }
                  if (prh.ResponseXML!=null) {
                    prh.parse();
                  }
                  if (typeof(prh.CallBackFunc)=='string' && prh.CallBackFunc!='') {
                    // Execute callback function
                    eval(prh.CallBackFunc);
                  }
                }
                break;
    }
    return true;
  }


  /**
   * Get complete XML response object
   * @return   object   XML object
   */
  this.getXML=function() {
    try {
      return this.ResponseXML;
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
      return this.ResponseText;
    } catch (e) {
      return '';
    }
  }

  /**
   * Parsed DOM XML and set object properties
   * @return  boolean   TRUE on success or FALSE on any error
   */
  this.parse=function() {
    var xml=this.getXML();
    var root=null;
    var header=null;
    var service=null;
    var status=null;
    var message=null;
    var data=null;
    try {
      if (xml) {
        root=xml.getElementsByTagName(rootXmlElementName);
        if (root.length==1) {
          root=root[0];
          // <header>
          header=root.getElementsByTagName('header');
          if (header.length) {
            header=header[0];
            service=header.getElementsByTagName('service');
            if (service.length) this.service=this.getCdata(service[0]);
            status=header.getElementsByTagName('status');
            if (status.length) this.status=stringToNumber(this.getCdata(status[0]));
            message=header.getElementsByTagName('message');
            if (message.length) this.message=this.getCdata(message[0]);
          }
          // <data>
          data=root.getElementsByTagName('data');
          if (data.length) this.data=this.parseXMLToArray(data[0]);
        } else {
          throw(1);
        }
        return true;
      } else {
        throw(1);
      }
    } catch (e) {
      this.message+='\n'+this.getResponseString();
      return false;
    }
  }


  /**
   * Convert DOM XML to an array
   * @param   object    node    DOM node
   * @return  array
   */
  this.parseXMLToArray=function(node) {
    var out=new Array();
    var child=null;
    var cdata=null;
    var cdata_node_nr=-1;
    var use=false;
    var tmp=null;
    try {
      if (node.childNodes.length>0) {
        for (var i=0; i<node.childNodes.length; i++) {
          child=node.childNodes[i];
          if (child.nodeType==1) {
            cdata_node_nr=false;
            tmp=this.parseXMLToArray(child);
            if (typeof(out[child.nodeName])=='undefined') {
              out[child.nodeName]=new Array();
            }
            use=(typeof(tmp)=='string');
            if (!use) {
              for (var iii in tmp) {
                use=true;
                break;
              }
            }
            if (use) {
              out[child.nodeName].push(tmp);
            }
          } else if (child.nodeType==4 && cdata_node_nr==-1) {
            cdata_node_nr=i;
          }
        }
        if (cdata_node_nr>=0 && null!=(cdata=this.getCdata(node, cdata_node_nr))) {
          out=cdata;
        }
      }
    } catch (e) {}
    return out;
  }


  /**
   * Parse CDATA contents
   * @param     object    node        Node
   * @param     int       child_nr    Optional. Number of Child containing CDATA.
   * @return string
   */
  this.getCdata=function(node, child_nr) {
    var cdata=null;
    try {
      var cdata_replaces_count=0;
      var cdata_replaced_from=0;
      var cdata_replaced_to=0;
      if (typeof(child_nr)=='number' && node.childNodes[child_nr].nodeType==4) {
        cdata=node.childNodes[child_nr].nodeValue;
      } else {
        for (var i=0; i<node.childNodes.length; i++) {
          if (node.childNodes[i].nodeType==4) {
            cdata=node.childNodes[i].nodeValue;
            break;
          }
        }
      }
      if (cdata!=null && cdata.length>0) {
        cdata_replaces_count=stringToNumber(node.getAttribute('cdata_replaces_count'));
        if (cdata_replaces_count>0) {
          cdata_replaced_from=node.getAttribute('cdata_replaced_from');
          cdata_replaced_to=node.getAttribute('cdata_replaced_to');
          if (cdata_replaced_from!=null && cdata_replaced_from!='' && cdata_replaced_to!=null && cdata_replaced_to!='') {
            cdata=cdata.split(cdata_replaced_to, cdata_replaces_count+1).join(cdata_replaced_from);
          }
        }
      }
    } catch (e) {}
    return cdata;
  }


  // Create new XMLHttpRequest object
  this.createXMLHttpRequestObject();
}