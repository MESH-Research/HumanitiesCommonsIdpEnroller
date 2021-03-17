<?php
/**
 * COmanage Registry MLA Humanities Commons IdP Enroller Configs Controller
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
 * @package       registry
 * @since         COmanage Registry v1.1.0
 * @license       Apache License, Version 2.0 (http://www.apache.org/licenses/LICENSE-2.0)
 * @version       $Id$
 */

App::uses("SEWController", "Controller");

class HumanitiesCommonsIdpEnrollersController extends SEWController {
  // Class name, used by Cake
  public $name = "HumanitiesCommonsIdpEnrollers";
    
  // Establish pagination parameters for HTML views
  public $paginate = array(
    'limit' => 25,
    'order' => array(
      'ldap_serverurl' => 'asc'
    )
  );

  /**
   * Authorization for this Controller, called by Auth component
   * - precondition: Session.Auth holds data used for authz decisions
   * - postcondition: $permissions set with calculated permissions
   *
   * @since  COmanage Registry 1.1.0
   * @return Array Permissions
   */
  
  function isAuthorized() {
    $roles = $this->Role->calculateCMRoles();

    // Construct the permission set for this user, which will also be passed to the view.
    $p = array();
    
    // All operations require platform administrator
    
    // Delete an existing configuration?
    $p['delete'] = ($roles['cmadmin'] || $roles['coadmin']);
    
    // Edit an existing configuration?
    $p['edit'] = ($roles['cmadmin'] || $roles['coadmin']);
    
    // View the existing configuration?
    $p['index'] = ($roles['cmadmin'] || $roles['coadmin']);
    
    // View the existing confinguration?
    $p['view'] = ($roles['cmadmin'] || $roles['coadmin']);
    
    $this->set('permissions', $p);
    return $p[$this->action];
  }
}
