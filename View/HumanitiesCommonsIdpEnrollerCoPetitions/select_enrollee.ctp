<?php
/**
 * COmanage Registry MLA Humanities Commons IdP Enroller CoPetitions Petitioner Attributes View
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
  $params = array('title' => _txt('pl.humanitiescommonsidpenroller.copetition.select.view.title'));
  print $this->element("pageTitleAndButtons", $params);
 
  print $this->Form->create(false);
?>

<script type="text/javascript"> 
  $(function() {

$('.hc_fields, .co_topbox_hc_fields').hide();

$('.co-info-topbox input[type="radio"]').change(function( e ) {

e.preventDefault();

if( this.value == 'HC' ) {

 $('.hc_fields, .co_topbox_hc_fields').fadeToggle();

} else {

$('.hc_fields:visible, .co_topbox_hc_fields:visible').fadeToggle();

//lets empty the password fields so submit can go through
$('#password1').val('');
$('#password2').val('');

$('#selectEnrolleeForm').submit();

}

});

$("#selectEnrolleeForm").submit(function() {

//lets check if the user selected yes before checking password fields for validation
if( $('.co-info-topbox input[type="radio"]:checked').val() == 'yes' ) { 
 
// setup
  var valid = true;
  var pwdconstraints = /^(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{10,}$/; // minimum 10 chars, one int, one lower alpha, one upper alpha
  var usernameconstraints = /^[a-zA-Z0-9]+$/; // only ascii numbers and letters
  var username = $("#username").val();
  var pw1 = $("#password1").val();
  var pw2 = $("#password2").val();

  $("#coSpinner").show();
  $(".field-error").empty();
  $("input[type='password']").removeClass("form-error");

  // validate
  if (!usernameconstraints.test(username)) {
    $("#username").addClass("form-error");
    $("#username-error").html('<span class="error-message"><?php print _txt('pl.humanitiescommonsidpenroller.provision.view.username.constraints'); ?></span>');
    valid = false;
  }
  if (!pwdconstraints.test(pw1)) {
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
  }

});

//ends anonymous function
});

</script>

<div id="tabs-attributes">
  <div class="ui-corner-all co-info-topbox">
      <?php
        $options = array();
        $options['HC'] = 'Yes';
        $options['Other'] = 'No';
        $args['legend'] = 'Create Humanities Commons account?';
        print $this->Form->radio('idpselection', $options, $args);
      ?>
  </div>
  <div class="ui-state-highlight ui-corner-all co-info-topbox co_topbox_hc_fields">
    <p>
      <span class="ui-icon ui-icon-info co-info"></span>
      <strong><?php print _txt('pl.humanitiescommonsidpenroller.provision.view.password.constraints'); ?></strong>
    </p>
  </div>
  <div class="fields hc_fields" style="margin-top: 1em;">
    <div class="ui-widget modelbox hc_fields_two">
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
                $args['value'] = $vv_username;
                $args['disabled'] = 'disabled';
                print $this->Form->input('username', $args);
              ?>
              <span id="username-error" class="field-error"></span>
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
