<?php

class MYPDF extends TCPDF {

	//Page header
	public function Header() {
		// Logo
		$image_file = base_url().'assets/images/logoFacyt.jpg';
		$this->Image($image_file, 20, 5, 25, 25, 'JPG', '', 'T', false, 300, '', false, false, 0, false, false, false);
		// Set font
        $this->SetFont('helvetica', 'B', 18);
        
		// Title
		$this->SetY(13);
        $this->Cell(210, 10, "Sistema Automático de Mantenimiento FACYT", 0, false, 'C', 0, '', 0, false, 'M', 'M');
        
        $this->SetFont('helvetica', 'B', 15);
		$this->SetY(23);
        $this->Cell(200, 10, "Mantenimientos por Bien", 0, false, 'C', 0, '', 0, false, 'M', 'M');
		$this->SetY(28);
        $this->SetFont('helvetica', 'B', 8);
        $this->Cell(320, 10, "Fecha Impresión: " . date("d/m/Y"), 0, false, 'C', 0, '', 0, false, 'M', 'M');
                
		$this->SetY(30);
        $this->writeHTML("<hr>", true, false, false, false, '');
	}

	// Page footer
	public function Footer() {
		// Position at 15 mm from bottom
		$this->SetY(-15);
		// Set font
		$this->SetFont('helvetica', 'I', 8);
		// Page number
		$this->Cell(0, 10, 'Página '.$this->getAliasNumPage().'/'.$this->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');
	}
}

$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Ennio Gonzalez');
$pdf->SetTitle('Mantenimientos por Bien');
$pdf->SetSubject('Mantenimientos por Bien');
$pdf->SetKeywords('TCPDF, PDF, example, test, guide');

// set default header data
$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);

// set header and footer fonts
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', 14));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP + 10, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

// set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// set some language-dependent strings (optional)
if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
	require_once(dirname(__FILE__).'/lang/eng.php');
	$pdf->setLanguageArray($l);
}

// set font
$pdf->SetFont('helvetica', 'B', 20);

// add a page
$pdf->AddPage();

$pdf->SetFont('helvetica', '', 10);

$bienAnterior = "";
$opcionAnterior = "";
$tbl = "";

foreach ($datos as $elemento) {

    if($bienAnterior != $elemento['bie_id']){

        if($bienAnterior != ""){
            $tbl .= "<br/><br/>";
        }

        $tbl .= "
        <table cellspacing=\"0\" cellpadding=\"1\" style=\"border: 1px solid black;font-size: 18pt;text-align: center;\">
            <tr >
                <td> " . $elemento['bien'] ."</td>
            </tr>
        </table>
            
        ";
        $opcionAnterior = "";
    }

    if($opcionAnterior != $elemento['opcion']){
        
        $tbl .= "
            <h3>" . $elemento['opcion'] ."</h3>
            <table>
                <thead>
                    <tr style=\"font-weight: bold; \">
                        <th style=\"width:25%\">Documento </th>
                        <th style=\"width:25%\">Inicio</th>
                        <th style=\"width:25%\">Fin</th>
                        <th style=\"width:25%\">Estatus</th>
                    </tr>
                </thead>
            </table>
        ";

    }

    $tbl .= "<hr><table><tr>
                    <td style=\"width:25%\">" . $elemento['documento'] . "</td>
                    <td style=\"width:25%\">" . $elemento['fec_ini'] . "</td>
                    <td style=\"width:25%\">" . $elemento['fec_fin'] . "</td>
                    <td style=\"width:25%\">" . $elemento['estatus'] . "</td>
                </tr></table>";

    


    $bienAnterior = $elemento['bie_id'];
    $opcionAnterior = $elemento['opcion'];
}


$pdf->writeHTML($tbl, true, false, false, false, '');


// -----------------------------------------------------------------------------

//Close and output PDF document
$pdf->Output('MantenimientosPorBien.pdf', 'I');