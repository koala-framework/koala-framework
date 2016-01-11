<?php
/**
 * Basic translated string
 *
 * More information: https://github.com/vivid-planet/koala-framework/wiki/Translation
 *
 * @param string translated string
 * @param string|string[] string will be replaced with {0} in translated string, array with their {index}
 * @package Trl
 */
function trl($string, $text = array()) {
    return Kwf_Trl::getInstance()->trl($string, $text);
}

/**
 * Translated string in a context
 *
 * @param string Context describing usage of translated string
 * @param string translated string
 * @param string|string[] string will be replaced with {0} in translated string, array with their {index}
 * @package Trl
 */
function trlc($context, $string, $text = array()) {
    return Kwf_Trl::getInstance()->trlc($context, $string, $text);
}

/**
 * Translated string in singular/plural form
 *
 * @param string singular form
 * @param string plural form including {0}
 * @param string|string[] string will be replaced with {0} in translated string, array with their {index}
 * @package Trl
 */
function trlp($single, $plural, $text =  array()) {
    return Kwf_Trl::getInstance()->trlp($single, $plural, $text);
}

/**
 * Translated string in singular/plural form in a context
 *
 * @param string Context describing usage of translated string
 * @param string singular form
 * @param string plural form including {0}
 * @param string|string[] string will be replaced with {0} in translated string, array with their {index}
 * @package Trl
 */
function trlcp($context, $single, $plural, $text = array()) {
    return Kwf_Trl::getInstance()->trlcp($context, $single, $plural, $text);
}

/**
 * @see trl
 * @package Trl
 */
function trlKwf($string, $text = array()) {
    return Kwf_Trl::getInstance()->trlKwf($string, $text);
}

/**
 * @see trlc
 * @package Trl
 */
function trlcKwf($context, $string, $text = array()) {
    return Kwf_Trl::getInstance()->trlcKwf($context, $string, $text);
}

/**
 * @see trlp
 * @package Trl
 */
function trlpKwf($single, $plural, $text =  array()) {
    return Kwf_Trl::getInstance()->trlpKwf($single, $plural, $text);
}

/**
 * @see trlcp
 * @package Trl
 */
function trlcpKwf($context, $single, $plural, $text = array()) {
    return Kwf_Trl::getInstance()->trlcpKwf($context, $single, $plural, $text);
}

// trl functions for e.g. placeholders
/**
 * @see trl
 * @package Trl
 */
function trlStatic($string, $text = array()) {
    return '*trlserialized-'.serialize(array('type' => 'trl', 'args' => array($string, $text))).'-/trlserialized*';
}

/**
 * @see trlc
 * @package Trl
 */
function trlcStatic($context, $string, $text = array()) {
    return '*trlserialized-'.serialize(array('type' => 'trlc', 'args' => array($context, $string, $text))).'-/trlserialized*';
}

/**
 * @see trlp
 * @package Trl
 */
function trlpStatic($single, $plural, $text =  array()) {
    return '*trlserialized-'.serialize(array('type' => 'trlp', 'args' => array($single, $plural, $text))).'-/trlserialized*';
}

/**
 * @see trlcp
 * @package Trl
 */
function trlcpStatic($context, $single, $plural, $text = array()) {
    return '*trlserialized-'.serialize(array('type' => 'trlcp', 'args' => array($context, $single, $plural, $text))).'-/trlserialized*';
}

/**
 * @see trl
 * @package Trl
 */
function trlKwfStatic($string, $text = array()) {
    return '*trlserialized-'.serialize(array('type' => 'trlKwf', 'args' => array($string, $text))).'-/trlserialized*';
}

/**
 * @see trlc
 * @package Trl
 */
function trlcKwfStatic($context, $string, $text = array()) {
    return '*trlserialized-'.serialize(array('type' => 'trlcKwf', 'args' => array($context, $string, $text))).'-/trlserialized*';
}

/**
 * @see trlp
 * @package Trl
 */
function trlpKwfStatic($single, $plural, $text =  array()) {
    return '*trlserialized-'.serialize(array('type' => 'trlpKwf', 'args' => array($single, $plural, $text))).'-/trlserialized*';
}

/**
 * @see trlcp
 * @package Trl
 */
function trlcpKwfStatic($context, $single, $plural, $text = array()) {
    return '*trlserialized-'.serialize(array('type' => 'trlcpKwf', 'args' => array($context, $single, $plural, $text))).'-/trlserialized*';
}


/**
 * @package Trl
 */
class Kwf_Trl
{
    private $_trlElements = array();

    private $_languages; //cache
    private $_useUserLanguage = true;
    private $_webCodeLanguage;

    private $_authedUserTargetLanguageCache;

