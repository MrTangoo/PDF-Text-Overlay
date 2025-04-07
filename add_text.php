<?php
require 'vendor/autoload.php';

use setasign\Fpdi\Fpdi;

// Function to calculate adjusted coordinates based on percentage
function calculateAdjustedCoordinates($percentage, $dimension)
{
    return ($percentage / 100) * $dimension;
}

$texte = $_POST['texte'] ?? '';
$x = isset($_POST['x']) ? (float) $_POST['x'] : 0; // Get the X position as a percentage
$y = isset($_POST['y']) ? (float) $_POST['y'] : 0; // Get the Y position as a percentage

// File paths
$pdf_tmp = __DIR__ . '/uploads/v.pdf';
$output_path = __DIR__ . '/uploads/v.pdf';

if (!file_exists($pdf_tmp)) {
    die('Le fichier PDF n\'existe pas.');
}

// Initialize the FPDI object
$pdf = new Fpdi();
$page_count = $pdf->setSourceFile($pdf_tmp);

for ($i = 1; $i <= $page_count; $i++) {
    $pdf->AddPage();
    $tplIdx = $pdf->importPage($i);
    $pdf->useTemplate($tplIdx, 0, 0);

    if ($i == 1) { // Only modify the first page for the moment
        $page_dimensions = $pdf->getTemplateSize($tplIdx);
        $pdf_width = $page_dimensions['width'];
        $pdf_height = $page_dimensions['height'];

        // Convert percentage to actual coordinates
        $adjusted_x = calculateAdjustedCoordinates($x, $pdf_width);
        $adjusted_y = $pdf_height - calculateAdjustedCoordinates($y, $pdf_height); // Invert Y for PDF

        // Set font and position the text
        $pdf->SetFont('Arial', 'B', 16);
        $pdf->SetXY($adjusted_x, $adjusted_y);
        $pdf->Cell(0, 10, utf8_decode($texte), 0, 1);
    }
}

// Output the modified PDF to a file
$pdf->Output($output_path, 'F');

// Provide download link and preview
echo "<h2>Aperçu du PDF</h2><a href='/uploads/v.pdf' download>Télécharger</a><br><br>";
echo "<iframe src='/uploads/v.pdf' style='width: 824px; height: 1190px;'></iframe>";
echo "<br><br><a href='index.html'>Retour</a>";
