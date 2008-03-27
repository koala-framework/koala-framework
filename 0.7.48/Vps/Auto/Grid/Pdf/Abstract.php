<?php
require_once 'tcpdf.php';

abstract class Vps_Auto_Grid_Pdf_Abstract extends TCPDF
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
        $date = new Zend_Date();
        $headStr = $date->get(Zend_Date::WEEKDAY).', '.date('d.m.Y, H:i');

        $yBefore = $this->GetY();
        $this->SetY(10);
        $this->Cell(0, 0, $headStr, 0, 1, 'R');
        $this->SetY($yBefore);
    }
}
