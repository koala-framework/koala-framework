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
        $this->SetAuthor(trlVps("Vivid Planet Software GmbH"));
        $this->SetCreator("Vivid Planet Software GmbH mit FPDF");
        $this->SetTitle("Wochenbericht");
        $this->setLIsymbol(utf8_encode(chr(0x95))); // = •
        $this->setListIndentWidth($this->getStringWidth("00"));
       // $this->AddFont('times', '', 'times');
       // $this->AddFont('arial', '', 'arial');
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

    public function textArea ($text, $align = "L", $indent = 0, $height = 0, $fontweight = '', $ln = 1, $border = 0)
    {
        $this->SetFont($this->getFont(), $fontweight, $this->getFontsize());
        $xtmp = $this->GetX();
        $this->SetX($this->GetX()+$indent);
        $this->MultiCell($this->getMaxTextWidth(), $height, $this->decodeText($text), $border, $align, 0, $ln);
        $this->SetX($xtmp);
        $this->SetFont($this->getFont(), "", $this->getFontsize());
    }

    public function textAreaHTML ($text, $align = "L", $indent = 0, $height = 0)
    {
        $xtmp = $this->GetX();
        $this->SetX($this->GetX()+$indent);
        try {
            $html = $this->writeHTML($this->decodeText($text), false, 0, false, false, $align);
        } catch (Exception $e) {
            $html = $this->writeHTML(
                '<strong style="color:red">' . trlVps('Fehler') . '</strong>' .
                ': <br/>' .
                $e->getMessage()
            );
        }
        $this->MultiCell($this->getMaxTextWidth(), $height, $html, 0, $align, 0);
        $this->SetX($xtmp);
    }

    public function textBox ($text, $fontweight = '', $align = 'L', $border = '', $linebreak = 0, $fontsize = false, $width = false, $fill = 0) {
        if (!$fontsize) $fontsize = $this->getFontsize();
        $this->SetFont($this->getFont(), $fontweight, $fontsize);
        if (!$width) $width = $this->getMaxTextWidth();
        $this->Cell($width, 3, $this->decodeText($text), $border, $linebreak, $align, $fill);
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
        $text = str_replace(' href=""', ' href="#"', $text);
        $text = str_replace('<a>', '<a href="#">', $text);
        $text = str_replace("€", utf8_encode(chr(0x80)), $text);
        $text = str_replace("—", utf8_encode(chr(0x97)), $text);
        $text = str_replace("•", utf8_encode(chr(0x95)), $text);
        return $text;
    }

    public function setCellHeightRatio ($ratio)
    {
        $this->cell_height_ratio = $ratio;
    }

    //workaround für bug bei pdf erstellung
    protected $_check = false;
    /*public function AddFont($family, $style='', $file='')
    {
        if (!file_exists($this->_getfontpath().$file.".php") && $file) {
            $config = Zend_Registry::get('config');
            $file = $config->path->tcpdf_fonts."/".$file.".php";
            //$this->_check = true;
        }

        $allowedStyles = array('B', 'I', 'BI', 'IB', '');
        if (!in_array($style, $allowedStyles)) {
            if (in_array(substr($style, 0, 1), $allowedStyles)) {
                $style = substr($style, 0, 1);
            }
        }
        return parent::AddFont($family, $style, $file);
    }*/

  /*  public function SetFont($family, $style='', $size=0, $fontfile='') {
        $config = Zend_Registry::get('config');
        $filetmp = $config->path->tcpdf_fonts."/".$family.".php";

        if (file_exists($filetmp)) {
            $fontfile = $filetmp;
        }
        //$this->_font = $family;
        parent::SetFont($family, $style, $size, $fontfile);
    }*/


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
