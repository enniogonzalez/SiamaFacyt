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
        $this->Cell(210, 10, "Sistema de Gestión de Mantenimiento FACYT", 0, false, 'C', 0, '', 0, false, 'M', 'M');
        
        $this->SetFont('helvetica', 'B', 15);
		$this->SetY(23);
        $this->Cell(200, 10, "Formato de Proveedor", 0, false, 'C', 0, '', 0, false, 'M', 'M');
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
$pdf->SetTitle('Formato de Proveedor');
$pdf->SetSubject('Formato de Proveedor');
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

// -----------------------------------------------------------------------------
//                          Informacion Encabezado
// -----------------------------------------------------------------------------

$data = array(
	"pro_id"        =>"",
	"rif"           =>"",
	"raz_soc"       =>"",
	"reg_nac_con"   =>"",
	"direccion"     =>"",
	"telefonos"     =>"",
	"correo"        =>"",
	"observaciones" =>"",
);
$tbl = "
    <table cellspacing=\"0\" cellpadding=\"1\" style=\"border: 1px solid black;\">
        <tr>
            <td style=\"width:15%;\"> <strong>RIF:</strong></td>
            <td style=\"width:35%;\">" . $datos['rif'] . "</td>
            <td style=\"width:15%;\"><strong>RNC:</strong></td>
            <td style=\"width:35%;\">" . $datos['reg_nac_con'] . "</td>
        </tr>
        <tr>
            <td style=\"width:15%;\"> <strong>Razón Social:</strong></td>
            <td style=\"width:75%;\">" . $datos['raz_soc'] . "</td>
        </tr>
        <tr>
            <td style=\"width:15%;\"> <strong>Correo:</strong></td>
            <td style=\"width:35%;\">" . $datos['correo'] . "</td>
            <td style=\"width:15%;\"><strong>Teléfonos:</strong></td>
            <td style=\"width:35%;\">" . $datos['telefonos'] . "</td>
        </tr>
        <tr>
            <td style=\"width:15%;\"> <strong>Dirección:</strong></td>
            <td style=\"width:75%;\">" . $datos['direccion'] . "</td>
        </tr>
        <tr>
            <td style=\"width:15%;\"> <strong>Observaci&oacute;n:</strong></td>
            <td style=\"width:75%;\">" . $datos['observaciones'] . "</td>
        </tr>
</table>
";

$pdf->writeHTML($tbl, true, false, false, false, '');


// -----------------------------------------------------------------------------

//Close and output PDF document
$pdf->Output('formatoProveedor' . substr("0000000000" . trim($datos['pro_id'] ),-10)  . '.pdf', 'I');
