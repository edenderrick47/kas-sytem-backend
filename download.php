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



if (!isset($_GET['file']) || empty($_GET['file'])) {
 exit();
}
$root = "backups/";
$file = basename($_GET['file']);
$path = $root.$file;
$type = '';

if (is_file($path)) {
 $size = filesize($path);
 if (function_exists('mime_content_type')) {
 $type = mime_content_type($path);
 } else if (function_exists('finfo_file')) {
 $info = finfo_open(FILEINFO_MIME);
 $type = finfo_file($info, $path);
 finfo_close($info);
 }
 if ($type == '') {
 $type = "application/force-download";
 }
 // Definir headers
 header("Content-Type: $type");
 header("Content-Disposition: attachment; filename=$file");
 header("Content-Transfer-Encoding: binary");
 header("Content-Length: " . $size);
 // Descargar archivo
 readfile($path);
} else {
 die("El archivo no existe.");
}

?>