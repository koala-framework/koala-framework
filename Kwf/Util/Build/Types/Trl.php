<?php
class Kwf_Util_Build_Types_Trl extends Kwf_Util_Build_Types_Abstract
{
    protected function _build($options)
    {
        if (!file_exists('build/trl')) {
            mkdir('build/trl');
        }

        foreach (glob('build/trl/*') as $f) {
            unlink($f);
        }
        $config = Zend_Registry::get('config');

        // First ask components for their languages
        $langs = array();
        try {
            if (Kwf_Component_Data_Root::getComponentClass()) {
                foreach(Kwc_Abstract::getComponentClasses() as $c) {
                    if (Kwc_Abstract::getFlag($c, 'hasAvailableLanguages')) {
                        $langs = array_merge($langs, call_user_func(array($c, 'getAvailableLanguages'), $c));
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

        // If components don't have languages use static config
        if (!$langs && is_array($config->languages)) {
            foreach ($config->languages as $lang=>$name) {
                $langs[] = $lang;
            }
        }

        // If there's no static config use webCodeLanguage
        if (!$langs && $config->webCodeLanguage) {
            $langs[] = $config->webCodeLanguage;
        }

        $langs = array_unique($langs);

        //used by webpack
        file_put_contents('build/trl/languages.json', json_encode($langs));

        foreach ($langs as $l) {
            if ($l != $config->webCodeLanguage) {
                $c = $this->_loadTrlArray(Kwf_Trl::SOURCE_WEB, $l);
                file_put_contents(Kwf_Trl::generateBuildFileName(Kwf_Trl::SOURCE_WEB, $l), serialize($c));
            }

            if ($l != 'en') {
                $c = $this->_loadTrlArray(Kwf_Trl::SOURCE_KWF, $l);
                file_put_contents(Kwf_Trl::generateBuildFileName(Kwf_Trl::SOURCE_KWF, $l), serialize($c));
            }
        }
    }

    private function _convertTrlEntry($entry)
    {
        $ctx = isset($entry['msgctxt']) ? implode($entry['msgctxt']) : '';
        $translation = isset($entry['msgstr']) ? implode($entry['msgstr']) : '';
        if (isset($entry['msgid_plural']) && isset($entry['msgstr[0]'])) {
            $translation = implode($entry['msgstr[0]']);
        }
        if ($translation == '') return false;
        $msgId = implode($entry['msgid']);
        if ($msgId == '') return false;

        $ret = array(
            'key' => $msgId.'-'.$ctx,
            'translation' => $translation
        );

        if (isset($entry['msgid_plural'])) {
            $msgIdPlural =  implode($entry['msgid_plural']);
            $pluralTranslation = '';
            if (isset($entry['msgstr[1]'])) {
                $pluralTranslation = implode($entry['msgstr[1]']);
            }
            $ret['pluralKey'] = $msgIdPlural.'-'.$ctx;
            $ret['pluralTranslation'] = $pluralTranslation;
        }

        return $ret;
    }

    private function _loadKwfTrlArray($targetLanguage)
    {
        $trlEntries = array();

        $kwfTrlFile = KWF_PATH . '/trl/' . $targetLanguage . '.po';
        if (!file_exists($kwfTrlFile)) {
            $trlConfig = json_decode(file_get_contents(KWF_PATH . '/composer.json'));
            if (!file_exists(KWF_PATH . '/trl')) mkdir(KWF_PATH . '/trl');
            $this->_downloadTrlFile($trlConfig->extra->{'kwf-lingohub'}, $kwfTrlFile, $targetLanguage);
        }
        $kwfPoParser = \Sepia\PoParser::parseFile($kwfTrlFile);
        foreach ($kwfPoParser->entries() as $entry) {
            $entry = $this->_convertTrlEntry($entry);
            if (!$entry) continue;
            $trlEntries[$entry['key']] = array(
                'source' => $kwfTrlFile,
                'translation' => $entry['translation']
            );
            if (isset($entry['pluralKey'])) {
                $trlEntries[$entry['pluralKey']] = array(
                    'source' => $kwfTrlFile,
                    'translation' => $entry['pluralTranslation']
                );
            }
        }

        $poParsers = $this->_getAllPoParsersForPackagesExceptKwfPackage($targetLanguage);
        foreach ($poParsers as $file => $poParser) {
            foreach ($poParser->entries() as $entry) {
                $entry = $this->_convertTrlEntry($entry);

                if (isset($trlEntries[$entry['key']])) {
                    if ($trlEntries[$entry['key']]['source'] == $kwfTrlFile) {
                        echo "\n - Translation defined in kwf is also translated in package:";
                        echo "\n - $file: {$entry['key']}\n";

                    } else if ($trlEntries[$entry['key']]['translation'] != $entry['translation']) {
                        $secondFile = $trlEntries[$entry['key']]['source'];
                        echo "\n - Translation is defined in a second package but translated differently:";
                        echo "\n - $secondFile & $file | {$entry['key']}";
                        echo "\n - Please consider adding context to one or both.\n";
                    }
                } else {
                    $trlEntries[$entry['key']] = array(
                        'source' => $file,
                        'translation' => $entry['translation']
                    );
                }

                if (isset($entry['pluralKey'])) {
                    if (isset($trlEntries[$entry['pluralKey']])) {
                        if ($trlEntries[$entry['pluralKey']]['source'] == $kwfTrlFile) {
                            echo "\nTranslation defined in kwf is also translated in package: $file:{$entry['pluralKey']}\n";

                        } else if ($trlEntries[$entry['pluralKey']]['translation'] != $entry['pluralTranslation']) {
                            $secondFile = $trlEntries[$entry['pluralKey']]['source'];
                            echo "\nTranslation is defined in a second package but translated differently:";
                            echo "\n{$entry['pluralKey']} | $secondFile & $file";
                            echo "\nPlease consider adding context to one or both.\n";
                        }
                    } else {
                        $trlEntries[$entry['pluralKey']] = array(
                            'source' => $file,
                            'translation' => $entry['pluralTranslation']
                        );
                    }
                }
            }
        }
        $trl = array();
        foreach ($trlEntries as $id => $value) {
            $trl[$id] = $value['translation'];
        }
        return $trl;
    }

    private function _loadWebTrlArray($targetLanguage)
    {
        $trlEntries = array();
        $filename = 'trl/'.$targetLanguage.'.po';
        if (file_exists($filename)) {
            require_once VENDOR_PATH.'/autoload.php';
            $poParser = \Sepia\PoParser::parseFile($filename);

            foreach ($poParser->entries() as $entry) {
                $entry = $this->_convertTrlEntry($entry);

                if (isset($trlEntries[$entry['key']])) {
                    if ($trlEntries[$entry['key']]['translation'] != $entry['translation']) {
                        $secondFile = $trlEntries[$entry['key']]['source'];
                        echo "\n - Translation is defined in a second package but translated differently:";
                        echo "\n - $secondFile & $filename | {$entry['key']}";
                        echo "\n - Please consider adding context to one or both.\n";
                    }
                } else {
                    $trlEntries[$entry['key']] = array(
                        'source' => $filename,
                        'translation' => $entry['translation']
                    );
                }

                if (isset($entry['pluralKey'])) {
                    if (isset($trlEntries[$entry['pluralKey']])) {
                        if ($trlEntries[$entry['pluralKey']]['translation'] != $entry['pluralTranslation']) {
                            $secondFile = $trlEntries[$entry['pluralKey']]['source'];
                            echo "\nTranslation is defined in a second package but translated differently:";
                            echo "\n{$entry['pluralKey']} | $secondFile & $filename";
                            echo "\nPlease consider adding context to one or both.\n";
                        }
                    } else {
                        $trlEntries[$entry['pluralKey']] = array(
                            'source' => $filename,
                            'translation' => $entry['pluralTranslation']
                        );
                    }
                }
            }
        }

        $trl = array();
        foreach ($trlEntries as $id => $value) {
            $trl[$id] = $value['translation'];
        }
        return $trl;
    }

    private function _loadTrlArray($source, $targetLanguage)
    {
        if ($source == 'kwf') {
            return $this->_loadKwfTrlArray($targetLanguage);
        } else if ($source == 'web') {
            return $this->_loadWebTrlArray($targetLanguage);
        } else {
            throw new Kwf_Exception_NotYetImplemented();
        }
    }

    private function _getAllPoParsersForPackagesExceptKwfPackage($targetLanguage)
    {
        // check all composer packages
        $files = $this->_getAllPackagesTrlFilesExceptKwfTrlFiles($targetLanguage);
        require_once VENDOR_PATH.'/autoload.php';
        $poParsers = array();
        foreach ($files as $file) {
            $poParsers[$file] = \Sepia\PoParser::parseFile($file);
        }
        return $poParsers;
    }

    private function _getAllPackagesTrlFilesExceptKwfTrlFiles($targetLanguage)
    {
        $composerFiles = array_merge(
            //Explicitly add koala-framework composer.json because of tests
            array(KWF_PATH.'/composer.json'),
            glob(VENDOR_PATH.'/*/*/composer.json')
        );
        $existingFiles = array();
        $composerFiles = array_unique($composerFiles);
        foreach ($composerFiles as $composerFile) {
            if (strpos($composerFile, 'koala-framework/koala-framework') !== false) continue; // ignore kwf trl-files as they are the base for package-translations
            $trlDir = dirname($composerFile).'/trl/';

            $trlFilePath = $trlDir.$targetLanguage.'.po';
            if (file_exists($trlFilePath)) {
                $existingFiles[] = $trlFilePath;
                continue;
            }

            $composerConfig = json_decode(file_get_contents($composerFile));
            if (!isset($composerConfig->extra)) continue;
            if (!isset($composerConfig->extra->{'kwf-lingohub'})) continue;
            if (!file_exists($trlDir)) mkdir($trlDir);
            $trlConfig = $composerConfig->extra->{'kwf-lingohub'};
            if (!$this->_downloadTrlFile($trlConfig, $trlFilePath, $targetLanguage)) {
                continue;
            }
            $existingFiles[] = $trlFilePath;
        }
        return $existingFiles;
    }

    private function _downloadTrlFile($trlConfig, $trlFilePath, $language)
    {
        $client = new Zend_Http_Client(Kwf_Registry::get('config')->trl->downloadUrl."/{$trlConfig->account}/{$trlConfig->project}/$language");
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
        if (!$file) return false;
        file_put_contents($trlFilePath, $file);
        return true;
    }

    public function getTypeName()
    {
        return 'trl';
    }
}
