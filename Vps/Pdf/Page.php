<?p

/** Zend_Pdf_Page 
require_once 'Zend/Pdf/Page.php

class Vps_Pdf_Page extends Zend_Pdf_Pa

    const OPTIONS_HALIGN_LEFT     = 
    const OPTIONS_HALIGN_RIGHT    = -
    const OPTIONS_HALIGN_CENTER   = -
  
    const OPTIONS_VALIGN_TOP      = -
    const OPTIONS_VALIGN_BOTTOM   = -
    const OPTIONS_VALIGN_MIDDLE   = -
  
    const OPTIONS_WRAP_ENABLED     = 
  
    /
     * Draw a line of text at the specified positio
    
     * @param string $te
     * @param float 
     * @param float 
     * @param string $charEncoding (optional) Character encoding of source tex
     *   Defaults to current local
     * @param array $options (optional) Options for how text is ouput includi
     *   halign, valign, wrap, wrap-indent, and wrap-pad.  If an alignme
     *   is set, the positioning variables will act as offset
     * @throws Zend_Pdf_Excepti
     
    public function drawText($text, $x, $y, $charEncoding = '', $options = array(
   
        if (isset($options['wrap']))
            switch ($options['wrap'])
                case self::OPTIONS_WRAP_ENABLE
                    $this->verifyFontType(

                    $buffer = '
                  
                    if (isset($options['wrap-pad']))
                        $wrappad = $options['wrap-pad'
                    } else
                        $wrappad = 
                   
                  
                    for ($j = 0; $j < strlen($text); $j++)
                        if ($buffer == '')
                            // If the remaining text fits then don't bother trying to wrap anythi
                            if ($this->getTextWidth($text) <= $this->getWidth() - $x - $wrappad)
                                return $this->drawText($text, $x, $y, $charEncoding
                                                       array_diff_key($options, array('wrap'=>null))
                           
                       
                      
                        // Append characters onto the buffer.
                        $buffer .= substr($text, $j, 1
                      
                        // Until we reach a spa
                        if (substr($buffer,-1)==' ')
                            if (isset($oldbuffer) && !empty($oldbuffer))
                                // If the "new" buffer doesn't fit (presumably the old does), it is time to wr
                                if ($this->getTextWidth($buffer) > $this->getWidth() - $x - $wrappad)
                                    $this->drawText(trim($oldbuffer), $x, $y, $charEncoding
                                                    array_diff_key($options, array('wrap' => null))
                                    if (isset($options['wrap-indent']))
                                        $x = $options['wrap-indent'
                                        $options = array_diff_key($options, array('wrap-indent' => null)
                                   
                                    $text = substr($text, strlen($oldbuffer)
                                    $j = -
                                    $y = $y-$this->getTextHeight(
                                    $buffer = '
                               
                           
                            $oldbuffer = $buffe
                       
                   
                    brea
                  
                defaul
                    brea
           
       
      
        if (isset($options['halign']))
	        switch ($options['halign'])
	            case self::OPTIONS_HALIGN_RIGH
	                $this->verifyFontType(
	                $x = $this->getWidth() - $this->getTextWidth($text) - $
	                brea
	              
	            case self::OPTIONS_HALIGN_CENTE
	                $this->verifyFontType(
	                $x = $x + ($this->getWidth() / 2) - ($this->getTextWidth($text) / 2
	                brea
	              
	            defaul
	                brea
	       
       
      
        if (isset($options['valign']))
	        switch ($options['valign'])
	            case self::OPTIONS_VALIGN_TO
	                $this->verifyFontType(
	                $y = $this->getHeight() - $this->getTextHeight() - $
	                brea
	              
	            case self::OPTIONS_VALIGN_BOTTO
	                $this->verifyFontType(
	                $y = $this->getTextHeight() + $
	                brea
	              
	            case self::OPTIONS_VALIGN_MIDDL
	                $this->verifyFontType(
	                $y = ($this->getHeight() / 2) - ($this->getTextHeight() / 2) - $
	                brea
	              
	            defaul
	                brea
	       
       

        parent::drawText($text, $x, $y, $charEncoding
      
        return $
   
  
    /
     * Get the suggested height of text in text spa
    
     * @return heig
     
    public function getTextHeight()
        $height = $this->getFont()->getLineHeight() 
                  $this->getFont()->getUnitsPerEm() 
                  $this->getFontSize(
                
        return $heigh
   
  
    /
     * Get the assumed width of text in text spa
    
     * @throws Zend_Pdf_Excepti
     * @return wid
     
    public function getTextWidth($tex
   
        // Create an array of ASCII valu
        $asciivals = array_map(create_function('$char','return ord($char);'),str_split($text)
  
        // Convert ASCII values to glyph numbe
        $glyphnums = $this->getFont()->cmap->glyphNumbersForCharacters($asciivals
  
        // Create an array of widths for each gly
        $glyphwidths = $this->getFont()->widthsForGlyphs($glyphnums
      
        // Sum the widths and convert them to text spa
        $width = array_sum($glyphwidths) / $this->getFont()->getUnitsPerEm() * $this->getFontSize(
      
        return $widt
   
  
    /
     * Verify that the currently set font is a standard typ
    
     * @throws Zend_Pdf_Excepti
     
    private function verifyFontType()
        if ($this->getFont()->getFontType() != Zend_Pdf_Font::TYPE_STANDARD)
            throw new Zend_Pdf_Exception('Dynamic text alignment is only available with standard fonts'
                                         Zend_Pdf_Exception::NOT_IMPLEMENTED
       
   

