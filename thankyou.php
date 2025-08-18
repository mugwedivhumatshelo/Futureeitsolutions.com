<?php
require('fpdf/fpdf.php'); // Make sure you have fpdf library in this folder

$email = $_GET['email'] ?? 'guest';
$cart_json = $_GET['cart'] ?? null;

if(!$cart_json && isset($_COOKIE["cart_{$email}_for_invoice"])) {
    $cart_json = $_COOKIE["cart_{$email}_for_invoice"];
}

$cart = json_decode($cart_json, true) ?? [];

class PDF extends FPDF {
    function Header() {
        $this->SetFont('Arial','B',16);
        $this->Cell(0,10,'Futuree IT Solutions',0,1,'C');
        $this->Ln(5);
    }
}

$pdf = new PDF();
$pdf->AddPage();
$pdf->SetFont('Arial','',12);

if(empty($cart)){
    $pdf->Cell(0,10,'No items found in cart.',0,1);
} else {
    $pdf->Cell(0,10,"Invoice for: $email",0,1);
    $pdf->Ln(5);

    $pdf->Cell(90,10,'Product',1);
    $pdf->Cell(30,10,'Qty',1);
    $pdf->Cell(30,10,'Price',1);
    $pdf->Cell(40,10,'Total',1);
    $pdf->Ln();

    $total = 0;
    foreach($cart as $item){
        $qty = $item['qty'] ?? 1;
        $price = $item['price'];
        $line_total = $qty * $price;
        $total += $line_total;

        $pdf->Cell(90,10,$item['name'],1);
        $pdf->Cell(30,10,$qty,1);
        $pdf->Cell(30,10,"R".number_format($price,2),1);
        $pdf->Cell(40,10,"R".number_format($line_total,2),1);
        $pdf->Ln();
    }

    $pdf->Ln(5);
    $pdf->Cell(150,10,'Grand Total',1);
    $pdf->Cell(40,10,"R".number_format($total,2),1);
}

$pdf->Output('I','Invoice.pdf');
?>
