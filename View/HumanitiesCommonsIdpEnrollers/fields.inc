<!--
/**
 * COmanage Registry MLA Humanities Commons IdP Enroller Plugin Config Fields
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
-->
<?php
  // Determine if fields are editable
  $e = false;
  
  if(($this->action == "add" && $permissions['add']) || ($this->action == "edit" && $permissions['edit']))
    $e = true;
    
  // We shouldn't get here if we don't have at least read permission, but check just in case
  
  if(!$e && !$permissions['view'])
    return false;
  
  $l = 1;
  
  print $this->Form->hidden('co_enrollment_flow_wedge_id', array('default' => $humanities_commons_idp_enrollers[0]['CoEnrollmentFlowWedge']['id'])) . "\n";
?>
<table id="<?php print $this->action; ?>_humanities_commons_idp_enroller" class="ui-widget">
  <tbody>
    <tr class="line<?php print ($l % 2); $l++; ?>">
      <td>
        <strong class="fieldTitle"><?php print _txt('pl.humanitiescommonsidpenroller.config.ldap_serverurl'); ?></strong><font class="required">*</font><br />
        <font class="desc"><?php print _txt('pl.humanitiescommonsidpenroller.config.ldap_serverurl.desc'); ?></font>
      </td>
      <td>
        <?php print ($e ? $this->Form->input('ldap_serverurl', array('size' => 50)) : Sanitize::html($humanities_commons_idp_enrollers[0]['HumanitiesCommonsIdpEnroller']['ldap_serverurl'])); ?>
      </td>
    </tr>
    <tr class="line<?php print ($l % 2); $l++; ?>">
      <td>
        <strong class="fieldTitle"><?php print _txt('pl.humanitiescommonsidpenroller.config.ldap_binddn'); ?></strong><font class="required">*</font><br />
        <font class="desc"><?php print _txt('pl.humanitiescommonsidpenroller.config.ldap_binddn.desc'); ?></font>
      </td>
      <td>
        <?php print ($e ? $this->Form->input('ldap_binddn', array('size' => 50)) : Sanitize::html($humanities_commons_idp_enrollers[0]['HumanitiesCommonsIdpEnroller']['ldap_binddn'])); ?>
      </td>
    </tr>
    <tr class="line<?php print ($l % 2); $l++; ?>">
      <td>
        <strong class="fieldTitle"><?php print _txt('pl.humanitiescommonsidpenroller.config.ldap_bind_password'); ?></strong><font class="required">*</font><br />
        <font class="desc"><?php print _txt('pl.humanitiescommonsidpenroller.config.ldap_bind_password.desc'); ?></font>
      </td>
      <td>
        <?php print ($e ? $this->Form->password('ldap_bind_password', array('size' => 50)) : "" ); ?>
      </td>
    </tr>
    <tr class="line<?php print ($l % 2); $l++; ?>">
      <td>
        <strong class="fieldTitle"><?php print _txt('pl.humanitiescommonsidpenroller.config.ldap_basedn'); ?></strong><font class="required">*</font><br />
        <font class="desc"><?php print _txt('pl.humanitiescommonsidpenroller.config.ldap_basedn.desc'); ?></font>
      </td>
      <td>
        <?php print ($e ? $this->Form->input('ldap_basedn', array('size' => 50)) : Sanitize::html($humanities_commons_idp_enrollers[0]['HumanitiesCommonsIdpEnroller']['ldap_basedn'])); ?>
      </td>
    </tr>
    <tr class="line<?php print ($l % 2); $l++; ?>">
      <td>
        <strong class="fieldTitle"><?php print _txt('pl.humanitiescommonsidpenroller.config.username_id_type'); ?></strong><font class="required">*</font><br />
        <font class="desc"><?php print _txt('pl.humanitiescommonsidpenroller.config.username_id_type.desc'); ?></font>
      </td>
      <td>
        <?php print ($e ? $this->Form->input('username_id_type', array('size' => 50)) : Sanitize::html($humanities_commons_idp_enrollers[0]['HumanitiesCommonsIdpEnroller']['username_id_type'])); ?>
      </td>
    </tr>
    <tr class="line<?php print ($l % 2); $l++; ?>">
      <td>
        <strong class="fieldTitle"><?php print _txt('pl.humanitiescommonsidpenroller.config.hc_idp_scope'); ?></strong><font class="required">*</font><br />
        <font class="desc"><?php print _txt('pl.humanitiescommonsidpenroller.config.hc_idp_scope.desc'); ?></font>
      </td>
      <td>
        <?php print ($e ? $this->Form->input('hc_idp_scope', array('size' => 50)) : Sanitize::html($humanities_commons_idp_enrollers[0]['HumanitiesCommonsIdpEnroller']['hc_idp_scope'])); ?>
      </td>
    </tr>
    <tr class="line<?php print ($l % 2); $l++; ?>">
      <td>
        <strong class="fieldTitle"><?php print _txt('pl.humanitiescommonsidpenroller.config.debug'); ?></strong><font class="required">*</font><br />
        <font class="desc"><?php print _txt('pl.humanitiescommonsidpenroller.config.debug.desc'); ?></font>
      </td>
      <td>
        <?php print ($e ? $this->Form->input('debug', array('size' => 50)) : Sanitize::html($humanities_commons_idp_enrollers[0]['HumanitiesCommonsIdpEnroller']['debug'])); ?>
      </td>
    </tr>
    <tr>
      <td>
        <i><font class="required"><?php print _txt('fd.req'); ?></font></i><br />
      </td>
      <td>
        <?php
          if($e) {
            print $this->Form->submit($submit_label);
            print $this->Form->button(_txt('op.reset'), 
                                      array('type'=>'reset'));
          }
        ?>
      </td>
    </tr>
  </tbody>
</table>
