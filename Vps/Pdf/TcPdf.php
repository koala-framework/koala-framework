<?php
require_once 'tcpdf.php';
class Vps_Pdf_TcPdf extends TCPDF
{
    protected $_font = 'arial';
    protected $_font_bold = 'arialb';
    protected $_font_italic = 'ariali';
        
    public function __construct()
    {
        parent::__construct("P", "mm", array(210, 297));
        $this->SetFont($this->_font, "", 16);
        $this->SetAuthor("Vivid Planet Software GmbH");
        $this->SetCreator("Vivid Planet Software GmbH mit FPDF");
        $this->SetTitle("Wochenbericht");
    }

    public function getRightMargin()
    {
        return $this->rMargin;
    }

    public function getLeftMargin()
    {
        return $this->lMargin;
    }

    public function getTopMargin()
    {
        return $this->tMargin;
    }

    public function Output ($name='',$dest='')
    {
        if ($dest == 'I' || $dest == 'D' || (!$dest && !$name)) {
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Last-Modified: ' . gmdate("D, d M Y H:i:s \G\M\T", time() - 60*60*24));
            header('Accept-Ranges: none');
        }
        return parent::Output($name, $dest);
    }

    public function setPageWidth($value)
    {
        $this->w = $value;
    }

    public function setPageHeight($value)
    {
        $this->h = $value;
    }

    public function setLasth ($value){
        $this->lasth = $value;
    }
}
