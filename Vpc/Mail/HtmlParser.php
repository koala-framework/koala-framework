<?php
class Vpc_Mail_HtmlParser
{
    //aktueller zustand von parser währen des parsens
    private $_parser;
    private $_stack;
    private $_ret;

    //einstellungen für parser
    private $_styles;
    
    private $_noCloseTags = array('br', 'img', 'hr');


    public function __construct(array $styles)
    {
        $this->_styles = $styles;
    }

    protected function endElement($parser, $tag)
    {
        $tag = strtolower($tag);
        if ($tag == 'body') return;
        $stackItem = array_pop($this->_stack);
        foreach (array_reverse($stackItem['appendedTags']) as $t) {
            $this->_ret .= "</$t>";
        }
        if (!in_array($stackItem['tag'], $this->_noCloseTags)) {
            $this->_ret .= "</$stackItem[tag]>";
        }
    }

    private static function _matchesStyle($stack, $style)
    {
        $tag = $stack[count($stack)-1]['tag'];
        $class = $stack[count($stack)-1]['class'];
        if (isset($style['tag'])) {
            if (isset($style['selector'])) throw new Vps_Exception("don't use tag AND selector");
            return ($style['tag'] == '*' || $style['tag'] == $tag) && (!isset($style['class']) || $class == $style['class']);
        } else if (isset($style['selector'])) {
            $selectors = explode(' ', $style['selector']); //css-artiger selector
            $selectors = array_reverse($selectors);
            $stack = array_reverse($stack);
            foreach ($selectors as $selector) {
                foreach ($stack as $stackItem=>$s) {
                    if ($selector == $s['tag']
                        || (isset($s['class']) && $selector == '.'.$s['class'])
                        || (isset($s['class']) && $selector == $s['tag'].'.'.$s['class'])
                    ) {
                        $stack = array_slice($stack, $stackItem);
                        continue 2;
                    }
                }
                return false;
            }
            return true;
        }
        throw new Vps_Exception_NotYetImplemented();
    }

    protected function startElement($parser, $tag, $attributes)
    {
        $tag = strtolower($tag);
        if ($tag == 'body') return;

        $class = '';
        if (isset($attributes['CLASS'])) {
            $class = $attributes['CLASS'];
        }
        $stack = $this->_stack;
        $stack[] = array( //extra stack der _matches übergeben werden kann, den richtigen stack kömma nu ned bauen
            'tag' => $tag,
            'class' => $class,
            'appendedTags' => array()
        );

        $appendTags = array();
        foreach ($this->_styles as $s) {
            if (self::_matchesStyle($stack, $s)) {
                $appendTags = array();
                if (isset($s['styles'])) {
                    foreach ($s['styles'] as $style=>$value) {
                        if ($style == 'font-family') {
                            $appendTags['font']['face'] = $value;
                        } else if ($style == 'font-size') {
                            $appendTags['font']['size'] = $value;
                        } else if ($style == 'color') {
                            $appendTags['font']['color'] = $value;
                        } else if ($style == 'font-weight' && $value == 'bold') {
                            $appendTags['b'] = array();
                        }
                    }
                }
                if (isset($s['replaceTag'])) {
                    $tag = $s['replaceTag'];
                    $attributes = array();
                }
            }
        }
        
        $this->_ret .= "<$tag";
        foreach ($attributes as $n=>$v) {
            $n= strtolower($n);
            $this->_ret .= " $n=\"$v\"";
        }
        if (in_array($tag, $this->_noCloseTags)) {
            $this->_ret .= " /";
        }
        $this->_ret .= ">";

        $stackItem = array(
            'tag' => $tag,
            'class' => $class,
            'appendedTags' => array()
        );
        foreach ($appendTags as $t=>$attr) {
            $stackItem['appendedTags'][] = $t;
            $this->_ret .= "<$t";
            foreach ($attr as $k=>$v) {
                $this->_ret .= " $k=\"$v\"";
            }
            $this->_ret .= ">";
        }
        array_push($this->_stack, $stackItem);
    }

    protected function characterData($parser, $cdata)
    {
        $this->_ret .= $cdata;
    }

    public function parse($html)
    {
        // replace entities
        $html = preg_replace('/&([a-z0-9#]{2,5});/i', '+$1;', $html);

        $this->_stack = array();
        $this->_ret = '';
        $this->_parser = xml_parser_create();

        xml_set_object($this->_parser, $this);

        xml_set_element_handler(
          $this->_parser,
          'startElement',
          'endElement'
        );

        xml_set_character_data_handler(
          $this->_parser,
          'characterData'
        );

        xml_set_default_handler(
          $this->_parser,
          'characterData'
        );

        $result = xml_parse($this->_parser,
          '<body>'.$html.'</body>',
          true);
        if (!$result) {
            // wenn man ein nicht geschlossenes <br> rein gibt, schreit er hier,
            // macht aber normal weiter. wenns zu oft vorkommt, evtl. exception
            // entfernen und ignorieren, oder was andres überlegen :-)
            $errorCode = xml_get_error_code($this->_parser);
            $ex = new Vps_Exception("Mail HtmlParser XML Error $errorCode: ".xml_error_string($errorCode));
            $ex->logOrThrow();
        }

        // re-replace entities
        $this->_ret = preg_replace('/\+([a-z0-9#]{2,5});/i', '&$1;', $this->_ret);

        return $this->_ret;
    }
}
