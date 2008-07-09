<?php
class Vpc_Basic_Text_Parser
{
    protected $_row;
    protected $_parser;
    protected $_stack = array();
    protected $_P = array();
    protected $_SPAN = array();
    protected $_finalHTML;
    protected $_deleteContent = false;
    protected $_enableColor = false;
    protected $_enableTagsWhitelist = true;
    protected $_enableStyles = true;

    public function __construct(Vpc_Basic_Text_Row $row = null)
    {
        $this->_row = $row;
    }

    protected function endElement($parser, $element)
    {
        $tag = array_pop($this->_stack);
        if ($tag == 'SPAN'){
            if ($tag != '') $this->_finalHTML .= '</'.$tag.'>';
        } else if ($tag) {
            $this->_finalHTML .= '</'.$tag.'>';
        }
    }

    protected function startElement($parser, $element, $attributes)
    {
        if ($element == 'SPAN') {
            if (isset($attributes['STYLE'])) {
                $style = $attributes['STYLE'];
            } else {
                $style = '';
            }
            if (preg_match('# *font-weight *: *bold *; *#', $style, $matches)){
                 array_push($this->_stack, 'strong');
                 $this->_finalHTML .= '<strong>';
            } elseif (preg_match('# *font-style *: *italic *; *#', $style, $matches)){
                 array_push($this->_stack, 'em');
                 $this->_finalHTML .= '<em>';
            } elseif (preg_match('# *text-decoration *: *underline *; *#', $style, $matches)){
                 array_push($this->_stack, 'u');
                 $this->_finalHTML .= '<u>';
            } elseif (preg_match('# *color *: *[0-9A-Za-z]* *#', $style, $matches) && $this->_enableColor){
                 array_push($this->_stack, 'span');
                 $this->_finalHTML .= '<span style="'.$style.'">';
            } elseif (preg_match('# *background-color *: *[0-9A-Za-z]* *#', $style, $matches) && $this->_enableColor){
                 array_push($this->_stack, 'span');
                 $this->_finalHTML .= '<span style="'.$style.'">';
            } else {
                if ($this->_enableStyles && isset($attributes['CLASS']) && preg_match('#^style[0-9]+$#', $attributes['CLASS'])) {
                    array_push($this->_stack, 'span');
                    $this->_finalHTML .= '<span class="'.$attributes['CLASS'].'">';
                } else {
                    array_push($this->_stack, false);
                }
            }
        } elseif ($element == 'BODY' || $element == 'O:P') {
            array_push($this->_stack, false);
            //do nothing
        } elseif ($element == 'SCRIPT') {
            array_push($this->_stack, false);
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
            if ($this->_enableTagsWhitelist
                && !in_array(strtolower($element), array_keys($this->_tagsWhitelist))) {
                //ignore this tag
                array_push($this->_stack, false);
            } else {
                array_push($this->_stack, strtolower($element));
                $this->_finalHTML .= '<'.strtolower($element);
                foreach ($attributes as $key => $value) {
                    if (!$this->_enableStyles
                        || in_array(strtolower($key), $this->_tagsWhitelist[strtolower($element)])) {
                        if ($key != 'CLASS' || preg_match('#^style[0-9]+$#', $value)) {
                            $this->_finalHTML .= ' ' . strtolower($key) . '="'. $value . '"';
                        }
                    }
                }
                $this->_finalHTML .= '>';
            }
        }

    }

    protected function characterData($parser, $cdata)
    {
        if (!$this->_deleteContent){
            $this->_finalHTML .= $cdata;
            $this->_deleteContent = false;
        }
    }

    public function setEnableColor($value)
    {
        $this->_enableColor = $value;
    }

    public function setEnableTagsWhitelist($value)
    {
        $this->_enableTagsWhitelist = $value;
    }

    public function setEnableStyles($value)
    {
        $this->_enableStyles = $value;
    }

    public function parse($html)
    {
        $this->_tagsWhitelist = array(
            'p'=>array('class'), 'a'=>array('href'),
            'img'=>array('src'), 'br'=>array(), 'strong'=>array(), 'em'=>array(),
            'u'=>array(), 'ul'=>array(), 'ol'=>array(), 'li'=>array()
        );
        if ($this->_enableStyles) {
            $this->_tagsWhitelist = array_merge($this->_tagsWhitelist, 
                array('span'=>array('class'), 'h1'=>array('class'), 'h2'=>array('class'),
                      'h3'=>array('class'), 'h4'=>array('class'),
                      'h5'=>array('class'), 'h6'=>array('class')));
        }
        $this->_stack = array();
        $this->_finalHTML = '';
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
        return $this->_finalHTML;
    }
}
