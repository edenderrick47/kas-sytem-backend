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



	require_once ("../../../loader.php");

	$db = new Conexion;
	$user = new User;
	$core = new Core;
	$userData = $user->cdp_getUserData();

	$year = date('Y');

	$data = array();


	for ($month = 1; $month <= 12; $month ++){

    	$sql="SELECT IFNULL(SUM(total_order), 0) as total FROM cdb_consolidate WHERE month(c_date)='$month' AND year(c_date)='$year'"; 
	       
        $db->cdp_query($sql); 
        $total_data= $db->cdp_registro();

		$data[] = number_format($total_data->total, 2,'.','');
        
    }
	echo json_encode($data);
