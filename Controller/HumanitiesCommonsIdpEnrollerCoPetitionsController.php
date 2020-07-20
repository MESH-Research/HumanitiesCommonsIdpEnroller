<?php
/**
 * COmanage Registry MLA Humanities Commons IdP Enroller CoPetitions Controller
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

App::uses('CoPetitionsController', 'Controller');

class HumanitiesCommonsIdpEnrollerCoPetitionsController extends CoPetitionsController {
  // Class name, used by Cake
  public $name = "HumanitiesCommonsIdpEnrollerCoPetitions";

  public $uses = array(
                  'CoPetition',
                  'HumanitiesCommonsIdpEnroller.HumanitiesCommonsIdpEnrollerConfig'
                 );

  /**
   * Collect identifier used with WordPress and as username for Humanities Commons IdP
   *
   * @since  COmanage Registry v1.1.0
   * @param  Integer $id       CO Petition ID
   * @param  Array   $onFinish Redirect target on completion
   */

  //protected function execute_plugin_collectIdentifier($id, $onFinish) {
  protected function execute_plugin_checkEligibility($id, $onFinish) {
    $logPrefix = "HumanitiesCommonsIdpEnrollerCoPetitionsController execute_plugin_collectIdentifier ";

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

    // Use the petition id to find the petition
    $args = array();
    $args['conditions']['CoPetition.id']                 = $id;
    $args['contain']                                     = array();
    $args['contain']['EnrolleeCoPerson']['Identifier']   = array();
    $args['contain']['EnrolleeCoPerson']['Name']         = array();
    $args['contain']['EnrolleeCoPerson']['EmailAddress'] = array();

    $coPetition = $this->CoPetition->find('first', $args);
    if (empty($coPetition)) {
      $this->log($logPrefix . "ERROR: could not find petition with id $id. Displaying error to user and ending flow.");
      $this->Flash->set(_txt('er.humanitiescommonsidpenroller.copetition.id.none', array($id)), array('key' => 'error'));
      $this->redirect("/");
      return;
    }

    // If the petition already has a username then do not present
    // a form and just redirect.
    if ($coPetition['CoPetition']['co_enrollment_flow_id'] != '654' ) {
    foreach($coPetition['EnrolleeCoPerson']['Identifier'] as $identifier) {
      if($identifier['type'] == $config['HumanitiesCommonsIdpEnrollerConfig']['username_id_type'] && 
          !empty($identifier['identifier']) ) {
            ( $debug ?  $this->log($logPrefix . "Petition already includes the identifier so silently continuing with enrollment") : null);
            $this->redirect($onFinish);
          }
    }
    }
    // If the authenticated identifier was provided by the Humanities Commons IdP
    // add the username as an identifier, update name and email in LDAP using
    // values in the petition, do not show a form to collect username and
    // instead redirect.
    list($username, $domain) = explode("@", $coPetition['CoPetition']['authenticated_identifier']);
    $idType = $config['HumanitiesCommonsIdpEnrollerConfig']['username_id_type'];
    $coPersonId = $coPetition['EnrolleeCoPerson']['id'];
    if ($domain == $config['HumanitiesCommonsIdpEnrollerConfig']['hc_idp_scope']) {
      ( $debug ?  $this->log($logPrefix . "Authenticated identifier provided by Humanities Commons IdP so adding it.") : null);

      $newIdentifier                               = array();
      $newIdentifier['Identifier']['identifier']   = $username;
      $newIdentifier['Identifier']['type']         = $idType;
      $newIdentifier['Identifier']['status']       = SuspendableStatusEnum::Active;
      $newIdentifier['Identifier']['co_person_id'] = $coPersonId;

      $err = false;

      try {
        $this->CoPetition->EnrolleeCoPerson->Identifier->create();
        $args = array();
        $args['provision'] = false;
        $this->CoPetition->EnrolleeCoPerson->Identifier->save($newIdentifier, $args);

      } catch (Exception $e) {
        $err = true;
        $this->Flash->set($e->getMessage(), array('key' => 'error'));
      }

      if ($err) {
        // If we cannot attach the identifier to the CoPerson record we abort
        // the enrollment flow entirely since the user will not able to get
        // to the application without it.
        $this->log($logPrefix . "ERROR: could not save identifier $username. Redirecting user to / and ending flow.");
        $this->redirect("/");
      } 

      ( $debug ? $this->log($logPrefix . "Saved new identifier $username") : null);
      
      // Also provision the name and email to the existing LDAP record
      // using details from the petition.
      if (!$this->_updateLdap($username, $coPetition, $config)) {
        // Failure to update LDAP here is probably not critical since name and email
        // are not necessary for HumanitiesCommons IdP authentication, although having
        // no email makes password recovery harder.
        $this->log($logPrefix . "Unable to update LDAP record for username $username");
      }

      // Create some history
      $actorCoPersonId = $this->Session->read('Auth.User.co_person_id');
      
      $txt = _txt('pl.humanitiescommonsidpenroller.copetition.identifier.autoselected', array($username, $idType));
      
      $this->CoPetition->EnrolleeCoPerson->HistoryRecord->record($coPersonId,
                                                                 null,
                                                                 null,
                                                                 $actorCoPersonId,
                                                                 ActionEnum::CoPersonEditedPetition,
                                                                 $txt);

      $this->CoPetition->CoPetitionHistoryRecord->record($id,
                                                         $actorCoPersonId,
                                                         PetitionActionEnum::AttributesUpdated,
                                                         $txt);

      // Redirect to continue the enrollment flow
      ( $debug ? $this->log($logPrefix . "is complete and redirecting browser to " . print_r($onFinish, true)) : null);
      $this->redirect($onFinish);

    }

    // Display a form to allow the user to specify username
    if($this->request->is('post')) {
      ( $debug ? $this->log($logPrefix . "received POST data and processing it now") : null);
      // POST, process the request
      if (preg_match('/^[a-zA-Z0-9]+$/', $this->request->data['username'])) {
        $username = $this->request->data['username'];
        $idType = $config['HumanitiesCommonsIdpEnrollerConfig']['username_id_type'];
        $coPersonId = $coPetition['EnrolleeCoPerson']['id'];

        $newIdentifier                               = array();
        $newIdentifier['Identifier']['identifier']   = $username;
        $newIdentifier['Identifier']['type']         = $idType;
        $newIdentifier['Identifier']['status']       = SuspendableStatusEnum::Active;
        $newIdentifier['Identifier']['co_person_id'] = $coPersonId;

        $err = false;

        try {
          $this->CoPetition->EnrolleeCoPerson->Identifier->create();
          $args = array();
          $args['provision'] = false;
          $this->CoPetition->EnrolleeCoPerson->Identifier->save($newIdentifier, $args);

        } catch (Exception $e) {
          $err = true;
          $this->Flash->set($e->getMessage(), array('key' => 'error'));
          unset($this->request->data['username']);
        }

        if (!$err) {
          // Create some history
          $actorCoPersonId = $this->Session->read('Auth.User.co_person_id');
          
          $txt = _txt('pl.humanitiescommonsidpenroller.copetition.identifier.selected', array($username, $idType));
          
          $this->CoPetition->EnrolleeCoPerson->HistoryRecord->record($coPersonId,
                                                                     null,
                                                                     null,
                                                                     $actorCoPersonId,
                                                                     ActionEnum::CoPersonEditedManual,
                                                                     $txt);

          $this->CoPetition->CoPetitionHistoryRecord->record($id,
                                                             $actorCoPersonId,
                                                             PetitionActionEnum::AttributesUpdated,
                                                             $txt);

          $this->redirect($onFinish);
        }
            
        // Problem saving, fall through render form again
        $this->log($logPrefix . "ERROR: problem saving $username. Rendering form again.");
      }

      // Bad username input, fall through render form again
      $this->log($logPrefix . "WARNING: bad username input. Rendering form again.");
    }

    // GET, fall through to display view
    ( $debug ? $this->log($logPrefix . "received GET so displaying form to collect username") : null);
  }

  /**
   * Set a cookie to flag when the enrollment flow is an identity linking flow
   * and forced reauthentication should be used.
   *
   * @since  COmanage Registry v3.2.0
   * @param  Integer $id       CO Petition ID
   * @param  Array   $onFinish Redirect target on completion
   */

  protected function execute_plugin_selectEnrollee($id, $onFinish) {
    $logPrefix = "HumanitiesCommonsIdpEnrollerCoPetitionsController execute_plugin_selectEnrollee ";

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

    // Use the petition id to find the petition
    $args = array();
    $args['conditions']['CoPetition.id']                 = $id;
    $args['contain']                                     = array();
    $args['contain']['EnrolleeCoPerson']['Identifier']   = array();
    $args['contain']['EnrolleeCoPerson']['Name']         = array();
    $args['contain']['EnrolleeCoPerson']['EmailAddress'] = array();
    $args['contain']['CoEnrollmentFlow']                 = array();

    $coPetition = $this->CoPetition->find('first', $args);

    if (empty($coPetition)) {
      $this->log($logPrefix . "ERROR: Could not find petition with id $id. Redirecting browser to /");
      $this->Flash->set(_txt('er.humanitiescommonsidpenroller.copetition.id.none', array($id)), array('key' => 'error'));
      $this->redirect("/");
      return;
    }
    // We only want to fire on account linking flows
    if ($coPetition['CoEnrollmentFlow']['match_policy'] != EnrollmentMatchPolicyEnum::Self || ! strpos( $coPetition['CoEnrollmentFlow']['name'], 'Additional Login Method' ) ) {
      ( $debug ?  $this->log($logPrefix . "Not an account linking flow so redirecting") : null);
      $this->redirect($onFinish);
    }

    // Set cookie to flag that we want forced re-authentication because this
    // is an identity linking flow that may use the same IdP, e.g. Google
    // gateway for both identities.
    $forced_reauth_cookie_name = 'registry_forced_reauth_requested';
    setcookie($forced_reauth_cookie_name, "1", time() + 300, '/', $_SERVER['HTTP_HOST'], true, true);
    ( $debug ?  $this->log($logPrefix . "Set cookie $forced_reauth_cookie_name to indicate forced reauthentication requested") : null);

    $this->redirect($onFinish);
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
   * Update the record in LDAP
   * - precondition: record with uid=username exists in LDAP
   *
   * @since  COmanage Directory 1.1.0
   * @param  String $username RDN of the record to be updated
   * @param  Array $coPetition CoPetition for this enrollment
   * @param  Array $config Configuration for the plugin
   * @return Boolen true if LDAP updated false otherwise
   */

  protected function _updateLdap($username, $coPetition, $config) {
    $logPrefix = "HumanitiesCommonsIdpEnrollerCoPetitionsController _updateLdap ";

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

    $mail = $this->_parseEmail($coPetition, $config);
    list($givenName, $sn) = $this->_parseName($coPetition, $config);

    $basedn = $config['HumanitiesCommonsIdpEnrollerConfig']['ldap_basedn'];
    $dn = "uid=$username,$basedn";

    $attributes              = array();
    $attributes['givenName'] = $givenName;
    $attributes['sn']        = $sn;
    $attributes['cn']        = "$givenName $sn";
    $attributes['mail']      = $mail;

    // Update the record
    if(!@ldap_modify($cxn, $dn, $attributes)) {
      $msg = ldap_error($cxn) . " : " .  strval(ldap_errno($cxn));
      $this->log($logPrefix . "Error when modifying DN: " . $msg);
      return false;
    }
  
    // Drop the connection
    ldap_unbind($cxn);

    return true;
  }
}
