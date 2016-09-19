<?php
/**
 * COmanage Registry MLA Humanities Commons IdP Enroller Account Model
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
  
class HumanitiesCommonsIdpEnrollerAccount extends AppModel {
  // Define class name for cake
  public $name = "HumanitiesCommonsIdpEnrollerAccount";
  
  // No database table for this model
  public $useTable = false;

  // Validation rules for password
  public $validate = array(
    'password1' => array(
      'notBlank' => array(
        'rule' => 'notBlank',
        'message' => 'Please insert password',
        'last' => true
      ),
      'minLength' => array(
        'rule' => array('minLength', 10),
        'message' => 'Your password must be at least 10 characters long',
        'last' => true
      ),
      'containsNumber' => array(
        'rule' => '/^.*[0-9]+.*$/',
        'message' => 'Your password must contain at least one number',
        'last' => true
      ),
      'containsLower' => array(
        'rule' => '/^.*[a-z]+.*$/',
        'message' => 'Your password must contain at least one lowercase letter',
        'last' => true
      ),
      'containsUpper' => array(
        'rule' => '/^.*[A-Z]+.*$/',
        'message' => 'Your password must contain at least one uppercase letter',
        'last' => true
      ),
    ),
    'password2' => array(
      'notBlank' => array(
        'rule' => 'notBlank',
        'message' => 'Please enter your password again',
        'last' => true
      ),
      'same' => array(
        'rule' => array('samePasswordValues', 'password1'),
        'message' => 'The two passwords must match',
        'last' => true
      )
    )
  );

  /**
   * Compare two password values
   *
   * @since  COmanage Directory 1.1.0
   * @param  Array $check input from validation call
   * @param  String $fieldName name of the field being validated
   * @return Boolean true if values are the same or false otherwise
   */

  public function samePasswordValues($check, $fieldName) {
    $password2 = $check['password2'];
    $password1 = $this->data[$this->name][$fieldName];

    if ($password1 != $password2) {
      return false;
    } else {
      return true;
    }
  }
}
