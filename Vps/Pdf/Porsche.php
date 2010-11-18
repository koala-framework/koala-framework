<?php
class Vps_Pdf_Porsche
{
    public static function footer(Vps_Pdf_TcPdf $pdf)
    {
        $pdf->SetFont("Arial", "", 7);
        $pdf->writeHTMLCell(60, 0, 20, 260, $html = "<b>Porsche Austria GmbH & Co OG</b><br />Gro&szlig;handel f&uuml;r<br />Volkswagen");
        $pdf->setXY(63, 260);
        $pdf->MultiCell(60, 0, "A-5021 Salzburg\nVogelweiderstraße 75\nPostfach 164\nTelefon +43/662/4681/\nTelefax +43/662/4681/\nhttp://www.porsche-holding.com", 0, 'L');
        $pdf->setXY(106, 260);
        $pdf->MultiCell(60, 0, "Bankverbindung:\nBank Austria Creditanstalt\nKonto 438 208 506\nBLZ 12000\nIBAN AT61 1200 0004 3820 8506\nBIC: BKAUATWW", 0, 'L');
        $pdf->setXY(149, 260);
        $pdf->MultiCell(60, 0, "Rechtsform: Offene Gesellschaft\nSitz: Salzburg\nFN 27015 d / Landesgericht Salzburg\nDVR: 0088412\nUID-NR.: ATU 34242904", 0, 'L');
                
        $pdf->Image(VPS_PATH.'/Vps/Pdf/Porsche/footer.jpg', 16, 285, 180, 0);
    }
}
