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
  // Add style overrides
  print $this->Html->css('HumanitiesCommonsIdpEnroller.default');

  // Add page title
  $params = array('title' => _txt('pl.humanitiescommonsidpenroller.provision.view.title'));
  print $this->element("pageTitleAndButtons", $params);

  print $this->Form->create(false);
  
  if(!empty($vv_petitionId)) {
    print $this->Form->hidden('petitionId', array('default' => $vv_petitionId)) . "\n";
  }
  
  if(!empty($vv_petitionToken)) {
    print $this->Form->hidden('petitionToken', array('default' => $vv_petitionToken)) . "\n";
  }
?>

<script type="text/javascript">
  $("#provisionForm").submit(function() {
    // setup
    var valid = true;
    var constraints = /^(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{10,}$/; // minimum 10 chars, one int, one lower alpha, one upper alpha
    var pw1 = $("#password1").val();
    var pw2 = $("#password2").val();

    $("#coSpinner").show();
    $(".field-error").empty();
    $("input[type='password']").removeClass("form-error");

    // validate
    if (!constraints.test(pw1)) {
      $("#password1").addClass("form-error");
      $("#password1-error").html('<span class="error-message"><?php print _txt('pl.humanitiescommonsidpenroller.provision.view.password.constraints'); ?></span>');
      valid = false;
    }
    if (pw1 != pw2) {
      $("#password2").addClass("form-error");
      $("#password2-error").html('<span class="error-message"><?php print _txt('pl.humanitiescommonsidpenroller.provision.view.password.confirm.error'); ?></span>');
      valid = false;
    }

    if (!valid) {
      $("#coSpinner").hide();
      return false;
    }
    return true;
  });
</script>

<div id="tabs-attributes">
  <div class="ui-state-highlight ui-corner-all co-info-topbox">
    <p>
      <span class="ui-icon ui-icon-info co-info"></span>
      <strong><?php print _txt('pl.humanitiescommonsidpenroller.provision.view.password.constraints'); ?></strong>
    </p>
  </div>
  <div class="fields" style="margin-top: 1em;">
    <div class="ui-widget modelbox">
      <div class=boxtitle">
        <strong><?php print _txt('pl.humanitiescommonsidpenroller.provision.view.password.new'); ?></strong>
        <span class="required">*</span>
      </div>
      <table class="ui-widget">
        <tbody>
          <tr>
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
              <?php
                $args = array();
                $args['type'] = 'hidden';
                $args['value'] = $vv_username;
                print $this->Form->input('username', $args);
              ?>
            </td>
          </tr>
          <tr>
            <td>
              <strong><?php print _txt('pl.humanitiescommonsidpenroller.provision.view.password.label'); ?></strong>
              <span class="required">*</span>
            </td>
            <td>
              <?php
                $args = array();
                $args['label'] = false;
                $args['required'] = 'required';
                print $this->Form->password('password1', $args);
              ?>
              <span id="password1-error" class="field-error"></span>
            </td>
          </tr>
          <tr>
            <td>
              <strong><?php print _txt('pl.humanitiescommonsidpenroller.provision.view.password.confirm.label'); ?></strong>
              <span class="required">*</span>
            </td>
            <td>
              <?php
                $args = array();
                $args['label'] = false;
                $args['required'] = 'required';
                print $this->Form->password('password2', $args);
              ?>
              <span id="password2-error" class="field-error"></span>
            </td>
          </tr>
          <tr>
            <td></td>
            <td><span class="desc"><?php print _txt('pl.humanitiescommonsidpenroller.provision.view.password.aftersubmit'); ?></span></td>
          </tr>
        </tbody>
      </table>
    </div>
    <table class="ui-widget submit-table">
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
<?php
  print $this->Form->end();
