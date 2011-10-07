<?php
class Kwf_Hlp {
    
    protected $_xml;
    
    public function __construct()
    {
        $filename = 'hlp.xml';
        if (is_file($filename)) {
            $this->_xml = new SimpleXMLElement(file_get_contents($filename));
        }
        $filename = KWF_PATH . '/hlp.xml';
        if (is_file($filename)) {
            $this->_xmlKwf = new SimpleXMLElement(file_get_contents($filename));
        }
    }
    
    function hlp($key) {
        $language = Kwf_Trl::getInstance()->getTargetLanguage();
        if ($this->_xml) {
            $element = $this->_xml->xpath("/hlp/text[@key='$key']/$language");
            if ($element) {
                return (string)$element[0];
            }
        }
        return null;
    }

    function hlpKwf($key) {
        $language = Kwf_Trl::getInstance()->getTargetLanguage();
        if ($this->_xml) {
            $element = $this->_xmlKwf->xpath("/hlp/text[@key='$key']/$language");
            if ($element) {
                return (string)$element[0];
            }
        }
        return null;
    }
}