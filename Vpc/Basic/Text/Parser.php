<?php
class Vpc_Basic_Text_Parser
{
    //aktueller zustand von parser währen des parsens
    protected $_parser;
    protected $_stack;
    protected $_finalHTML;
    protected $_deleteContent;

    //einstellungen für parser
    protected $_row;
    protected $_enableColor = false;
    protected $_enableTagsWhitelist = true;
    protected $_enableStyles = true;
    protected $_enableCursorSpan = false;
    private $_masterStyles;


    public function __construct(Vpc_Basic_Text_Row $row = null)
    {
        $this->_row = $row;
    }

    public function setMasterStyles($ms)
    {
        $this->_masterStyles = $ms;
    }

    protected function _getMasterStyles()
    {
        if (!isset($this->_masterStyles)) {
            throw new Vps_Exception("you must call setMasterStyles");
        }
        return $this->_masterStyles;
    }

    protected function endElement($parser, $element)
    {
        $tag = array_pop($this->_stack);
        if ($tag && !$this->_deleteContent) {
            $this->_finalHTML .= '</'.$tag.'>';
        }
        if ($element == "SCRIPT") {
            $this->_deleteContent--;
        }
    }

    protected function startElement($parser, $element, $attributes)
    {
        $finalHTML = $this->_finalHTML;
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
                $allowedClasses = array();
                foreach ($this->_getMasterStyles() as $s) {
                    if ($s['tagName'] == strtolower($element)) {
                        $allowedClasses[] = $s['className'];
                    }
                }
                if ($this->_enableCursorSpan && isset($attributes['CLASS']) && $attributes['CLASS']=='_mce_type-bookmark') {
                    array_push($this->_stack, 'span');
                    $this->_finalHTML .= '<span class="'.$attributes['CLASS'].'"';
                    if (isset($attributes['STYLE'])) $this->_finalHTML .= ' style="'.$attributes['STYLE'].'"';
                    if (isset($attributes['ID'])) $this->_finalHTML .= ' id="'.$attributes['ID'].'"';
                    $this->_finalHTML .= '>';
                } else if ($this->_enableStyles && isset($attributes['CLASS'])
                    && (preg_match('#^style[0-9]+$#', $attributes['CLASS'])
                        || in_array($attributes['CLASS'], $allowedClasses))
                ) {
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
            $this->_deleteContent++;
        } else {
            if ($element == 'IMG') {
                $src = $attributes['SRC'];
                $id = preg_quote($this->_row->component_id);
                if (preg_match('#/media/([^/]+)/('.$id.'-i[0-9]+)#', $src, $m)) {
                    //"/media/$class/$id/$type/$checksum/$filename.$extension$random"
                    $class = Vpc_Abstract::getChildComponentClass($this->_row->getModel()
                                ->getComponentClass(), 'child', 'image');
                    $imageRow = Vpc_Abstract::createModel($class)->getRow($m[2]);

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
                $this->_finalHTML .= '<'.strtolower($element);
                foreach ($attributes as $key => $value) {
                    if (in_array(strtolower($key), $this->_tagsWhitelist[strtolower($element)])) {
                        $allowedClasses = array();
                        foreach ($this->_getMasterStyles() as $s) {
                            if ($s['tagName'] == strtolower($element)) {
                                $allowedClasses[] = $s['className'];
                            }
                        }
                        if ($key != 'CLASS' || (preg_match('#^style[0-9]+$#', $value) || in_array($value, $allowedClasses))) {
                            $this->_finalHTML .= ' ' . strtolower($key) . '="'. $value . '"';
                        }
                    }
                }
                if ($element == 'BR' || $element == 'IMG') {
                    $this->_finalHTML .= ' /';
                    array_push($this->_stack, false);
                } else {
                    array_push($this->_stack, strtolower($element));
                }
                $this->_finalHTML .= '>';
            }
        }
        if ($this->_deleteContent) {
            $this->_finalHTML = $finalHTML;
        }

    }

    protected function characterData($parser, $cdata)
    {
        $cdata = preg_replace("#<!--.*-->#", "", $cdata);
        if (!$this->_deleteContent){
            $this->_finalHTML .= $cdata;
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

    public function setEnableCursorSpan($value)
    {
        $this->_enableCursorSpan = $value;
    }

    public function parse($html)
    {
        $this->_tagsWhitelist = array(
            'p'=>array(), 'a'=>array('href'),
            'img'=>array('src'), 'br'=>array(), 'strong'=>array(), 'em'=>array(),
            'u'=>array(), 'strike'=>array(), 'ul'=>array(), 'ol'=>array(), 'li'=>array(),
            'sup'=>array(), 'sub'=>array()
        );
        if ($this->_enableStyles) {
            $this->_tagsWhitelist = array_merge($this->_tagsWhitelist,
                array('span'=>array('class'), 'h1'=>array('class'), 'h2'=>array('class'),
                      'h3'=>array('class'), 'h4'=>array('class'),
                      'h5'=>array('class'), 'h6'=>array('class')));
            $this->_tagsWhitelist['p'][] = 'class';
        }
        $this->_stack = array();
        $this->_finalHTML = '';
        $this->_deleteContent = 0;
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
