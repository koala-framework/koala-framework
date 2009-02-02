<?php
/**
 * @group Pdf
 */
class Vps_Pdf_Test extends PHPUnit_Framework_TestCase
{
    /**
     * Http host muss gesetzt werden da sonst in der config von tcpdf
     * in Zeile 65 die Variable nicht gesetzt wird.
     *
     */
    public function setUp()
    {
        $_SERVER['HTTP_HOST'] = Zend_Registry::get('testDomain');
    }

    public function tearDown()
    {
        unset($_SERVER['HTTP_HOST']);
    }

    public function testFonts()
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
        $pdf->textBox("helvetican");
        $pdf->Ln(5);
        $pdf->SetFont('arial', '', 10);
        $pdf->textBox("arial");
    }
    public function testNotExistingFonts()
    {
        $this->setExpectedException('Vps_Exception');
        $pdf = new Vps_Pdf_TcPdf();
        $pdf->addPage();
        $pdf->Ln(5);
        $pdf->SetFont('helveticablub', '', 10);
    }
}
