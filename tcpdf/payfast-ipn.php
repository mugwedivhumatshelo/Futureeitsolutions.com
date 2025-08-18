<?php
require_once('tcpdf_min/tcpdf.php');

// Read raw POST from PayFast
$pfData = $_POST;

// Verify PayFast sandbox payment (basic verification)
$pfValid = true; // In production, validate checksum or call PayFast verification endpoint

if($pfValid && isset($pfData['payment_status']) && $pfData['payment_status'] == 'COMPLETE'){

    $email = $pfData['custom_str1'] ?? 'guest@example.com';
    $items = json_decode(file_get_contents("cart_{$email}.json"), true) ?? [];

    $invoiceNumber = time();
    $pdf = new TCPDF();
    $pdf->AddPage();
    $pdf->SetFont('helvetica', 'B', 16);
    $pdf->Cell(0,10,'INVOICE',0,1,'C');
    $pdf->SetFont('helvetica','',12);
    $pdf->Ln(5);
    $pdf->Cell(0,8,"Invoice #: $invoiceNumber",0,1);
    $pdf->Cell(0,8,"Customer Email: $email",0,1);
    $pdf->Ln(5);

    // Table header
    $pdf->SetFont('helvetica','B',12);
    $pdf->Cell(90,8,'Product',1);
    $pdf->Cell(30,8,'Qty',1);
    $pdf->Cell(40,8,'Price',1);
    $pdf->Cell(30,8,'Total',1);
    $pdf->Ln();

    $pdf->SetFont('helvetica','',12);
    $total = 0;
    foreach($items as $item){
        $qty = $item['qty'] ?? 1;
        $price = $item['price'] ?? 0;
        $lineTotal = $qty*$price;
        $total += $lineTotal;
        $pdf->Cell(90,8,$item['name'],1);
        $pdf->Cell(30,8,$qty,1);
        $pdf->Cell(40,8,number_format($price,2),1);
        $pdf->Cell(30,8,number_format($lineTotal,2),1);
        $pdf->Ln();
    }

    $pdf->SetFont('helvetica','B',12);
    $pdf->Cell(160,8,'Grand Total',1);
    $pdf->Cell(30,8,number_format($total,2),1);

    $filename = "invoice_{$invoiceNumber}.pdf";
    $pdf->Output(__DIR__.'/'.$filename, 'F'); // Save PDF on server

    // Optionally send email to customer with PDF
    // mail($email, "Your Invoice", "Thank you for your order", $filename);

    // Clear saved cart
    unlink("cart_{$email}.json");

    http_response_code(200);
}
?>
