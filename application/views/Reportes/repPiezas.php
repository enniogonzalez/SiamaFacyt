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
        $this->Cell(200, 10, "Piezas", 0, false, 'C', 0, '', 0, false, 'M', 'M');
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
$pdf->SetTitle('Piezas');
$pdf->SetSubject('Piezas');
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
        <tr>
            <td style=\"width:18%;\"> <strong>Nombre:</strong></td>
            <td style=\"width:35%;\">" . $datos['nombre'] . "</td>
            <td style=\"width:15%;\"><strong>Estatus:</strong></td>
            <td style=\"width:32%;\">" . $datos['estatus'] . "</td>
        </tr>
        <tr>
            <td style=\"width:18%;\"> <strong>Modelo:</strong></td>
            <td style=\"width:35%;\">" . $datos['modelo'] . "</td>
            <td style=\"width:15%;\"><strong>Serial:</strong></td>
            <td style=\"width:32%;\">" . $datos['pie_ser'] . "</td>
        </tr>
        <tr>
            <td style=\"width:18%;\"> <strong>Inventario UC:</strong></td>
            <td style=\"width:35%;\">" . $datos['inv_uc'] . "</td>
            <td style=\"width:15%;\"><strong>Bien:</strong></td>
            <td style=\"width:32%;\">" . $datos['nombie'] . "</td>
        </tr>
        <tr>
            <td style=\"width:18%;\"> <strong>Marca:</strong></td>
            <td style=\"width:35%;\">" . $datos['nommar'] . "</td>
            <td style=\"width:15%;\"><strong>Proveedor:</strong></td>
            <td style=\"width:32%;\">" . $datos['nompro'] . "</td>
        </tr>
        <tr>
            <td style=\"width:18%;\"> <strong>Partida:</strong></td>
            <td style=\"width:35%;\">" . $datos['nompar'] . "</td>
            <td style=\"width:15%;\"><strong>Fabricación:</strong></td>
            <td style=\"width:32%;\">" . $datos['fec_fab'] . "</td>
        </tr>
        <tr>
            <td style=\"width:18%;\"> <strong>Instalacón:</strong></td>
            <td style=\"width:35%;\">" . $datos['fec_ins'] . "</td>
            <td style=\"width:15%;\"><strong>Adquisición:</strong></td>
            <td style=\"width:32%;\">" . $datos['fec_adq'] . "</td>
        </tr>
        <tr>
            <td style=\"width:18%;\"> <strong>Tipo Adquisición:</strong></td>
            <td style=\"width:72%;\">" . $datos['tip_adq'] . "</td>
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
$pdf->Output('Pieza' . substr("0000000000" . trim($datos['par_id'] ),-10)  . '.pdf', 'I');
