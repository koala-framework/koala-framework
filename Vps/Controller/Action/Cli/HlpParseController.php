<?php
class Vps_Controller_Action_Cli_HlpParseController extends Vps_Controller_Action_Cli_Abstract
{
    public static function getHelp()
    {
        return "parse for hlp() calls and add them to hlp.xml";
    }

    public function indexAction()
    {
        $maskedTexts = $this->_findMaskedTexts('./');
        $this->_createXmlFromTexts($maskedTexts, 'application/hlp.xml', Vps_Trl::getLanguages());

        $vpsLanguages = array_unique(array_merge(array('en'), Vps_Trl::getLanguages()));
        $maskedTexts = $this->_findMaskedTexts(VPS_PATH, true);
        $maskedTexts += $this->_findMaskedTexts('./application', true);
        $this->_createXmlFromTexts($maskedTexts, VPS_PATH . '/hlp.xml', $vpsLanguages);
        echo "Parsing durchgelaufen\n";
        $this->_helper->viewRenderer->setNoRender(true);
    }

    protected function _findMaskedTexts($directory, $isVps = false)
    {
        $results = array();
        $iterator = new RecursiveDirectoryIterator($directory);
        foreach (new RecursiveIteratorIterator($iterator, RecursiveIteratorIterator::CHILD_FIRST) as $file) {
            $extension = end(explode('.', $file->getFileName()));
            if ($extension == 'php' || $extension == 'js' || $extension == 'tpl') {
                $m = array();
                $vps = $isVps ? 'Vps' : '';
                preg_match_all("#hlp$vps\('(.*)'\)#", implode("", file($file)), $m);
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
