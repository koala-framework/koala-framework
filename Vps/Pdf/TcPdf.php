<?php
require_once 'tcpdf.php';
class Vps_Pdf_TcPdf extends TCPDF
{

    protected $_font = 'helvetica'; //sollte eigentlich helvetica sein
    protected $_fontsize = 10;
    protected $_component;

    public function __construct($component = null , $format = 'A4')
    {
        $this->_component = $component;
        parent::__construct("P", "mm", $format);
        $this->SetFont($this->_font, "B", 16);
        $this->SetAuthor("Vivid Planet Software GmbH");
        $this->SetCreator("Vivid Planet Software GmbH mit FPDF");
        $this->SetTitle("Wochenbericht");
        $this->setLIsymbol(utf8_encode(chr(0x95))); // = •
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

    public function getBottomMargin()
    {
        return $this->bMargin;
    }

    public function Output ($name='',$dest='I')
    {
        if ($dest == 'I' || $dest == 'D' || (!$dest && !$name)) {
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

    public function setLasth ($value)
    {
        $this->lasth = $value;
    }

    public function getLasth ()
    {
        return $this->lasth;
    }

    public function getLineHeight()
    {
        return $this->FontSize * $this->cell_height_ratio;
    }

    public function implode($parts, $delimiter = "\n")
    {
        $implodeParts = array();
        foreach ($parts as $key => $part) {
            if ($part) {
                if (!is_int($key)) {
                    $part = $key . $part;
                }
                $implodeParts[] = $part;
            }
        }
        return implode($delimiter, $implodeParts);
    }

    public function textArea ($text, $align = "L", $indent = 0, $height = 0, $fontweight = '')
    {
        $this->SetFont($this->getFont(), $fontweight, $this->getFontsize());
        $xtmp = $this->GetX();
        $this->SetX($this->GetX()+$indent);
        $this->MultiCell($this->getMaxTextWidth(), $height, $this->decodeText($text), 0, $align, 0);
        $this->SetX($xtmp);
        $this->SetFont($this->getFont(), "", $this->getFontsize());
    }

    public function textAreaHTML ($text, $align = "L", $indent = 0, $height = 0)
    {
        $xtmp = $this->GetX();
        $this->SetX($this->GetX()+$indent);
        $this->MultiCell($this->getMaxTextWidth(), $height,
                            $this->writeHTML($this->decodeText($text), false, 0, false, false, $align)
                         , 0, $align, 0);
        $this->SetX($xtmp);
    }

    public function textBox ($text, $fontweight = '', $align = 'L', $border = '', $linebreak = 0, $fontsize = false) {
        if (!$fontsize) $fontsize = $this->getFontsize();
        $this->SetFont($this->getFont(), $fontweight, $fontsize);
        $this->Cell($this->getMaxTextWidth(), 3, $this->decodeText($text), $border, $linebreak, $align);
        $this->SetFont($this->getFont(), "", $this->getFontsize());
    }

    public function getFont()
    {
        return $this->_font;
    }

    public function getFontsize()
    {
        return $this->_fontsize;
    }

    public function setFontProjectFontSize($fontsize)
    {
        $this->_fontsize = $fontsize;
    }

    public function decodeText ($text)
    {
        $text = str_replace("€", utf8_encode(chr(0x80)), $text);
        $text = str_replace("—", utf8_encode(chr(0x97)), $text);
        $text = str_replace("•", utf8_encode(chr(0x95)), $text);
        return $text;
    }

    public function setCellHeightRatio ($ratio) {
        $this->cell_height_ratio = $ratio;
    }

    //workaround für bug bei pdf erstellung
    public function AddFont($family, $style='', $file='') {
        $allowedStyles = array('B', 'I', 'BI', 'IB', '');
        if (!in_array($style, $allowedStyles)) {
            if (in_array(substr($style, 0, 1), $allowedStyles)) {
                $style = substr($style, 0, 1);
            }
        }
        return parent::AddFont($family, $style, $file);
    }


    /*
     * patch für tcpdf -> bei nummernzeichen werden automatish zwei
     * zeilenumbrüche durchgeführt.
     */
    protected function closeHTMLTagHandler(&$dom, $key, $cell=false) {
            $temp = $this->lasth;
            switch($dom[$key]['value']) {
                case 'ul':
                case 'ol':
                    $this->lasth = $this->lasth / 2;
                    break;
            }
            parent::closeHTMLTagHandler(&$dom, $key, $cell);
            $this->lasth = $temp;
    }

    public function getMaxTextWidth()
    {
        return $this->getPageWidth()-$this->getRightMargin()-$this->getLeftMargin();
    }

    public function writeCheckbox($check) {
        if ($check) $text = "checked";
        else $text = "unchecked";
        $this->Image("images/pdf/$text.jpg", $this->GetX(), $this->GetY(), 5, 5);
    }



}
