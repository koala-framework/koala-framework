<?php

class Vps_Controller_Action_Trl_IndexController extends Vps_Controller_Action
{
    public function indexAction()
    {
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
                    preg_match_all('#text=".*"#', $line, $m);
                    $temp = substr($m[0][0], 0, strlen($m[0][0])-1);
                    $name = $this->_formatSmartyString(str_replace("text=\"", '', $temp));
                    if ($this->_checkNotExists($name, $xml)){
                        $element = $xml->addChild('text');
                         $element->addChild('en', $name);
                         $element->addChild('de', '_');
                    }
                }
            }
        }
    }

    protected function _pregMatchTrlcSmarty ($m, $xml){
        foreach($m as $lines){
            if ($lines){
                foreach ($lines as $line){
                    preg_match_all('# text=\"[^".]*\"#', $line, $m);
                    $temp = substr($m[0][0], 0, strlen($m[0][0])-1);
                    $text = $this->_formatSmartyString(str_replace(" text=\"", '', $temp));

                    preg_match_all('# context=\"[^".]*\"#', $line, $m);
                    $temp = substr($m[0][0], 0, strlen($m[0][0])-1);
                    $context = $this->_formatSmartyString(str_replace(" context=\"", '', $temp));
                    if ($this->_checkNotExistsContext($text, $xml, $context)){
                        $element = $xml->addChild('text');
                        $element->addChild('en', $text);
                        $element->addChild('de', '_');
                        $element->addAttribute('context', $context);
                    }
                }
            }
        }
    }

    protected function _pregMatchTrlpSmarty ($m, $xml){
        foreach($m as $lines){
            if ($lines){
                foreach ($lines as $line){
                    preg_match_all('# single=\"[^".]*\"#', $line, $m);
                    $temp = substr($m[0][0], 0, strlen($m[0][0])-1);
                    $single = $this->_formatSmartyString(str_replace(" single=\"", '', $temp));

                    preg_match_all('# plural=\"[^".]*\"#', $line, $m);
                    $temp = substr($m[0][0], 0, strlen($m[0][0])-1);
                    $plural = $this->_formatSmartyString(str_replace(" plural=\"", '', $temp));

                    if ($this->_checkNotExists($single, $xml)){
                        $element = $xml->addChild('text');
                        $element->addChild('en', $single);
                        $element->addChild('de', '_');
                        $element->addChild('en_plural', $plural);
                        $element->addChild('de_plural', '_');
                    }

                }
            }
        }
    }

    protected function _pregMatchTrlcpSmarty ($m, $xml){
        foreach($m as $lines){
            if ($lines){
                foreach ($lines as $line){
                    preg_match_all('# single=\"[^".]*\"#', $line, $m);
                    $temp = substr($m[0][0], 0, strlen($m[0][0])-1);
                    $single = $this->_formatSmartyString(str_replace(" single=\"", '', $temp));

                    preg_match_all('# plural=\"[^".]*\"#', $line, $m);
                    $temp = substr($m[0][0], 0, strlen($m[0][0])-1);
                    $plural = $this->_formatSmartyString(str_replace(" plural=\"", '', $temp));

                    preg_match_all('# context=\"[^".]*\"#', $line, $m);
                    $temp = substr($m[0][0], 0, strlen($m[0][0])-1);
                    $context = $this->_formatSmartyString(str_replace(" context=\"", '', $temp));

                if ($this->_checkNotExistsContext($single, $xml, $context)){
                        $element = $xml->addChild('text');
                        $element->addChild('en', $single);
                        $element->addChild('de', '_');
                        $element->addChild('en_plural', $plural);
                        $element->addChild('de_plural', '_');
                        $element->addAttribute('context', $context);
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
                    $name= $this->_getText( $m[2][$key]);
                    if ($this->_checkNotExists($name, $xml)){
                        $element = $xml->addChild('text');
                         $element->addChild('en', $name);
                         $element->addChild('de', '_');
                    }
                }
            } else {
                $name= $this->_getText( $m[1][$key]);
                if ($this->_checkNotExists($name, $xml)){
                    $element = $xml->addChild('text');
                    $element->addChild('en', $name);
                    $element->addChild('de', '_');
                }
            }
        }
    }

    protected function _pregMatchTrlc ($m, $xml){
        foreach($m[0] as $key => $trl){
            if ($m[1][$key] == ""){
                if (!($m[2][$key] == "")){
                    $context = $this->_getText( $m[1][$key]);
                    $name = $this->_getText( $m[2][$key]);
                    if ($this->_checkNotExistsContext($name, $xml, $context)){
                        $element = $xml->addChild('text');
                        $element->addChild('en', $name);
                        $element->addChild('de', '_');
                        $element->addAttribute('context', $context);
                    }
                }
            } else {
                    $context = $this->_getText($m[1][$key]);
                    $name = $this->_getText($m[2][$key]);
                    if ($this->_checkNotExistsContext($name, $xml, $context)){
                        $element = $xml->addChild('text');
                        $element->addChild('en', $name);
                        $element->addChild('de', '_');
                        $element->addAttribute('context', $context);
                    }
            }
        }
    }

    protected function _pregMatchTrlp ($m, $xml){
        foreach($m[0] as $key => $trl){
            if ($m[1][$key] == ""){
                if (!($m[2][$key] == "")){
                    $name = $this->_getText($m[1][$key]);
                    $plural = $this->_getText($m[2][$key]);
                    if ($this->_checkNotExists($name, $xml)){
                        $element = $xml->addChild('text');
                        $element->addChild('en', $name);
                        $element->addChild('de', '_');
                        $element->addChild('en_plural', $plural);
                        $element->addChild('de_plural', '_');
                    }
                }
            } else {
                    $name = $this->_getText($m[1][$key]);
                    $plural = $this->_getText($m[2][$key]);
                    if ($this->_checkNotExists($name, $xml)){
                        $element = $xml->addChild('text');
                        $element->addChild('en', $name);
                        $element->addChild('de', '_');
                        $element->addChild('en_plural', $plural);
                        $element->addChild('de_plural', '_');
                    }
            }
        }
    }

    protected function _pregMatchTrlcp ($m, $xml, $type){
        foreach($m[0] as $key => $trl){
            if ($m[1][$key] == ""){
                if (!($m[2][$key] == "")){

                    $strings = $this->_splitStringTrlcp($m[0][$key], "\"", $type);
                    $context = $strings[0];
                    $name = $strings[1];
                    $plural = $strings[2];
                    if ($this->_checkNotExistsContext($name, $xml, $context)){

                        $element = $xml->addChild('text');
                        $element->addChild('en', $name);
                        $element->addChild('de', '_');
                        $element->addChild('en_plural', $plural);
                        $element->addChild('de_plural', '_');
                        $element->addAttribute('context', $context);
                    }
                }
            } else {
                    $strings = $this->_splitStringTrlcp($m[0][$key], '\'', $type);
                    $context = $strings[0];
                    $name = $strings[1];
                    $plural = $strings[2];
                    if ($this->_checkNotExistsContext($name, $xml, $context)){
                        $element = $xml->addChild('text');
                        $element->addChild('en', $name);
                        $element->addChild('de', '_');
                        $element->addChild('en_plural', $plural);
                        $element->addChild('de_plural', '_');
                        $element->addAttribute('context', $context);
                    }
            }
        }
    }

    protected function _splitStringTrlcp($string, $explode, $mode){
       $start = 0;
       $strings = explode($explode.',', $string);
       $strings[0] = str_replace('trlcp'.$mode.'(\'', '', $this->_getText($strings[0]));
       $strings[++$start] = substr($this->_getText($strings[$start]), 1, strlen($strings[$start]));
       $strings[++$start] = substr($this->_getText($strings[$start]), 1, strlen($strings[$start]));
       $strings[++$start] = substr(str_replace('))', '', self::_getText($strings[$start])), 1, strlen($strings[$start]));
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

                if ($element->en == $needle){
                    return false;
                }
        }
        return true;
    }

    protected function _checkNotExistsContext($needle, $xml, $context){
        foreach ($xml->text as $element) {
                if ($element->en == $needle && $element['context'] == $context){
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