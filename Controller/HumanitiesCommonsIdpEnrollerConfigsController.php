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

App::uses("StandardController", "Controller");

class HumanitiesCommonsIdpEnrollerConfigsController extends StandardController {
  // Class name, used by Cake
  public $name = "HumanitiesCommonsIdpEnrollerConfigs";
    
  // Establish pagination parameters for HTML views
  public $paginate = array(
    'limit' => 25,
    'order' => array(
      'name' => 'asc'
    )
  );

  /**
   * Add a configuration
   *
   * @since  COmanage Directory 1.1.0
   * @return void
   */

  public function add() {
    // If configuration already exists redirect to edit action
    $args = array();
    $args['conditions']['HumanitiesCommonsIdpEnrollerConfig.id'] = 1;
    $args['contain'] = true;
    $config = $this->HumanitiesCommonsIdpEnrollerConfig->find('first', $args);
    if (!empty($config)) {
      $this->redirect(array('action' => 'edit', 1));
    }

    parent::add();
  }

  /**
   * Edit a configuration
   *
   * @since  COmanage Directory 1.1.0
   * @param  Integer $id ID of the configuration
   * @return void
   */

  public function edit($id) {
    // There should only be one configuration per platform
    if($id != 1) {
      $this->Flash->set(_txt('er.humanitiescommonsidpenroller.config.singleton'), array('key' => 'error'));
      $this->redirect(array('action' => 'edit', 1));
    }

    // If no configuration exists yet redirect to add action
    $args = array();
    $args['conditions']['HumanitiesCommonsIdpEnrollerConfig.id'] = 1;
    $args['contain'] = true;
    $config = $this->HumanitiesCommonsIdpEnrollerConfig->find('first', $args);
    if (empty($config)) {
      $this->redirect(array('action' => 'add'));
    }

    parent::edit($id);

    // Set page title for editing
    $this->set('title_for_layout', _txt('op.humanitiescommonsidpenroller.config.edit'));
  }

  /**
   * Index action to view all configurations
   *
   * @since  COmanage Directory 1.1.0
   * @return void
   */

  public function index() {
    // Since there should only be one configuration redirect index action
    // to view action with id = 1.
    $this->redirect(array('action' => 'view', 1));
  }
  
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
    
    // Add a new configuration?
    $p['add'] = $roles['cmadmin'];
    
    // Delete an existing configuration?
    $p['delete'] = $roles['cmadmin'];
    
    // Edit an existing configuration?
    $p['edit'] = $roles['cmadmin'];
    
    // View the existing configuration?
    $p['index'] = $roles['cmadmin'];
    
    // View the existing confinguration?
    $p['view'] = $roles['cmadmin'];
    
    $this->set('permissions', $p);
    return $p[$this->action];
  }

  /**
   * View a configuration
   *
   * @since  COmanage Directory 1.1.0
   * @param  Integer $id ID of the configuration
   * @return void
   */

  public function view($id) {
    // There should only be one configuration per platform
    if($id != 1) {
      $this->Flash->set(_txt('er.humanitiescommonsidpenrollerconfig.singleton'), array('key' => 'error'));
      $this->redirect(array('action' => 'view', 1));
    }

    // If no configuration exists yet redirect to add action
    $args = array();
    $args['conditions']['HumanitiesCommonsIdpEnrollerConfig.id'] = 1;
    $args['contain'] = true;
    $config = $this->HumanitiesCommonsIdpEnrollerConfig->find('first', $args);
    if (empty($config)) {
      $this->redirect(array('action' => 'add'));
    }

    parent::view($id);

    // Set page title for viewing
    $this->set('title_for_layout', _txt('op.humanitiescommonsidpenroller.config.view'));
  }
}
