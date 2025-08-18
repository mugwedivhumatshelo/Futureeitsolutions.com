<?php
// payfast_notify.php
require 'vendor/autoload.php'; // if using composer for tcpdf or adjust path

// Capture POST data from PayFast
$data = $_POST;
file_put_contents('payfast_notify_log.txt', date('Y-m-d H:i:s').' - '.json_encode($data)."\n", FILE_APPEND);

// Minimal verification (for sandbox; for live, verify signature and status)
if(isset($data['payment_status']) && $data['payment_status'] === 'COMPLETE') {
    $email = $data['email_address'] ?? 'guest';
    $amount = $data['amount_gross'] ?? 0;

    $ordersFile = 'orders.json';
    $orders = file_exists($ordersFile) ? json_decode(file_get_contents($ordersFile), true) : [];

    $orders[] = [
        'email' => $email,
        'amount' => $amount,
        'items' => [], // Optional: read from saved cart
        'timestamp' => date('Y-m-d H:i:s'),
        'pf_data' => $data
    ];

    file_put_contents($ordersFile, json_encode($orders, JSON_PRETTY_PRINT));

    // Optionally remove the cart
    $cartFile = "carts/cart_$email.json";
    if(file_exists($cartFile)) unlink($cartFile);
}
http_response_code(200); // PayFast expects 200
?>
