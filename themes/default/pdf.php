<?php
    $pdf = new FPDF('P','mm','A4');
    $pdf->AliasNbPages();
    $pdf->AddPage();
    
    $image_url = str_replace('http://'.$_SERVER['HTTP_HOST'], $_SERVER['DOCUMENT_ROOT'], $image);
    if (file_exists($image_url)) {
        $imagesize = getimagesize($image_url);
        $imagesize[0] = ($imagesize[0] * 25.4) / 72;
        $imagesize[1] = ($imagesize[1] * 25.4) / 72;
        $imw = 190;
        $imh = 20;
        if ($imagesize[0] > $imagesize[1]) {
            if ($imagesize[0] > 190) {
                $wpc = 190 / $imagesize[0];
                $imh = $imagesize[1] * $wpc;
            } else {
                $imh = $imagesize[1];
                $imw = $imagesize[0];
            }
        } else {
            if ($imagesize[1] > 20) {
                $wph = 20 / $imagesize[1];
                $imw = $imagesize[0] * $wph;
            } else {
                $imh = $imagesize[1];
                $imw = $imagesize[0];
            }
        }

        $pdf->Image(str_replace('http://'.$_SERVER['HTTP_HOST'], $_SERVER['DOCUMENT_ROOT'], $image), 10, 6, $imw, $imh);
    }
    
    $pdf->SetX(15); $pdf->SetY($pdf->GetY()+ $imh + 5);
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
