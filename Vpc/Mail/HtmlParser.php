<?php
class Vpc_Mail_HtmlParser
{
    //aktueller zustand von parser währen des parsens
    protected $_parser;
    protected $_stack;
    protected $_ret;

    //einstellungen für parser
    protected $_styles;


    public function __construct(array $styles)
    {
        $this->_styles = $styles;
    }

    protected function endElement($parser, $tag)
    {
        $tag = strtolower($tag);
        $stackItem = array_pop($this->_stack);
        foreach (array_reverse($stackItem['appendedTags']) as $t) {
            $this->_ret .= "</$t>";
        }
        $this->_ret .= "</$tag>";
    }

    protected function startElement($parser, $tag, $attributes)
    {
        $tag = strtolower($tag);
        $this->_ret .= "<$tag";
        foreach ($attributes as $n=>$v) {
            $n= strtolower($n);
            $this->_ret .= " $n=\"$v\"";
        }
        $this->_ret .= ">";


        $class = '';
        if (isset($attributes['CLASS'])) {
            $class = $attributes['CLASS'];
        }
        foreach ($this->_styles as $s) {
            if (($s['tag'] == '*' || $s['tag'] == $tag) && (!isset($s['class']) || $class == $s['class'])) {
                $appendTags = array();
                foreach ($s['styles'] as $style=>$value) {
                    if ($style == 'font-family') {
                        $appendTags['font']['name'] = $value;
                    } else if ($style == 'font-size') {
                        $appendTags['font']['size'] = $value;
                    } else if ($style == 'color') {
                        $appendTags['font']['color'] = $value;
                    } else if ($style == 'font-weight' && $value == 'bold') {
                        $appendTags['b'] = array();
                    }
                }
            }
        }

        $stackItem = array(
            'tag' => $tag,
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

        xml_parse($this->_parser,
          $html,
          true);

        return $this->_ret;
    }
}
