<?php
    $pdf = new FPDF('P','mm','A4');
    $pdf->AliasNbPages();
    $pdf->AddPage();

    $pdf->SetX(15); $pdf->SetY($pdf->GetY() + 5);
    $pdf->SetFont('Arial','BU',10);
    $pdf->Cell(95,8,$title,0,0,'L');
    $pdf->Cell(95,8,"Banner ID: #".$post_id,0,0,'R');
    $pdf->Ln(15);
    
    // Overview
    $pdf->SetFillColor(255,255,255);
    $pdf->SetTextColor(0,0,0);
    $pdf->SetX(10);
    $pdf->SetFont('Arial','B',9);
    $pdf->Cell(55,5,'',0,0,'C',1);
    $pdf->SetFillColor(227,227,227);
    $pdf->Cell(45,5,'Count',1,0,'C',1);
    $pdf->Cell(45,5,'Cost Per',1,0,'R',1);
    $pdf->Cell(45,5,'Total',1,0,'R',1);
    $pdf->Ln(5);
    $pdf->SetFillColor(255,255,255);
    $pdf->SetX(10);
    $pdf->SetFont('Arial','',9);
    
    $pdf->Cell(55,5,'Impressions',1,0,'C',1);
    $pdf->Cell(45,5,$impressions,1,0,'C',1);
    $pdf->Cell(45,5,$per_impression,1,0,'R',1);
    $pdf->Cell(45,5,$impression_total_output,1,0,'R',1);
    $pdf->Ln(5);
    
    $pdf->Cell(55,5,'Clicks',1,0,'C',1);
    $pdf->Cell(45,5,$clicks,1,0,'C',1);
    $pdf->Cell(45,5,$per_click,1,0,'R',1);
    $pdf->Cell(45,5,$click_total_output,1,0,'R',1);
    $pdf->Ln(5);
    
    $pdf->Cell(55,5,'',0,0,'C',1);
    $pdf->Cell(45,5,'',0,0,'C',1);
    $pdf->SetFont('Arial','B',9);
    $pdf->Cell(45,5,'TOTAL',0,0,'R',1);
    $pdf->SetFillColor(227,227,227);
    $pdf->Cell(45,5,$total_made_output,1,0,'R',1);
    $pdf->Ln(15);

    // IPs
    $pdf->SetFillColor(227,227,227);
    $pdf->SetTextColor(0,0,0);
    $pdf->SetX(10);
    $pdf->SetFont('Arial','B',9);
    $pdf->Cell(95,5,'Date/Time',1,0,'C',1);
    $pdf->Cell(95,5,'IP Address',1,0,'C',1);
    $pdf->Ln(5);
    $pdf->SetFillColor(255,255,255);
    $pdf->SetX(10);
    $pdf->SetFont('Arial','',9);
    foreach ($clicks_detailed as $click) {
        $pdf->Cell(95,5,date('d/m/Y h:i:sa', $click->timestamp),1,0,'C',1);
        $pdf->Cell(95,5,$click->ip_address,1,0,'C',1);
        $pdf->Ln(5);
    }

    $pdf_path = plugin_dir_path(__FILE__)."../../outputs/output.pdf";
    $pdf->Output($pdf_path, "F");
?>
