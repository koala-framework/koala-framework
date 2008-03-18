<?php

class Vps_Controller_Action_Trl_IndexController extends Vps_Controller_Action
{
    private $_defaultLanguage;
    private $_languages = array();
    public function indexAction()
    {

        //festsetzen der sprachen
        $config = new Zend_Config_Ini('application/config.ini');
        $cnt = 0;
        foreach($config->production->languages as $key => $value){
            if ($cnt == 0){
                $this->_defaultLanguage = $key;
                $cnt++;
            } else {
                $this->_languages[] = $key;
            }
        }

        //das Project
       $directory = ".";
       $inipath = 'application/trl.xml';

       if (!file_exists($inipath)){
           $file = fopen($inipath, 'a+');
           fputs($file, "<trl></trl>");
       } else {
           $file = fopen($inipath, 'a+');
       }
       fclose($file);

       $contents = file_get_contents($inipath);
       $xml = new SimpleXMLElement($contents);

       file_put_contents($inipath, $this->_asPrettyXML($this->parseDocuments($directory, array('php', 'js', 'tpl'), $xml, '')->asXML()));

      //das Vps
       $this->_defaultLanguage = 'en';
       $this->_languages = array();
       foreach($config->production->languages as $key => $value){
           if ($key != 'en'){
               $this->_languages[] = $key;
           }
       }
       $directory = VPS_PATH;
       $inipath = $directory.'/trl.xml';

       if (!file_exists($inipath)){
           $file = fopen($inipath, 'a+');
           fputs($file, "<trl></trl>");
       } else {
           $file = fopen($inipath, 'a+');
       }

       fclose($file);

       $contents = file_get_contents($inipath);
       $xml = new SimpleXMLElement($contents);

       file_put_contents($inipath, $this->_asPrettyXML($this->parseDocuments($directory, array('php', 'js', 'tpl', 'html'), $xml, 'Vps')->asXML()));

       echo "erfolgreich ausgeführt";
       exit();
    }

    function parseDocuments($directory, $filter = array('php', 'xsl', 'xml', 'tpl', 'css'), $xml, $type)
    {

        $iterator = new RecursiveDirectoryIterator($directory);

        foreach(new RecursiveIteratorIterator($iterator, RecursiveIteratorIterator::CHILD_FIRST) as $file)
        {

            if(!$file->isDir())
            {

                $extension = end(explode('.', $file->getFileName()));
                if(in_array($extension, $filter))
                {

                    if($extension=='php' || $extension =='js') {
                        //nach trl( aufrufe suchen
                        $m = array();

                        $file = implode("", file($file));
                        preg_match_all('#trl'.$type.'\("(.+?)"\)|trl'.$type.'\(\'(.+?)\'\)#', $file, $m);
                        $this->_pregMatchTrl($m, $xml);

                        preg_match_all('#trl'.$type.'\(\'(.+?)\', (.*)\)|trl'.$type.'\(\"(.+?)\", (.*)\)#', $file, $m);
                        $this->_pregMatchTrl($m, $xml);

                        preg_match_all('#trlc'.$type.'\(\'(.+?)\', +(.*), +(.*)\)|trlc'.$type.'\(\"(.+?)\", +(.*), +(.*)\)#', $file, $m);
                        $this->_pregMatchTrlc($m, $xml);

                        preg_match_all('#trlp'.$type.'\(\'(.+?)\', +(.*), +(.*)\)|trlp'.$type.'\(\"(.+?)\", +(.*), +(.*)\)#', $file, $m);
                        $this->_pregMatchTrlp($m, $xml);

                        preg_match_all('#trlcp'.$type.'\(\'(.+?)\', +(.*), +(.*), +(.*)\)|trlcp'.$type.'\(\"(.+?)\", +(.*), +(.*), +(.*)\)#', $file, $m);
                        $this->_pregMatchTrlcp($m, $xml, $type);
                    } elseif ($extension=='tpl' || $extension=='html') {
                        $file = implode("", file($file));
                        preg_match_all('#{trl'.$type.' .*?}#', $file, $m);
                        $this->_pregMatchTrlSmarty($m, $xml);

                        preg_match_all('#{trlc'.$type.' .*?}#', $file, $m);
                        $this->_pregMatchTrlcSmarty($m, $xml);

                        preg_match_all('#{trlp'.$type.' .*?}#', $file, $m);
                        $this->_pregMatchTrlpSmarty($m, $xml);

                        preg_match_all('#{trlcp'.$type.' .*?}#', $file, $m);
                        $this->_pregMatchTrlcpSmarty($m, $xml);



                    }
                }
            }
        }
        return $xml;
    }

