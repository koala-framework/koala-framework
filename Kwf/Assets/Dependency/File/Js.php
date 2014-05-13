<?php
class Kwf_Assets_Dependency_File_Js extends Kwf_Assets_Dependency_File
{
    private $_parsedElementsCache;
    private $_contentsCache;

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
        $ret = parent::getContents($language);

        if ($baseUrl = Kwf_Setup::getBaseUrl()) {
            //TODO properly implement this, we can't do it here as it's dependent on config
            //$ret = preg_replace('#url\\((\s*[\'"]?)/assets/#', 'url($1'.$baseUrl.'/assets/', $ret);
            //$ret = preg_replace('#([\'"])/(kwf|vkwf|admin|assets)/#', '$1'.$baseUrl.'/$2/', $ret);
        }
        return $ret;
    }

    public function warmupCaches()
    {
        $fileName = $this->getFileNameWithType();

        $buildFile = "cache/uglifyjs/".$fileName;
        if (!file_exists("$buildFile.buildtime") || filemtime($this->getAbsoluteFileName()) != file_get_contents("$buildFile.buildtime")) {

            $pathType = substr($this->_fileName, 0, strpos($this->_fileName, '/'));

            $dir = substr($buildFile, 0, strrpos($buildFile, '/'));
            if (!file_exists($dir)) mkdir($dir, 0777, true);
            file_put_contents($buildFile, $this->_getRawContents(null));
            $uglifyjs = Kwf_Config::getValue('server.uglifyjs');
            $cmd = "$uglifyjs ";
            $cmd .= "--source-map ".escapeshellarg("$buildFile.min.js.map.json").' ';
            $cmd .= "--prefix 2 ";
            $cmd .= "--output ".escapeshellarg("$buildFile.min.js").' ';
            $cmd .= escapeshellarg($buildFile);
            $out = array();
            system($cmd, $retVal);
            if ($retVal) {
                throw new Kwf_Exception("uglifyjs failed");
            }
            $contents = file_get_contents("$buildFile.min.js");
            $contents = str_replace("\n//# sourceMappingURL=$buildFile.min.js.map.json", '', $contents);

            $map = new Kwf_Assets_Util_SourceMap(file_get_contents("$buildFile.min.js.map.json"), $contents);


            if (strpos($contents, '.cssClass') !== false) {
                $cssClass = $this->_getComponentCssClass();
                if ($cssClass) {
                    if (preg_match_all('#\'\.cssClass([\s\'\.])#', $contents, $m)) {
                        foreach ($m[0] as $k=>$i) {
                            $map->stringReplace($i, '\'.'.$cssClass.$m[1][$k]);
                        }
                    }
                }
            }

            if ($pathType == 'ext') {
                $map->stringReplace('../images/', '/assets/ext/resources/images/');
            } else if ($pathType == 'mediaelement') {
                $map->stringReplace('url(', 'url(/assets/mediaelement/build/');
            }

            $map->save("$buildFile.min.js.map.json", "$buildFile.min.js"); //adds last extension
            unset($map);

            $trlElements = array();
            $useTrl = !in_array($pathType, array('ext', 'ext4', 'extensible', 'ravenJs', 'jquery', 'tinymce', 'mediaelement', 'mustache', 'modernizr'));
            if ($useTrl) $trlElements = Kwf_Trl::getInstance()->parse($contents, 'js');
            file_put_contents("$buildFile.min.js.trl", serialize($trlElements));

            file_put_contents("$buildFile.buildtime", filemtime($this->getAbsoluteFileName()));
        }

        $this->_contentsCacheSourceMap = file_get_contents("$buildFile.min.js.map.json");
        $this->_contentsCache = file_get_contents("$buildFile.min.js");
        $this->_parsedElementsCache = unserialize(file_get_contents("$buildFile.min.js.trl"));

        return array(
            'contents' => $this->_contentsCache,
            'sourceMap' => $this->_contentsCacheSourceMap,
            'trlElements' => $this->_parsedElementsCache
        );
    }

    private function _getCompliedContents()
    {
        if (!isset($this->_contentsCache)) {
            $this->warmupCaches();
        }
        return array(
            'contents' => $this->_contentsCache,
            'sourceMap' => $this->_contentsCacheSourceMap,
            'trlElements' => $this->_parsedElementsCache
        );
    }

    protected function _getContents($language, $pack)
    {
        if ($pack) {
            $ret = $this->_getCompliedContents();
        } else {
            $contents = $this->_getRawContents(null);
            $ret = array(
                'contents' => $contents,
                'trlElements' => Kwf_Trl::getInstance()->parse($contents, 'js')
            );
            unset($contents);
        }

        if ($ret['trlElements']) {

            if (isset($ret['sourceMap'])) {
                $buildFile = "cache/assets/".$this->getFileNameWithType().'-'.$language;
                $dir = substr($buildFile, 0, strrpos($buildFile, '/'));
                if (!file_exists($dir)) mkdir($dir, 0777, true);

                if (file_exists($buildFile)) {
                    $ret = array(
                        'contents' => file_get_contents($buildFile),
                        'sourceMap' => file_get_contents("$buildFile.map"),
                    );
                } else {
                    $map = new Kwf_Assets_Util_SourceMap($ret['sourceMap'], $ret['contents']);
                    foreach ($this->_getTrlReplacements($ret, $language) as $value) {
                        $map->stringReplace($value['search'], $value['replace']);
                    }
                    $map->save("$buildFile.map", $buildFile);
                    unset($map);
                }
            } else {
                foreach ($this->_getTrlReplacements($ret, $language) as $value) {
                    $ret['contents'] = str_replace($value['search'], $value['replace'], $ret['contents']);
                }
                unset($ret['trlElements']);
            }
        }

        return $ret;
    }

    private function _getTrlReplacements($ret, $language)
    {
        static $jsLoader;
        if (!isset($jsLoader)) $jsLoader = new Kwf_Trl_JsLoader();
        $replacements = $jsLoader->getReplacements($ret['trlElements'], $language);
        $replacements = array_merge($replacements, $this->_getHelpReplacements($ret['contents'], $language));
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
        return $c['contents'];
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
        return $c['contents'];
    }

    public final function getContentsPackedSourceMap($language)
    {
        $c = $this->_getContents($language, true);
        return $c['sourceMap'];
    }
}
