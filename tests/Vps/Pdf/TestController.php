<?php
class Vps_Pdf_TestController extends Vps_Controller_Action
{
    public function indexAction()
    {
        $pdf = new Vps_Pdf_TcPdf();
        $pdf->addPage();
        $pdf->Ln(5);
        $pdf->SetFont('helvetica', '', 10);
        $pdf->textBox("helvetica");
        $pdf->Ln(5);
        $pdf->SetFont('comic', '', 10);
        $pdf->textBox("comic");
        $pdf->Ln(5);
        $pdf->SetFont('helvetican', '', 10);
        $pdf->textBox("helveticanb", 'B');
        $pdf->Ln(5);
        $pdf->SetFont('helvetican', '', 10);
        $pdf->textBox("helvetican");
        $pdf->Ln(5);
        $pdf->SetFont('arial', '', 10);
        $pdf->textBox("arial", '');
        $pdf->Ln(5);
        $pdf->SetFont('arial', '', 10);
        $pdf->textBox("arialb", 'B');
        $data = array();
        $data['contents'] = $pdf->Output("text.php", "S");
        $data['mimeType'] = "application/pdf";
        Vps_Media_Output::output($data);
        exit;

    }
}