    protected function _pregMatchTrlSmarty ($m, $xml){
        foreach($m as $lines){
            if ($lines){
                foreach ($lines as $line){
                    preg_match_all('#text=".*"|text=\'.*\'#', $line, $m);
                    $temp = substr($m[0][0], 0, strlen($m[0][0])-1);
                    $name = $this->_formatSmartyString(str_replace("text=\"", '', $temp));
                    $name = $this->_formatSmartyString(str_replace('text=\'', '', $name));
                    if ($this->_checkNotExists($name, $xml)){
                         $element = $xml->addChild('text');
                         $lang = $element->addChild($this->_defaultLanguage, $name);
                         $lang->addAttribute('default', true);
                         foreach ($this->_languages as $lang){
                             $element->addChild($lang, '_');
                         }
                    } else {
                        $this->_checkLanguages($name, $xml, false);
                    }
                }
            }
        }

    }

    protected function _pregMatchTrlcSmarty ($m, $xml){
        foreach($m as $lines){
            if ($lines){
                foreach ($lines as $line){
                    preg_match_all('# text=\"[^".]*\"| text=\'[^\'.]*\'#', $line, $m);
                    $temp = substr($m[0][0], 0, strlen($m[0][0])-1);
                    $text = $this->_formatSmartyString(str_replace(" text=\"", '', $temp));
                    $text = $this->_formatSmartyString(str_replace(' text=\'', '', $text));

                    preg_match_all('# context=\"[^".]*\"| context=\'[^\'.]*\'#', $line, $m);
                    $temp = substr($m[0][0], 0, strlen($m[0][0])-1);
                    $context = $this->_formatSmartyString(str_replace(" context=\"", '', $temp));
                    $context = $this->_formatSmartyString(str_replace(' context=\'', '', $context));
                    if ($this->_checkNotExistsContext($text, $xml, $context)){
                        $element = $xml->addChild('text');
                        $lang = $element->addChild($this->_defaultLanguage, $text);
                        $lang->addAttribute('default', true);
                        foreach ($this->_languages as $lang){
                             $element->addChild($lang, '_');
                        }
                        $element->addAttribute('context', $context);
                    } else {
                        $this->_checkLanguages($text, $xml, false);
                    }
                }
            }
        }
    }

    protected function _pregMatchTrlpSmarty ($m, $xml){
        foreach($m as $lines){
            if ($lines){
                foreach ($lines as $line){
                    preg_match_all('# single=\"[^".]*\"| single=\'[^\'.]*\'#', $line, $m);
                    $temp = substr($m[0][0], 0, strlen($m[0][0])-1);
                    $single = $this->_formatSmartyString(str_replace(" single=\"", '', $temp));
                    $single = $this->_formatSmartyString(str_replace(' single=\'', '', $single));

                    preg_match_all('# plural=\"[^".]*\"| plural=\'[^\'.]*\'#', $line, $m);
                    $temp = substr($m[0][0], 0, strlen($m[0][0])-1);
                    $plural = $this->_formatSmartyString(str_replace(" plural=\"", '', $temp));
                    $plural = $this->_formatSmartyString(str_replace(' plural=\'', '', $plural));

                    if ($this->_checkNotExists($single, $xml)){
                        $element = $xml->addChild('text');
                        $lang = $element->addChild($this->_defaultLanguage, $single);
                        $lang->addAttribute('default', true);
                        $lang = $element->addChild($this->_defaultLanguage.'_plural', $single);
                        $lang->addAttribute('default', true);

                        foreach ($this->_languages as $lang){
                             $element->addChild($lang, '_');
                             $element->addChild($lang.'_plural', '_');
                        }
                    } else {
                        $this->_checkLanguages($single, $xml, true);
                    }
                }
            }
        }
    }

