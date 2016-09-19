<?php
/**
 * COmanage Registry MLA Humanities Commons IdP Enroller Accounts Provision View
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

  // Add page title
  $params = array('title' => _txt('pl.humanitiescommonsidpenroller.provision.view.title'));
  print $this->element("pageTitleAndButtons", $params);

  $submit_label = _txt('op.add');
  
  print $this->Form->create(false);
  
  if(!empty($vv_petitionId)) {
    print $this->Form->hidden('petitionId', array('default' => $vv_petitionId)) . "\n";
  }
  
  if(!empty($vv_petitionToken)) {
    print $this->Form->hidden('petitionToken', array('default' => $vv_petitionToken)) . "\n";
  }
?>
<div>
  <div id="tabs-attributes">
    <div class="fields" style="overflow:hidden;">
      <div class="ui-widget modelbox">
        <?php print _txt('pl.humanitiescommonsidpenroller.provision.view.password.constraints'); ?>
      </div>
      <div class="ui-widget modelbox">
        <div class="boxtitle">
          <strong><?php print _txt('pl.humanitiescommonsidpenroller.provision.view.username.label'); ?></strong>
          <span class="required">*</span>
        </div>
        <table class="ui-widget">
          <tbody>
            <tr class="line0">
              <td>
                <strong><?php print _txt('pl.humanitiescommonsidpenroller.provision.view.username.label'); ?></strong>
              </td>
              <td>
                <?php 
                  $args = array();
                  $args['type'] = 'text';
                  $args['label'] = false;
                  $args['value'] = $vv_username;
                  $args['disabled'] = 'disabled';
                  print $this->Form->input('username', $args); 
                ?>    
              </td>
            </tr>
                <?php 
                  $args = array();
                  $args['type'] = 'hidden';
                  $args['value'] = $vv_username;
                  print $this->Form->input('username', $args); 
                ?>    
          </tbody>
        </table>
      </div>
      <div class="ui-widget modelbox">
        <div class=boxtitle">
          <strong><?php print _txt('pl.humanitiescommonsidpenroller.provision.view.password.new'); ?></strong>
          <span class="required">*</span>
        </div>
        <table class="ui-widget">
          <tbody>
            <tr class="line0">
              <td>
                <strong><?php print _txt('pl.humanitiescommonsidpenroller.provision.view.password.label'); ?></strong>
              </td>
              <td>
                <?php 
                  $args = array();
                  $args['label'] = false;
                  print $this->Form->password('password1', $args); 
                ?>    
              </td>
            </tr>
            <tr class="line1">
              <td>
                <strong><?php print _txt('pl.humanitiescommonsidpenroller.provision.view.password.confirm.label'); ?></strong>
              </td>
              <td>
                <?php 
                  $args = array();
                  $args['label'] = false;
                  print $this->Form->password('password2', $args); 
                ?>    
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      <div class="ui-widget modelbox">
        <?php print _txt('pl.humanitiescommonsidpenroller.provision.view.password.aftersubmit'); ?>
      </div>
      <table class="ui-widget">
        <tbody>
          <tr>
            <td>
              <em><span class="required"><?php print _txt('fd.req'); ?></span></em><br />
            </td>
            <td class="submitCell">
              <?php print $this->Form->submit(_txt('op.submit')); ?>
            </td>
          </tr>
        </tbody>
      </table>

    </div>
  </div>
</div>
<?php
  print $this->Form->end();
