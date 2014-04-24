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

    protected function _getContents($language, $pack)
    {
        $pathType = substr($this->_fileName, 0, strpos($this->_fileName, '/'));
        $useTrl = !in_array($pathType, array('ext', 'ext4', 'extensible', 'ravenJs', 'jquery', 'tinymce', 'mediaelement', 'mustache', 'modernizr'));

        if (isset($this->_contentsCache) && $pack) {
            $ret = $this->_contentsCache;
        } else {

            $ret = parent::getContents($language);

            //TODO same code is in in File_Css too
            if ($pathType == 'ext') {
                //hack um bei ext-css-dateien korrekte pfade für die bilder zu haben
                $ret = str_replace('../images/', '/assets/ext/resources/images/', $ret);
            } else if ($pathType == 'mediaelement') {
                //hack to get the correct paths for the mediaelement pictures
                $ret = str_replace('url(', 'url(/assets/mediaelement/build/', $ret);
            }

            if ($baseUrl = Kwf_Setup::getBaseUrl()) {
                $ret = preg_replace('#url\\((\s*[\'"]?)/assets/#', 'url($1'.$baseUrl.'/assets/', $ret);
                $ret = preg_replace('#([\'"])/(kwf|vkwf|admin|assets)/#', '$1'.$baseUrl.'/$2/', $ret);
            }

            $cssClass = $this->_getComponentCssClass();
            if ($cssClass) {
                $ret = preg_replace('#\'\.cssClass([\s\'\.])#', '\'.'.$cssClass.'$1', $ret);
            }

            if ($pack) {
                $ret = self::pack($ret);
                $this->_contentsCache = $ret;
            }

            if ($useTrl) {
                //Kwf_Trl::parse is very slow, try to cache it
                //this mainly helps during development when ccw clears the assets cache but this cache stays
                static $cache;
                if (!isset($cache)) {
                    $cache = new Zend_Cache_Core(array(
                        'lifetime' => null,
                        'automatic_serialization' => true,
                        'automatic_cleaning_factor' => 0,
                        'write_control' => false,
                    ));
                    $cache->setBackend(new Zend_Cache_Backend_File(array(
                        'cache_dir' => 'cache/assets',
                        'cache_file_umask' => 0666,
                        'hashed_directory_umask' => 0777,
                        'hashed_directory_level' => 2,
                    )));
                }
                $cacheId = 'trlParsedElements'.str_replace(array('\\', ':', '/', '.', '-'), '_', $this->_fileName);
                $cacheData = $cache->load($cacheId);
                if ($cacheData) {
                    if ($cacheData['mtime'] != filemtime($this->getFileName())) {
                        $cacheData = false;
                    }
                }
                if (!$cacheData) {
                    $cacheData = array(
                        'contents' => Kwf_Trl::getInstance()->parse($ret, 'js'),
                        'mtime' => filemtime($this->getFileName())
                    );
                    $cache->save($cacheData, $cacheId);
                }
                $this->_parsedElementsCache = $cacheData['contents'];
            }
        }

        if ($useTrl) {
            static $jsLoader;
            if (!isset($jsLoader)) $jsLoader = new Kwf_Trl_JsLoader();

            $ret = $jsLoader->trlLoad($ret, $this->_parsedElementsCache, $language);
            $ret = $this->_hlp($ret, $language);
        }

        return $ret;
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
        return $this->_getContents($language, false);
    }

    private function _hlp($contents, $language)
    {
        $matches = array();
        preg_match_all("#hlp\(['\"](.+?)['\"]\)#", $contents, $matches);
        foreach ($matches[0] as $key => $search) {
            $r = Zend_Registry::get('hlp')->hlp($matches[1][$key], $language);
            $r = str_replace(array("\n", "\r", "'"), array('\n', '', "\\'"), $r);
            $contents = str_replace($search, "'" . $r . "'", $contents);
        }
        return $contents;
    }

    public final function getContentsPacked($language)
    {
        return $this->_getContents($language, true);
    }
}