    protected function _pregMatchTrlcpSmarty ($m, $xml){
        foreach($m as $lines){
            if ($lines){
                foreach ($lines as $line){
                    preg_match_all('# single=\"[^".]*\"| single=\"[^".]*\"#', $line, $m);
                    $temp = substr($m[0][0], 0, strlen($m[0][0])-1);
                    $single = $this->_formatSmartyString(str_replace(" single=\"", '', $temp));
                    $single = $this->_formatSmartyString(str_replace(" single=\"", '', $single));

                    preg_match_all('# plural=\"[^".]*\"|  plural=\'[^\'.]*\'#', $line, $m);
                    $temp = substr($m[0][0], 0, strlen($m[0][0])-1);
                    $plural = $this->_formatSmartyString(str_replace(" plural=\"", '', $temp));
                    $plural = $this->_formatSmartyString(str_replace(' plural=\'', '', $plural));

                    preg_match_all('# context=\"[^".]*\"|  context=\'[^\'.]*\'#', $line, $m);
                    $temp = substr($m[0][0], 0, strlen($m[0][0])-1);
                    $context = $this->_formatSmartyString(str_replace(" context=\"", '', $temp));
                    $context = $this->_formatSmartyString(str_replace(' context=\'', '', $context));

                    if ($this->_checkNotExistsContext($single, $xml, $context)){
                            $element = $xml->addChild('text');
                            $lang = $element->addChild($this->_defaultLanguage, $single);
                            $lang->addAttribute('default', true);
                            $lang = $element->addChild($this->_defaultLanguage.'_plural', $plural);
                            $lang->addAttribute('default', true);

                            foreach ($this->_languages as $lang){
                                 $element->addChild($lang, '_');
                                 $element->addChild($lang.'_plural', '_');
                            }
                            $element->addAttribute('context', $context);
                    } else {
                            $this->_checkLanguages($single, $xml, true);
                    }

                }
            }
        }
    }

    protected function _formatSmartyString($string){
        return str_replace("[", "{", str_replace("]", "}", $string));
    }

    protected function _pregMatchTrl ($m, $xml){
        foreach($m[0] as $key => $trl){
            if ($m[1][$key] == ""){
                if (!($m[2][$key] == "")){
                    $this->_insertToXmlTrl($m, $key, $xml, 2);
                } else {
                    $this->_insertToXmlTrl($m, $key, $xml, 3);
                }
            } else {
                $this->_insertToXmlTrl($m, $key, $xml, 1);
            }
        }
    }

    private function _insertToXmlTrl ($m, $key, $xml, $val){
            $name= $this->_getText( $m[$val][$key]);
            if ($this->_checkNotExists($name, $xml)){
                $element = $xml->addChild('text');
                $lang = $element->addChild($this->_defaultLanguage, $name);
                $lang->addAttribute('default', true);
                foreach ($this->_languages as $lang){
                    $element->addChild($lang, '_');
                }
            } else {
                $this->_checkLanguages($name, $xml, false);
            }
    }

    protected function _pregMatchTrlc ($m, $xml){
        foreach($m[0] as $key => $trl){
            if ($m[1][$key] == ""){
                if (!($m[2][$key] == "")){
                    $this->_insertToXmlTrlc ($m, $key, $xml, 1);
                } else {
                    $this->_insertToXmlTrlc ($m, $key, $xml, 4);
                }
            } else {
                $this->_insertToXmlTrlc ($m, $key, $xml, 1);
            }
        }
    }

    private function _insertToXmlTrlc ($m, $key, $xml, $val){
            $context = $this->_getText( $m[$val][$key]);
            $text = $this->_getText( $m[++$val][$key]);
            if ($this->_checkNotExistsContext($text, $xml, $context)){
                $element = $xml->addChild('text');
                $lang = $element->addChild($this->_defaultLanguage, $text);
                $lang->addAttribute('default', true);
                foreach ($this->_languages as $lang){
                     $element->addChild($lang, '_');
                }
                $element->addAttribute('context', $context);
            } else {
                $this->_checkLanguages($text, $xml, false);
            }
    }

    protected function _pregMatchTrlp ($m, $xml){
        foreach($m[0] as $key => $trl){
            if ($m[1][$key] == ""){
                if (!($m[2][$key] == "")){
                    $this->_insertToXmlTrlp ($m, $key, $xml, 1);
                } else {
                    $this->_insertToXmlTrlp ($m, $key, $xml, 4);
                }
            } else {
                    $this->_insertToXmlTrlp ($m, $key, $xml, 1);
            }
        }
    }

    private function _insertToXmlTrlp ($m, $key, $xml, $val){
        $single = $this->_getText($m[$val][$key]);
        $plural = $this->_getText($m[++$val][$key]);
        if ($this->_checkNotExists($single, $xml)){
            $element = $xml->addChild('text');
            $lang = $element->addChild($this->_defaultLanguage, $single);
            $lang->addAttribute('default', true);
            $lang = $element->addChild($this->_defaultLanguage.'_plural', $single);
            $lang->addAttribute('default', true);

            foreach ($this->_languages as $lang){
                 $element->addChild($lang, '_');
                 $element->addChild($lang.'_plural', '_');
            }
        } else {
            $this->_checkLanguages($single, $xml, true);
        }
    }

