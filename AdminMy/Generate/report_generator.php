<?php
require_once('../../db_connection.php');
require_once('fpdf/fpdf.php');
error_reporting(E_ALL);
ini_set('display_errors', 1);

$title = $_POST['report_title'];

$query = "SELECT 
    ap.student_number,
    s.name_with_initials,
    c.company_name,
    p.preference_name
FROM
    assigned_preferences ap
JOIN
    student s ON ap.student_number = s.student_number
JOIN
    company c ON ap.selected_companyID = c.companyID
JOIN
    preferences p ON ap.preference_id = p.preference_id
WHERE
    ap.selected_companyID IS NOT NULL;
";

$result = mysqli_query($conn, $query);

class PDF extends FPDF
{
    function Header()
    {
        // Left align Header
        $this->SetFont('Arial', '', 10);
        $this->Cell(0, 10, 'Generated on: ' . date('Y-m-d H:i:s'), 0, 1, 'L');
    }

    function Footer()
    {
        // Left align Footer
        $this->SetY(-15);
        $this->SetFont('Arial', '', 8);
        $this->Cell(0, 10, 'Department of Industrial Management, Faculty of Science, University of Kelaniya', 0, 0, 'L');
        
        // Right align Footer for page number
        $this->Cell(0, 10, 'Page ' . $this->PageNo(), 0, 0, 'R');
    }
}

$pdf = new PDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 16);
$pdf->Cell(0, 10, $title, 0, 1, 'C');
$pdf->Ln(10);

$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(35, 10, 'Student Number', 1);
$pdf->Cell(45, 10, 'Name with Initials', 1);
$pdf->Cell(70, 10, 'Company Name', 1);
$pdf->Cell(40, 10, 'Preference Name', 1);
$pdf->Ln();

while ($row = mysqli_fetch_assoc($result)) {
    $pdf->Cell(35, 10, $row['student_number'], 1);
    $pdf->Cell(45, 10, $row['name_with_initials'], 1);
    $pdf->Cell(70, 10, $row['company_name'], 1);
    $pdf->Cell(40, 10, $row['preference_name'], 1);
    $pdf->Ln();
}

mysqli_close($conn);

ob_end_clean();
$pdf->Output('internship_placement_report.pdf', 'D');