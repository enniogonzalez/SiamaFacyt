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
        $this->Cell(200, 10, "Formato de Usuario", 0, false, 'C', 0, '', 0, false, 'M', 'M');
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
$pdf->SetTitle('Formato de Usuario');
$pdf->SetSubject('Formato de Usuario');
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

$tbl = "
    <table cellspacing=\"0\" cellpadding=\"1\" style=\"border: 1px solid black;\">
        <tr >
			<td style=\"width:15%;\"> <strong>Usuario:</strong></td>
			<td style=\"width:35%;\">" . $datos['username'] . "</td>
			<td style=\"width:15%;\"><strong>Nombre:</strong></td>
			<td style=\"width:35%;\">" . $datos['nombre'] . "</td>
		</tr>";

if($datos['loc_nom'] != ""){
	$tbl .= "
	<tr >
		<td style=\"width:15%;\"> <strong>Rol:</strong></td>
		<td style=\"width:35%;\">" . $datos['rol_nom'] . "</td>
		<td style=\"width:15%;\"><strong>Localización:</strong></td>
		<td style=\"width:35%;\">" . $datos['loc_nom'] . "</td>
	</tr>
	<tr>
		<td> <strong>Correo:</strong></td>
		<td colspan='3'>" . $datos['correo'] . "</td>
	</tr>";
}else{
	$tbl .= "
	<tr >
		<td style=\"width:15%;\"> <strong>Rol:</strong></td>
		<td style=\"width:35%;\">" . $datos['rol_nom'] . "</td>
		<td style=\"width:15%;\"><strong>Correo:</strong></td>
		<td style=\"width:35%;\">" . $datos['correo'] . "</td>
	</tr>";
}
		
$tbl .= "
        <tr >
            <td> <strong>Observaci&oacute;n:</strong></td>
            <td colspan='3'>" . $datos['observaciones'] . "</td>
        </tr>
</table>
";

$pdf->writeHTML($tbl, true, false, false, false, '');


// -----------------------------------------------------------------------------

//Close and output PDF document
$pdf->Output('formatoUsuario' . substr("0000000000" . trim($datos['usu_id'] ),-10)  . '.pdf', 'I');