    protected function _pregMatchTrlcp ($m, $xml, $type){
        foreach($m[0] as $key => $trl){
            if ($m[1][$key] == ""){
                    $strings = $this->_splitStringTrlcp($m[0][$key], "\"", $type);
                    $context = $strings[0];
                    $single = $strings[1];
                    $plural = $strings[2];
                    if ($this->_checkNotExistsContext($single, $xml, $context)){
                            $element = $xml->addChild('text');
                            $lang = $element->addChild($this->_defaultLanguage, $single);
                            $lang->addAttribute('default', true);
                            $lang = $element->addChild($this->_defaultLanguage.'_plural', $plural);
                            $lang->addAttribute('default', true);

                            foreach ($this->_languages as $lang){
                                 $element->addChild($lang, '_');
                                 $element->addChild($lang.'_plural', '_');
                            }
                            $element->addAttribute('context', $context);
                    } else {
                            $this->_checkLanguages($single, $xml, true);
                    }
            } else {
                    $strings = $this->_splitStringTrlcp($m[0][$key], '\'', $type);
                    $context = $strings[0];
                    $single = $strings[1];
                    $plural = $strings[2];
                    if ($this->_checkNotExistsContext($single, $xml, $context)){
                            $element = $xml->addChild('text');
                            $lang = $element->addChild($this->_defaultLanguage, $single);
                            $lang->addAttribute('default', true);
                            $lang = $element->addChild($this->_defaultLanguage.'_plural', $plural);
                            $lang->addAttribute('default', true);

                            foreach ($this->_languages as $lang){
                                 $element->addChild($lang, '_');
                                 $element->addChild($lang.'_plural', '_');
                            }
                            $element->addAttribute('context', $context);
                    } else {
                            $this->_checkLanguages($single, $xml, true);
                    }
            }
        }
    }

    protected function _splitStringTrlcp($string, $explode, $mode){
       $start = 0;
       $strings = explode($explode.',', $string);
       $strings[0] = str_replace('trlcp'.$mode.'('.$explode, '', $this->_getText($strings[0]));
       $strings[++$start] = str_replace($explode, '', substr($this->_getText($strings[$start]), 1, strlen($strings[$start])));
       $strings[++$start] = str_replace($explode, '', substr($this->_getText($strings[$start]), 1, strlen($strings[$start])));
       $strings[++$start] = str_replace($explode, '', substr(str_replace('))', '', self::_getText($strings[$start])), 1, strlen($strings[$start])));
       return $strings;
   }

    protected function _getText($name){
            if(strpos($name, '{')){
                $values = explode(',', $name);
                return str_replace("'", '', $values[0]);
            } else {
                return $name;
            }
   }

    protected function _checkNotExists($needle, $xml){
        foreach ($xml->text as $element) {
                $default = $this->_defaultLanguage;
                if ($element->$default == $needle){
                    return false;
                }
        }
        return true;
    }

    protected function _checkLanguages($needle, $xml, $plural){
        foreach ($xml->text as $element) {
                $default = $this->_defaultLanguage;
                if ($element->$default == $needle){
                    foreach ($this->_languages as $lang){
                        if (!$element->$lang){
                            $element->addChild($lang, '_');
                            if ($plural){
                                $element->addChild($lang.'_plural', '_');
                            }
                        }
                    }
                }
        }
        return true;
    }

    protected function _checkNotExistsContext($needle, $xml, $context){
        $temp_lang = $this->_defaultLanguage;
        foreach ($xml->text as $element) {
                if ($element->$temp_lang == $needle && $element['context'] == $context){
                    return false;
                }
        }
        return true;
    }

    protected function _asPrettyXML($string)
    {
        $indent = 3;
        /**
         * put each element on it's own line
         */
        $string =preg_replace("/>\s*</",">\n<",$string);

        /**
         * each element to own array
         */
        $xmlArray = explode("\n",$string);

        /**
         * holds indentation
         */
        $currIndent = 0;

        /**
         * set xml element first by shifting of initial element
         */
        $string = array_shift($xmlArray) . "\n";

        foreach($xmlArray as $element) {
            /** find open only tags... add name to stack, and print to string
             * increment currIndent
             */

            if (preg_match('/^<([\w])+[^>\/]*>$/U',$element)) {
                $string .=  str_repeat(' ', $currIndent) . $element . "\n";
                $currIndent += $indent;
            }

            /**
             * find standalone closures, decrement currindent, print to string
             */
            elseif ( preg_match('/^<\/.+>$/',$element)) {
                $currIndent -= $indent;
                $string .=  str_repeat(' ', $currIndent) . $element . "\n";
            }
            /**
             * find open/closed tags on the same line print to string
             */
            else {
                $string .=  str_repeat(' ', $currIndent) . $element . "\n";
            }
        }

        return $string;

    }

}