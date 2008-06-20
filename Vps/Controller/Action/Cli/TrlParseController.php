<?php
class Vps_Controller_Action_Cli_TrlParseController extends Vps_Controller_Action_Cli_Abstract
{

    public static function getHelp()
    {
        return "parse for translation calls";
    }
    public static function getHelpOptions()
    {
        return array(
            array(
                'param'=> 'type',
                'value'=> array('all', 'web', 'vps'),
                'valueOptional' => true,
                'help' => 'what to parse'
            )
        );
    }

    private $_defaultLanguage;
    private $_languages = array();
    public function indexAction()
    {
        //festsetzen der sprachen
        $this->_languages = Zend_Registry::get('trl')->getLanguages();
        $this->_codeLanguage = Zend_Registry::get('trl')->getWebCodeLanguage();

        $type = $this->_getParam('type');

        //das Project
        $directoryWeb = ".";
        $pathWeb = 'application/trl.xml';
        if (file_exists($pathWeb)){
            $contents = file_get_contents($pathWeb);
        } else {
            $contents = "<trl></trl>";
        }
        $xmlWeb = new SimpleXMLElement($contents);

        //das Vps
        $this->_codeLanguage = 'en';
        $directoryVps = VPS_PATH;
        $pathVps = $directoryVps.'/trl.xml';

        if (file_exists($pathVps)){
            $contents = file_get_contents($pathVps);
        } else {
            $contents = "<trl></trl>";
        }
        $xmlVps = new SimpleXMLElement($contents);

        $xmlFiles = array('web' => $xmlWeb, 'vps' => $xmlVps);
        $directories = array('web' => $directoryWeb, 'vps' => $directoryVps);
        $xmlFiles = $this->_parseDocuments($directories, $xmlFiles);

        if ($type == "all" || $type == "web") {
            file_put_contents($pathWeb, $this->_asPrettyXML($xmlFiles['web']->asXML()));
        }
        if ($type == "all" || $type == "vps") {
            file_put_contents($pathVps, $this->_asPrettyXML($xmlFiles['vps']->asXML()));
        }


        echo "successfull\n";
        exit();
    }

    private function _parseDocuments($directories, $xml)
    {
        foreach ($directories as $directory){
            $iterator = new RecursiveDirectoryIterator($directory);
            foreach(new RecursiveIteratorIterator($iterator) as $file)
            {
                if(!$file->isDir()) {
                    $extension = end(explode('.', $file->getFileName()));
                    if($extension=='php' || $extension =='js') {
                        //nach trl aufrufen suchen
                        $ret = array();
                        $ret = Zend_Registry::get('trl')->parse(file_get_contents($file));
                        if ($ret){
                            $this->_insertToXml($ret, $xml);
                        }
                    }
                }
            }
        }
        return $xml;
    }

    private function _insertToXml ($entries, $xml)
    {
        foreach ($entries as $key => $entry){
            $xmlname = $entry['source'];
            $allowxml = $this->_getParam('type');

            if ($allowxml == $xmlname || ($allowxml == 'all' && $entry && $xml)){
                if ($this->_checkNotExists($entry, $xml[$xmlname])) {
                    $element = $xml[$xmlname]->addChild('text');
                    $lang = $element->addChild($this->_getDefaultLanguage($entry), $entry['text']);
                    $lang->addAttribute('default', true);
                    if (isset($entry['plural'])) {
                        $lang = $element->addChild($this->_getDefaultLanguage($entry).'_plural', $entry['plural']);
                    }
                    foreach ($this->_languages as $lang) {
                        if ($lang != $this->_getDefaultLanguage($entry)) {
                            $element->addChild($lang, '_');
                            if (isset($entry['plural'])) {
                                $element->addChild($lang.'_plural', '_');
                            }
                        }
                    }
                    if (isset($entry['context'])) {
                        $element->addAttribute('context', $entry['context']);
                    }

                } else {
                    $this->_checkLanguages($entry['text'], $xml[$xmlname], false);
                }
            }
        }
    }

    protected function _checkNotExists($entry, $xml)
    {
        $defaultLanguage = $this->_getDefaultLanguage($entry);
        foreach ($xml->text as $element) {
            if ($element->$defaultLanguage == $entry['text'] &&
            (!isset($entry['context']) ||
            $element['context'] == $entry['context'])) {
                return false;
            }
        }
        return true;
    }

    protected function _checkLanguages($needle, $xml, $plural)
    {
        foreach ($xml->text as $element) {
            $default = $this->_codeLanguage;
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

    private function _getDefaultLanguage($entry)
    {
        if ($entry['source'] == Vps_Trl::SOURCE_VPS) {
            return 'en';
        } else {
            return Zend_Registry::get('trl')->getWebCodeLanguage();
        }
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
                $string .=  str_repeat(' ', 0) . $element . "\n";
                $currIndent += $indent;
            }

            /**
             * find standalone closures, decrement currindent, print to string
             */
            elseif ( preg_match('/^<\/.+>$/',$element)) {
                $currIndent -= $indent;
                $string .=  str_repeat(' ', 0) . $element . "\n";
            }
            /**
             * find open/closed tags on the same line print to string
             */
            else {
                $string .=  str_repeat(' ', 0) . $element . "\n";
            }
        }

        return $string;

    }

}

