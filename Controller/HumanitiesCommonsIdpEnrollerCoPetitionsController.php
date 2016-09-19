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
    'Identifier');

  /**
   * Callback after petitioner attributes collected
   *
   * @since  COmanage Directory 1.1.0
   * @param  Integer $id ID of the petition
   * @param  Mixed $onFinish Passed to redirect after function exit
   * @return void
   */

  protected function execute_plugin_petitionerAttributes($id, $onFinish) {
    $logPrefix = "HumanitiesCommonsIdpEnrollerCoPetitionsController execute_plugin_petitionerAttributes ";

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

    // Use the petition id to find the petition
    $args = array();
    $args['conditions']['CoPetition.id'] = $id;
    $args['contain'] = false;
    $coPetition = $this->CoPetition->find('first', $args);
    if (empty($coPetition)) {
      $this->Flash->set(_txt('er.humanitiescommonsidpenroller.copetition.id.none', array($id)), array('key' => 'error'));
      $this->redirect("/");
      return;
    }

    // Confirm the petition has an associated token
    if(!isset($coPetition['CoPetition']['petitioner_token'])) {
      $this->Flash->set(_txt('er.humanitiescommonsidpenroller.copetition.token.none', array($id)), array('key' => 'error'));
      $this->redirect("/");
    }

    $token = $coPetition['CoPetition']['petitioner_token'];

    // Write the petition ID and token into the session
    $this->Session->write('HC.petitionId', $id);
    ( $debug ? $this->log($logPrefix . "Wrote petition id $id into the session") : null);

    $this->Session->write('HC.petitionToken', $token);
    ( $debug ? $this->log($logPrefix . "Wrote petition token $token into the session") : null);

    $this->redirect($onFinish);
  }
}
