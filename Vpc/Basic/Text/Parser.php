<?php
class Vpc_Basic_Text_Parser
{
    protected $_parser;
    protected $_elementStack = array();
    protected $_stack = array();
    protected $_P = array();
    protected $_SPAN = array();
    protected $_finalHTML;

    public function __construct()
    {
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
    }

    public function readCatalog($catalog)
    {
        xml_parse(
          $this->_parser,
          $catalog, true);
    }

    protected function endElement($parser, $element)
    {
        $element = array_pop($this->_elementStack);

        if ($element == "SPAN"){
            $tag = array_pop($this->_stack);
            if ($tag != "") $this->_finalHTML .= "</".$tag.">";
        } elseif ($element == "BODY" || $element == "O:P" || $element == "BR" || $element == "IMG") {
            //do nothing
        }
        else {
            $this->_finalHTML .= "</".$element.">";

        }
    }

    protected function startElement($parser, $element, $attributes)
    {
        array_push($this->_elementStack, $element);

        if ($element == "SPAN" && array_key_exists("STYLE", $attributes)){

            if (preg_match("# *font-weight *: +bold *; *#", $attributes["STYLE"], $matches)){
                 array_push($this->_stack, "strong");
                 $this->_finalHTML .= "<strong>";
            } elseif (preg_match("# *font-style *: +italic *; *#", $attributes["STYLE"], $matches)){
                 array_push($this->_stack, "em");
                 $this->_finalHTML .= "<em>";
            } elseif (preg_match("# *text-decoration *: +underline *; *#", $attributes["STYLE"], $matches)){
                 array_push($this->_stack, "u");
                 $this->_finalHTML .= "<u>";
            }
        }
        elseif ($element == "BODY" || $element == "O:P" ) {
            //do nothing
        }
        else {
            $this->_finalHTML .= "<".$element;
            foreach ($attributes as $key => $value) {
                 $this->_finalHTML .= ' ' . $key . ':'. $value;
            }

            $this->_finalHTML .= ">";
        }
    }

    protected function characterData($parser, $cdata)
    {
        $level   = sizeof($this->_elementStack) - 1;
        $element = $this->_elementStack[$level];
        $this->_finalHTML .= $cdata;
    }

    public function getFinalHtml()
    {
        return $this->_finalHTML;

    }


}
