<?php
/**
 * COmanage Registry MLA Humanities Commons IdP Enroller Model
 *
 * Copyright (C) 2016 Modern Language Association
 * 
 * Licensed under the Apache License, Version 2.0 (the "License"); you may not use this file except in compliance with
 * the License. You may obtain a copy of the License at
 * 
 * http://www.apache.org/licenses/LICENSE-2.0
 * 
 * Unless required by applicable law or agreed to in writing, software distributed under
 * the License is distributed on an "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY
 * KIND, either express or implied. See the License for the specific language governing
 * permissions and limitations under the License.
 *
 * @copyright     Copyright (C) 2016 Modern Language Association
 * @link          http://www.internet2.edu/comanage COmanage Project
 * @package       registry-plugin
 * @since         COmanage Registry v1.1.0
 * @license       Apache License, Version 2.0 (http://www.apache.org/licenses/LICENSE-2.0)
 * @version       $Id$
 */

class HumanitiesCommonsIdpEnroller extends AppModel {
  // Required by COmanage Plugins
  public $cmPluginType = "enroller";

  // Document foreign keys
  public $cmPluginHasMany = array();

  // Add behaviors
  public $actsAs = array('Containable', 'Changelog' => array('priority' => 5));

  // Association rules from this model to other models
  public $belongsTo = array("CoEnrollmentFlowWedge");

  // Default display field for cake generated views
  public $displayField = "ldap_serverurl";
  
  // Validation rules for table elements
  public $validate = array(
    'co_enrollment_flow_wedge_id' => array(
      'rule' => 'numeric',
      'required' => true,
      'allowEmpty' => false
    ),
    'username_id_type' => array(
      'rule' => 'notBlank',
      'required' => true,
      'allowEmpty' => false
    ),
    'ldap_serverurl' => array(
      'rule' => 'notBlank',
      'required' => true,
      'allowEmpty' => false
    ),
    'ldap_binddn' => array(
      'rule' => 'notBlank',
      'required' => true,
      'allowEmpty' => false
    ),
    'ldap_bind_password' => array(
      'rule' => 'notBlank',
      'required' => true,
      'allowEmpty' => false
    ),
    'ldap_basedn' => array(
      'rule' => 'notBlank',
      'required' => true,
      'allowEmpty' => false
    ),
    'hc_idp_scope' => array(
      'rule' => 'notBlank',
      'required' => true,
      'allowEmpty' => false
    ),
    'username_env_variable' => array(
      'rule' => array('validateInput'),
      'required' => false,
      'allowEmpty' => true
    ),
  );
  
  /**
   * Expose menu items.
   * 
   * @since COmanage Registry v1.1.0
   * @return Array with menu location type as key and array of labels, controllers, actions as values.
   */
  
  public function cmPluginMenus() {
    return array();
  }
}
