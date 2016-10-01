<?php
/**
 * COmanage Registry MLA Humanities Commons IdP Enroller Accounts Controller
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

class HumanitiesCommonsIdpEnrollerAccountsController extends StandardController {
  // Class name, used by Cake
  public $name = "HumanitiesCommonsIdpEnrollerAccounts";
  public $uses = array(
                  'CoPetition',
                  'HumanitiesCommonsIdpEnroller.HumanitiesCommonsIdpEnrollerConfig',
                  'HumanitiesCommonsIdpEnroller.HumanitiesCommonsIdpEnrollerAccount',
                 );

  /**
   * Callback before other controller methods are invoked or views are rendered.
   *
   * @since  COmanage Registry 1.1.0
   */
  
  function beforeFilter() {
    parent::beforeFilter();  

    // Allow anonymous access since user not authenticated yet
    $this->Auth->allow('provision');
  }

  /**
   * Provision account
   * - precondition: Petition ID and token set in session
   * - postcondition: Flash message set on error
   *
   * @since  COmanage Directory 1.1.0
   * @return void
   */

  function provision() {
    $logPrefix = "HumanitiesCommonsIdpEnrollerAccountsController provision ";

    // Find our configuration
    $args = array();
    $args['conditions']['HumanitiesCommonsIdpEnrollerConfig.id'] = 1;
    $args['contain']                                             = true;
    $config = $this->HumanitiesCommonsIdpEnrollerConfig->find('first', $args);
    if (empty($config)) {
      $this->Flash->set(_txt('er.humanitiescommonsidpenroller.account.noconfig'), array('key' => 'error'));
      $this->redirect("/");
    }

    // Set debugging level
    $debug = $config['HumanitiesCommonsIdpEnrollerConfig']['debug'];

    ( $debug ?  $this->log($logPrefix . "called") : null);

    // Process submitted form
    if($this->request->is('post')) {

      // Validate the password inputs
      $this->HumanitiesCommonsIdpEnrollerAccount->set($this->request->data);
      if($this->HumanitiesCommonsIdpEnrollerAccount->validates()) {

        // Provision account to LDAP
        if(!$this->_provisionLdap($config)) {
          $this->Flash->set(_txt('er.humanitiescommonsidpenroller.account.ldap'), array('key' => 'error'));
          $this->redirect("/");
        }

        // Redirect to the target passed in the query string
        $this->redirect(urldecode($this->request->query['target']));
      }
      
      // Fall through to displaying the form again

    }
  }

  /**
   * Provision account in LDAP server
   * - precondition: LDAP server connection details configured for plugin
   *
   * @since  COmanage Directory 1.1.0
   * @param  Array $coPetition CoPetition for this enrollment
   * @param  Array $config Configuration for the plugin
   * @return Boolean true if account provisioned or false if error
   */

  function _provisionLdap($config) {
    $logPrefix = "HumanitiesCommonsIdpEnrollerAccountsController _provisionLdap ";

    $cxn = ldap_connect($config['HumanitiesCommonsIdpEnrollerConfig']['ldap_serverurl']);
    
    if(!$cxn) {
      throw new RuntimeException(_txt('er.ldapprovisioner.connect'), 0x5b /*LDAP_CONNECT_ERROR*/);
    }
    
    // Use LDAP v3 
    ldap_set_option($cxn, LDAP_OPT_PROTOCOL_VERSION, 3);
    
    // Bind to LDAP server
    $binddn = $config['HumanitiesCommonsIdpEnrollerConfig']['ldap_binddn'];
    $bindPassword = $config['HumanitiesCommonsIdpEnrollerConfig']['ldap_bind_password'];
    if(!@ldap_bind($cxn, $binddn, $bindPassword)) {
      $msg = ldap_error($cxn) . " : " .  strval(ldap_errno($cxn));
      $this->log($logPrefix . "Unable to bind to LDAP server: " . $msg);
      return false;
    }

    $uid = $this->request->data['username'];

    // Create DN and attributes
    $basedn = $config['HumanitiesCommonsIdpEnrollerConfig']['ldap_basedn'];
    $dn = "uid=$uid,$basedn";
    $attributes = array();
    $attributes['objectClass'][0] = 'inetOrgPerson';
    $attributes['objectClass'][1] = 'pwmUser';
    $attributes['uid'] = $uid;
    $attributes['givenName'] = "placeholder";
    $attributes['sn'] = "placeholder";
    $attributes['cn'] = "placeholder";
    $attributes['userPassword'] = $this->request->data['password1'];

    // Add the new account
    if(!@ldap_add($cxn, $dn, $attributes)) {
      $msg = ldap_error($cxn) . " : " .  strval(ldap_errno($cxn));
      $this->log($logPrefix . "Error when adding DN: " . $msg);
      return false;
    }
  
    // Drop the connection
    ldap_unbind($cxn);

    return true;
  }
}
