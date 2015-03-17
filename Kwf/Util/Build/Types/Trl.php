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
        Kwf_Trl::getInstance()->clearCache();

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

    private function _loadTrlArray($source, $target, $plural)
    {
        $poParser = $this->_getPoParser($target);
        $c = array();
        foreach ($poParser->entries() as $entry) {
            $ctx = isset($entry['msgctxt']) ? $entry['msgctxt'][0] : '';
            $translation = $entry['msgstr'][0];
            if (isset($entry['msgid_plural'])) {
                $translation = $entry['msgstr'][1];
            }
            if ($translation == '') continue;
            $c[(isset($entry['msgid_plural']) ? $entry['msgid_plural'][0] : $entry['msgid'][0]).'-'.$ctx] = $translation;
        }
        return $c;
    }

    protected function _getPoParser($targetLanguage)
    {
        require_once VENDOR_PATH.'/autoload.php';
        $poParser = new \Sepia\PoParser;
        $poParser->parseFile('trl/'.$targetLanguage.'.po');
        return $poParser;
    }

    public function getTypeName()
    {
        return 'trl';
    }
}
