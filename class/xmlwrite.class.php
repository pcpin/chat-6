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
 * Define whether to use built-in xmlwriter functionality or custom solution
 */
define('PCPIN_XMLWRITER_BUILT_IN',    function_exists('xmlwriter_open_memory')
                                   && function_exists('xmlwriter_set_indent')
                                   && function_exists('xmlwriter_set_indent_string')
                                   && function_exists('xmlwriter_start_document')
                                   && function_exists('xmlwriter_start_element')
                                   && function_exists('xmlwriter_end_element')
                                   && function_exists('xmlwriter_write_attribute')
                                   && function_exists('xmlwriter_write_cdata')
                                   && function_exists('xmlwriter_output_memory'));

/**
 * This class contains methods for creating PCPIN XML files
 * @author Konstantin Reznichak <k.reznichak@pcpin.com>
 * @copyright Copyright &copy; 2008, Konstantin Reznichak
 */
class PCPIN_XMLWrite {


  /**
   * XMLWriter resource
   * @var resource
   */
  var $xml=null;

  /**
   * Name of the root element
   * @var string
   */
  var $root_name='';

  /**
   * XML encoding
   * @var string
   */
  var $encoding='';

  /**
   * Flag: TRUE: indentation of XML will be activated
   * @var boolean
   */
  var $indent=false;

  /**
   * Indent string
   * @var string
   */
  var $indent_string='  ';

  /**
   * Escape sequence for CDATA contents (used by $this->xmlwriter_write_cdata() method)
   * @var string
   */
  var $cdata_escape_sequence='';

  /**
   * <header> element: Service name
   * @var string
   */
  var $header_service='';

  /**
   * <header> element: Status
   * @var int
   */
  var $header_status=0;

  /**
   * <header> element: Message
   * @var string
   */
  var $header_message='';

  /**
   * Data to be used in <data> element
   * @var array
   */
  var $xml_data=null;

  /**
   * Debug timers
   * @var array
   */
  var $debug_timers=null;



  /**
   * Constructor
   * @param   string    $header_service   Service name
   * @param   string    $encoding         Optional. XML encoding
   * @param   string    $name             Optional. Name of the root element
   * @param   string    $type             Optional. Type of the root element
   * @param   boolean   $indent           Optional. Whether to indent XML or not
   * @param   string    $indent_string    Optional. Indent string
   */
  function PCPIN_XMLWrite($header_service, $encoding=PCPIN_XMLDOC_ENCODING, $name=PCPIN_XMLDOC_ROOT_NAME, $indent=PCPIN_XMLDOC_INDENT, $indent_string=PCPIN_XMLDOC_INDENT_STRING) {
    $this->set('root_name', $name);
    $this->set('encoding', $encoding);
    $this->set('indent', $indent);
    $this->set('indent_string', $indent_string);
    $this->set('cdata_escape_sequence', '_'.PCPIN_Common::randomString(12).'_');
    $this->set('xml_data', array());
    $this->set('header_service', $header_service);
  }


  /**
   * Set status for XML header
   * @param   int     $header_status      New status
   */
  function setHeaderStatus($header_status) {
    $this->header_status=$header_status;
  }


  /**
   * Set message for XML header
   * @param   string    $header_message     New message
   */
  function setHeaderMessage($header_message) {
    $this->header_message=$header_message;
  }

  /**
   * Set data
   * @param   array   $data     New data
   */
  function setData($data) {
    $this->xml_data=$data;
  }

  /**
   * Set debug timers
   * @param   array   $timers   Debug timers
   */
  function setDebugTimers($timers) {
    $this->debug_timers=$timers;
  }


  /**
   * Set class property value
   * @param   string    $property   Property name
   * @param   mixed     $value      New value
   */
  function set($property, $value) {
    switch ($property) {
      case 'root_name':
      case 'encoding':
      case 'indent':
      case 'indent_string':
      case 'cdata_escape_sequence':
      case 'header_service':
      case 'header_status':
      case 'header_message':
        $this->$property=$value;
      break;
    }
  }


  /**
   * Get class property value
   * @param   string    $property   Property name
   * @return   mixed
   */
  function get($property) {
    switch ($property) {
      case 'root_name':
      case 'encoding':
      case 'indent':
      case 'indent_string':
      case 'cdata_escape_sequence':
      case 'header_service':
      case 'header_status':
      case 'header_message':
      case 'xml_data':
      case 'debug_timers':
        return $this->$property;
      break;
    }
  }





