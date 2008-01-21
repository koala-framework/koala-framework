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
    	header('Accept-Ranges: bytes');
		parent::Output($name, $dest);


    }
}
