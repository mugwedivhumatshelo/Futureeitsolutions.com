<?php
$data = json_decode(file_get_contents('php://input'), true);
$email = preg_replace('/[^a-zA-Z0-9_\-@\.]/','',$data['email']);
if(!is_dir('carts')) mkdir('carts',0777,true);
file_put_contents("carts/cart_$email.json", json_encode($data['cart']));
echo json_encode(['status'=>'ok']);
?>
