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





require_once("../loader.php");

$db = new Conexion;

$search = cdp_sanitize($_REQUEST['track']);
$search = intval($search);


$sql_digits = "SELECT * FROM cdb_settings";

$db->cdp_query($sql_digits);
$db->cdp_execute();
$trackd = $db->cdp_registro();

$digits = $trackd->track_digit;

$format_track = str_pad($search, "" . $digits . "", "0", STR_PAD_LEFT);

$sql = "SELECT order_no FROM cdb_customers_packages WHERE order_no = '" . $format_track . "'";

$db->cdp_query($sql);
$db->cdp_execute();

$data = $db->cdp_registro();

if ($data) {

	echo json_encode(true);
} else {

	echo json_encode(false);
}
