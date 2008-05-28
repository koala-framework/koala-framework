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

    public function getTargetLanguage()
    {
        $languages = $this->getLanguages();
        if (isset(Zend_Registry::get('userModel')->getAuthedUser()->language)){
            $userLanguage = Zend_Registry::get('userModel')->getAuthedUser()->language;
            if (array_search($userLanguage, $languages)) {
                return $userLanguage;
            }
        }
        return $this->getWebCodeLanguage();
    }

    public function getWebCodeLanguage()
    {
         $config = Zend_Registry::get('config');
         if ($config->webCodeLanguage) {
                return $config->webCodeLanguage;
            }
    }

    private function _getXml($type)
    {
        return $type == 'project' ? $this->_xml : $this->_xmlVps;
    }

    public function trl($string, $placeholders, $type)
    {
        $params = $this->_makeArray($placeholders);
        $text = $this->_findElement($string, $this->_getXml($type), $type);
        if ($params == null) {
            return $text;
        } else {
            foreach ($params as $key => $value) {
                $text = str_replace('{'.$key.'}', $value, $text);
            }
            return $text;
        }
    }


    function trlc($context, $string, $placeolders, $type)
    {
        $params = $this->_makeArray($placeolders);
        $text = $this->_findElement($string, $this->_getXml($type), $type, $context);

        if ($text == null) {
            return $string;
        } else {
            foreach ($params as $key => $value) {
                $text = str_replace('{'.$key.'}', $value, $text);
            }
            return $text;
        }
    }

    function trlp($single, $plural, $placeolders, $type, $prog_lang)
    {
        $params = $this->_makeArray($placeolders);
        $single = $this->_findElement($single, $this->_getXml($type), $type);
        $plural = $this->_findElementPlural($plural, $this->_getXml($type), $type);
        if ($params == null){
           return $placeolders;
       } else {
           if ($params[0] != 1) {
               foreach ($params as $key => $value) {
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

    function trlcp($context, $single, $plural, $placeolders, $type, $prog_lang)
    {
        $params = $this->_makeArray($placeolders);
        $single = $this->_findElement($single, $this->_getXml($type), $type, $context);
        $plural = $this->_findElementPlural($plural, $this->_getXml($type), $type, $context);
        if ($params == null) {
            return $placeolders;
        } else {
            if ($params[0] != 1){
                foreach ($params as $key => $value){
                    if ($prog_lang == 'js') return $plural;
                    $text = str_replace('{'.$key.'}', $value, $plural);
                }
            } else {
                foreach ($params as $key => $value) {
                    if ($prog_lang == 'js') return $single;
                    $text = str_replace('{'.$key.'}', $value, $single);
                }
            }
            return $text;
        }
    }

    private function _makeArray($placeolders)
    {
        return is_array($placeolders) ? $placeolders : array($placeolders);
    }

    protected function _findElement($needle, $xml, $type, $context = null)
    {
        if ($type == 'project') $temp_default_lang = $this->getWebCodeLanguage();
        else $temp_default_lang = "en";
        $temp_target_lang = $this->getTargetLanguage();
        foreach ($xml->text as $element) {
            if ($element->$temp_default_lang == $needle && $element->$temp_target_lang != '_' && ($element['context'] == $context)){
                return (string) $element->$temp_target_lang;
            }
        }
        return $needle;
    }

    protected function _findElementPlural($needle, $xml, $type, $context = null)
    {
        if ($type == 'project') $temp_default_lang = $this->getWebCodeLanguage();
        else $temp_default_lang = "en";
        $temp_target_lang = $this->getTargetLanguage();
        $temp_plural = $temp_default_lang.'_plural';
        $temp_target_plural = $temp_target_lang.'_plural';
        foreach ($xml->text as $element) {
            if ($element->$temp_plural == $needle && $element->$temp_target_plural != '_' && ($element['context'] == $context)){
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

    public function parse($content)
    {
        return array_merge($this->_parseType('trl', $content),
                           $this->_parseType('trlc', $content));
    }

    private function _parseType($type, $content)
    {   $pattern = null;
        switch($type) {
            case "trl":
                $pattern = '#(trl|trlVps)((\("(.+?)"\))|(\(\'(.+?)\'\))|'.
                             '(\(\'(.+?)\', (.*)\))|(\(\"(.+?)\", (.*)\)))#';
                break;
            case "trlc":
                $pattern = '#(trlc|trlcVps)((\(\'(.+?)\', \'(.*)\'\))|(\("(.+?)", "(.*)"\))|'.
                			'(\(\'(.+?)\', +\'(.*)\', +(.*)\))|(\(\"(.+?)\", +"(.*)", +(.*)\)))#';

                break;
        }
        if ($pattern) {
            preg_match_all($pattern, $content, $m);
            if ($m[0]){
                return $this->_rearrange($m);
            }
        }
        return array();
    }

    private function _rearrange ($m)
    {
        $retAll = array();
        foreach($m[0] as $key => $value) {
            $ret = array();
            if ($m[1][$key] == "trl" || $m[1][$key] == "trlVps"){
                $ret['isVps'] = strpos($m[1][$key], 'Vps');
                $ret['type'] = str_replace('Vps', '', $m[1][$key]);
                if (($m[4][$key] != "")) {
                    $ret['text'] = $m[4][$key];
                } elseif (($m[6][$key] != "")) {
                    $ret['text'] = $m[6][$key];
                }  elseif (($m[8][$key] != "")) {
                    $ret['text'] = $m[8][$key];
                }  elseif (($m[11][$key] != "")) {
                    $ret['text'] = $m[11][$key];
                }
            } elseif ($m[1][$key] == "trlc" || $m[1][$key] == "trlcVps"){
                $ret['isVps'] = strpos($m[1][$key], 'Vps');
                $ret['type'] = str_replace('Vps', '', $m[1][$key]);
                if (($m[4][$key] != "")) {
                    $ret['context'] = $m[4][$key];
                    $ret['text'] = $m[5][$key];
                } elseif (($m[7][$key] != "")) {
                    $ret['context'] = $m[7][$key];
                    $ret['text'] = $m[8][$key];
                }  elseif (($m[10][$key] != "")) {
                    $ret['context'] = $m[10][$key];
                    $ret['text'] = $m[11][$key];
                }  elseif (($m[14][$key] != "")) {
                    $ret['context'] = $m[14][$key];
                    $ret['text'] = $m[15][$key];
                }
            }
            $retAll[] = $ret;
        }
        return $retAll;
    }

}