    /**
     * @internal
     */
    const SOURCE_KWF = 'kwf';
    /**
     * @internal
     */
    const SOURCE_WEB = 'web';

    private static $_instance = null;

    /**
     * @return Kwf_Trl
     */
    public static function getInstance()
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function setUseUserLanguage($useUserLanguage)
    {
        $this->_useUserLanguage = $useUserLanguage;
    }

    public function getLanguages()
    {
        if (!isset($this->_languages)) {
            $langauges = Kwf_Config::getValueArray('languages');
            if ($langauges) {
                $this->_languages = array_keys($langauges);
            } else {
                $this->_languages = array($this->getWebCodeLanguage());
            }
        }
        return $this->_languages;
    }

    public function setLanguages($languages) //notwendig zum testen
    {
        $this->_languages = $languages;
    }

    public function getTargetLanguage()
    {
        if (PHP_SAPI == 'cli' || !$this->_useUserLanguage) {
            return $this->getWebCodeLanguage();
        }

        //shortcut
        if (count($this->getLanguages()) == 1) {
            return $this->getWebCodeLanguage();
        }

        if (!Kwf_Setup::hasAuthedUser()) {
            return $this->getWebCodeLanguage();
        } else {
            if ($this->_authedUserTargetLanguageCache) return $this->_authedUserTargetLanguageCache;
            //TODO: this ALWAYS requires a db connection, should be saved in session
            $userModel = Kwf_Registry::get('userModel');
            $authedUser = $userModel->getAuthedUser();
            if (!isset($userModel->getAuthedUser()->language) ||
                !($userLanguage = $userModel->getAuthedUser()->language) ||
                !in_array($userLanguage, $this->getLanguages())
            ) {
                $this->_authedUserTargetLanguageCache = $this->getWebCodeLanguage();
                return $this->_authedUserTargetLanguageCache;
            }
            $this->_authedUserTargetLanguageCache = $userLanguage;
            return $this->_authedUserTargetLanguageCache;
        }
    }

    public function getWebCodeLanguage()
    {
        if (!$this->_webCodeLanguage) {
            $this->_webCodeLanguage = Kwf_Config::getValue('webCodeLanguage');
        }
        return $this->_webCodeLanguage;
    }

    public function setWebCodeLanguage($code)
    {
        $this->_webCodeLanguage = $code;
    }

    public function trlStaticExecute($trlStaticData, $language = null)
    {
        $ret = $trlStaticData;

        $stack = array();
        $locations = array();
        $offset = 0;
        while (true) {
            $nextStart = strpos($ret, '*trlserialized-', $offset);
            $nextEnd = strpos($ret, '-/trlserialized*', $offset);
            if ($nextStart === false && $nextEnd === false) break;
            if ($nextStart === false || $nextEnd < $nextStart) {
                //next token is end
                if (!$stack) {
                    throw new Kwf_Exception("unexpected end");
                }
                $start = array_pop($stack);

                if (!$stack) {
                    //if outer replace with translated content
                    $l = array(
                        'start' => $start,
                        'end' => $nextEnd
                    );
                    $trlStaticData = substr($ret, $l['start']+15, $l['end']-$l['start']-15);
                    $trlStaticData = unserialize($trlStaticData);

                    $args = $trlStaticData['args'];
                    if ($args[1]) {
                        if (is_array($args[1])) {
                            foreach ($args[1] as $k=>$i) {
                                $args[1][$k] = $this->trlStaticExecute($i, $language);
                            }
                        } else {
                            $args[1] = $this->trlStaticExecute($args[1], $language);
                        }
                    }
                    $args[] = $language;
                    $replace = call_user_func_array(
                        array($this, $trlStaticData['type']), $args
                    );
                    $ret = substr($ret, 0, $l['start']).$replace.substr($ret, $l['end']+16);
                    $offset = $start+strlen($replace);
                } else {
                    //if second is inner ignore it, will get replaced thru recursion
                    $offset = $nextEnd+16;
                }
            } else {
                //next token is start
                $stack[] = $nextStart;
                $offset = $nextStart+15;
            }
        }
        return $ret;
    }

    public function trlKwf($string, $params = array(), $language = null)
    {
        return $this->_trlc('', $string, $params, self::SOURCE_KWF, $language);
    }

    public function trl($string, $params = array(), $language = null)
    {
        return $this->_trlc('', $string, $params, self::SOURCE_WEB, $language);
    }
    public function trlcKwf($context, $string, $params = array(), $language = null)
    {
        return $this->_trlc($context, $string, $params, self::SOURCE_KWF, $language);
    }

    public function trlc($context, $string, $params = array(), $language = null)
    {
        return $this->_trlc($context, $string, $params, self::SOURCE_WEB, $language);
    }

