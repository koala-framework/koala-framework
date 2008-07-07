<?php
require_once 'tcpdf.php';
class Vps_Pdf_TcPdf extends TCPDF
{

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
        if ($dest == 'I' || $dest == 'D') {
            //Workaround für IE problem: unterschied von Apache-Auslieferung
            //(wo es funktionierte)
            //von lenz mittels sniffer herausgefunden
            //ist definitiv notwendig - ie ist böööse
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
