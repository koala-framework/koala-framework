<?php
class Vps_Trl {

    private $_defaultLanguage;
    private $_targetLanguage;

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

    function trlp($single, $plural, $text, $type, $prog_lang){

        $params = $this->_checkArray($text);
        $xml = $this->_setupTrl($type);
        $single = $this->_findElement($single, $xml);
        $plural = $this->_findElementPlural($plural, $xml);
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

        $params = $this->_checkArray($text);
        $xml = $this->_setupTrl($type);
        $single = $this->_findElement($single, $xml, $context);
        $plural = $this->_findElementPlural($plural, $xml, $context);
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

                   //festsetzen der sprachen
                $config = new Zend_Config_Ini('application/config.ini');
                $cnt = 0;
                foreach($config->production->languages as $key => $value){
                    if ($cnt == 0){
                        $this->_defaultLanguage = $key;

                    } elseif ($cnt == 1){
                        $this->_targetLanguage = $key;
                    }
                    $cnt++;
                }

           } else {
               $directory = VPS_PATH;
               $inipath = $directory.'/trl.xml';
               $this->_defaultLanguage = 'en';
               $this->_targetLanguage = 'de';
           }
           $contents = file_get_contents($inipath);
           return new SimpleXMLElement($contents);
    }

    protected function _findElement($needle, $xml, $context = null){
        $temp_default_lang = $this->_defaultLanguage;
        $temp_target_lang = $this->_targetLanguage;
        foreach ($xml->text as $element) {
                if ($element->$temp_default_lang == $needle && $element->$temp_target_lang != '_' && ($element['context'] == $context)){
                    return (string) $element->$temp_target_lang;
                }
        }
        return $needle;
    }

    protected function _findElementPlural($needle, $xml, $context = null){
        $temp_default_lang = $this->_defaultLanguage;
        $temp_target_lang = $this->_targetLanguage;
        foreach ($xml->text as $element) {
                if ($element->$temp_default_lang.'_plural' == $needle && $element->$temp_target_lang.'_plural' != '_' && ($element['context'] == $context)){
                    return (string) $element->$temp_target_lang.'_plural';
                }
        }
        return $needle;
    }

    function getTrlpValues($context, $single, $plural, $type){
       $xml =  $this->_setupTrl($type);
       $values = array();
       $values['plural'] = $this->_findElementPlural($plural, $xml, $context);
       $values['single'] = $this->_findElement($single, $xml, $context);
       return $values;
    }


}