<?php
abstract class Kwf_Grid_Pdf_Abstract extends Kwf_Pdf_TcPdf
{

    protected $_fields = array();

    public function setFields($fields)
    {
        $this->_fields = $fields;
    }

    public function footer()
    {
        $this->SetY($this->GetPageHeight() - 15);
        $this->Cell(0, 10, 'Page '.$this->PageNo().'/{nb}', 0, 0, 'R');
    }

    public function Header()
    {
        $date = new Kwf_Date('now');
        $headStr = $date->format('l, d.m.Y, H:i');

        $yBefore = $this->GetY();
        $this->SetY(10);
        $this->Cell(0, 0, $headStr, 0, 1, 'R');
        $this->SetY($yBefore);
    }
}
