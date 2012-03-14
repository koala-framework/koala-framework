<?php
class Kwc_Basic_Text_HtmlToTextParser
{
    private $_ret;
    private $_li = false;
    private $_liLayer = 0;
    private $_p = false;
    private $_pClose = false;
    private $_href = false;
    private $_trim = true;

    protected function _startElement($parser, $element, $attributes)
    {
        $element = strtolower($element);
        
        //debugging
//        echo "startElement: $element \n";
//        foreach ($attributes as $attribute) {
//            echo "attribute: $attribute \n";
//        }
        
        if ($element == 'h1' || $element == 'h2' || $element == 'h3' || $element == 'h4' || $element == 'h5') {
            $this->_ret .= "\n";
        }else if ($element == 'li' && !$this->_li) {
            $this->_ret .= "* ";
            $this->_li = true;
        }else if ($element == 'li' && $this->_li) {
            $this->_liLayer += 1;
            $this->_ret .= "\n";
            for ($i=1;$i<=$this->_liLayer;$i++){
                $this->_ret .= "    ";
            }
            $this->_ret .= "* ";
            $this->_li = true;
        }else if ($element == 'p') {
            if ($this->_p){
                $this->_ret .= "\n";
            }
            $this->_pClose = false;
            $this->_p = true;
        } else if ($element == 'a'){
            if (isset($attributes['HREF'])){
                $this->_href = $attributes['HREF'];
            } else {
                $this->_href = false;
            }
        } else if ($element == 'a') {
            $this->_trim = false;
        }
    }

    protected function _characterData($parser, $cdata)
    {
//        echo "cData: $cdata \n";
        if ($this->_trim) {
            $cdata = trim($cdata);
        }
        $cdata = preg_replace("/\n/", ' ', $cdata);
        $cdata = preg_replace('/\s\s+/', ' ', $cdata);
        $cdata = str_replace('+nbsp;', ' ', $cdata);
        $this->_pClose = false;
        if ($this->_href) {
            $this->_ret .= " " . $cdata;
            $this->_ret .= ": " . $this->_href;
            $this->_href = false;
        } else {
            $this->_ret .= $cdata;
        }
    }

    protected function _endElement($parser, $element)
    {
        $element = strtolower($element);
//        echo "endElement: $element \n";
        if ($element == 'p' && !$this->_pClose) {
            $this->_ret .= "\n";
            $this->_p = false;
            $this->_pClose = true; //damit bei 2 </p> nur 1 \n erzeugt wird
        } else if ($element == 'p' && $this->_pClose) {
            $this->_p = false;
            $this->_pClose = true;
        } else if ($element == 'h1' || $element == 'h2'  || $element == 'h3' || $element == 'h4' || $element == 'h5') {
            $this->_ret .= "\n\n";
        }else if ($element == 'br' || $element == 'strong') {
            $this->_ret .= "\n";
        }else if ($element == 'li' && $this->_liLayer) {
            $this->_liLayer -= 1;
        }else if ($element == 'li' && !$this->_liLayer) {
            $this->_ret .= "\n";
            $this->_li = false;
        } else if ($element == 'a') {
            $this->_trim = false;
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
        $this->_ret = preg_replace_callback(
                '/{cc[^}]+}/',
                create_function(
                      '$data',
                     'return str_replace(" ", "*kwfSpace*", $data[0]);'
                ),
                $this->_ret
        );
        //p($this->_ret);
        $this->_ret = wordwrap($this->_ret, 75, "\n", false);
        $this->_ret = preg_replace_callback(
                '/{cc[^}]+}/',
                create_function(
                        '$data',
                        'return str_replace("*kwfSpace*", " ", $data[0]);'
                ),
                $this->_ret
        );
//         //p($this->_ret);
//         $this->_ret = str_replace('*entity*', '&', $this->_ret);
//         $this->_ret = html_entity_decode($this->_ret);
//         //d($this->_ret);
        return $this->_ret;
    }
}
