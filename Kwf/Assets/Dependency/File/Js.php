<?php
class Kwf_Assets_Dependency_File_Js extends Kwf_Assets_Dependency_File
{
    private $_parsedElementsCache;
    private $_contentsCache;

    public function getMimeType()
    {
        return 'text/javascript';
    }
//
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
        $fileName = $this->getFileNameWithType();

        $pathType = $this->getType();
        $useTrl = !in_array($pathType, array('ext2', 'ext', 'extensible', 'ravenJs', 'jquery', 'tinymce', 'mediaelement', 'mustache', 'modernizr'));
        $buildFile = sys_get_temp_dir().'/kwf-uglifyjs/'.$fileName.'.'.md5(file_get_contents($this->getAbsoluteFileName()));
        if (!file_exists("$buildFile.min.js")) {

            $dir = dirname($buildFile);
            if (!file_exists($dir)) mkdir($dir, 0777, true);
            file_put_contents($buildFile, $this->_getRawContents(null));
            $uglifyjs = "node ".dirname(dirname(dirname(dirname(dirname(__FILE__))))).'/node_modules/uglify-js/bin/uglifyjs';
            $cmd = "$uglifyjs ";
            $cmd .= "--source-map ".escapeshellarg("$buildFile.min.js.map.json").' ';
            $cmd .= "--prefix 2 ";
            $cmd .= "--output ".escapeshellarg("$buildFile.min.js").' ';
            $cmd .= escapeshellarg($buildFile);
            $cmd .= " 2>&1";
            $out = array();
            exec($cmd, $out, $retVal);
            if ($retVal) {
                throw new Kwf_Exception("uglifyjs failed: ".implode("\n", $out));
            }
            $contents = file_get_contents("$buildFile.min.js");
            $contents = str_replace("\n//# sourceMappingURL=$buildFile.min.js.map.json", '', $contents);

            $mapData = json_decode(file_get_contents("$buildFile.min.js.map.json"), true);
            if (count($mapData['sources']) > 1) {
                throw new Kwf_Exception("uglifyjs must not return multiple sources, ".count($mapData['sources'])." returned for '$this'");
            }
            unset($mapData['file']);
            $mapData['sources'][0] = $this->getFileNameWithType();
            file_put_contents("$buildFile.min.js.map.json", json_encode($mapData));

            $map = new Kwf_SourceMaps_SourceMap(file_get_contents("$buildFile.min.js.map.json"), $contents);

            if (strpos($contents, '.cssClass') !== false) {
                $cssClass = $this->_getComponentCssClass();
                if ($cssClass) {
                    if (preg_match_all('#([\'"])\.cssClass([\s\'"\.])#', $contents, $m)) {
                        foreach ($m[0] as $k=>$i) {
                            $map->stringReplace($i, $m[1][$k].'.'.$cssClass.$m[2][$k]);
                        }
                    }
                }
            }

            if ($pathType == 'ext2') {
                $map->stringReplace('../images/', '/assets/ext2/resources/images/');
            } else if ($pathType == 'mediaelement') {
                $map->stringReplace('url(', 'url(/assets/mediaelement/build/');
            }


            $map->save("$buildFile.min.js.map.json", "$buildFile.min.js"); //adds last extension
            unset($map);

            if ($useTrl) {
                $trlElements = Kwf_Trl::getInstance()->parse($contents, 'js');
                file_put_contents("$buildFile.min.js.trl", serialize($trlElements));
            }
        }

        $this->_contentsCacheSourceMap = file_get_contents("$buildFile.min.js.map.json");
        $this->_contentsCache = file_get_contents("$buildFile.min.js");
        if ($useTrl) {
            $this->_parsedElementsCache = unserialize(file_get_contents("$buildFile.min.js.trl"));
        } else {
            $this->_parsedElementsCache = array();
        }

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
                $dir = dirname($buildFile);
                if (!file_exists($dir)) mkdir($dir, 0777, true);

                if (!file_exists("$buildFile.buildtime") || filemtime($this->getAbsoluteFileName()) != file_get_contents("$buildFile.buildtime")) {
                    $map = new Kwf_SourceMaps_SourceMap($ret['sourceMap'], $ret['contents']);
                    foreach ($this->_getTrlReplacements($ret, $language) as $value) {
                        $map->stringReplace($value['search'], $value['replace']);
                    }
                    $map->save("$buildFile.map", $buildFile);
                    unset($map);
                    file_put_contents("$buildFile.buildtime", filemtime($this->getAbsoluteFileName()));
                }
                $ret = array(
                    'contents' => file_get_contents($buildFile),
                    'sourceMap' => file_get_contents("$buildFile.map"),
                );
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
