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
        $this->Cell(200, 10, "Bienes", 0, false, 'C', 0, '', 0, false, 'M', 'M');
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
$pdf->SetTitle('Bienes');
$pdf->SetSubject('Bienes');
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
            <td style=\"width:16%;\"> <strong>Nombre:</strong></td>
            <td style=\"width:34%;\">" . $datos['nombre'] . "</td>
            <td style=\"width:18%;\"><strong>Estatus:</strong></td>
            <td style=\"width:32%;\">" . $datos['estatus'] . "</td>
        </tr>
        <tr>
            <td > <strong>Modelo:</strong></td>
            <td >" . $datos['modelo'] . "</td>
            <td ><strong>Serial:</strong></td>
            <td >" . $datos['bie_ser'] . "</td>
        </tr>
        <tr>
            <td> <strong>Inventario UC:</strong></td>
            <td>" . $datos['inv_uc'] . "</td>
            <td><strong>Localización:</strong></td>
            <td>" . $datos['nomloc'] . "</td>
        </tr>
        <tr>
            <td> <strong>Marca:</strong></td>
            <td>" . $datos['nommar'] . "</td>
            <td><strong>Proveedor:</strong></td>
            <td>" . $datos['nompro'] . "</td>
        </tr>
        <tr>
            <td> <strong>Partidas:</strong></td>
            <td>" . $datos['nompar'] . "</td>
            <td><strong>Custodio:</strong></td>
            <td>" . $datos['nomcus'] . "</td>
        </tr>
        <tr>
            <td> <strong>Fabricación:</strong></td>
            <td>" . $datos['fec_fab'] . "</td>
            <td><strong>Instalacón:</strong></td>
            <td>" . $datos['fec_ins'] . "</td>
        </tr>
        <tr>
            <td> <strong>Adquisición:</strong></td>
            <td>" . $datos['fec_adq'] . "</td>
            <td><strong>Tipo Adquisición:</strong></td>
            <td>" . $datos['tip_adq'] . "</td>
        </tr>
        <tr>
            <td> <strong>Fuente Ali.:</strong></td>
            <td>" . $datos['fue_ali'] . "</td>
            <td><strong>Uso:</strong></td>
            <td>" . $datos['cla_uso'] . "</td>
        </tr>
        <tr>
            <td> <strong>Tipo:</strong></td>
            <td>" . $datos['tipo'] . "</td>
            <td><strong>Tec. Predo.:</strong></td>
            <td>" . $datos['tec_pre'] . "</td>
        </tr>
        <tr>
            <td> <strong>Riesgo:</strong></td>
            <td>" . $datos['riesgo'] . "</td>
            <td><strong>Voltaje:</strong></td>
            <td>" . $datos['med_vol'] . " " . $datos['uni_vol'] . "</td>
        </tr>
        <tr>
            <td> <strong>Amperaje:</strong></td>
            <td>" . $datos['med_amp'] . " " . $datos['uni_amp'] . "</td>
            <td><strong>Potencia:</strong></td>
            <td>" . $datos['med_pot'] . " " . $datos['uni_pot'] . "</td>
        </tr>
        <tr>
            <td> <strong>Frecuencia:</strong></td>
            <td>" . $datos['med_fre'] . " " . $datos['uni_fre'] . "</td>
            <td><strong>Capacidad:</strong></td>
            <td>" . $datos['med_cap'] . " " . $datos['uni_cap'] . "</td>
        </tr>
        <tr>
            <td> <strong>Presión:</strong></td>
            <td>" . $datos['med_pre'] . " " . $datos['uni_pre'] . "</td>
            <td><strong>Flujo:</strong></td>
            <td>" . $datos['med_flu'] . " " . $datos['uni_flu'] . "</td>
        </tr>
        <tr>
            <td> <strong>Temperatura:</strong></td>
            <td>" . $datos['med_tem'] . " " . $datos['uni_tem'] . "</td>
            <td><strong>Peso:</strong></td>
            <td>" . $datos['med_pes'] . " " . $datos['uni_pes'] . "</td>
        </tr>
        <tr >
            <td> <strong>Velocidad:</strong></td>
            <td>" . $datos['med_vel'] . " " . $datos['uni_vel'] . "</td>
        </tr>
        <tr >
            <td> <strong>Fab. Recomen.:</strong></td>
            <td colspan=\"3\">" . $datos['rec_fab'] . "</td>
        </tr>
        <tr >
            <td> <strong>Observaci&oacute;n:</strong></td>
            <td colspan=\"3\">" . $datos['observaciones'] . "</td>
        </tr>
    </table>
";

$pdf->writeHTML($tbl, true, false, false, false, '');

// -----------------------------------------------------------------------------
//                          Piezas del Bien
// -----------------------------------------------------------------------------

$pdf->SetFont('helvetica', '', 10);
$tbl =" <br>
        <h2>Piezas</h2>
        <table>
            <thead>
                <tr style=\"font-weight: bold; \">
                    <th style=\"width:55%\">Pieza </th>
                    <th style=\"width:35%\">Inventario UC</th>
                    <th style=\"width:10%\">Estatus</th>
                </tr>
            </thead>
        </table><hr>";


foreach ($datos['Piezas'] as $elemento) {
        $tbl .= "<table><tr>
                        <td style=\"width:55%\">" . $elemento['nombre'] . "</td>
                        <td style=\"width:35%\">" . $elemento['inv_uc'] . "</td>
                        <td style=\"width:10%\">" . $elemento['estatus'] . "</td>
                    </tr></table><hr>";
}

$tbl .= "<table><tr><td><strong>" . count($datos['Piezas']) . " Piezas</strong></td></tr></table>";


$pdf->writeHTML($tbl, true, false, false, false, '');



// -----------------------------------------------------------------------------

//Close and output PDF document
$pdf->Output('Bien' . substr("0000000000" . trim($datos['bie_id'] ),-10)  . '.pdf', 'I');
