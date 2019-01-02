<?php
class Kwc_Basic_Text_ApiContentHtmlParser
{
    protected $_parser;
    protected $_stack;
    protected $_childComponents;


    public function __construct()
    {
    }

    protected function endElement($parser, $element)
    {
        array_pop($this->_stack);
    }

    protected function startElement($parser, $element, $attributes)
    {
        $current = $this->_stack[count($this->_stack)-1];
        if (!isset($current)) $current->children = array();
        $newBlock = null;
        if (strtolower($element) == 'a') {
            if (isset($this->_childComponents[$attributes['HREF']])) {
                $newBlock['component'] = $this->_childComponents[$attributes['HREF']];
            } else {
                $newBlock = null;
            }
        } else {
            $newBlock = array(
                'element' => strtolower($element),
            );
        }

        if ($newBlock) {
            $newBlock = (object)$newBlock;
            $current->children[] = $newBlock;
        }
        array_push($this->_stack, $newBlock);
    }

    protected function characterData($parser, $cdata)
    {
        $current = $this->_stack[count($this->_stack)-1];


        if ($current) {
            if (!isset($current)) $current->children = array();
            $current->children[] = (object)array(
                'text' => $cdata
            );
        }
    }

    public function parse(Kwf_Component_Data $data)
    {
        $row = $data->getComponent()->getRow();
        $html = str_replace('&nbsp;', '&#160;', $row->content); //Known bug https://bugs.php.net/bug.php?id=15092
        foreach ($data->getChildComponents() as $c) {
            $this->_childComponents[$c->dbId] = $c;
        }

        $this->_stack = array(
            (object)array(
                'element' => 'html',
            )
        );
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
            "<BODY>".$html."</BODY>",
            true);

        return $this->_stack[0]->children[0]->children;
    }
}
