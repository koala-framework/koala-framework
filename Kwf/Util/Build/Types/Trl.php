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
        } catch (Kwf_Trl_BuildFileMissingException $e) {
            $originatingException = $e->getSettingsNonStaticTrlException();
            if ($originatingException) {
                throw $originatingException;
            }
            throw $e;
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
                $ctx = isset($entry['msgctxt']) ? implode($entry['msgctxt']) : '';
                $translation = isset($entry['msgstr']) ? implode($entry['msgstr']) : '';
                if (isset($entry['msgid_plural']) && isset($entry['msgstr[0]'])) {
                    $translation = implode($entry['msgstr[0]']);
                }
                if ($translation == '') continue;
                $msgId = implode($entry['msgid']);
                if ($msgId == '') continue;
                $msgKey = $msgId.'-'.$ctx;
                if (isset($c[$msgKey])) {
                    echo "\nDuplicate entry in trl-files: $msgKey => $translation\n";
                }
                $c[$msgKey] = $translation;

                if (isset($entry['msgid_plural'])) {
                    $msgIdPlural =  implode($entry['msgid_plural']);
                    $pluralTranslation = '';
                    if (isset($entry['msgstr[1]'])) {
                        $pluralTranslation = implode($entry['msgstr[1]']);
                    }
                    $c[$msgIdPlural.'-'.$ctx] = $pluralTranslation;
                }
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
            $files = $this->_getAllLibraryTrlFiles($targetLanguage);
        }
        require_once VENDOR_PATH.'/autoload.php';
        $poParsers = array();
        foreach ($files as $file) {
            $poParser = \Sepia\PoParser::parseFile($file);
            array_push($poParsers, $poParser);
        }
        return $poParsers;
    }

    private function _getAllLibraryTrlFiles($targetLanguage)
    {
        $composerFiles = array_merge(
            //Explicitly add koala-framework composer.json because of tests
            array(KWF_PATH.'/composer.json'),
            glob(VENDOR_PATH.'/*/*/composer.json')
        );
        $existingFiles = array();
        $composerFiles = array_unique($composerFiles);
        foreach ($composerFiles as $composerFile) {
            $trlDir = dirname($composerFile).'/trl/';

            $trlFilePath = $trlDir.$targetLanguage.'.po';
            if (file_exists($trlFilePath)) {
                $existingFiles[] = $trlFilePath;
                continue;
            }

            $composerConfig = json_decode(file_get_contents($composerFile));
            if (!isset($composerConfig->extra)) continue;
            if (!isset($composerConfig->extra->{'kwf-lingohub'})) continue;
            $trlConfig = $composerConfig->extra->{'kwf-lingohub'};
            $client = new Zend_Http_Client(Kwf_Registry::get('config')->trl->downloadUrl."/{$trlConfig->account}/{$trlConfig->project}/$targetLanguage");
            $response = $client->request();
            if ($response->isError()) {
                if ($response->getStatus() == 404) {
                    $file = "\n";
                } else {
                    throw new Kwf_Exception('Downloading resource from trl.koala-framework failed.');
                }
            } else {
                $file = $response->getBody();
            }
            if (!$file) continue;
            if (!file_exists($trlDir)) mkdir($trlDir);
            file_put_contents($trlFilePath, $file);
            $existingFiles[] = $trlFilePath;
        }
        return $existingFiles;
    }

    public function getTypeName()
    {
        return 'trl';
    }
}