    private function _trlc($context, $string, $params, $source, $language = null)
    {
        $params = $this->_makeArray($params);
        $text = $this->_findElement($string, $source, $context, $language);
        foreach ($params as $key => $value) {
            $text = str_replace('{'.$key.'}', $value, $text);
        }
        return $text;
    }

    public function trlp($single, $plural, $params,  $language = null)
    {
        return $this->_trlcp('', $single, $plural, $params, self::SOURCE_WEB, $language);
    }

    public function trlpKwf($single, $plural, $params,  $language = null)
    {
        return $this->_trlcp('', $single, $plural, $params, self::SOURCE_KWF, $language);
    }

    public function trlcp($context, $single, $plural, $params, $language = null)
    {
        return $this->_trlcp($context, $single, $plural, $params, self::SOURCE_WEB, $language);
    }

    public function trlcpKwf($context, $single, $plural, $params, $language = null)
    {
        return $this->_trlcp($context, $single, $plural, $params, self::SOURCE_KWF, $language);
    }

    private function _trlcp($context, $single, $plural, $params, $source, $language = null)
    {
        $params = $this->_makeArray($params);
        if ($params[0] != 1){
            $text = $this->_findElementPlural($single, $plural, $source, $context, $language);
        } else {
            $text = $this->_findElement($single, $source, $context, $language);
        }
        foreach ($params as $key => $value){
            $text = str_replace('{'.$key.'}', $value, $text);
        }
        return $text;
    }

    private function _makeArray($placeolders)
    {
        return is_array($placeolders) ? $placeolders : array($placeolders);
    }

    public function unsetTrlElements()
    {
        $this->_trlElements = array();
    }

    public function setTrlElements($trlElements)
    {
        $this->_trlElements = $trlElements;
    }

    public function _loadTrlElements($source, $target, $plural)
    {
        if ($source == self::SOURCE_WEB) $codeLanguage = $this->getWebCodeLanguage();
        else $codeLanguage = "en";

        if ($codeLanguage == $target) {
            $this->_trlElements[$source][$target] = array();
            return;
        }

        $buildFileName = Kwf_Trl::generateBuildFileName($source, $target, $plural);
        if (file_exists($buildFileName)) {
            $c = unserialize(file_get_contents($buildFileName));
        } else {
            throw new Kwf_Trl_BuildFileMissingException("$buildFileName was not created in build");
        }
        $this->_trlElements[$source][$target.($plural ? '_plural' : '')] = $c;
        return $c;
    }

    public static function generateBuildFileName($source, $target, $plural)
    {
        return 'build/trl/'.$source.$target.($plural ? '_plural' : '');
    }

    protected function _findElement($needle, $source, $context, $language = null)
    {
        if ($language) $target = $language;
        else $target = $this->getTargetLanguage();

        $cacheId = 'trl-'.$source.'-'.$target.'-'.$needle.'-'.$context;
        $ret = Kwf_Cache_SimpleStatic::fetch($cacheId, $success);
        if ($success) {
            return $ret;
        }

        if (!isset($this->_trlElements[$source][$target])) {
            $this->_loadTrlElements($source, $target, false);
        }
        if (isset($this->_trlElements[$source][$target][$needle.'-'.$context])) {
            $ret = $this->_trlElements[$source][$target][$needle.'-'.$context];
        } else {
            $ret = $needle;
        }
        Kwf_Cache_SimpleStatic::add($cacheId, $ret);
        return $ret;
    }

    //TODO: wofuer wird der $needle parameter verwendet?!
    protected function _findElementPlural($needle, $plural, $source, $context = '', $language = null)
    {
        if ($language) $target = $language;
        else $target = $this->getTargetLanguage();

        $cacheId = 'trlp-'.$source.'-'.$target.'-'.$plural.'-'.$context;
        $ret = Kwf_Cache_SimpleStatic::fetch($cacheId, $success);
        if ($success) {
            return $ret;
        }

        if (!isset($this->_trlElements[$source][$target.'_plural'])) {
            $this->_loadTrlElements($source, $target, true);
        }
        if (isset($this->_trlElements[$source][$target.'_plural'][$plural.'-'.$context])) {
            $ret = $this->_trlElements[$source][$target.'_plural'][$plural.'-'.$context];
        } else {
            $ret = $plural;
        }
        Kwf_Cache_SimpleStatic::add($cacheId, $ret);
        return $ret;
    }

    function getTrlpValues($context, $single, $plural, $source, $language = null)
    {
        $values = array();
        $values['plural'] = $this->_findElementPlural($single, $plural, $source, $context, $language);
        $values['single'] = $this->_findElement($single, $source, $context, $language);
        return $values;
    }
}

