<?php
function hlp($string) {
    return Zend_Registry::get('hlp')->hlp($string);
}

function hlpVps($string) {
    return Zend_Registry::get('hlp')->hlpVps($string);
}

function trl($string, $text = array()) {
    return Vps_Trl::getInstance()->trl($string, $text, Vps_Trl::SOURCE_WEB);
}

function trlc($context, $string, $text = array()) {
    return Vps_Trl::getInstance()->trlc($context, $string, $text, Vps_Trl::SOURCE_WEB);
}

function trlp($single, $plural, $text =  array()) {
    return Vps_Trl::getInstance()->trlp($single, $plural, $text, Vps_Trl::SOURCE_WEB);
}

function trlcp($context, $single, $plural, $text = array()) {
    return Vps_Trl::getInstance()->trlcp($context, $single, $plural, $text, Vps_Trl::SOURCE_WEB);
}

function trlVps($string, $text = array()) {
    return Vps_Trl::getInstance()->trl($string, $text, Vps_Trl::SOURCE_VPS);
}

function trlcVps($context, $string, $text = array()) {
    return Vps_Trl::getInstance()->trlc($context, $string, $text, Vps_Trl::SOURCE_VPS);
}

function trlpVps($single, $plural, $text =  array()) {
    return Vps_Trl::getInstance()->trlp($single, $plural, $text, Vps_Trl::SOURCE_VPS);
}

function trlcpVps($context, $single, $plural, $text = array()) {
    return Vps_Trl::getInstance()->trlcp($context, $single, $plural, $text, Vps_Trl::SOURCE_VPS);
}

// trl functions for e.g. placeholders
function trlStatic($string, $text = array()) {
    return '*trlserialized-'.serialize(array('type' => 'trl', 'args' => array($string, $text))).'-trlserialized*';
}

function trlcStatic($context, $string, $text = array()) {
    return '*trlserialized-'.serialize(array('type' => 'trlc', 'args' => array($context, $string, $text))).'-trlserialized*';
}

function trlpStatic($single, $plural, $text =  array()) {
    return '*trlserialized-'.serialize(array('type' => 'trlp', 'args' => array($single, $plural, $text))).'-trlserialized*';
}

function trlcpStatic($context, $single, $plural, $text = array()) {
    return '*trlserialized-'.serialize(array('type' => 'trlcp', 'args' => array($context, $single, $plural, $text))).'-trlserialized*';
}

function trlVpsStatic($string, $text = array()) {
    return '*trlserialized-'.serialize(array('type' => 'trlVps', 'args' => array($string, $text))).'-trlserialized*';
}

function trlcVpsStatic($context, $string, $text = array()) {
    return '*trlserialized-'.serialize(array('type' => 'trlcVps', 'args' => array($context, $string, $text))).'-trlserialized*';
}

function trlpVpsStatic($single, $plural, $text =  array()) {
    return '*trlserialized-'.serialize(array('type' => 'trlpVps', 'args' => array($single, $plural, $text))).'-trlserialized*';
}

function trlcpVpsStatic($context, $single, $plural, $text = array()) {
    return '*trlserialized-'.serialize(array('type' => 'trlcpVps', 'args' => array($context, $single, $plural, $text))).'-trlserialized*';
}


class Vps_Trl
{
    private $_cache = array();

    private $_modelWeb;
    private $_modelVps;
    private $_languages; //cache
    private $_useUserLanguage = true;
    private $_webCodeLanguage;

    const SOURCE_VPS = 'vps';
    const SOURCE_WEB = 'web';
    const TRLCP = 'trlcp';
    const TRLP = 'trlp';
    const TRLC = 'trlc';
    const TRL = 'trl';

    const ERROR_INVALID_CHAR = 'invalidChar';
    const ERROR_INVALID_STRING = 'invalidString';
    const ERROR_WRONG_NR_OF_ARGUMENTS = 'wrongNrOfArguments';
    protected $_errorMessages = array(
        self::ERROR_INVALID_CHAR => 'Unallowed character inbetween two quotationmark blocks (e.g. "aa"."bb)"',
        self::ERROR_INVALID_STRING => 'String is not valid. Unallowed characters are used',
        self::ERROR_WRONG_NR_OF_ARGUMENTS => 'To few arguments.'
    );


