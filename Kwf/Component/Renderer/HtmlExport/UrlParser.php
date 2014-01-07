<?php
class Kwf_Component_Renderer_HtmlExport_UrlParser
{
    private $_relativeUrlPrefix;

    private $_parser;
    private $_ret;

    private $_noCloseTags = array('br', 'img', 'hr');

    public function __construct($relativeUrlPrefix)
    {
        $this->_relativeUrlPrefix = $relativeUrlPrefix;
    }

    protected function endElement($parser, $tag)
    {
        $tag = strtolower($tag);
        if ($tag == 'body') return;
        if (!in_array($tag, $this->_noCloseTags)) {
            $this->_ret .= "</$tag>";
        }
    }

    protected function startElement($parser, $tag, $attributes)
    {
        $tag = strtolower($tag);
        if ($tag == 'body') return;

        foreach (array_keys($attributes) as $n) {
            if (($n=='SRC' || $n=='HREF') && substr($attributes[$n], 0, 1) == '/') {
                $attributes[$n] = $this->_relativeUrlPrefix.$attributes[$n];
            }
            if (strtolower($n) != $n) {
                $attributes[strtolower($n)] = $attributes[$n];
                unset($attributes[$n]);
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
    }

    protected function characterData($parser, $cdata)
    {
        $this->_ret .= $cdata;
    }

    public function parse($html)
    {
        // replace entities
        $html = preg_replace('/&([a-z0-9#]{2,5});/i', '+$1;', $html);

        //before sending to xml parser make sure we have valid xml by tidying it up
        $config = array(
            'indent'         => true,
            'output-xhtml'   => true,
            'clean'          => false,
            'wrap'           => '86',
            'doctype'        => 'omit',
            'drop-proprietary-attributes' => false,
            'word-2000'      => true,
            'show-body-only' => true,
            'bare'           => true,
            'enclose-block-text'=>true,
            'enclose-text'   => true,
            'join-styles'    => false,
            'join-classes'   => false,
            'logical-emphasis' => true,
            'lower-literals' => true,
            'literal-attributes' => false,
            'indent-spaces' => 2,
            'quote-nbsp'     => true,
            'output-bom'     => false,
            'char-encoding'  =>'utf8',
            'newline'        =>'LF',
            'uppercase-tags' => false,
            'drop-font-tags' => false,
        );
        if (class_exists('tidy')) {
            $tidy = new tidy;
            $tidy->parseString($html, $config, 'utf8');
            $tidy->cleanRepair();
            $html = $tidy->value;
        } else {
            require_once Kwf_Config::getValue('externLibraryPath.htmLawed').'/htmLawed.php';
            $html = htmLawed($html);
        }

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
            $ex = new Kwf_Exception("HtmlExport UrlParser XML Error $errorCode: ".xml_error_string($errorCode).
                    "in line ".xml_get_current_line_number($this->_parser)." parsed html: ".$html
                    );
            $ex->logOrThrow();
        }

        // re-replace entities
        $this->_ret = preg_replace('/\+([a-z0-9#]{2,5});/i', '&$1;', $this->_ret);

        return $this->_ret;
    }
}
