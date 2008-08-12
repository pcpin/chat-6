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
 * This class contains custom methods for creating XML files
 * @author Konstantin Reznichak <k.reznichak@pcpin.com>
 * @copyright Copyright &copy; 2008, Konstantin Reznichak
 */
class PCPIN_XMLWriter_Custom {

  /**
   * XML structure
   * @var array
   */
  var $xml_struct=array();

  /**
   * Array with elements
   * @var array
   */
  var $elements=null;

  /**
   * Current depth
   * @var int
   */
  var $depth=0;

  /**
   * A reference to the current element
   * @var array
   */
  var $current_element=null;

  /**
   * Indentation flag
   * @var boolean
   */
  var $indent=false;

  /**
   * Indentation string
   * @var string
   */
  var $indent_string='';

  /**
   * Escape sequence for CDATA
   * @var string
   */
  var $cdata_escape_sequence='';

  /**
   * XML element array template
   * @var array
   */
  var $xml_element_tpl=array();



  /**
   * Constructor
   */
  function PCPIN_XMLWriter_Custom() {
    $this->xml_element_tpl=array('name'=>'',
                                 'attributes'=>array(),
                                 'cdata'=>'',
                                 'parent'=>null,
                                 'curr_child'=>null,
                                 'inner_xml'=>''
                                 );
    $this->current_element=$this->xml_element_tpl;
    $this->depth=0;
    $this->elements=array();
    $this->indent=false;
    $this->indent_string='';
    $this->cdata_escape_sequence='';
  }


  /**
   * Make XML using data from supplied object
   * @param   object    &$xmlobj    A reference to PCPIN_XMLWrite object
   */
  function makeXML(&$xmlobj) {
    $this->indent=$xmlobj->get('indent');
    $this->indent_string=$xmlobj->get('indent_string');
    $this->cdata_escape_sequence=$xmlobj->get('cdata_escape_sequence');

    // XML Document declaration
    $this->start_document('1.0', $xmlobj->get('encoding'), true);

    // <ROOT>
    $this->start_element($xmlobj->get('root_name'));

    // <header>
    $this->start_element('header');

      // <service>
      $this->start_element('service');
      $this->write_cdata_safe($xmlobj->get('header_service'));
      $this->end_element(); // </service>

      // <status>
      $this->start_element('status');
      $this->write_cdata_safe($xmlobj->get('header_status'));
      $this->end_element(); // </status>

      // <message>
      $this->start_element('message');
      $this->write_cdata_safe($xmlobj->get('header_message'));
      $this->end_element(); // </message>

    $this->end_element(); // </header>
    // <data>
    $this->start_element('data');
    $this->dataElement($xmlobj->get('xml_data'), '');
    $this->end_element(); // </data>

    $debug_timers=$xmlobj->get('debug_timers');
    if (!empty($debug_timers) && is_array($debug_timers)) {
      // <debug_timers>
      $this->start_element('debug_timers');

      foreach ($debug_timers as $key=>$val) {
        $this->start_element($key);
        $this->write_cdata_safe($val);
        $this->end_element();
      }

      $this->end_element(); // </debug_timers>
    }

    $this->end_element(); // <ROOT>

    return $this->indent? $this->current_element['inner_xml'] : ($this->current_element['inner_xml']."\n");
  }


  function start_document($version, $encoding, $standalone=false) {
    $this->current_element['inner_xml'].='<?xml version="'.htmlspecialchars($version).'" encoding="'.htmlspecialchars($encoding).'"'.($standalone? (' standalone="yes"') : '').'?>'."\n";
  }


  function start_element($name) {
    $this->depth++;
    $element=$this->xml_element_tpl;
    $element['name']=$name;
    $element['parent']=&$this->current_element;
    $this->current_element['curr_child']=$element;
    $this->current_element=&$this->current_element['curr_child'];
  }


 function write_cdata_safe($cdata) {
    $count=0;
    if (substr(phpversion(), 0, 1)>=5) {
      // We have PHP 5
      $cdata=str_replace(']]>', $this->cdata_escape_sequence, $cdata, $count);
    } else {
      // PHP 4
      $offset=0;
      while (false!==($offset=strpos($cdata, ']]>', $offset))) {
        $count++;
      }
      if ($count>0) {
        $cdata=str_replace(']]>', $this->cdata_escape_sequence, $cdata);
      }
    }
    if ($count>0) {
      $this->write_attribute('cdata_replaces_count', $count);
      $this->write_attribute('cdata_replaced_from', ']]>');
      $this->write_attribute('cdata_replaced_to', $this->cdata_escape_sequence);
    }
    $this->write_cdata($cdata);
  }


  function write_cdata($cdata) {
    $this->current_element['cdata'].='<![CDATA['.$cdata.']]>';
  }


  function write_attribute($name, $value) {
    $this->current_element['attributes'][$name]=$value;
  }


  function end_element() {
    $this->depth--;
    if (!$this->indent) {
      $indent_string='';
      $indent_lf='';
    } else {
      $indent_string=$this->indent? (str_repeat($this->indent_string, $this->depth)) : '';
      $indent_lf="\n";
    }
    $xml=$indent_string.'<'.$this->current_element['name'];
    if (!empty($this->current_element['attributes'])) {
      foreach ($this->current_element['attributes'] as $key=>$val) {
        $xml.=' '.$key.'="'.htmlspecialchars($val).'"';
      }
    }
    if ($this->current_element['inner_xml']=='' && $this->current_element['cdata']=='') {
      $xml.='/>';
    } else {
      if ($this->current_element['inner_xml']=='') {
        $xml.='>'.$this->current_element['cdata'].'</'.$this->current_element['name'].'>';
      } else {
        $xml.='>'.$indent_lf.$this->current_element['inner_xml'].$indent_string.'</'.$this->current_element['name'].'>';
      }
    }
    $this->current_element['parent']['inner_xml'].=$xml.$indent_lf;
    $this->current_element=&$this->current_element['parent'];
    $this->current_element['curr_child']=null;
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
            $this->start_element($last_tag);
            $this->write_cdata_safe($val);
            $this->end_element();
          } elseif (is_array($val)) {
            if (!is_int(key($val))) {
              $this->start_element($last_tag);
              $this->dataElement($val, $last_tag);
              $this->end_element();
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