<?php
class Vpc_Basic_Text_Parser
{
    protected $_row;
    protected $_parser;
    protected $_elementStack = array();
    protected $_stack = array();
    protected $_P = array();
    protected $_SPAN = array();
    protected $_finalHTML;
    protected $_deleteContent = false;
    protected $_enableColor = false;

    public function __construct(Vpc_Basic_Text_Row $row)
    {
        $this->_row = $row;

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

    protected function endElement($parser, $element)
    {
        $element = array_pop($this->_elementStack);

        if ($element == 'SPAN'){
            $tag = array_pop($this->_stack);
            if ($tag != '') $this->_finalHTML .= '</'.$tag.'>';
        } elseif ($element == 'BODY' || $element == 'O:P' || $element == 'BR' || $element == 'IMG' || $element == 'SCRIPT') {
            //do nothing
        }
        else {
            $this->_finalHTML .= '</'.$element.'>';

        }
    }

    protected function startElement($parser, $element, $attributes)
    {
        array_push($this->_elementStack, $element);

        if ($element == 'SPAN') {
            if (isset($attributes['STYLE'])) {
                $style = $attributes['STYLE'];
            } else {
                $style = '';
            }
            if (preg_match('# *font-weight *: +bold *; *#', $style, $matches)){
                 array_push($this->_stack, 'strong');
                 $this->_finalHTML .= '<strong>';
            } elseif (preg_match('# *font-style *: +italic *; *#', $style, $matches)){
                 array_push($this->_stack, 'em');
                 $this->_finalHTML .= '<em>';
            } elseif (preg_match('# *text-decoration *: +underline *; *#', $style, $matches)){
                 array_push($this->_stack, 'u');
                 $this->_finalHTML .= '<u>';
            } elseif (preg_match('# *color *: +[0-9,]* *#', $style, $matches) && $this->_enableColor){
                 array_push($this->_stack, 'span');
                 $this->_finalHTML .= '<span style="'.$style.'">';
            } elseif (preg_match('# *background-color *: +[0-9,A-Za-z]* *#', $style, $matches) && $this->_enableColor){
                 array_push($this->_stack, 'span');
                 $this->_finalHTML .= '<span style="'.$style.'">';
            } else {
                array_push($this->_stack, 'span');
                $this->_finalHTML .= '<'.$element;
                foreach ($attributes as $key => $value) {
                    $this->_finalHTML .= ' ' . $key . '="'. $value . '"';
                }

                $this->_finalHTML .= '>';
            }
        } elseif ($element == 'BODY' || $element == 'O:P') {
            //do nothing
        } elseif ($element == 'SCRIPT') {
            $this->_deleteContent = true;
        } else {
            if ($element == 'IMG') {
                $src = $attributes['SRC'];
                $id = preg_quote($this->_row->component_id);
                if (preg_match('#/media/([^/]+)/('.$id.'-i[0-9]+)#', $src, $m)) {
                    //"/media/$class/$id/$rule/$type/$checksum/$filename.$extension$random"
                    $classes = Vpc_Abstract::getSetting($this->_row->getTable()
                                ->getComponentClass(), 'childComponentClasses');
                    $t = Vpc_Abstract::getSetting($classes['image'], 'tablename');
                    $t = new $t(array('componentClass' => $classes['image']));
                    $imageRow = $t->find($m[2])->current();

                    if (isset($attributes['WIDTH']) && $imageRow) {
                        $imageRow->width = $attributes['WIDTH'];
                    }
                    if (isset($attributes['HEIGHT']) && $imageRow) {
                        $imageRow->height = $attributes['HEIGHT'];
                    }
                    if (isset($attributes['STYLE']) && $imageRow) {
                        if (preg_match('#[^a-zA-Z\\-]*width: *([0-9]+)px#', $attributes['STYLE'], $m)) {
                            $imageRow->width = $m[1];
                        }
                        if (preg_match('#[^a-zA-Z\\-]*height: *([0-9]+)px#', $attributes['STYLE'], $m)) {
                            $imageRow->height = $m[1];
                        }
                    }
                    
                    if ($imageRow) {
                        $imageRow->save();
                        $attributes['WIDTH'] = $imageRow->width;
                        $attributes['HEIGHT'] = $imageRow->height;
                        if (isset($attributes['STYLE'])) unset($attributes['STYLE']);
                    }
                }
            }
            $this->_finalHTML .= '<'.$element;
            foreach ($attributes as $key => $value) {
                 $this->_finalHTML .= ' ' . $key . '="'. $value . '"';
            }

            $this->_finalHTML .= '>';
        }

    }

    protected function characterData($parser, $cdata)
    {
       if (!$this->_deleteContent){
            $level   = sizeof($this->_elementStack) - 1;
            $element = $this->_elementStack[$level];
            $this->_finalHTML .= $cdata;
            $this->_deleteContent = false;
        }
    }

    public function setEnableColor($value)
    {
        $this->_enableColor = $value;
    }

    public function parse($html)
    {
        xml_parse($this->_parser,
          "<BODY>".$html."</BODY>",
          true);
        return $this->_finalHTML;
    }
}
