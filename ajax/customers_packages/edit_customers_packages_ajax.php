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

ini_set('display_errors', 0);


require_once("../../loader.php");
require_once("../../helpers/querys.php");
require_once("../../helpers/phpmailer/class.phpmailer.php");
require_once("../../helpers/phpmailer/class.smtp.php");
require_once '../../helpers/vendor/autoload.php';
require_once '../../helpers/src/src/Twilio/autoload.php';

use Twilio\Rest\Client;

$user = new User;
$core = new Core;
$errors = array();

if (empty($_POST['tracking_purchase']))

    $errors['tracking_purchase'] = $lang['validate_field_ajax170'];

if (empty($_POST['provider_purchase']))

    $errors['provider_purchase'] = $lang['validate_field_ajax172'];

if (empty($_POST['price_purchase']))

    $errors['price_purchase'] = $lang['validate_field_ajax174'];

if (empty($_POST['sender_id']))
    $errors['sender_id'] = $lang['validate_field_ajax150'];

if (empty($_POST['sender_address_id']))
    $errors['sender_address_id'] = $lang['validate_field_ajax145'];


if (empty($_POST['agency']))
    $errors['agency'] = $lang['validate_field_ajax148'];

if (empty($_POST['origin_off']))
    $errors['origin_off'] = $lang['validate_field_ajax149'];


if (empty($_POST['order_item_category']))
    $errors['order_item_category'] = $lang['validate_field_ajax151'];

if (empty($_POST['order_package']))
    $errors['order_package'] = $lang['validate_field_ajax152'];

if (empty($_POST['order_courier']))
    $errors['order_courier'] = $lang['validate_field_ajax153'];

if (empty($_POST['order_service_options']))
    $errors['order_service_options'] = $lang['validate_field_ajax154'];

if (empty($_POST['order_deli_time']))
    $errors['order_deli_time'] = $lang['validate_field_ajax155'];

if (empty($_POST['status_courier']))
    $errors['status_courier'] = $lang['validate_field_ajax157'];

