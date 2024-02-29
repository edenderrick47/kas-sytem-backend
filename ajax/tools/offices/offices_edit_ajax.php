<?php
// *************************************************************************
// *                                                                       *
// * CARGO PRO -  Integrated Web Shipping System                         *
// * Copyright (c) DOTCREATIVE. All Rights Reserved                            *
// *                                                                       *
// *************************************************************************
// *                                                                       *
// * Email: info@dotcreative.co.ke                                              *
// * Website: http://dotcreative.co.ke                                         *
// *                                                                       *
// *************************************************************************
// *                                                                       *
// * This software is furnished under a license and may be used and copied *
// * only  in  accordance  with  the  terms  of such  license and with the *
// * inclusion of the above copyright notice.                              *
// *                          *
// *                                                                       *
// *************************************************************************



require_once("../../../loader.php");
require_once("../../../helpers/querys.php");

$errors = array();

if (empty($_POST['name_off']))
  $errors['name_off'] = $lang['validate_field_ajax84'];

if (cdp_officeExistsjmbj1($_POST['name_off'], $_POST['id']))
  $errors['name_off'] = $lang['validate_field_ajax85'];

if (empty($_POST['code_off']))
  $errors['code_off'] = $lang['validate_field_ajax86'];

if (cdp_codeofficeExists($_POST['code_off'], $_POST['id']))
  $errors['code_off'] = $lang['validate_field_ajax87'];

if (empty($_POST['address']))
  $errors['address'] = $lang['validate_field_ajax88'];

if (empty($_POST['city']))
  $errors['city'] = $lang['validate_field_ajax89'];

if (empty($_POST['phone_off']))
  $errors['phone_off'] = $lang['validate_field_ajax90'];

if (CDP_APP_MODE_DEMO === true) {
?>

  <div class="alert alert-warning" id="success-alert">
    <p><span class="icon-minus-sign"></span><i class="close icon-remove-circle"></i>
      <span>Error! </span> There was an error processing the request
    <ul class="error">

      <li>
        <i class="icon-double-angle-right"></i>
        This is a demo version, this action is not allowed, <a class="btn waves-effect waves-light btn-xs btn-success" href="https://codecanyon.net/item/courier-deprixa-pro-integrated-web-system-v32/15216982" target="_blank">Buy DEPRIXA PRO</a> the full version and enjoy all the functions...

      </li>


    </ul>
    </p>
  </div>
  <?php
} else {
  if (empty($errors)) {

    $data = array(
      'name_off' => cdp_sanitize($_POST['name_off']),
      'code_off' => cdp_sanitize($_POST['code_off']),
      'address' => cdp_sanitize($_POST['address']),
      'city' => cdp_sanitize($_POST['city']),
      'phone_off' => cdp_sanitize($_POST['phone_off']),
      'id' => cdp_sanitize($_POST['id'])
    );

    $insert = cdp_UpdateOffices($data);
    if ($insert) {

      $messages[] = $lang['message_ajax_success_updated'];
    } else {

      $errors['critical_error'] = $lang['message_ajax_error1'];
    }
  }


  if (!empty($errors)) {
  ?>
    <div class="alert alert-danger" id="success-alert">
      <p><span class="icon-minus-sign"></span><i class="close icon-remove-circle"></i>
        <?php echo $lang['message_ajax_error2']; ?>
      <ul class="error">
        <?php
        foreach ($errors as $error) { ?>
          <li>
            <i class="icon-double-angle-right"></i>
            <?php
            echo $error;

            ?>

          </li>
        <?php

        }
        ?>


      </ul>
      </p>
    </div>



  <?php
  }

  if (isset($messages)) {

  ?>
    <div class="alert alert-info" id="success-alert">
      <p><span class="icon-info-sign"></span><i class="close icon-remove-circle"></i>
        <?php
        foreach ($messages as $message) {
          echo $message;
        }
        ?>
      </p>
    </div>

<?php
  }
}
?>