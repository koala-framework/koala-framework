<?php
class Vps_Hlp {
    
    protected $_xml;
    
    public function __construct()
    {
        $filename = 'application/hlp.xml';
        if (is_file($filename)) {
            $this->_xml = new SimpleXMLElement(file_get_contents($filename));
        }
        $filename = VPS_PATH . '/hlp.xml';
        if (is_file($filename)) {
            $this->_xmlVps = new SimpleXMLElement(file_get_contents($filename));
        }
    }
    
    function hlp($key) {
        $language = Vps_Trl::getInstance()->getTargetLanguage();
        if ($this->_xml) {
            $element = $this->_xml->xpath("/hlp/text[@key='$key']/$language");
            if ($element) {
                return (string)$element[0];
            }
        }
        return null;
    }

    function hlpVps($key) {
        $language = Vps_Trl::getInstance()->getTargetLanguage();
        if ($this->_xml) {
            $element = $this->_xmlVps->xpath("/hlp/text[@key='$key']/$language");
            if ($element) {
                return (string)$element[0];
            }
        }
        return null;
    }
}