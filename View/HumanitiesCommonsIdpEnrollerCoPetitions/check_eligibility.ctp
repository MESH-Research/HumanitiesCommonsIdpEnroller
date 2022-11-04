<?php
/**
 * COmanage Registry MLA Humanities Commons IdP Enroller CoPetitions Collect Identifier View
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
  if ($current_enrollment_flow_id=='604') {
  $params = array('title' => 'Check Username');
  } else {
  $params = array('title' => _txt('pl.humanitiescommonsidpenroller.copetition.view.title'));
  }
  print $this->element("pageTitleAndButtons", $params);

  print $this->Form->create(false);
?>

<script type="text/javascript">
  $("#collectIdentifierForm").submit(function() {
    // setup
    var valid = true;
    var usernameconstraints = /^[a-zA-Z0-9]+$/; // only ascii numbers and letters
    var username = $("#username").val();

    $("#coSpinner").show();
    $(".field-error").empty();
    $("input[type='password']").removeClass("form-error");

    // validate
    if (!usernameconstraints.test(username)) {
      $("#username").addClass("form-error");
      $("#username-error").html('<span class="error-message"><?php print _txt('pl.humanitiescommonsidpenroller.provision.view.username.constraints'); ?></span>');
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
      <strong><?php print _txt('pl.humanitiescommonsidpenroller.copetition.view.username.constraints'); ?></strong>
    </p>
  </div>
  <div class="fields" style="margin-top: 1em;">
    <div class="ui-widget modelbox">
      <table class="ui-widget">
        <tbody>
          <tr>
            <td>
              <strong><?php print _txt('pl.humanitiescommonsidpenroller.provision.view.username.label'); ?></strong>
              <span class="required">*</span>
            </td>
            <td>
              <?php
                $args = array();
                $args['type'] = 'text';
                $args['label'] = false;
                $args['required'] = 'required';
                if(!empty($vv_suggested_username)) {
                  $args['value'] = $vv_suggested_username;
                }
                if(!empty($this->Session->read('HASTAC_username'))) {
                  $args['value'] = $this->Session->read('HASTAC_username');
                }
                print $this->Form->input('username', $args);
              ?>
              <span id="username-error" class="field-error"></span>
            </td>
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
