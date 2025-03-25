<?php
require 'vendor/autoload.php';

use setasign\Fpdi\Fpdi;

// Récupérer les données du formulaire
$texte = $_POST['texte'];
$x = (float) $_POST['x']; // Récupérer la position X en pourcentage
$y = (float) $_POST['y']; // Récupérer la position Y en pourcentage
$pdf_tmp = __DIR__ . '/uploads/v.pdf';
$output_path = __DIR__ . '/output/output.pdf';

$pdf = new Fpdi();
$page_count = $pdf->setSourceFile($pdf_tmp);

for ($i = 1; $i <= $page_count; $i++) {
    $pdf->AddPage();
    $tplIdx = $pdf->importPage($i);
    $pdf->useTemplate($tplIdx, 0, 0);

    $pdf->SetFont('Arial', 'B', 16);

    if ($i == 1) {
        // Récupérer les dimensions réelles du PDF
        $page_dimensions = $pdf->getTemplateSize($tplIdx);
        $pdf_width = $page_dimensions['width'];
        $pdf_height = $page_dimensions['height'];

        // Ajustement des coordonnées
        $adjusted_x = ($x / 100) * $pdf_width;  // Convertir le pourcentage en pixels
        $adjusted_y = $pdf_height - (($y / 100) * $pdf_height);  // Convertir le pourcentage en pixels et ajuster pour l'origine du PDF

        // Ajouter le texte à la position spécifiée
        $pdf->SetXY($adjusted_x, $adjusted_y);
        $pdf->Cell(0, 10, utf8_decode($texte), 0, 1);
    }
}

$pdf->Output($output_path, 'F');

// Afficher un lien pour télécharger le PDF modifié
echo "<h2>Aperçu du PDF</h2><a href='output/output.pdf' download>Télécharger</a><br><br>";
echo "<iframe src='output/output.pdf' style='width: 824px; height: 1190px;'></iframe>";
echo "<br><br><a href='index.php'>Retour</a>";
