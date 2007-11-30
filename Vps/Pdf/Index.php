<?php
class Vps_Pdf_Index extends Zend_Pdf_Filter_Ascii85
{
    private $_cols = 0;
    private $_borders = array('left' => 20, 'right' => 20, 'top' => 20, 'bottom' => 20);
    private $_styles = array('headline' => array(), 'row1' => array(), 'row2' => array(), 'top' => array());
    private $_pageNr = array ('active' => 0, 'x' => 580, 'y' => 10);
    private $_date = array ('active' => 0, 'x' => 420, 'y' => 820);
    private $_pdfFile = array();
    private $_columns = array();
    private $_columnwidths = array();
    private $_data = array();
    private $_headline = '';
    private $_font;
    private $_textboxes = array();
    
    public function __construct($data, $headline)
    {
        $this->_data = $data;
        $this->_headline = $headline;
        $this->_cols = sizeof(array_keys($data[0]));
        $this->_columns = array_keys($data[0]);
        $this->_setDefaultStyles();
        $this ->_pdfFile = new Zend_Pdf();
        $this->_font = Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA);
        $this->_setDefaultWidths();
    }
    
    private function _setDefaultWidths()
    {
        $columwidth = (595 - $this->_borders['left'] - $this->_borders['right']) / $this->_cols;
        foreach ($this->_columns AS $column) {
            $this->_columnwidths[$column] = $columwidth;
        }
    }
    
    public function setColumnWidth($column, $value) 
    {
        $this->_columnwidths[$column] = $value;
    }
    
    public function setDateSettings($setting, $value) 
    {
        $this->_date[$setting] = $value;
    }
    
    public function setPageNrSettings($setting, $value) 
    {
        $this->_pageNr[$setting] = $value;
    }
    
    public function setBorderSettings($setting, $value) 
    {
        $this->_borders[$setting] = $value;
    }
    
    public function savePdf($filename) 
    {
        $pdfpage = $this ->_pdfFile->newPage(Zend_Pdf_Page::SIZE_A4);
        $pdfpage->setFont($this->_font, 18);
        //$pdfpage->drawText($this->_headline, $this->_borders['left'], 842 - ($this->_borders['top'] + 18));
        $this->_drawText($this->_headline, $pdfpage, $this->_borders['left'], 842 - ($this->_borders['top'] + 18));
        $this->_drawTextBoxes($pdfpage);
        // $this->_drawTextBoxes1($pdfpage);
        
        $pdfpage->setFont($this->_font, 12);
        $this->_createTable($pdfpage, $this->_data);
        $this->_pdfFile->save("$filename.pdf");
    }
    
    
    private function _drawText ($text, $pagename, $x, $y)
    {
        //$text = "This is\tan example\nstring";
        $lines =  array();
        $line = strtok($text, "\n");
        while (is_string($line)) {
            if ($line) {
                $lines[] = $line;
            }
            $line = strtok("\n");
            echo $line.'<br>';
        }
        $space = 0;
        //$lines = array_reverse($lines);
        foreach ($lines AS $line) {
            $pagename->drawText($line, $x, $y + $space);
            $space -= 20;
            echo "line ".$line;
        }
    }
    private function _setDefaultStyles() 
    {
        $color = new Zend_Pdf_Color_Html('silver');
        $this->_styles['row1'] = new Zend_Pdf_Style();
        $this->_styles['row1']->setFillColor($color);
        
        $color = new Zend_Pdf_Color_Html('black');
        $this->_styles['row2'] = new Zend_Pdf_Style();
        $this->_styles['row2']->setFillColor($color);
        
        $color = new Zend_Pdf_Color_Html('red');
        $this->_styles['top'] = new Zend_Pdf_Style();
        $this->_styles['top']->setFillColor($color);
    }
    
    
    private function _createTable($pdfPage, $data)
    {
        $page_nr = 1;   
        $y = 750;
        $x = $this->_borders['left'];
        
        
        $pdfPage->setStyle($this->_styles['top']);
        foreach ($this->_columns AS $column) {
            $pdfPage->drawText($column, $x, $y);
            $x = $x + $this->_columnwidths[$column] + 2;
        }
        $y = $y - 15;        
        
        $cnt = 0;
        foreach ($data AS $row) {
            $x = $this->_borders['left'];
            
            
            if ($cnt % 2 == 0) {
                $pdfPage->setStyle($this->_styles['row1']);
            }
            if (is_array($row)) {
                $y = $y - 15;
                
                foreach ($row AS $elementkey => $element) {
                    $pdfPage->drawText($element, $x, $y);
                    $x = $x + $this->_columnwidths[$elementkey] + 2;
                }
            } else {
                $pdfPage->drawText($row, $x, $y = $y - 15);
            }
            $cnt++;
            $pdfPage->setStyle($this->_styles['row2']);
            $pdfPage->drawRectangle($this->_borders['left'], $y - 3, 595 - $this->_borders['right'], $y + 12, Zend_Pdf_Page::SHAPE_DRAW_STROKE);
            
            if ($y < $this->_borders['bottom']) {
                if ($this->_date['active']) $pdfPage->drawText($this->_getDate(), $this->_date['x'], $this->_date['y']);
                if ($this->_pageNr['active']) $pdfPage->drawText($page_nr, $this->_pageNr['x'], $this->_pageNr['y']);
                $page_nr++;
                $this->_pdfFile->pages[] = $pdfPage;
                $pdfPage = $this ->_pdfFile->newPage(Zend_Pdf_Page::SIZE_A4);
                $pdfPage->setFont($this->_font, 12);
                $y = 800;
                
                $x = $this->_borders['left'];
                $pdfPage->setStyle($this->_styles['top']);
                foreach ($this->_columns AS $column) {
                    $pdfPage->drawText($column, $x, $y);
                    $x = $x + $this->_columnwidths[$column] + 2;
                    
                }
                $y = $y - 15;
            }
        }
        if ($this->_date['active']) $pdfPage->drawText($this->_getDate(), $this->_date['x'], $this->_date['y']);
        if ($this->_pageNr['active']) $pdfPage->drawText($page_nr, $this->_pageNr['x'], $this->_pageNr['y']);
        $this->_pdfFile->pages[] = $pdfPage;
    }    
    
    private function _getDate()
    {
        date_default_timezone_set('Europe/Berlin');
        $date = new Zend_Date();
        return $date->get(Zend_Date::DATE_FULL);
    }

    private function _drawTextBoxes($pdfPage)
    {
        $pdfPage->setFont($this->_font, 12);
        $pdfPage->setStyle($this->_styles['top']);
        foreach ($this->_textboxes AS $textbox) {
            $this->_drawTextBoxFormatted($pdfPage, $textbox);

            /*$lines = str_split($textbox['text'], $textbox['maxlength']);
            $space = 0;
            foreach ($lines AS $line){
                $pdfPage->drawText($line, $textbox['x'], $textbox['y'] + $space);
                $space -= 14;
                echo "line ".$line;
            }*/
        }
    }

    private function _drawTextBoxFormatted ($pdfPage, $textbox)
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

    public function insertTextbox($text, $maxlength, $x, $y)
    {
        $this->_textboxes[] = array('text' => $text, 'maxlength' => $maxlength, 'x' => $x, 'y' => $y);
    }
}
