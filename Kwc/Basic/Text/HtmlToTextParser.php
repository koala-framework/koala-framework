<?php
class Kwc_Basic_Text_HtmlToTextParser
{
    private $_ret;
    private $_cdata;
    private $_li = false; // $liLayer can be 0 if $_li is set
    private $_liLayer = 0;
    private $_p = false;
    private $_pClose = false;
    private $_lastHref = false;
    private $_noLineBreak = false;
    private $_firstCData = true;

    protected function _startElement($parser, $element, $attributes)
    {
        $element = strtolower($element);
        if ($element == 'h1' || $element == 'h2' || $element == 'h3' || $element == 'h4' || $element == 'h5') {
            $this->_ret .= "\n";
        }else if ($element == 'li' && !$this->_li) {
            $this->_ret .= "* ";
            $this->_li = true;
        }else if ($element == 'li' && $this->_li) {
            $this->_liLayer += 1;
            $this->_ret .= trim($this->_cdata) . "\n";
            $this->_cdata = '';
            for ($i=1;$i<=$this->_liLayer;$i++){
                $this->_ret .= "    ";
            }
            $this->_ret .= "* ";
            $this->_li = true;
        }else if ($element == 'p') {
            if ($this->_p){
                $this->_ret .= trim($this->_cdata) . "\n";
                $this->_cdata = '';
            }
            $this->_pClose = false;
            $this->_p = true;
        } else if ($element == 'a'){
            if (isset($attributes['HREF'])){
                $this->_lastHref = $attributes['HREF']; //save the href to print it out later (after the cdata of the link)
            } else {
                $this->_lastHref = false;
            }
        }
    }

    protected function _characterData($parser, $cdata)
    {
        $cdata = preg_replace('/\\\s\\\s+/', ' ', $cdata);
        $cdata = preg_replace("/\n/", ' ', $cdata);
        $cdata = preg_replace('/\s\s+/', ' ', $cdata);
        $cdata = str_replace('+nbsp;', ' ', $cdata);
        if ($this->_firstCData) {
            $cdata = ltrim($cdata);
            $this->_firstCData = false;
        }
        if ($cdata != '') $this->_noLineBreak = false;
        $this->_pClose = false;
        $this->_cdata .= $cdata;
    }

    protected function _endElement($parser, $element)
    {
        $this->_cdata = trim($this->_cdata);
        $lines = explode("\n",$this->_cdata);
        $this->_cdata = '';
        $count = count($lines);
        $i = 1;
        foreach ($lines as $line) {
            if ($count < $i) {
                $this->_cdata .= trim($line) . "\n";
            } else {
                $this->_cdata .= trim($line);
            }
        $i++;
        }
        $this->_ret .= $this->_cdata;
        $this->_cdata = '';

        //check the endtag of elements
        $element = strtolower($element);
        //links
        if ($this->_lastHref) {
            $this->_ret .= ": " . $this->_lastHref . " ";
            $this->_lastHref = false;
        }
        //paragraphs
        if ($element == 'p' && !$this->_pClose) {
            if (!$this->_noLineBreak){
                $this->_ret .= "\n";
            }
            $this->_p = false;
            $this->_pClose = true; //if there are 2 </p> only 1 \n should be printed
        } else if ($element == 'p' && $this->_pClose) {
            $this->_p = false;
            $this->_pClose = true;
        //headlines
        } else if ($element == 'h1' || $element == 'h2'  || $element == 'h3' || $element == 'h4' || $element == 'h5') {
            $this->_ret .= "\n\n";
            $this->_noLineBreak = true;
        //linebreake
        }else if ($element == 'br') {
            if (!$this->_noLineBreak) {
            $this->_ret .= "\n";
            }
        //lists
        }else if ($element == 'li' && $this->_liLayer) {
            $this->_liLayer -= 1;
        }else if ($element == 'li' && !$this->_liLayer) {
            $this->_ret .= "\n";
            $this->_li = false;
        }
    }

    public static function parse($html)
    {
        $parser = new self();
        return $parser->_parse($html);
    }

    private function _parse($html)
    {
        $html = preg_replace('/&([a-z0-9#]{2,5});/i', '+$1;', $html);
        $html = str_replace('\n', ' ', $html);
        $this->_parser = xml_parser_create();

        xml_set_object($this->_parser, $this);

        xml_set_element_handler(
                $this->_parser,
                '_startElement',
                '_endElement'
        );

        xml_set_character_data_handler(
                $this->_parser,
                '_characterData'
        );

        xml_set_default_handler(
                $this->_parser,
                '_characterData'
        );

        $html = preg_replace('#<kwc(.*?)>#', '{kwc$1}', $html);

        $this->_ret = '';
        $result = xml_parse($this->_parser,
                "<BODY>".$html."</BODY>",
                true);
        if (!$result) {
            // wenn man ein nicht geschlossenes <br> rein gibt, schreit er hier,
            // macht aber normal weiter. wenns zu oft vorkommt, evtl. exception
            // entfernen und ignorieren, oder was andres überlegen :-)
            $errorCode = xml_get_error_code($this->_parser);
            $ex = new Kwf_Exception("Mail HtmlParser XML Error $errorCode: ".xml_error_string($errorCode));
            $ex->logOrThrow();
        }

        //replace spaces in links (otherwise there would be a linebreak inside the link after wordwrap)
        $this->_ret = preg_replace_callback(
                '/{kwc[^}]+}/',
                create_function(
                      '$data',
                     'return str_replace(" ", "*kwfSpace*", $data[0]);'
                ),
                $this->_ret
        );

        //make a linebreak after 72 chars
        $this->_ret = wordwrap($this->_ret, 75, "\n", false);
        //reconstruct spaces inside links
        $this->_ret = preg_replace_callback(
                '/{kwc[^}]+}/',
                create_function(
                        '$data',
                        'return str_replace("*kwfSpace*", " ", $data[0]);'
                ),
                $this->_ret
        );

        $this->_ret = preg_replace('#{kwc(.*?)}#', '<kwc$1>', $this->_ret);

        return $this->_ret;
    }
}
