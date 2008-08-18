<?php
class Vps_Trl
{
    private $_xml;
    private $_xmlVps;
    private $_languages; //cache

    const SOURCE_VPS = 'vps';
    const SOURCE_WEB = 'web';

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
        if (count($languages) > 2 && isset(Zend_Registry::get('userModel')->getAuthedUser()->language)){
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
        return $type == self::SOURCE_WEB ? $this->_xml : $this->_xmlVps;
    }

    public function trl($string, $params, $source)
    {
        return $this->trlc(null, $string, $params, $source);
    }

    function trlc($context, $string, $params, $source)
    {
        $params = $this->_makeArray($params);
        $text = $this->_findElement($string, $source, $context);
        foreach ($params as $key => $value) {
            $text = str_replace('{'.$key.'}', $value, $text);
        }
        return $text;
    }

    function trlp($single, $plural, $params, $source)
    {
        return $this->trlcp(null, $single, $plural, $params, $source);
    }

    function trlcp($context, $single, $plural, $params, $source)
    {
        $params = $this->_makeArray($params);
        if ($params[0] != 1){
            $text = $this->_findElementPlural($plural, $source, $context);
        } else {
            $text = $this->_findElement($single, $source, $context);
        }
        foreach ($params as $key => $value){
            $text = str_replace('{'.$key.'}', $value, $text);
        }
        return $text;
    }

    private function _makeArray($placeolders)
    {
        return is_array($placeolders) ? $placeolders : array($placeolders);
    }

    protected function _findElement($needle, $source, $context = null)
    {
        if ($source == self::SOURCE_WEB) $codeLanguage = $this->getWebCodeLanguage();
        else $codeLanguage = "en";

        $target = $this->getTargetLanguage();
        foreach ($this->_getXml($source)->text as $element) {
            if ($element->$codeLanguage == $needle && $element->$target != '_' && ($element['context'] == $context)){
                return (string) $element->$target;
            }
        }
        return $needle;
    }

    protected function _findElementPlural($needle, $source, $context = null)
    {
        if ($source == self::SOURCE_WEB) $codeLanguage = $this->getWebCodeLanguage();
        else $codeLanguage = "en";
        $target = $this->getTargetLanguage().'_plural';
        $plural = $codeLanguage.'_plural';
        foreach ($this->_getXml($source)->text as $element) {
            if ($element->$plural == $needle && $element->$target != '_' && ($element['context'] == $context)) {
                return (string)$element->$target;
            }
        }
        return $needle;
    }

    function getTrlpValues($context, $single, $plural, $source)
    {
        $values = array();
        $values['plural'] = $this->_findElementPlural($plural, $source, $context);
        $values['single'] = $this->_findElement($single, $source, $context);
        return $values;
    }

    public function parse($content)
    {
        return array_merge($this->_parseType('trl', $content),
                           $this->_parseType('trlc', $content),
                           $this->_parseType('trlp', $content),
                           $this->_parseType('trlcp', $content));
    }

    private function _parseType($type, $content)
    {
        $pattern = null;
        switch($type) {
            case "trl":
                $pattern = '#(trl|trlVps)((\("(.+?)"[\)|^,])|(\(\'(.*?)\'[\)|^,])|'.
                             '(\(\'(.+?)\', +(.+?)\))|(\(\"(.+?)\", +(.+?)\)))#s';
                break;
            case "trlc":
                $pattern = '#(trlc|trlcVps)((\(\'(.*?)\', +\'(.*?)\'[\)|^,])|(\("(.*?)", +"(.*?)"[\)|^,])|'.
                			'(\(\'(.+?)\', +\'(.*?)\', +(.*?)\))|(\(\"(.+?)\", +"(.*?)", +(.*?)\)))#s';
                break;
            case "trlp":
                $pattern = '#(trlp|trlpVps)((\(\'(.+?)\', *\'(.*?)\', *(.*?)\))|(\(\"(.*?)\", +"(.*?)", +(.*?)\)))#s';
                break;
            case "trlcp":
                $pattern = '#(trlcp|trlcpVps)((\(\'(.+?)\', \'(.+?)\', *\'(.+?)\', *(.+?)\))|(\("(.+?)", \"(.+?)\", +"(.*?)", +(.*?)\)))#s';
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
            $ret['type'] = $m[1][$key];
            $ret['before'] = $m[0][$key];
            if ($ret['type'] == "trl" || $ret['type'] == "trlVps"){
                if (($m[4][$key] != "")) {
                    $ret['text'] = $m[4][$key];
                } elseif (($m[6][$key] != "")) {
                    $ret['text'] = $m[6][$key];
                }  elseif (($m[8][$key] != "")) {
                    $ret['text'] = $m[8][$key];
                }  elseif (($m[11][$key] != "")) {
                    $ret['text'] = $m[11][$key];
                }
            } elseif ($ret['type'] == "trlc" || $ret['type'] == "trlcVps"){
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
            } elseif ($ret['type'] == "trlp" || $ret['type'] == "trlpVps"){
                if (($m[4][$key] != "")) {
                    $ret['text'] = $m[4][$key];
                    $ret['plural'] = $m[5][$key];
                } elseif (($m[8][$key] != "")) {
                    $ret['text'] = $m[8][$key];
                    $ret['plural'] = $m[9][$key];
                }
            } elseif ($ret['type'] == "trlcp" || $ret['type'] == "trlcpVps"){
                if (($m[4][$key] != "")) {
                    $ret['context'] = $m[4][$key];
                    $ret['text'] = $m[5][$key];
                    $ret['plural'] = $m[6][$key];
                } elseif (($m[9][$key] != "")) {
                    $ret['context'] = $m[9][$key];
                    $ret['text'] = $m[10][$key];
                    $ret['plural'] = $m[11][$key];
                }
            } else {
                throw new Vps_Exception("Unknown type: '$ret[type]' for trl-parsing");
            }
            if (strpos($ret['type'], 'Vps')) {
                $ret['source'] = Vps_Trl::SOURCE_VPS;
                $ret['type'] = str_replace('Vps', '', $ret['type']);
            } else {
                $ret['source'] = Vps_Trl::SOURCE_WEB;
            }
            $ret['text'] = $this->_unescapeString($ret['text']);
            if (isset($ret['plural'])) $ret['plural'] = $this->_unescapeString($ret['plural']);
            if (isset($ret['context'])) $ret['context'] = $this->_unescapeString($ret['context']);
            $retAll[] = $ret;
        }
        return $retAll;
    }

    private function _unescapeString($text)
    {
        $newText = "";
        for ($i = 0; $i < strlen($text); $i++) {
            if ($text[$i] == '\\'){
                switch ($text[$i+1]){
                    case 'n': $temp = "\n"; $i++; break;
                    case 't': $temp = "\t"; $i++; break;
                    case '"': $temp = "\""; $i++; break;
                    case '$': $temp = "\$"; $i++; break;
                    case '\\': $temp = $text[$i]; $i++; break;
                    case "'": $temp = "'"; $i++; break;
                    default: $temp = $text[$i].$text[$i+1]; $i++; $break;
                }
            } else {
                $temp = $text[$i];
            }
            $newText .= $temp;
        }
        return $newText;
    }
}