  /**
   * Create XML string. The <data> element will be filled from $this->xml_data array, format as follows:
   *      $this->xml_data = array ( <KEY> => <VAL>, <KEY> => <VAL>, {...} )
   *      <KEY> - when integer: repeated parent element, otherwise: name of an element
   *      <VAL> - when scalar or NULL: CDATA, when Array: see $this->xml_data
   * @return string
   */
  function makeXML() {
    if (PCPIN_XMLWRITER_BUILT_IN) {
      // Using built-in XMLWriter functionality
      return $this->makeXML_XMLWriter();
    } else {
      // Using custom implementation
      if (function_exists('_pcpin_loadClass')) {
        _pcpin_loadClass('xmlwriter_custom');
      }
      $xml=new PCPIN_XMLWriter_Custom();
      return $xml->makeXML($this);
    }
  }

  /**
   * Make XML document using built-in XMLWriter functionality
   * @return string
   */
  function makeXML_XMLWriter() {
    // XMLWriter object instance
    $this->xml=xmlwriter_open_memory();

    // Indentation
    xmlwriter_set_indent($this->xml, $this->indent);
    xmlwriter_set_indent_string($this->xml, $this->indent_string);

    // XML Document declaration
    xmlwriter_start_document($this->xml, '1.0', $this->encoding, 'yes');

    // <ROOT>
    xmlwriter_start_element($this->xml, $this->root_name);

    // <header>
    xmlwriter_start_element($this->xml, 'header');

      // <service>
      xmlwriter_start_element($this->xml, 'service');
      $this->xmlwriter_write_cdata($this->header_service);
      xmlwriter_end_element($this->xml); // </service>

      // <status>
      xmlwriter_start_element($this->xml, 'status');
      $this->xmlwriter_write_cdata($this->header_status);
      xmlwriter_end_element($this->xml); // </status>

      // <message>
      xmlwriter_start_element($this->xml, 'message');
      $this->xmlwriter_write_cdata($this->header_message);
      xmlwriter_end_element($this->xml); // </message>

    xmlwriter_end_element($this->xml); // </header>

    // <data>
    xmlwriter_start_element($this->xml, 'data');
    $this->dataElement($this->xml_data, '');
    xmlwriter_end_element($this->xml); // </data>

    if (!empty($this->debug_timers) && is_array($this->debug_timers)) {
      // <debug_timers>
      xmlwriter_start_element($this->xml, 'debug_timers');

      foreach ($this->debug_timers as $key=>$val) {
        xmlwriter_start_element($this->xml, $key);
        $this->xmlwriter_write_cdata($val);
        xmlwriter_end_element($this->xml);
      }

      xmlwriter_end_element($this->xml); // </debug_timers>
    }

    xmlwriter_end_element($this->xml); // <ROOT>
    xmlwriter_end_document($this->xml); // Finish XML document

    // Generate XML string
    return xmlwriter_output_memory($this->xml, true);
  }


  /**
   * Write CDATA into element, escape unallowed character sequences "]]>"
   * @param     string    $cdata      A string to write
   */
 function xmlwriter_write_cdata($cdata) {
    $count=-1;
    $cdata=str_replace(']]>', $this->cdata_escape_sequence, $cdata, $count);
    if ($count>0) {
      xmlwriter_write_attribute($this->xml, 'cdata_replaces_count', $count);
      xmlwriter_write_attribute($this->xml, 'cdata_replaced_from', ']]>');
      xmlwriter_write_attribute($this->xml, 'cdata_replaced_to', $this->cdata_escape_sequence);
    }
    xmlwriter_write_cdata($this->xml, $cdata);
  }


  /**
   * Make recursive elements
   * @param   array   $data   Data
   */
 function dataElement($data, $last_tag) {
    if (!empty($data)) {
      foreach ($data as $key=>$val) {
        $last_tag=!is_int($key)? $key : $last_tag;
        if ($last_tag!='') {
          if (is_scalar($val) || is_null($val)) {
            xmlwriter_start_element($this->xml, $last_tag);
            $this->xmlwriter_write_cdata($val);
            xmlwriter_end_element($this->xml);
          } elseif (is_array($val)) {
            if (!is_int(key($val))) {
              xmlwriter_start_element($this->xml, $last_tag);
              $this->dataElement($val, $last_tag);
              xmlwriter_end_element($this->xml);
            } else {
              $this->dataElement($val, $last_tag);
            }
          }
        }
      }
    }
  }

}
?>