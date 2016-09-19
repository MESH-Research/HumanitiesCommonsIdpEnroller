<?php
/**
 * COmanage Registry MLA Humanities Commons IdP Enroller Plugin Language File
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
  
global $cm_lang, $cm_texts;

// When localizing, the number in format specifications (eg: %1$s) indicates the argument
// position as passed to _txt.  This can be used to process the arguments in
// a different order than they were passed.

$cm_humanities_commons_idp_enroller_texts['en_US'] = array(
  // Titles, per-controller
  'ct.humanities_commons_idp_enroller_configs.1'  => 'Humanities Commons IdP Enroller Configuration',
  'ct.humanities_commons_idp_enroller_configs.pl' => 'Humanities Commons IdP Enroller Configurations',
  
  // Error messages
  'er.humanitiescommonsidpenroller.account.ldap' => 'Error provisioning account to LDAP',
  'er.humanitiescommonsidpenroller.account.noconfig' => 'Cannot find Humanities Commons IdP Enroller Configuration',
  'er.humanitiescommonsidpenroller.account.petition.id.session.none' => 'Cannot find petition ID in session',
  'er.humanitiescommonsidpenroller.account.petition.id.mismatch' => 'Submitted petition ID does not match that found in the session',
  'er.humanitiescommonsidpenroller.account.petition.id.missing' => 'Petition ID missing',
  'er.humanitiescommonsidpenroller.account.petition.none' => 'Cannot find petition with ID %1$s',
  'er.humanitiescommonsidpenroller.account.petition.token.mismatch' => 'Submitted petition token does not match that found in the session',
  'er.humanitiescommonsidpenroller.account.petition.token.missing' => 'Petition token missing',
  'er.humanitiescommonsidpenroller.account.petition.token.session.none' => 'Cannot find petition token in session',
  'er.humanitiescommonsidpenroller.account.petition.token.session.bad' => 'Bad petition token in session',
  'er.humanitiescommonsidpenroller.config.singleton' => 'Only one Humanities Commons IdP Enroller Configuration is permitted',
  'er.humanitiescommonsidpenroller.config.username' => 'Cannot find username in petition',
  'er.humanitiescommonsidpenroller.copetition.id.none' => 'Cannot find petition with ID %1$s',
  'er.humanitiescommonsidpenroller.copetition.token.none' => 'Cannot find token for petition with ID %1$s',
  
  // Plugin texts
  'pl.humanitiescommonsidpenroller.config.username_id_type'       => 'Username ID Type',
  'pl.humanitiescommonsidpenroller.config.username_id_type.desc'  => 'ID type to use for the provisioned username',
  'pl.humanitiescommonsidpenroller.config.ldap_serverurl'       => 'LDAP Server URL',
  'pl.humanitiescommonsidpenroller.config.ldap_serverurl.desc'  => 'URL for the Humanities Commons IdP LDAP server (ldap[s]://hostname[:port])',
  'pl.humanitiescommonsidpenroller.config.ldap_binddn'       => 'Bind DN',
  'pl.humanitiescommonsidpenroller.config.ldap_binddn.desc'  => 'DN to authenticate as to provision users',
  'pl.humanitiescommonsidpenroller.config.ldap_bind_password'       => 'Password',
  'pl.humanitiescommonsidpenroller.config.ldap_bind_password.desc'  => 'Password to use for authentication',
  'pl.humanitiescommonsidpenroller.config.ldap_basedn'       => 'People Base DN',
  'pl.humanitiescommonsidpenroller.config.ldap_basedn.desc'  => 'Base DN to provision accounts under',
  'pl.humanitiescommonsidpenroller.config.debug'       => 'Debug',
  'pl.humanitiescommonsidpenroller.config.debug.desc'  => 'Toggle for debug mode',
  'pl.humanitiescommonsidpenroller.config.menu'        => 'HC IdP Enroller Configuration',
  'pl.humanitiescommonsidpenroller.provision.view.title' => 'Choose Your Password for the Humanities Commons Login Server',
  'pl.humanitiescommonsidpenroller.provision.view.password.constraints' => 'Your password must be at least 10 characters long and include at least one number, lowercase letter, and uppercase letter.',
  'pl.humanitiescommonsidpenroller.provision.view.username.label' => 'Username',
  'pl.humanitiescommonsidpenroller.provision.view.password.new' => 'New Password',
  'pl.humanitiescommonsidpenroller.provision.view.password.label' => 'Password',
  'pl.humanitiescommonsidpenroller.provision.view.password.confirm.label' => 'Confirm Password',
  'pl.humanitiescommonsidpenroller.provision.view.password.aftersubmit' => 'Click "Submit" to set your password. You will then need to authenticate using your username and password.',


  'op.humanitiescommonsidpenroller.config.edit'       => 'Edit Humanities Commons IdP Enroller Configuration',
  'op.humanitiescommonsidpenroller.config.view'       => 'Humanities Commons IdP Enroller Configuration',
);
