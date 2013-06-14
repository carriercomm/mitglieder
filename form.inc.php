<?php

require_once("fpdf/fpdf.php");
require_once("fpdf/fpdi.php");

function fillPDFForm($url, $codeprefix, $fdf, $fdf_opt, $porto, $destFile) {
	$code = file_get_contents("https://poststelle.junge-piraten.de/code.php?data=" . urlencode(serialize(array($fdf, $fdf_opt))));

	$rand = rand(1000,9999);
	file_put_contents("/tmp/phppdf-".$rand."-tpl.pdf", file_get_contents("https://wiki.junge-piraten.de/wiki/Spezial:Dateipfad/SepaLastschriftmandat.pdf"));
	file_put_contents("/tmp/phppdf-".$rand.".fdf", create_fdf($fdf, $fdf_opt));
	system("pdftk /tmp/phppdf-".$rand."-tpl.pdf fill_form /tmp/phppdf-".$rand.".fdf output /tmp/phppdf-" . $rand . ".pdf flatten");
	unlink("/tmp/phppdf-" . $rand . "-tpl.pdf");
	unlink("/tmp/phppdf-" . $rand . ".fdf");

	$fpdf = new FPDI("P", "mm", "A4");
	$fpdf->SetMargins(0,0,0);
	$fpdf->SetFont('Courier','',8);
	$fpdf->SetTextColor(0,0,0);

	$fpdf->setSourceFile("/tmp/phppdf-" . $rand . ".pdf");
	$fpdf->AddPage();
	$tpl = $fpdf->importPage(1);
	$fpdf->useTemplate($tpl, 0, 0, 0, 0, true);
	unlink("/tmp/phppdf-" . $rand . ".pdf");

	// Barcode
	drawBarcodeVert($fpdf, 22, 280, 19, $codeprefix . "-" . $code);
	$fpdf->Text(23, 278, $codeprefix . "-" . $code);

	if ($porto > 0) {
		// Frankieren
		$pagecount = $fpdf->setSourceFile("marken".$porto.".pdf");
		$tpl = $fpdf->importPage(1);
		$fpdf->useTemplate($tpl, 0, 0, 0, 0, true);
	}

	$fpdf->Output($destFile, "F");
	return $code;
}

function create_fdf ($strings, $keys) {
	$fdf = "%FDF-1.2\n%âãÏÓ\n";
	$fdf .= "1 0 obj \n<</FDF<</Fields[";

	foreach ($strings as $key => $value) {
		$key = addcslashes($key, "\n\r\t\\()");
		$value = addcslashes(iconv("UTF8", "ISO-8859-1", $value), "\n\r\t\\()");
		$fdf .= "<</V({$value})/T({$key})>>";
	}
	foreach ($keys as $key => $value) {
		$key = addcslashes($key, "\n\r\t\\()");
		$value = addcslashes($value, "\n\r\t\\()");
		$fdf .= "<</V/{$value}/T({$key})>>";
	}

	$fdf .= "]";
	$fdf .= ">>>>\nendobj\ntrailer\n<</Root 1 0 R>>\n";
	$fdf .= "%%EOF\n";

	return $fdf;
}

function getCode($key) {
	$keys = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9', 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', '-', '.', ' ', '$', '/', '+', '%', '*');
	$codes = array(    // 0 added to add an extra space
            '0001101000',   /* 0 */
            '1001000010',   /* 1 */
            '0011000010',   /* 2 */
            '1011000000',   /* 3 */
            '0001100010',   /* 4 */
            '1001100000',   /* 5 */
            '0011100000',   /* 6 */
            '0001001010',   /* 7 */
            '1001001000',   /* 8 */
            '0011001000',   /* 9 */
            '1000010010',   /* A */
            '0010010010',   /* B */
            '1010010000',   /* C */
            '0000110010',   /* D */
            '1000110000',   /* E */
            '0010110000',   /* F */
            '0000011010',   /* G */
            '1000011000',   /* H */
            '0010011000',   /* I */
            '0000111000',   /* J */
            '1000000110',   /* K */
            '0010000110',   /* L */
            '1010000100',   /* M */
            '0000100110',   /* N */
            '1000100100',   /* O */
            '0010100100',   /* P */
            '0000001110',   /* Q */
            '1000001100',   /* R */
            '0010001100',   /* S */
            '0000101100',   /* T */
            '1100000010',   /* U */
            '0110000010',   /* V */
            '1110000000',   /* W */
            '0100100010',   /* X */
            '1100100000',   /* Y */
            '0110100000',   /* Z */
            '0100001010',   /* - */
            '1100001000',   /* . */
            '0110001000',   /*   */
            '0101010000',   /* $ */
            '0101000100',   /* / */
            '0100010100',   /* + */
            '0001010100',   /* % */
            '0100101000'    /* * */
        );
	return $codes[array_search(strtoupper($key), $keys)];
}

function drawBarcodeHoriz($fpdf, $x, $y, $width, $code, $lwidth = 0.8) {
	$code = "*" . $code . "*";
	$height = strlen($code) * 13 * $lwidth;

	$fpdf->setLineWidth($lwidth);

	$color = 0;
	$y_ = $y + $height;
	for ($k=0;$k<strlen($code);$k++) {
		$bar = getCode($code[$k]);
		for ($i=0;$i<strlen($bar);$i++) {
			$fpdf->setDrawColor($color);
			for ($j = 0; $j < intval($bar{$i})+1; $j++) {
				$fpdf->Line($x,$y_,$x+$width,$y_);
				$y_ -= $lwidth;
			}
			$color = ($color == 0) ? 255 : 0;
		}
	}
}

function drawBarcodeVert($fpdf, $x, $y, $height, $code, $lwidth = 0.8) {
	$code = "*" . $code . "*";
	$width = strlen($code) * 13 * $lwidth;

	$fpdf->setLineWidth($lwidth);

	$color = 0;
	$x_ = $x + $width;
	for ($k=0;$k<strlen($code);$k++) {
		$bar = getCode($code[$k]);
		for ($i=0;$i<strlen($bar);$i++) {
			$fpdf->setDrawColor($color);
			for ($j = 0; $j < intval($bar{$i})+1; $j++) {
				$fpdf->Line($x_,$y,$x_,$y+$height);
				$x_ -= $lwidth;
			}
			$color = ($color == 0) ? 255 : 0;
		}
	}
}
