<?php
class Kwf_Controller_Action_Cli_Web_HlpParseController extends Kwf_Controller_Action_Cli_Abstract
{
    public static function getHelp()
    {
        return "parse for hlp() calls and add them to hlp.xml";
    }

    public function indexAction()
    {
        $maskedTexts = $this->_findMaskedTexts('./');
        $trl = Kwf_Trl::getInstance();
        $this->_createXmlFromTexts($maskedTexts, 'hlp.xml', $trl->getLanguages());

        $kwfLanguages = array_unique(array_merge(array('en'), $trl->getLanguages()));
        $maskedTexts = $this->_findMaskedTexts(KWF_PATH, true);
        $maskedTexts += $this->_findMaskedTexts('./application', true);
        $this->_createXmlFromTexts($maskedTexts, KWF_PATH . '/hlp.xml', $kwfLanguages);
        echo "Parsing durchgelaufen\n";
        $this->_helper->viewRenderer->setNoRender(true);
    }

    protected function _findMaskedTexts($directory, $isKwf = false)
    {
        $results = array();
        $iterator = new RecursiveDirectoryIterator($directory);
        foreach (new RecursiveIteratorIterator($iterator, RecursiveIteratorIterator::CHILD_FIRST) as $file) {
            $extension = end(explode('.', $file->getFileName()));
            if ($extension == 'php' || $extension == 'js' || $extension == 'tpl') {
                $m = array();
                $kwf = $isKwf ? 'Kwf' : '';
                preg_match_all("#hlp$kwf\('(.*)'\)#", implode("", file($file)), $m);
                foreach ($m[1] as $key) {
                    // Dateiname mit Pfad vernünftig formatieren
                    $results[$key][] = str_replace($directory . '/', '', $file->getPathName());
                }
            }
        }
        return $results;
    }

    protected function _createXmlFromTexts($texts, $targetFile, $languages)
    {
        if (empty($texts)) { return; }
        if (!file_exists($targetFile)){
            $file = fopen($targetFile, 'a+');
            fputs($file, "<hlp></hlp>");
            fclose($file);
        }
        // Suchen, obs Eintrag schon gibt
        $xml = new SimpleXMLElement(file_get_contents($targetFile));
        foreach ($texts as $key => $controllers) {
            $xpath = "/hlp/text[@key='$key']";
            $search = $xml->xpath($xpath);
            if (!$search) { // Wenn nein, hinzufügen
                $element = $xml->addChild('text');
                $element->addAttribute('key', $key);
                $element->addAttribute('file', implode(', ', $controllers));
            } else {
                $element = $search[0];
            }
            // Für aktuellen Eintrag die Sprachen, die es noch nicht gibt, hinzufügen
            foreach ($languages as $language) {
                if (!$xml->xpath("$xpath/$language")) {
                    $element->addChild($language, '');
                }
            }
        }
        $string = $xml->asXML();

        // XML formatieren, echt schiach
        $string = str_replace("\t", "", $string);
        $string = str_replace("\n", "", $string);
        $string = str_replace(">", ">\n", $string);
        foreach ($languages as $language) {
            $string = str_replace("<$language>\n", "<$language>", $string);
            $string = str_replace("$language/>", "$language></$language>", $string);
        }
        $string = str_replace(">_</", "></", $string);
        $string = str_replace("></text>", ">\n</text>", $string);
        $string = str_replace("></hlp>", ">\n</hlp>", $string);
        $result = '';
        foreach (explode("\n", $string) as $line) {
            if (substr($line, 0, 6) == '<text ' || substr($line, 0, 6) == '</text') {
                $result .= "    ";
            } else if (
                substr($line, 0, 4) != '<hlp' &&
                substr($line, 0, 5) != '<?xml' &&
                substr($line, 0, 2) != '</'
            ){
                $result .= "        ";
            }
            $result .= $line . "\n";
        }
        // XML in Datei schreiben
        file_put_contents($targetFile, $result);

        echo("Datei $targetFile erstellt.\n");
    }

}