    private static $_instance = null;

    public static function getInstance()
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function __construct($config = array())
    {
        if (isset($config['modelVps'])) $this->_modelVps = $config['modelVps'];
        if (isset($config['modelWeb'])) $this->_modelVps = $config['modelWeb'];
    }

    public function setUseUserLanguage($useUserLanguage)
    {
        $this->_useUserLanguage = $useUserLanguage;
    }

    public function setModel($model, $type)
    {
        $this->_cache = array();
        if ($type == self::SOURCE_VPS) {
            $this->_modelVps = $model;
        } else {
            $this->_modelWeb = $model;
        }
    }

    public function getLanguages()
    {

        if (!isset($this->_languages)) {
            $config = Zend_Registry::get('config');
            if ($config->languages) {
                $this->_languages = array_values($config->languages->toArray());
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
        if (php_sapi_name() == 'cli' || !$this->_useUserLanguage) {
            return $this->getWebCodeLanguage();
        }

        //abkürzung
        if (count($this->getLanguages()) == 1) {
            return $this->getWebCodeLanguage();
        }

        //TODO: das benötigt IMMER eine datenbankverbindung, sollte in session gespeichert werden
        $userModel = Vps_Registry::get('userModel');
        if (!$userModel || !$userModel->getAuthedUser() ||
            !isset($userModel->getAuthedUser()->language) ||
            !$userModel->getAuthedUser()->language ||
            !in_array($userModel->getAuthedUser()->language, $this->getLanguages()))
        {
            return $this->getWebCodeLanguage();
        } else {
            return $userModel->getAuthedUser()->language;
        }
    }

    public function getWebCodeLanguage()
    {
        if (!$this->_webCodeLanguage) {
            $config = Zend_Registry::get('config');
            $this->_webCodeLanguage = $config->webCodeLanguage;
        }
        return $this->_webCodeLanguage;
    }

    public function setWebCodeLanguage($code)
    {
        $this->_webCodeLanguage = $code;
    }

    private function _getModel($type)
    {
        if ($type == self::SOURCE_WEB) {
            if (!isset($this->_modelWeb)) return Vps_Model_Abstract::getInstance('Vps_Trl_Model_Web');
            return $this->_modelWeb;
        } else {
            if (!isset($this->_modelVps)) return Vps_Model_Abstract::getInstance('Vps_Trl_Model_Vps');
            return $this->_modelVps;
        }
    }

    public function trlStaticExecute($trlStaticData, $language = null)
    {
        $ret = $trlStaticData;

        if (preg_match_all('/\*trlserialized-(.+?)-trlserialized\*/m', $trlStaticData, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $k => $match) {
                $trlStaticData = unserialize($match[1]);
                if (strtolower(substr($trlStaticData['type'], -3)) == 'vps') {
                    $trlStaticData['type'] = substr($trlStaticData['type'], 0, -3);
                    $source = Vps_Trl::SOURCE_VPS;
                } else {
                    $source = Vps_Trl::SOURCE_WEB;
                }

                $args = $trlStaticData['args'];
                $args[] = $source;
                $args[] = $language;

                $replaceString = call_user_func_array(
                    array($this, $trlStaticData['type']), $args
                );
                $ret = str_replace($match[0], $replaceString, $ret);
            }
        }

        return $ret;
    }

    public function trl($string, $params, $source, $language = null)
    {
        return $this->trlc('', $string, $params, $source, $language);
    }

    public function trlc($context, $string, $params, $source, $language = null)
    {
        $params = $this->_makeArray($params);
        $text = $this->_findElement($string, $source, $context, $language);
        foreach ($params as $key => $value) {
            $text = str_replace('{'.$key.'}', $value, $text);
        }
        return $text;
    }

    public function trlp($single, $plural, $params, $source, $language = null)
    {
        return $this->trlcp('', $single, $plural, $params, $source, $language);
    }

    function trlcp($context, $single, $plural, $params, $source, $language = null)
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

    private function _loadCache($source, $target, $plural)
    {
        if ($source == self::SOURCE_WEB) $codeLanguage = $this->getWebCodeLanguage();
        else $codeLanguage = "en";

        if ($codeLanguage == $target) {
            $this->_cache[$source][$target] = array();
            return;
        }

        if ($plural) $target = $target.'_plural';
        $cache = Vps_Cache::factory('Core', 'Memcached',
            array(
                'automatic_serialization'=>true,
                'caching' => !isset($this->_modelVps) && !isset($this->_modelWeb)
            )
        );
        $cacheId = 'trl_'.$source.$target.$plural;

        if (($c = $cache->load($cacheId)) === false) {
            $c = array();
            $m = $this->_getModel($source);
            if ($m instanceof Vps_Model_Xml) {
                $rows = array();
                if (file_exists($m->getFilePath())) {
                    $xml = simplexml_load_file($m->getFilePath());
                    $rows = $xml->text;
                }
            } else {
                $rows = $m->getRows();
            }
            foreach ($rows as $row) {
                if ($row->$target != '' && $row->$target != '_') {
                    $ctx = isset($row->context) ? $row->context : '';
                    $c[$row->{$codeLanguage.($plural ? '_plural' : '')}.'-'.$ctx] = (string)$row->$target;
                }
            }
            $cache->save($c, $cacheId);
        }
        $this->_cache[$source][$target] = $c;
    }

    protected function _findElement($needle, $source, $context, $language = null)
    {
        if ($language) $target = $language;
        else $target = $this->getTargetLanguage();

        if (!isset($this->_cache[$source][$target])) {
            $this->_loadCache($source, $target, false);
        }
        if (isset($this->_cache[$source][$target][$needle.'-'.$context])) {
            return $this->_cache[$source][$target][$needle.'-'.$context];
        } else {
            return $needle;
        }
    }

    protected function _findElementPlural($needle, $plural, $source, $context = '', $language = null)
    {
        if ($language) $target = $language;
        else $target = $this->getTargetLanguage();

        if (!isset($this->_cache[$source][$target.'_plural'])) {
            $this->_loadCache($source, $target, true);
        }
        if (isset($this->_cache[$source][$target.'_plural'][$plural.'-'.$context])) {
            return $this->_cache[$source][$target.'_plural'][$plural.'-'.$context];
        } else {
            return $plural;
        }
    }

    function getTrlpValues($context, $single, $plural, $source, $language = null)
    {
        $values = array();
        $values['plural'] = $this->_findElementPlural($single, $plural, $source, $context, $language);
        $values['single'] = $this->_findElement($single, $source, $context, $language);
        return $values;
    }

    public function parse($content, $type = 'php')
    {
        $parts = array();

        /*
         * beim parsen von datien mit über 10000 zeichen tritt
         * bei den regular expressions ein fehler auf -> diese
         * abfrage ist der bugfix
         */
        $from = 0;
        $length = 9500;
        $check = true;
        while ($check) {
            if ($from != 0 && $from > 500) {
                $from = $from - 500;
            }
            if (($from + $length) > strlen($content)) {
                $length = strlen($content)- $from;
                $check = false;
            }

            $newContent = substr($content, $from, $length);
            $from += $length;
            foreach ($this->_getExpressions($newContent) as $expression) {
                $parts[] = $this->_getContents($expression, $type);
            }
        }
        return $parts;
    }

    private function _getType($expression)
    {
        if (strpos($expression, 'trlcp') === 0) {
            return self::TRLCP;
        }
        if (strpos($expression, 'trlp') === 0) {
            return self::TRLP;
        }
        if (strpos($expression, 'trlc') === 0) {
            return self::TRLC;
        }
        if (strpos($expression, 'trl') === 0) {
            return self::TRL;
        }
    }

    private function _getExpressions($content)
    {
        $linenumber = 0;
        $parts = array();
        while (true) {
            $pattern = "#(.*?)((trlc?p?(Vps)?(Static)?) *\(['|\"].*)#s";
            preg_match($pattern, $content, $m);
            if (!$m) break;
            $text = $m[2];
            $linenumber = $linenumber + $this->_getLineNumber($m[1]);
            $content = '';
            $countMarksDouble = 0;
            $countMarksSingle = 0;
            $write = false;
            for ($i = 0; $i < strlen($text); $i++) {
                if ($text[$i] == '"' && ($i == 0 || $text[$i-1] != "\\") && ($countMarksSingle == 0 || $countMarksDouble != 0)) {
                    $countMarksDouble++;
                } else if ($text[$i] == "'" && ($i == 0 || $text[$i-1] != "\\") && ($countMarksSingle != 0 || $countMarksDouble == 0)) {
                    $countMarksSingle++;
                }
                if ($text[$i] == ')' && (($countMarksSingle % 2 == 0 && $countMarksSingle != 0)
                        || ($countMarksDouble % 2 == 0 && $countMarksDouble != 0))) {
                    $parts[] = array('expr' => (substr($text, 0, ++$i)), 'linenr' => $linenumber);
                    $linenumber--; //whyever
                    $content = substr($text, $i);
                    break;
                }
            }
        }
        return $parts;
    }

    private function _getLineNumber($text) {
        $array = explode("\n", $text);
        return count($array);
    }

    private function _getContents ($expressArray, $type)
    {
        $expression = $expressArray['expr'];
        $write = false;
        $words = array();
        $word = '';
        $countMarksDouble = 0;
        $countMarksSingle = 0;
        for ($i = 0; $i < strlen($expression); $i++) {
            //doppelte Anfürhungszeihen
            if (($expression[$i] == '"' && ($i == 0 || $expression[$i-1] != "\\")
                    && $countMarksDouble % 2 == 0) && !$write) {
                $countMarksDouble++;
                $write = true;
                $i++;
            } else if ($expression[$i] == '"' && ($i == 0 || $expression[$i-1] != "\\")
                    &&  $countMarksDouble % 2 == 1 && $write) {
                $countMarksDouble++;
                $write = false;
                $i++;
                $words[] = $word;
                $word = '';
            }
            //einfache Anführungszeichen
            if (!$write && $i < strlen($expression) && ($expression[$i] == "'" &&
                        ($i == 0 || $expression[$i-1] != "\\") && $countMarksSingle % 2 == 0)) {
                $countMarksSingle++;
                $write = true;
                $i++;
            } else if ($write && $i < strlen($expression) && $expression[$i] == "'" &&
                        ($i == 0 || $expression[$i-1] != "\\") &&  $countMarksSingle % 2 == 1) {
                $countMarksSingle++;
                $write = false;
                $i++;
                $words[] = $word;
                $word = '';
            }
            if ($write) {
                if ($expression[$i] == "\\" && in_array($expression[$i+1], array('"', "n"))) {
                    //do nothing
                } else {
                    $word .= $expression[$i];
                }
            }
            if ($this->_parseForError($expression, $i, $countMarksSingle, $countMarksDouble, $words, $type)) {
                return ( array_merge($this->_parseForError($expression, $i, $countMarksSingle, $countMarksDouble, $words, $type),
                            array('linenr' => $expressArray['linenr'])));
            }
        }


        if ($this->_checkArguments($this->_getType($expression), $words)) {
            return ( array_merge($this->_checkArguments($this->_getType($expression), $words),
                            array('linenr' => $expressArray['linenr'])));
        }
        switch ($this->_getType($expression)) {
            case self::TRLCP: $words = array('context' => $words[0], 'text' => $words[1], 'plural' => $words[2]); break;
            case self::TRLP: $words = array('text' => $words[0], 'plural' => $words[1]); break;
            case self::TRLC: $words = array('context' => $words[0], 'text' => $words[1]); break;
            case self::TRL: $words = array('text' => $words[0]); break;
         }
         $words['source'] = $this->_getSource($expression);
         $words['type'] = $this->_getType($expression);
         $words['before'] = $expression;
         $words['linenr'] = $expressArray['linenr'];

         return $words;
    }

    private function _checkArguments($type, $words) {
        if ($type == self::TRL) {
            if (count($words)< 1) {
                return array('error' => true, 'error_short' =>self::ERROR_WRONG_NR_OF_ARGUMENTS,
                          'message' => $this->_errorMessages[self::ERROR_WRONG_NR_OF_ARGUMENTS].
                                ' TRL needs at least one argument');
            }
        } elseif ($type == self::TRLC) {
            if (count($words) < 2) {
                return array('error' => true, 'error_short' =>self::ERROR_WRONG_NR_OF_ARGUMENTS,
                    'message' => $this->_errorMessages[self::ERROR_WRONG_NR_OF_ARGUMENTS].
                    ' TRLC needs at least two arguments');
            }
        } elseif ($type == self::TRLP) {
            if (count($words) < 2) {
                return array('error' => true, 'error_short' =>self::ERROR_WRONG_NR_OF_ARGUMENTS,
                    'message' => $this->_errorMessages[self::ERROR_WRONG_NR_OF_ARGUMENTS].
                    ' TRLP needs at least two arguments');
            }
        } elseif ($type == self::TRLC) {
            if (count($words) < 3) {
                return array('error' => true, 'error_short' =>self::ERROR_WRONG_NR_OF_ARGUMENTS,
                    'message' => $this->_errorMessages[self::ERROR_WRONG_NR_OF_ARGUMENTS].
                    ' TRLCP needs at least three arguments');
            }
        }
        return false;
    }

    private function _parseForError($expression, $i, $countMarksSingle, $countMarksDouble, $words, $type)
    {
        $letter = $expression[$i];
        if (($countMarksSingle == 0 && $countMarksDouble != 0 && $countMarksDouble % 2 == 0) ||
                ($countMarksDouble == 0 && $countMarksDouble != 0 && $countMarksSingle % 2 == 0)) {
                if ( in_array($letter, array(',', ' ', ')', '('))) { //ary -> buchstaben für array
                    return false;
                } else if (in_array($letter, array('.'))) {
                    return array('error' => true, 'error_short' =>self::ERROR_INVALID_CHAR,
                          'message' => $this->_errorMessages[self::ERROR_INVALID_CHAR]);
                } else {

                    return $this->_checkArguments($this->_getType($expression), $words);
                }

        }
        $blackList = array("\n");
        if ($type == 'php') {
            $blackList[] = '$';
        }
        if (($countMarksSingle == 0 && $countMarksDouble != 0 && $countMarksDouble % 2 == 1) ||
                ($countMarksDouble == 0 && $countMarksDouble != 0 && $countMarksSingle % 2 == 1)) {
                if ($expression[$i-1] == "\\" && $expression[$i] == "n") {
                        return array('error' => true, 'error_short' =>self::ERROR_INVALID_STRING,
                              'message' => $this->_errorMessages[self::ERROR_INVALID_STRING]);
                }
                if (in_array($letter, $blackList)) {
                    if ($expression[$i-1] != "\\") {
                        return array('error' => true, 'error_short' =>self::ERROR_INVALID_STRING,
                              'message' => $this->_errorMessages[self::ERROR_INVALID_STRING]);
                    } else {
                        return false;
                    }
                } else {
                    return false;
                }
        }

        return false;
    }

    private function _getSource($expression) {
        if (strpos($expression, 'Vps')) {
            return 'vps';
        } else {
            return 'web';
        }
    }

    private function _unescapeString($text)
    {
        $newText = "";
        for ($i = 0; $i < strlen($text); $i++) {

            if ($text[$i] == '\\'){
                switch ($text[$i+1]){
                    case 'n': $temp = "\n"; $i++; break;
                    case 't': $temp = "\t"; $i++; break;
                    case '"': $temp = "\""; $i++; break;
                    case '$': $temp = "\$"; $i++; break;
                    case '\\': $temp = $text[$i]; $i++; break;
                    case "'": $temp = "'"; $i++; break;
                    default: $temp = $text[$i].$text[$i+1]; $i++; $break;
                }
            } else {
                $temp = $text[$i];
            }
            $newText .= $temp;
        }
        return $newText;
    }
}

