<?php
class Kwc_Basic_Text_ApiContentHtmlParser
{
    protected $_parser;
    protected $_stack;
    protected $_childComponents;
    protected $_breakingElements = array(
        'p', 'ul', 'ol', 'li', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6'
    );

    public function __construct()
    {
    }

    protected function endElement($parser, $element)
    {
        if (isset($this->_stack[count($this->_stack)-1])) {
            $current = $this->_stack[count($this->_stack)-1];
            if (isset($current->element) && in_array($current->element, $this->_breakingElements)) {
                if (isset($current->children)) {
                    $filteredChildren = array();
                    foreach ($current->children as $key => $child) {
                        // empty text-elemente in block-element not allowed
                        if ($child->element == 'text' && trim($child->text) == '') continue;
                         // trailing spaces in last segment of block not allowed
                        if ($child->element == 'text' && $key == count($current->children)-1) $child->text = rtrim($child->text);
                        $filteredChildren[] = $child;
                    }
                    $current->children = $filteredChildren;
                }
            }
            // concat consecutive simple text-elements. Aufzählung => transformes to Aufz and ählung which should be Aufzählung
            if (isset($current->children)) {
                $concatedChildren = array();
                foreach ($current->children as $child) {
                    if (isset($concatedChildren[count($concatedChildren)-1])) {
                        $prevElement = $concatedChildren[count($concatedChildren)-1];
                        if ($prevElement->element == 'text' && $child->element == 'text') {
                            $concatedChildren[count($concatedChildren)-1]->text .= $child->text;
                            continue;
                        }
                    }
                    $concatedChildren[] = $child;
                }
                $current->children = $concatedChildren;
            }
        }
        $el = array_pop($this->_stack);
    }

    protected function startElement($parser, $element, $attributes)
    {
        $current = $this->_stack[count($this->_stack)-1];
        $newBlock = null;
        if (strtolower($element) == 'a') {
            if (isset($attributes['HREF']) && isset($this->_childComponents[$attributes['HREF']])) {
                $newBlock['element'] = 'component';
                $cmp = $this->_childComponents[$attributes['HREF']];
                if (is_instance_of($cmp->componentClass, 'Kwc_Basic_LinkTag_Abstract_Component') // support all link-tags
                    || is_instance_of($cmp->componentClass, 'Kwc_Basic_LinkTag_Component') // support special-case LinkTag-Component
                ) {
                    $newBlock['component'] = array(
                        "type" => "componentLink",
                        "id" => "no_id",
                        "data" => array(
                            "link" => $cmp
                        )
                    );
                } else {
                    $newBlock['component'] = $cmp;
                }
            }
        } else {
            $newBlock = array(
                'element' => strtolower($element)
            );
            if (!empty($attributes)) {
                $newBlock['attributes'] = array_change_key_case($attributes);
            }
        }
        if ($newBlock) {
            $newBlock = (object)$newBlock;
            if ($current->element == 'component') {
                $this->addElementToComponent($current, $newBlock);
            } else {
                if (!isset($current)) $current->children = array();
                $current->children[] = $newBlock;
            }
        }
        array_push($this->_stack, $newBlock);
    }

    protected function addElementToComponent(&$componentElement, $element)
    {
        // only componentLink supports adding child-elements. other components need to handle their content on their own
        if (!is_array($componentElement->component) || $componentElement->component['type'] != 'componentLink') return;

        if (!isset($componentElement->component['data']['content'])) {
            $componentElement->component['data']['content'] = array(
                "type" => "text",
                "id" => "no_id",
                "data" => array(
                    "content" => array()
                )
            );
        }
        $componentElement->component['data']['content']['data']['content'][] = $element;
    }

    protected function characterData($parser, $cdata)
    {
        $current = $this->_stack[count($this->_stack)-1];

        if ($current) {
            $cdata = preg_replace('/\s+/', ' ', $cdata);
            if (in_array($current->element, $this->_breakingElements)) {
                if (!isset($current->children) || count($current->children) == 0) {
                    $cdata = ltrim($cdata);
                }
            }
            if ($cdata) {
                $element = (object)array(
                    'element' => 'text',
                    'text' => $cdata
                );
                if (isset($current->attributes)) {
                    $element->attributes = $current->attributes;
                }
                if ($current->element == 'component') {
                    $this->addElementToComponent($current, $element);
                } else {
                    if (!isset($current)) $current->children = array();
                    $current->children[] = $element;
                }
            }
        }
    }

    public function parse(Kwf_Component_Data $data)
    {
        $row = $data->getComponent()->getRow();
        $html = str_replace('&nbsp;', '&#160;', $row->content); //Known bug https://bugs.php.net/bug.php?id=15092
        if (trim($html) == '') return array();

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

        $filteredChildren = array();
        foreach ($this->_stack[0]->children[0]->children as $child) {
            // tidy normally replaces text to p elements but this parser creates empty text elements
            if ($child->element == 'text' && trim($child->text) == '') continue;
            $filteredChildren[] = $child;
        }
        return $filteredChildren;
    }
}
