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
require_once("../../../helpers/backups_function.php");

/* == Delete SQL Backup == */
if (isset($_POST['restoreBackup'])) :

  $ok = doRestore($_POST['restoreBackup']);


  if ($ok == 1) { ?>

    <div class="alert alert-info" id="success-alert">
      <p><span class="icon-info-sign"></span><i class="close icon-remove-circle"></i>
        Database restored successfully!
      </p>
    </div>
  <?php
  } else {  ?>

    <div class="alert alert-info" id="danger-alert">
      <p><span class="icon-info-sign"></span><i class="close icon-remove-circle"></i>
        <?php echo $lang['message_ajax_error2']; ?>
      </p>
    </div>
<?php
  }

endif;
