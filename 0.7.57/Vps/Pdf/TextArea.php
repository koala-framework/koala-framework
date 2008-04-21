<?php
// in progress
class Vps_Pdf_TextArea
{
    // private $_styles = array('headline' => null, 'text' => null);
    private $_page = null;
    private $_settings = array ('text' => '',
                                'headline' => '',
                                'maxlength' => 50,
                                'x' => 10,
                                'y' => 10,
                                'styles' => array('headline' => null, 'text' => null));


    public function __construct($headline, $text)
    {
        $color = new Zend_Pdf_Color_Html('black');
        $this->_styles['styles']['headline'] = new Zend_Pdf_Style();
        $this->_styles['styles']['headline']->setFillColor($color);
        $this->_styles['styles']['headline']->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA), 13);

        $color = new Zend_Pdf_Color_Html('black');
        $this->_styles['styles']['text'] = new Zend_Pdf_Style();
        $this->_styles['styles']['text']->setFillColor($color);
        $this->_styles['styles']['text']->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA), 11);

        $this->_settings['headline'] = $headline;
        $this->_settings['text'] = $text;
    }

    public function setSetting ($setting, $value, $setting2 = null){
        if (!is_array($this->_settings[$setting]))
            $this->_settings[$setting] = $value;
        else
            $this->_settings[$setting][$setting2] = $value;

    }
    
    private function _drawTextAreaFormatted ($pdfPage, $textbox)
    {
        $text = $textbox['text'];
        $lines = array();
        $from = 0;

        $maxlength = $textbox['maxlength'];
        $check = true;
        while ($check) {
            if (substr($text, $maxlength) == ' ') {
                echo 'pasta';
                $lines[] = substr($text, $from, $maxlength);
                $from = $from + $maxlength;
            } else {
                if (($from + $maxlength) >= strlen($text)) {
                    $lines[] = substr($text, $from, $maxlength);
                    $check = false;
                } else {
                    $temp = $from + $maxlength;
                    $tempString = substr($text, $from, $maxlength);
                    $newlength = strripos($tempString, ' ') + 1;
                    $lines[] = substr($text, $from, $newlength);
                    $from =  $from + $newlength;
                }
            }
        }

        echo '<br><br>';
        $space = 2;
        foreach ($lines AS $line) {
                $pdfPage->drawText($line, $textbox['x'], $textbox['y'] + $space);
                $space -= 14;
                echo '-- '.$line.'<br>';
            
        }
    }

    public function test () {
        d($this->_settings);
    }
}