if (empty($errors)) {

    $settings = cdp_getSettingsCourier();
    $meter = $settings->meter;

    $min_cost_tax = $core->min_cost_tax;
    $min_cost_declared_tax = $core->min_cost_declared_tax;

    $dataShipment = array(
        'agency' =>  cdp_sanitize(intval($_POST["agency"])),
        'origin_off' =>  cdp_sanitize(intval($_POST["origin_off"])),
        'sender_id' =>  cdp_sanitize(intval($_POST["sender_id"])),
        'sender_address_id' =>  cdp_sanitize(intval($_POST["sender_address_id"])),
        'tracking_purchase' =>  cdp_sanitize($_POST["tracking_purchase"]),
        'provider_purchase' =>  cdp_sanitize($_POST["provider_purchase"]),
        'price_purchase' =>  cdp_sanitize(floatval($_POST["price_purchase"])),
        'order_package' =>  cdp_sanitize(intval($_POST["order_package"])),
        'order_item_category' =>  cdp_sanitize(intval($_POST["order_item_category"])),
        'order_courier' =>  cdp_sanitize(intval($_POST["order_courier"])),
        'order_service_options' =>  cdp_sanitize(intval($_POST["order_service_options"])),
        'order_deli_time' =>  cdp_sanitize(intval($_POST["order_deli_time"])),
        'status_courier' =>  cdp_sanitize(intval($_POST["status_courier"])),
        'order_id' =>  cdp_sanitize(intval($_POST["order_id"])),

    );

    $updateShip = cdp_updateCustomerPackages($dataShipment);

    $shipment_id =  cdp_sanitize(intval($_POST["order_id"]));

    if ($updateShip) {
        if (isset($_POST["packages"])) {
            cdp_deleteCustomersPackagesItems($shipment_id);

            $packages = json_decode($_POST['packages']);

            $sumador_total = 0;
            $sumador_valor_declarado = 0;
            $max_fixed_charge = 0;
            $sumador_libras = 0;
            $sumador_volumetric = 0;

            $precio_total = 0;
            $total_impuesto = 0;
            $total_descuento = 0;
            $total_seguro = 0;
            $total_peso = 0;
            $total_impuesto_aduanero = 0;
            $total_valor_declarado = 0;

            $tariffs_value = $_POST["tariffs_value"];
            $declared_value_tax = $_POST["declared_value_tax"];
            $insurance_value = $_POST["insurance_value"];
            $tax_value = $_POST["tax_value"];
            $discount_value = $_POST["discount_value"];
            $reexpedicion_value = $_POST["reexpedicion_value"];
            $price_lb = $_POST["price_lb"];
            $insured_value = $_POST["insured_value"];

            foreach ($packages as $package) {

                $dataAddresses = array(
                    'order_id' =>  $shipment_id,
                    'qty' =>  $package->qty,
                    'description' =>  $package->description,
                    'length' =>  $package->length,
                    'width' =>  $package->width,
                    'height' =>  $package->height,
                    'weight' =>  $package->weight,
                    'declared_value' =>  $package->declared_value,
                    'fixed_value' =>  $package->fixed_value,
                );

                cdp_insertCustomerPackagesItems($dataAddresses);

                // calculate weight columetric box size
                $total_metric = $package->length * $package->width * $package->height / $meter;
                $weight = $package->weight;

                // calculate weight x price
                if ($weight > $total_metric) {
                    $calculate_weight = $weight;
                    $sumador_libras += $weight;
                } else {
                    $calculate_weight = $total_metric;
                    $sumador_volumetric += $total_metric;
                }

                $precio_total = $calculate_weight * $price_lb;
                $sumador_total += $precio_total;
                $sumador_valor_declarado += $package->declared_value;
                $max_fixed_charge += $package->fixed_value;

                if ($sumador_total > $min_cost_tax) {
                    $total_impuesto = $sumador_total * $tax_value / 100;
                }

                if ($sumador_valor_declarado > $min_cost_declared_tax) {
                    $total_valor_declarado = $sumador_valor_declarado * $declared_value_tax / 100;
                }
            }

            $total_descuento = $sumador_total * $discount_value / 100;
            $total_peso = $sumador_libras + $sumador_volumetric;
            $total_seguro = $insured_value * $insurance_value / 100;
            $total_impuesto_aduanero = $total_peso * $tariffs_value;
            $total_envio = ($sumador_total - $total_descuento) + $total_seguro + $total_impuesto + $total_impuesto_aduanero + $total_valor_declarado + $max_fixed_charge + $reexpedicion_value;
        }

        $dataShipmentUpdateTotals = array(
            'order_id' =>  $shipment_id,
            'value_weight' =>  floatval($price_lb),
            'sub_total' =>  floatval($sumador_total),
            'tax_discount' =>  floatval($discount_value),
            'total_insured_value' => floatval($insured_value),
            'tax_insurance_value' => floatval($insurance_value),
            'tax_custom_tariffis_value' => floatval($tariffs_value),
            'tax_value' => floatval($tax_value),
            'declared_value' =>  floatval($declared_value_tax),
            'total_reexp' =>  floatval($reexpedicion_value),
            'total_declared_value' =>  floatval($total_valor_declarado),
            'total_fixed_value' =>  floatval($max_fixed_charge),
            'total_tax_discount' =>  floatval($total_descuento),
            'total_tax_insurance' =>  floatval($total_seguro),
            'total_tax_custom_tariffis' =>  floatval($total_impuesto_aduanero),
            'total_tax' =>  floatval($total_impuesto),
            'total_weight' =>  floatval($total_peso),
            'total_order' =>  floatval($total_envio),
        );

        $update = cdp_updateCustomerPackagesTotals($dataShipmentUpdateTotals);
        $shipment =   cdp_getCustomerPackage($shipment_id);
        $order_track =  $shipment->order_prefix . $shipment->order_no;

        if (isset($_FILES['filesMultiple']) && count($_FILES['filesMultiple']['name']) > 0 && $_FILES['filesMultiple']['tmp_name'][0] != '') {

            $target_dir = "../../order_files/";
            $deleted_file_ids = array();

            if (isset($_POST['deleted_file_ids']) && !empty($_POST['deleted_file_ids'])) {
                $deleted_file_ids = explode(",", $_POST['deleted_file_ids']);
            }

            foreach ($_FILES["filesMultiple"]['tmp_name'] as $key => $tmp_name) {

                if (!in_array($key, $deleted_file_ids)) {
                    $image_name = $order_track .  date("Y-m-d") . "_" . basename($_FILES["filesMultiple"]["name"][$key]);
                    $target_file = $target_dir . $image_name;
                    $imageFileType = pathinfo($target_file, PATHINFO_EXTENSION);
                    $imageFileZise = $_FILES["filesMultiple"]["size"][$key];

                    if ($imageFileZise > 0) {
                        move_uploaded_file($_FILES["filesMultiple"]["tmp_name"][$key], $target_file);
                        $imagen = basename($_FILES["filesMultiple"]["name"][$key]);
                    }

                    $target_file_db = "order_files/" . $image_name;
                    cdp_insertCustomerPackagesFiles($shipment_id, $target_file_db, $image_name, date("Y-m-d H:i:s"), '0', $imageFileType);
                }
            }
        }


        cdp_deleteCourierAddress($order_track);

        $sender_address_data = cdp_getSenderAddress(intval($_POST["sender_address_id"]));
        $sender_country = $sender_address_data->country;
        $sender_state = $sender_address_data->state;
        $sender_city = $sender_address_data->city;
        $sender_zip_code = $sender_address_data->zip_code;
        $sender_address = $sender_address_data->address;

        $_sender_country = cdp_getCountry($sender_country);
        $final_sender_country = $_sender_country['data'];

        $_sender_state = cdp_getState($sender_state);
        $final_sender_state = $_sender_state['data'];

        $sender_city = cdp_getCity($sender_city);
        $final_sender_city = $sender_city['data'];


        // SAVE ADDRESS FOR Shipments
        $dataAddresses = array(
            'order_id' =>   $shipment_id,
            'order_track' =>   $order_track,
            'sender_country' =>   $final_sender_country->name,
            'sender_state' =>   $final_sender_state->name,
            'sender_city' =>   $final_sender_city->name,
            'sender_zip_code' =>   $sender_zip_code,
            'sender_address' =>   $sender_address,
            'recipient_country' =>   '',
            'recipient_state' =>   '',
            'recipient_city' =>  '',
            'recipient_zip_code' =>   '',
            'recipient_address' =>   '',
        );

        cdp_insertCourierShipmentAddresses($dataAddresses);


        $messages[] = $lang['message_ajax_success_add_update'];
    } else {
        $errors['critical_error'] = $lang['message_ajax_error2'];
    }
}

if (!empty($errors)) {
    echo json_encode([
        'success' => false,
        'errors' => $errors
    ]);
} else {
    echo json_encode([
        'success' => true,
        'messages' => $messages,
        'shipment_id' => $shipment_id,
    ]);
}
