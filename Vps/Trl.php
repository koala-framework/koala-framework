<?php
class Vps_Trl {

    function trl($string, $text, $type){
       $params = $this->_checkArray($text);
       $xml = $this->_setupTrl($type);
       $text = $this->_findElement($string, $xml);
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
        $params = $this->_checkArray($text);
        $xml = $this->_setupTrl($type);
        $text = $this->_findElement($string, $xml, $context);
        if ($text == null){
           return $string;
       } else {
           foreach ($params as $key => $value){
               $text = str_replace('{'.$key.'}', $value, $text);
           }
           return $text;
       }
    }

    function trlp($single, $plural, $text, $type){
        $params = $this->_checkArray($text);
        $xml = $this->_setupTrl($type);
        $single = $this->_findElement($single, $xml);
        $plural = $this->_findElementPlural($plural, $xml);
        if ($params == null){
           return $text;
       } else {
           if ($params[0] != 1){
               foreach ($params as $key => $value){
                   $text = str_replace('{'.$key.'}', $value, $plural);
               }
           } else {
               foreach ($params as $key => $value){
                   $text = str_replace('{'.$key.'}', $value, $single);
               }
           }
           return $text;
       }
    }

    function trlcp($context, $single, $plural, $text, $type){
        $params = $this->_checkArray($text);
        $xml = $this->_setupTrl($type);
        $single = $this->_findElement($single, $xml, $context);
        $plural = $this->_findElementPlural($plural, $xml, $context);
        if ($params == null){
           return $text;
       } else {
           if ($params[0] != 1){
               foreach ($params as $key => $value){
                   $text = str_replace('{'.$key.'}', $value, $plural);
               }
           } else {
               foreach ($params as $key => $value){
                   $text = str_replace('{'.$key.'}', $value, $single);
               }
           }
           return $text;
       }
    }
    private function _checkArray($text) {
        if (is_array($text)){
            return $text;
        } else {
            return array($text);
        }
    }

    private function _setupTrl ($type){
           if ($type == 'project'){
               $directory = '.';
               $inipath = 'application/trl.xml';
           } else {
               $directory = VPS_PATH;
               $inipath = $directory.'/trl.xml';
           }
           $contents = file_get_contents($inipath);
           return new SimpleXMLElement($contents);
    }

    protected function _findElement($needle, $xml, $context = null){
        foreach ($xml->text as $element) {
                if ($element->en == $needle && $element->de != '_' && ($element['context'] == $context)){
                    return (string) $element->de;
                }
        }
        return $needle;
    }

    protected function _findElementPlural($needle, $xml, $context = null){
        foreach ($xml->text as $element) {
                if ($element->en_plural == $needle && $element->de_plural != '_' && ($element['context'] == $context)){
                    return (string) $element->de_plural;
                }
        }
        return $needle;
    }


}