<?php
$email = isset($_POST['email']) ? preg_replace('/[^a-zA-Z0-9_\-@\.]/','',$_POST['email']) : 'guest';
$cartFile = "carts/cart_$email.json";

if(!file_exists($cartFile)){
    die("Cart not found.");
}

$cartItems = json_decode(file_get_contents($cartFile), true);
$totalAmount = 0;
foreach($cartItems as $item){ $totalAmount += $item['price'] * ($item['qty'] ?? 1); }

// PayFast Settings
$payfast = [
    'merchant_id' => '13824819',
    'merchant_key'=> 'mvgtrbslnwbl0',
    'passphrase'  => 'Futureeit2021', // optional
    'return_url'  => 'https://yourdomain.com/thankyou.php?email='.$email,
    'cancel_url'  => 'https://yourdomain.com/cart.html',
    'notify_url'  => 'https://yourdomain.com/payfast_notify.php',
    'sandbox'     => true // set false for live
];

$payfastURL = $payfast['sandbox'] ? 'https://sandbox.payfast.co.za/eng/process' : 'https://www.payfast.co.za/eng/process';

$postData = [
    'merchant_id' => $payfast['merchant_id'],
    'merchant_key'=> $payfast['merchant_key'],
    'return_url'  => $payfast['return_url'],
    'cancel_url'  => $payfast['cancel_url'],
    'notify_url'  => $payfast['notify_url'],
    'name_first'  => $email,
    'email_address'=> $email,
    'amount'      => number_format($totalAmount,2,'.',''),
    'item_name'   => 'Futuree IT Solutions Order'
];

// Generate signature
$pfOutput = '';
foreach($postData as $key=>$val){ $pfOutput .= "$key=".urlencode($val)."&"; }
if($payfast['passphrase']!='') $pfOutput.="passphrase=".urlencode($payfast['passphrase']);
$pfSignature = md5($pfOutput);
$postData['signature'] = $pfSignature;

// Auto-submit to PayFast
echo "<form id='payfastForm' action='$payfastURL' method='POST'>";
foreach($postData as $k=>$v) echo "<input type='hidden' name='$k' value='$v'>";
echo "</form><script>document.getElementById('payfastForm').submit();</script>";
?>
