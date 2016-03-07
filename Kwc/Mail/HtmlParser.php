<?php
class Kwc_Mail_HtmlParser
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

    private static function _matchStackEntrySelector($stack, $selector)
    {
        $ret = true;

        $classes = explode(' ', $stack['class']);

        $selectorsWithTag = explode('.', $selector);
        $selectorTag = $selectorsWithTag[0];
        unset($selectorsWithTag[0]);
        $selectors = array_values($selectorsWithTag);

        if (!empty($selectorTag) && $selectorTag != $stack['tag']) {
            $ret = false;
        }

        foreach ($selectors as $s) {
            if (!in_array($s, $classes)) {
                $ret = false;
            }
        }

        return $ret;
    }

    private static function _matchesStyle($stack, $style)
    {
        $tag = $stack[count($stack)-1]['tag'];
        $class = $stack[count($stack)-1]['class'];
        if (isset($style['tag'])) {
            if (isset($style['selector'])) throw new Kwf_Exception("don't use tag AND selector");
            return ($style['tag'] == '*' || $style['tag'] == $tag) && (!isset($style['class']) || $class == $style['class']);
        } else if (isset($style['selector'])) {
            $selectors = explode(' ', $style['selector']); //css-artiger selector
            $selectors = array_reverse($selectors);
            $stack = array_reverse($stack);
            foreach ($selectors as $selector) {
                foreach ($stack as $stackItem=>$s) {
                    if (self::_matchStackEntrySelector($s, $selector) || $selector == $s['tag']) {
                        $stack = array_slice($stack, $stackItem+1);
                        continue 2;
                    }
                }
                return false;
            }
            return true;
        }
        throw new Kwf_Exception_NotYetImplemented();
    }

    protected function startElement($parser, $tag, $attributes)
    {
        $tag = strtolower($tag);
        if ($tag == 'body') return;

        foreach (array_keys($attributes) as $n) {
            if (strtolower($n) != $n) {
                $attributes[strtolower($n)] = $attributes[$n];
                unset($attributes[$n]);
            }
        }

        $class = '';
        if (isset($attributes['class'])) {
            $class = $attributes['class'];
        }
        $stack = $this->_stack;
        $stack[] = array( //extra stack der _matches übergeben werden kann, den richtigen stack kömma nu ned bauen
            'tag' => $tag,
            'class' => $class,
            'appendedTags' => array()
        );
        $appendTags = array();
        $styles = array();
        foreach ($this->_styles as $s) {
            if (self::_matchesStyle($stack, $s)) {
                $appendTags = array();
                if (isset($s['appendTags'])) {
                    $appendTags = $s['appendTags'];
                }
                if (isset($s['styles'])) {
                    foreach ($s['styles'] as $style=>$value) {
                        if ($style == 'font-family') {
                            $appendTags['font']['face'] = $value;
                        /*
                        } else if ($style == 'font-size') {
                            if (substr($value, -2) == 'px') {
                                $value = round((int)$value / 6); // TODO: das ist pi mal daumen
                                $appendTags['font']['size'] = $value;
                            } else {
                                $attributes['style'] .= "$style: $value; ";
                            }
                        */
                        } else if ($style == 'color') {
                            $appendTags['font']['color'] = $value;
                        } else if ($style == 'font-weight' && $value == 'bold') {
                            $appendTags['b'] = array();
                        } else if ($style == 'text-align') {
                            if ($value == 'center') {
                                $appendTags[$value] = array();
                            } else {
                                $attributes['align'] = $value;
                            }
                        } else {
                            $styles[$style] = $value;
                        }
                    }
                }
                if (isset($s['replaceTag'])) {
                    $tag = $s['replaceTag'];
                    $attributes = array();
                    $styles = array();
                }
            }
        }

        if ($styles) {
            $attributes['style'] = '';
            foreach ($styles as $s=>$v) {
                $attributes['style'] .= "$s: $v; ";
            }
        }

        $this->_ret .= "<$tag";
        foreach ($attributes as $n=>$v) {
            if ($v != "") $this->_ret .= " $n=\"$v\"";
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
        foreach ($appendTags as $tagOrIndex=>$attr) {
            $t = isset($attr['tag']) ? $attr['tag'] : $tagOrIndex;
            if (!in_array($t, $this->_noCloseTags)) {
                $stackItem['appendedTags'][] = $t;
            }
            $this->_ret .= "<$t";
            foreach ($attr as $k=>$v) {
                if ($k == 'tag') continue;
                $this->_ret .= " $k=\"$v\"";
            }
            if (in_array($t, $this->_noCloseTags)) {
                $this->_ret .= "/>";
            } else {
                $this->_ret .= ">";
            }
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

        $html = preg_replace('#<((kwc|/?plugin)[^>]*)>#', '+\1$', $html);

        //before sending to xml parser make sure we have valid xml by tidying it up
        $html = Kwf_Util_Tidy::repairHtml($html, array(
            'show-body-only' => false,
            'wrap'           => false,
        ));

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
            $ex = new Kwf_Exception("Mail HtmlParser XML Error $errorCode: ".xml_error_string($errorCode).
                    "in line ".xml_get_current_line_number($this->_parser)." parsed html: ".$html
                    );
            $ex->logOrThrow();
        }

        // re-replace entities
        $this->_ret = preg_replace('/\+([a-z0-9#]{2,5});/i', '&$1;', $this->_ret);

        $this->_ret = preg_replace('#\+(kwc|plugin|/plugin)(.*?)\$#', '<\1\2>', $this->_ret);

        return $this->_ret;
    }
}
