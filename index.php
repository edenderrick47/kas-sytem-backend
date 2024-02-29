<?php




if (!file_exists('config/config.php')) {
   header("location: install");
   exit;
}
require_once("loader.php");

$user = new User();
$core = new Core();
// ... ask if we are logged in here:
if ($user->cdp_loginCheck() == true) {

   if ($_SESSION['userlevel'] == 9) {

      include('views/dashboard/index.php');
   } else if ($_SESSION['userlevel'] == 1) {

      include('views/dashboard/dashboard_client.php');
   } else if ($_SESSION['userlevel'] == 2) {

      include('views/dashboard/index.php');
   } else if ($_SESSION['userlevel'] == 3) {

      include('views/dashboard/dashboard_driver.php');
   }
} else {

   header("location: login.php");
   exit;
}
