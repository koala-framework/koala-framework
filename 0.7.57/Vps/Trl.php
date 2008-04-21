<?php
class Vps_Trl
{
    private $_xml;
    private $_xmlVps;
    private $_languages; //cache

    public function __construct()
    {
        $filename = 'application/trl.xml';
        if (is_file($filename)) {
           $this->_xml = new SimpleXMLElement(file_get_contents($filename));
        }
        $filename = VPS_PATH . '/trl.xml';
        if (is_file($filename)) {
           $this->_xmlVps = new SimpleXMLElement(file_get_contents($filename));
        }
    }

    public function getLanguages()
    {
        if (!isset($this->_languages)) {
            $config = Zend_Registry::get('config');
            if ($config->languages) {
                $this->_languages = array_keys($config->languages->toArray());
            } else if ($config->webCodeLanguage) {
                $this->_languages = array($config->webCodeLanguage);
            }
            if (empty($this->_languages)) {
                throw new Vps_Exception('Neither config languages nor config webCodeLanguage set.');
            }
        }
        return $this->_languages;
    }
    
    public function getCurrentLanguage()
    {
        $languages = $this->getLanguages();
        if (isset(Zend_Registry::get('userModel')->getAuthedUser()->language)){
            $userLanguage = Zend_Registry::get('userModel')->getAuthedUser()->language;
            if (array_search($userLanguage, $languages)) {
                return $userLanguage;
            }
        }
        return $this->getDefaultLanguage();
    }

    public function getDefaultLanguage()
    {
        $languages = $this->getLanguages();
        return $languages[0];
    }
    
    private function _getXml($type)
    {
        return $type == 'project' ? $this->_xml : $this->_xmlVps;
    }
    
    function trl($string, $text, $type){
       $params = $this->_makeArray($text);
       $text = $this->_findElement($string, $this->_getXml($type));
       if ($params == null){
           return $text;
       } else {
           foreach ($params as $key => $value){
               $text = str_replace('{'.$key.'}', $value, $text);
           }
           return $text;
       }
    }

    function trlc($context, $string, $text, $type){
        $params = $this->_makeArray($text);
        $text = $this->_findElement($string, $this->_getXml($type), $context);
        if ($text == null){
           return $string;
       } else {
           foreach ($params as $key => $value){
               $text = str_replace('{'.$key.'}', $value, $text);
           }
           return $text;
       }
    }

    function trlp($single, $plural, $text, $type, $prog_lang){

        $params = $this->_makeArray($text);
        $single = $this->_findElement($single, $this->_getXml($type));
        $plural = $this->_findElementPlural($plural, $this->_getXml($type));
        if ($params == null){
           return $text;
       } else {
           if ($params[0] != 1){
               foreach ($params as $key => $value){
                   if ($prog_lang == 'js') return $plural;

                   $text = str_replace('{'.$key.'}', $value, $plural);
               }
           } else {
               foreach ($params as $key => $value){
                   if ($prog_lang == 'js') return $single;

                   $text = str_replace('{'.$key.'}', $value, $single);
               }
           }
           return $text;
       }
    }

    function trlcp($context, $single, $plural, $text, $type, $prog_lang){

        $params = $this->_makeArray($text);
        $single = $this->_findElement($single, $this->_getXml($type), $context);
        $plural = $this->_findElementPlural($plural, $this->_getXml($type), $context);
        if ($params == null){
           return $text;
       } else {
           if ($params[0] != 1){
               foreach ($params as $key => $value){
                   if ($prog_lang == 'js') return $plural;
                   $text = str_replace('{'.$key.'}', $value, $plural);
               }
           } else {
               foreach ($params as $key => $value){
                   if ($prog_lang == 'js') return $single;
                   $text = str_replace('{'.$key.'}', $value, $single);
               }
           }
           return $text;
       }
    }
    private function _makeArray($text) {
        return is_array($text) ? $text : array($text);
    }
    
    protected function _findElement($needle, $xml, $context = null){
        $temp_default_lang = $this->getDefaultLanguage();
        $temp_target_lang = $this->getCurrentLanguage();
        foreach ($xml->text as $element) {
                // en direkt hinschreiben ist nur ein quick-fix
                if ($element->en == $needle && $element->$temp_target_lang != '_' && ($element['context'] == $context)){
                    return (string) $element->$temp_target_lang;
                }
        }
        return $needle;
    }

    protected function _findElementPlural($needle, $xml, $context = null){
        $temp_default_lang = $this->getDefaultLanguage();
        $temp_target_lang = $this->getCurrentLanguage();
        $temp_plural = $temp_default_lang.'_plural';
        $temp_target_plural = $temp_target_lang.'_plural';
        foreach ($xml->text as $element) {
                // en_plural direkt hinschreiben ist nur ein quick-fix
                if ($element->en_plural == $needle && $element->$temp_target_plural != '_' && ($element['context'] == $context)){
                    return (string) $element->$temp_target_plural;
                }
        }
        return $needle;
    }

    function getTrlpValues($context, $single, $plural, $type){
       $xml = $this->_getXml($type);
       $values = array();
       $values['plural'] = $this->_findElementPlural($plural, $xml, $context);
       $values['single'] = $this->_findElement($single, $xml, $context);
       return $values;
    }

}