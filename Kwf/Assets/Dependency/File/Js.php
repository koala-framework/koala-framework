<?php
class Kwf_Assets_Dependency_File_Js extends Kwf_Assets_Dependency_File
{
    private $_parsedElementsCache;
    private $_contentsCacheSourceMap;

    public function getMimeType()
    {
        return 'text/javascript';
    }

    public function __construct($fileName)
    {
        parent::__construct($fileName);
    }

    protected function _getRawContents($language)
    {
        return parent::getContents($language);
    }

    public function warmupCaches()
    {
        if (isset($this->_contentsCache)) return;

        $fileName = $this->getFileNameWithType();
        $rawContents = $this->_getRawContents(null);


        $usesUniquePrefix = strpos($rawContents, '.kwcClass') !== false
            || strpos($rawContents, 'kwfup-') !== false
            || strpos($rawContents, 'kwcbem__') !== false;

        $pathType = $this->getType();
        if ($pathType == 'ext2' && strpos($rawContents, 'ext2-gen') !== false) {
            $usesUniquePrefix = true;
        }

        if ($usesUniquePrefix) {
            //when contents contain .cssClass we must cache per app
            $buildFile = 'cache/uglifyjs/'.$fileName.'.'.md5(file_get_contents($this->getAbsoluteFileName()).Kwf_Config::getValue('application.uniquePrefix'));
        } else {
            $buildFile = sys_get_temp_dir().'/kwf-uglifyjs/'.$fileName.'.'.md5(file_get_contents($this->getAbsoluteFileName()));
        }
        $useTrl = !in_array($pathType, array('ext2'));
        if (substr($this->getAbsoluteFileName(), 0, 24) == 'vendor/bower_components/') {
            //dependencies loaded via bower never use kwf translation system
            $useTrl = false;
        }

        if (!file_exists("$buildFile.min.js") || ($useTrl && !file_exists("$buildFile.min.js.trl"))) {

            $dir = dirname($buildFile);
            if (!file_exists($dir)) mkdir($dir, 0777, true);
            file_put_contents($buildFile, $rawContents);

            $map = Kwf_Assets_Dependency_Filter_UglifyJs::build($buildFile, $this->getFileNameWithType());

            $contents = file_get_contents("$buildFile.min.js");
            $replacements = array();
            if ($pathType == 'ext2') {
                $replacements['../images/'] = '/assets/ext2/resources/images/';
            } else if ($pathType == 'mediaelement') {
                $replacements['url('] = 'url(/assets/mediaelement/build/';
            }
            if ($usesUniquePrefix) {
                if ($pathType == 'ext2') {
                    //hack for ext2 to avoid duplicated ids getting generated
                    $uniquePrefix = Kwf_Config::getValue('application.uniquePrefix');
                    if ($uniquePrefix) {
                        $map->stringReplace('ext2-gen', $uniquePrefix.'-ext2-gen');
                    }
                }
                $cssClass = $this->_getComponentCssClass();
                if ($cssClass) {
                    if (preg_match_all('#([\'"])\.kwcClass([\s\'"\.])#', $contents, $m)) {
                        foreach ($m[0] as $k=>$i) {
                            $replacements[$i] = $m[1][$k].'.'.$cssClass.$m[2][$k];
                        }
                    }
                }
                if (strpos($rawContents, 'kwfup-') !== false) {
                    if (Kwf_Config::getValue('application.uniquePrefix')) {
                        $replacements['kwfup-'] = Kwf_Config::getValue('application.uniquePrefix').'-';
                    } else {
                        $replacements['kwfup-'] = '';
                    }
                }
                if (strpos($rawContents, 'kwcbem__') !== false) {
                    if (Kwf_Config::getValue('application.uniquePrefix')) {
                        $replacements['kwcbem__'] = $this->_getComponentCssClass().'__';
                    } else {
                        $replacements['kwcbem__'] = '';
                    }
                }
            }
            foreach ($replacements as $search=>$replace) {
                $map->stringReplace($search, $replace);
            }
            $map->save("$buildFile.min.js.map.json", "$buildFile.min.js"); //adds last extension

            if ($useTrl) {
                file_put_contents("$buildFile.min.js.trl", serialize(Kwf_Trl_Parser_JsParser::parseContent($contents)));
            }
        } else {
            $map = new Kwf_SourceMaps_SourceMap(file_get_contents("$buildFile.min.js.map.json"), file_get_contents("$buildFile.min.js"));
        }

        $this->_contentsCache = $map;
        if ($useTrl) {
            $this->_parsedElementsCache = unserialize(file_get_contents("$buildFile.min.js.trl"));
        } else {
            $this->_parsedElementsCache = array();
        }
    }

