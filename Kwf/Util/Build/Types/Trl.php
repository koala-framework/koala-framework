<?php
class Kwf_Util_Build_Types_Trl extends Kwf_Util_Build_Types_Abstract
{
    protected function _build()
    {
        if (!file_exists('build/trl')) {
            mkdir('build/trl');
        }

        foreach (glob('build/trl/*') as $f) {
            unlink($f);
        }
        $config = Zend_Registry::get('config');
        $langs = array();
        if ($config->webCodeLanguage) $langs[] = $config->webCodeLanguage;

        if ($config->languages) {
            foreach ($config->languages as $lang=>$name) {
                $langs[] = $lang;
            }
        }
        try {
            if (Kwf_Component_Data_Root::getComponentClass()) {
                foreach(Kwc_Abstract::getComponentClasses() as $c) {
                    if (Kwc_Abstract::getFlag($c, 'hasAvailableLanguages')) {
                        foreach (call_user_func(array($c, 'getAvailableLanguages'), $c) as $i) {
                            if (!in_array($i, $langs)) $langs[] = $i;
                        }
                    }
                }
            }
        } catch(Kwf_Exception $e) {
            $exceptionLocation = null;
            foreach ($e->getTrace() as $trace) {
                if (strpos($trace['file'], 'Kwf/Trl.php') === false
                    && (
                        $trace['function'] == 'trlKwf' || $trace['function'] == 'trl'
                        || $trace['function'] == 'trlcKwf' || $trace['function'] == 'trlc'
                        || $trace['function'] == 'trlpKwf' || $trace['function'] == 'trlp'
                        || $trace['function'] == 'trlcpKwf' || $trace['function'] == 'trlcp'
                    )
                ) {
                    $exceptionLocation = $trace;
                    break;
                }
            }
            if ($exceptionLocation) {
                $file = $exceptionLocation['file'];
                $line = $exceptionLocation['line'];
                throw new Kwf_Exception("In getSettings-method only static version of trl is allowed $file:$line");
            } else {
                throw $e;
            }
        }

        foreach ($langs as $l) {
            if ($l != $config->webCodeLanguage) {
                $c = $this->_loadTrlArray(Kwf_Trl::SOURCE_WEB, $l, true);
                file_put_contents(Kwf_Trl::generateBuildFileName(Kwf_Trl::SOURCE_WEB, $l, true), serialize($c));

                $c = $this->_loadTrlArray(Kwf_Trl::SOURCE_WEB, $l, false);
                file_put_contents(Kwf_Trl::generateBuildFileName(Kwf_Trl::SOURCE_WEB, $l, false), serialize($c));
            }

            if ($l != 'en') {
                $c = $this->_loadTrlArray(Kwf_Trl::SOURCE_KWF, $l, true);
                file_put_contents(Kwf_Trl::generateBuildFileName(Kwf_Trl::SOURCE_KWF, $l, true), serialize($c));

                $c = $this->_loadTrlArray(Kwf_Trl::SOURCE_KWF, $l, false);
                file_put_contents(Kwf_Trl::generateBuildFileName(Kwf_Trl::SOURCE_KWF, $l, false), serialize($c));
            }
        }
    }

    private function _loadTrlArray($source, $targetLanguage, $plural)
    {
        $poParsers = $this->_getPoParsers($source, $targetLanguage);
        $c = array();
        foreach ($poParsers as $poParser) {
            foreach ($poParser->entries() as $entry) {
                $ctx = isset($entry['msgctxt']) ? $entry['msgctxt'][0] : '';
                $translation = $entry['msgstr'][0];
                if (isset($entry['msgid_plural'])) {
                    $translation = $entry['msgstr'][1];
                }
                if ($translation == '') continue;
                $msgKey = (isset($entry['msgid_plural']) ? $entry['msgid_plural'][0] : $entry['msgid'][0]).'-'.$ctx;
                if (isset($c[$msgKey])) {
                    echo "\nDuplicate entry in trl-files: $msgKey => $translation\n";
                }
                $c[$msgKey] = $translation;
            }
        }
        return $c;
    }

    private function _getPoParsers($source, $targetLanguage)
    {
        $files = array();
        if ($source == Kwf_Trl::SOURCE_WEB) {
            if (file_exists('trl/'.$targetLanguage.'.po')) {
                $files = array('trl/'.$targetLanguage.'.po');
            }
        } else if ($source == Kwf_Trl::SOURCE_KWF) {
            // check all composer packages
            $this->_checkPackagesForTrlFilesAndTryDownloadFromKoalaWebsiteIfNotExisting($targetLanguage);
            $files = glob(VENDOR_PATH.'/*/*/trl/'.$targetLanguage.'.po');
        }
        require_once VENDOR_PATH.'/autoload.php';
        $poParsers = array();
        foreach ($files as $file) {
            $poParser = new \Sepia\PoParser;
            $poParser->parseFile($file);
            array_push($poParsers, $poParser);
        }
        return $poParsers;
    }

    private function _checkPackagesForTrlFilesAndTryDownloadFromKoalaWebsiteIfNotExisting($targetLanguage)
    {
        $composerFiles = glob(VENDOR_PATH.'/*/*/composer.json');

        foreach ($composerFiles as $composerFile) {
            $trlDir = dirname($composerFile).'/trl/';

            if (file_exists($trlDir.$targetLanguage.'.po')) continue;

            $composerConfig = json_decode(file_get_contents($composerFile));
            if (!isset($composerConfig->extra)) continue;
            if (!isset($composerConfig->extra->{'kwf-lingohub'})) continue;
            $trlConfig = $composerConfig->extra->{'kwf-lingohub'};
            $trlDownloadUrl = Kwf_Registry::get('config')->trl->downloadUrl;
            $file = @file_get_contents("$trlDownloadUrl/{$trlConfig->account}/{$trlConfig->project}/$targetLanguage");
            if (!$file) continue;
            if (!file_exists($trlDir)) mkdir($trlDir);
            file_put_contents($trlDir.$targetLanguage.'.po', $file);
        }
    }

    public function getTypeName()
    {
        return 'trl';
    }
}
