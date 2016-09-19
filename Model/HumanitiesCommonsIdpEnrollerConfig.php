<?php
/**
 * COmanage Registry MLA Humanities Commons IdP Enroller Config Model
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
  
class HumanitiesCommonsIdpEnrollerConfig extends AppModel {
  // Define class name for cake
  public $name = "HumanitiesCommonsIdpEnrollerConfig";

  // Add behaviors
  public $actsAs = array('Containable');

  // Document foreign keys
  public $cmPluginHasMany = array();

  // Default display field for cake generated views
  public $displayField = "ldap_serverurl";

  // Validation rules for table elements
  public $validate = array(
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
  );
}
