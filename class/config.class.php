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
 * Class PCPIN_Config
 * Manage dynamic configuration
 * @author Konstantin Reznichak <k.reznichak@pcpin.com>
 * @copyright Copyright &copy; 2007, Konstantin Reznichak
 */
class PCPIN_Config extends PCPIN_DB {

  /**
   * Configuration parameter ID
   * @var   int
   */
  var $_conf_id=0;

  /**
   * Configuration parameter group name
   * @var   string
   */
  var $_conf_group='';

  /**
   * Configuration parameter subgroup name
   * @var   string
   */
  var $_conf_subgroup='';

  /**
   * Configuration parameter name
   * @var   string
   */
  var $_conf_name='';

  /**
   * Configuration parameter value
   * @var   string
   */
  var $_conf_value='';

  /**
   * Configuration parameter type
   * @var   string
   */
  var $_conf_type='';

  /**
   * Configuration parameter value choices
   * @var   string
   */
  var $_conf_choices='';

  /**
   * Configuration parameter description
   * @var   string
   */
  var $_conf_description='';

  /**
   * Array with configuration parameters and their values
   * @var   array
   */
  var $_conf_all=null;

  /**
   * Array with configuration parameters and their values grouped
   * @var   array
   */
  var $_conf_all_grouped=null;



  /**
   * Constructor
   * @param   object  &$caller        Caller object
   */
  function PCPIN_Config(&$caller) {
    // Get parent properties
    $this->_db_pass_vars($caller, $this);
    // Load dynamic configuration.
    if ($this->_db_getList('_conf_group ASC', '_conf_subgroup ASC', '_conf_id ASC')) {
      foreach ($this->_db_list as $conf) {
        // Set appropriate value type
        $type=substr($conf['_conf_type'], 0, strpos($conf['_conf_type'], '_'));
        settype($conf['_conf_value'], $type);
        $this->_conf_all[$conf['_conf_name']]=$conf['_conf_value'];
        if (isset($this->_conf_all_grouped[$conf['_conf_group']])) {
          $this->_conf_all_grouped[$conf['_conf_group']][]=$conf;
        } else {
          $this->_conf_all_grouped[$conf['_conf_group']]=array($conf);
        }
      }
      // Free up memory
      $this->_db_freeList();
    } else {
      // No configuration found
      PCPIN_Common::dieWithError(-1, '<b>Fatal error</b>: No configuration found. Check your installation.');
    }
    $this->_db_pass_vars($this, $caller);
  }


  /**
   * Update settings in database
   * @param   array   $settings   New settings
   */
  function _conf_updateSettings($settings=null) {
    if (!empty($settings) && is_array($settings)) {
      foreach ($settings as $setting_name=>$setting_value) {
        if (   array_key_exists($setting_name, $this->_conf_all)
            && is_scalar($setting_value)
            && $setting_value!=$this->_conf_all[$setting_name]
            ) {
          $this->_conf_all[$setting_name]=$setting_value;
          if ($result=$this->_db_query($this->_db_makeQuery(1800, $setting_name, $setting_value))) {
            $this->_db_freeResult($result);
          }
        }
      }
    }
  }


}
?>