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
    'Identifier');

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
    $this->loadModel("HumanitiesCommonsIdpEnroller.HumanitiesCommonsIdpEnrollerConfig");
    $args = array();
    $args['conditions']['HumanitiesCommonsIdpEnrollerConfig.id'] = 1;
    $args['contain'] = true;
    $config = $this->HumanitiesCommonsIdpEnrollerConfig->find('first', $args);
    if (empty($config)) {
      $this->Flash->set(_txt('er.humanitiescommonsidpenroller.account.noconfig'), array('key' => 'error'));
      $this->redirect("/");
    }

    // Set debugging level
    $debug = $config['HumanitiesCommonsIdpEnrollerConfig']['debug'];

    ( $debug ?  $this->log($logPrefix . "called") : null);

    // Get the petition ID and token from the session.
    $petitionId = $this->Session->read('HC.petitionId');

    if(!isset($petitionId)) {
      $this->Flash->set(_txt('er.humanitiescommonsidpenroller.account.petition.id.session.none'), array('key' => 'error'));
      $this->redirect("/");
    }

    ( $debug ? $this->log($logPrefix . "Found petition Id $petitionId in session") : null);

    $tokenFromSession = $this->Session->read('HC.petitionToken');

    if(!isset($tokenFromSession)) {
      $this->Flash->set(_txt('er.humanitiescommonsidpenroller.account.petition.token.session.none'), array('key' => 'error'));
      $this->redirect("/");
    }

    ( $debug ? $this->log($logPrefix . "Found petition token $tokenFromSession in session") : null);

    // Use the petition id to find the petition
    $args = array();
    $args['conditions']['CoPetition.id'] = $petitionId;
    $args['contain'] = array();
    $args['contain']['EnrolleeCoPerson']['Identifier'] = array();
    $args['contain']['EnrolleeCoPerson']['Name'] = array();
    $args['contain']['EnrolleeCoPerson']['EmailAddress'] = array();
    $coPetition = $this->CoPetition->find('first', $args);
    if (empty($coPetition)) {
      $this->Flash->set(_txt('er.humanitiescommonsidpenroller.account.petition.none', array($petitionId)), array('key' => 'error'));
      $this->redirect("/");
      return;
    }

    // Compare the token from session to that found with petition
    $tokenFromPetition = $coPetition['CoPetition']['petitioner_token'];
    if(!($tokenFromPetition == $tokenFromSession)) {
      $this->Flash->set(_txt('er.humanitiescommonsidpenroller.account.petition.token.session.bad'), array('key' => 'error'));
      $this->redirect("/");
    }

    // Process submitted form
    if($this->request->is('post')) {

      // Check the submitted petition Id against that from session
      if (!isset($this->request->data['petitionId'])) {
        $this->Flash->set(_txt('er.humanitiescommonsidpenroller.account.petition.id.missing'), array('key' => 'error'));
        $this->redirect("/");
      }
      $submittedPetitionId = $this->request->data['petitionId'];
      if($submittedPetitionId != $petitionId) {
        $this->Flash->set(_txt('er.humanitiescommonsidpenroller.account.petition.id.mismatch'), array('key' => 'error'));
        $this->redirect("/");
      }

      // Check the submitted token against that from petition
      if (!isset($this->request->data['petitionToken'])) {
        $this->Flash->set(_txt('er.humanitiescommonsidpenroller.account.petition.token.missing'), array('key' => 'error'));
        $this->redirect("/");
      }
      $submittedPetitionToken = $this->request->data['petitionToken'];
      if($submittedPetitionToken != $tokenFromPetition) {
        $this->Flash->set(_txt('er.humanitiescommonsidpenroller.account.petition.token.mismatch'), array('key' => 'error'));
        $this->redirect("/");
      }

      // Validate the password inputs
      $this->loadModel('HumanitiesCommonsIdpEnroller.HumanitiesCommonsIdpEnrollerAccount');
      $this->HumanitiesCommonsIdpEnrollerAccount->set($this->request->data);

      if($this->HumanitiesCommonsIdpEnrollerAccount->validates()) {

        // Provision account to LDAP
        if(!$this->_provisionLdap($coPetition, $config)) {
          $this->Flash->set(_txt('er.humanitiescommonsidpenroller.account.ldap'), array('key' => 'error'));
          $this->redirect("/");
        }

        // Redirect to the target passed in the query string
        $this->redirect(urldecode($this->request->query['target']));
      }
      
      // Fall through to displaying the form again

    }

    // Set the petition ID and token for the view.
    $this->set('vv_petitionId', $petitionId);
    $this->set('vv_petitionToken', $tokenFromPetition);

    // Find the WordPress identifier the user has chosen previously
    // to use as the username. If we cannot find it bail out.
    $username = $this->_parseUsername($coPetition, $config);

    if(!isset($username)) {
      $this->Flash->set(_txt('er.humanitiescommonsidpenroller.config.username'), array('key' => 'error'));
      $this->redirect("/");
    }

    ( $debug ? $this->log($logPrefix . "Found username $username") : null);

    $this->set('vv_username', $username);
  }

  /**
   * Parse email from the CoPetition 
   * - precondition: Email set on CoPerson linked to CoPetition
   *
   * @since  COmanage Directory 1.1.0
   * @param  Array $coPetition CoPetition for this enrollment
   * @param  Array $config Configuration for the plugin
   * @return String email
   */

  function _parseEmail($coPetition, $config) {
    // We assume the email is being copied to the CoPerson from the
    // OrgIdentity during enrollment so that we can only inspect the
    // CoPerson. We also assume the form collects official email.
    if(!isset($coPetition['EnrolleeCoPerson']['EmailAddress'])) {
      return null;
    }
    foreach ($coPetition['EnrolleeCoPerson']['EmailAddress'] as $email) {
      $type = $email['type'];
      $mail = $email['mail'];
      $deleted = $email['deleted'];
      if (isset($mail) && empty($deleted) && $type == EmailAddressEnum::Official ) {
        return $mail;
      }
    }

    return null;
  }

  /**
   * Parse Name from the CoPetition 
   * - precondition: Name set on CoPerson linked to CoPetition
   *
   * @since  COmanage Directory 1.1.0
   * @param  Array $coPetition CoPetition for this enrollment
   * @param  Array $config Configuration for the plugin
   * @return Array array of given name and family name
   */

  function _parseName($coPetition, $config) {
    // We assume the name is being copied to the CoPerson from the
    // OrgIdentity during enrollment so that we can only inspect the
    // CoPerson. We also assume the form collects official name.
    if(!isset($coPetition['EnrolleeCoPerson']['Name'])) {
      return null;
    }
    foreach ($coPetition['EnrolleeCoPerson']['Name'] as $name) {
      $type = $name['type'];
      $given = $name['given'];
      $family = $name['family'];
      $deleted = $name['deleted'];
      if (isset($given) && isset($family) && empty($deleted) && $type == NameEnum::Official ) {
        return array($given, $family);
      }
    }

    return null;
  }

  /**
   * Parse username from the CoPetition 
   * - precondition: username set in identifier linked to CoPetition
   *
   * @since  COmanage Directory 1.1.0
   * @param  Array $coPetition CoPetition for this enrollment
   * @param  Array $config Configuration for the plugin
   * @return String username
   */

  function _parseUsername($coPetition, $config) {
    if(!isset($coPetition['EnrolleeCoPerson']['Identifier'])) {
      return null;  
    }
    foreach ($coPetition['EnrolleeCoPerson']['Identifier'] as $identifier) {
      $id = $identifier['identifier'];
      $type = $identifier['type'];
      $status = $identifier['status'];
      $deleted = $identifier['deleted'];
      if (isset($id) && $type == $config['HumanitiesCommonsIdpEnrollerConfig']['username_id_type'] && $status == StatusEnum::Active && empty($deleted)) {
        $username = $id;
        return $username;
      }
    }

    return null;
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

  function _provisionLdap($coPetition, $config) {
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

    $uid = $this->_parseUsername($coPetition, $config);
    $mail = $this->_parseEmail($coPetition, $config);
    list($givenName, $sn) = $this->_parseName($coPetition, $config);

    // Create DN and attributes
    $basedn = $config['HumanitiesCommonsIdpEnrollerConfig']['ldap_basedn'];
    $dn = "uid=$uid,$basedn";
    $attributes = array();
    $attributes['objectClass'][0] = 'inetOrgPerson';
    $attributes['objectClass'][1] = 'pwmUser';
    $attributes['uid'] = $uid;
    $attributes['givenName'] = $givenName;
    $attributes['sn'] = $sn;
    $attributes['cn'] = "$givenName $sn";
    $attributes['mail'] = $mail;
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