    private function _getCompliedContents()
    {
        if (!isset($this->_contentsCache)) {
            $this->warmupCaches();
        }
        return array(
            'contents' => $this->_contentsCache,
            'trlElements' => $this->_parsedElementsCache
        );
    }

    protected function _getContents($language, $pack)
    {
        if ($pack) {
            $compiledContents = $this->_getCompliedContents();
            $map = $compiledContents['contents'];
            $trlElements = $compiledContents['trlElements'];
        } else {
            $contents = $this->_getRawContents(null);
            $map = Kwf_SourceMaps_SourceMap::createEmptyMap($contents);
            $trlElements = Kwf_Trl_Parser_JsParser::parseContent($contents);
            unset($contents);
        }

        if ($trlElements) {

            $buildFile = false;
            if ($pack) {
                $buildFile = "cache/assets/".$this->getFileNameWithType().'-'.$language;
                $dir = dirname($buildFile);
                if (!file_exists($dir)) mkdir($dir, 0777, true);
            }

            if (!$buildFile || !file_exists("$buildFile.buildtime") || filemtime($this->getAbsoluteFileName()) != file_get_contents("$buildFile.buildtime")) {
                foreach ($this->_getTrlReplacements($trlElements, $map->getFileContents(), $language) as $value) {
                    $map->stringReplace($value['search'], $value['replace']);
                }
                if ($buildFile) {
                    $map->save("$buildFile.map", $buildFile);
                    file_put_contents("$buildFile.buildtime", filemtime($this->getAbsoluteFileName()));
                }
            } else {
                $map = new Kwf_SourceMaps_SourceMap(file_get_contents("$buildFile.map"), file_get_contents("$buildFile"));
            }
        }

        return $map;
    }

    private function _getTrlReplacements($trlElements, $contents, $language)
    {
        static $jsLoader;
        if (!isset($jsLoader)) $jsLoader = new Kwf_Trl_JsLoader();
        $replacements = $jsLoader->getReplacements($trlElements, $language);
        $replacements = array_merge($replacements, $this->_getHelpReplacements($contents, $language));
        return $replacements;
    }


    public static function pack($ret)
    {


        $ret = str_replace("\r", "\n", $ret);

        // remove comments
        $ret = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*'.'/!', '', $ret);
        // deaktiviert wg. urls mit http:// in hilfetexten $contents = preg_replace('!//[^\n]*!', '', $ret);

        // remove tabs, spaces, newlines, etc. - funktioniert nicht - da fehlen hinundwider ;
        //$ret = str_replace(array("\r", "\n", "\t"), "", $ret);

        // multiple whitespaces
        $ret = str_replace("\t", " ", $ret);
        $ret = preg_replace('/(\n)\n+/', '$1', $ret);
        $ret = preg_replace('/(\n)\ +/', '$1', $ret);
        $ret = preg_replace('/(\ )\ +/', '$1', $ret);

        return $ret;
    }

    public final function getContents($language)
    {
        $c = $this->_getContents($language, false);
        return $c->getFileContents();
    }

    private function _getHelpReplacements($contents, $language)
    {
        $ret = array();
        $matches = array();
        preg_match_all("#hlp\(['\"](.+?)['\"]\)#", $contents, $matches);
        foreach ($matches[0] as $key => $search) {
            $r = Zend_Registry::get('hlp')->hlp($matches[1][$key], $language);
            $r = str_replace(array("\n", "\r", "'"), array('\n', '', "\\'"), $r);
            $ret[] = array('search'=>$search, 'replace' => "'" . $r . "'");
        }
        return $ret;
    }

    public final function getContentsPacked($language)
    {
        $c = $this->_getContents($language, true);
        return $c;
    }
}
