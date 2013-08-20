<?php
class Kwf_Assets_Loader
{
    /**
     * @var Kwf_Assets_Dependencies
     */
    private $_dep = null;
    private $_config = null;
    private $_scssParser = null;
    private $_scssParserOptions = null;

    static public function load()
    {
        if (!isset($_SERVER['REQUEST_URI'])) return;
        require_once 'Kwf/Loader.php';
        $baseUrl = Kwf_Setup::getBaseUrl();
        if (substr($_SERVER['REQUEST_URI'], 0, strlen($baseUrl)+8)==$baseUrl.'/assets/') {
            $url = substr($_SERVER['REQUEST_URI'], strlen($baseUrl)+8);
            if (strpos($url, '?') !== false) {
                $url = substr($url, 0, strpos($url, '?'));
            }
            try {
                $l = new self();
                $out = $l->getFileContents($url);
                Kwf_Media_Output::output($out);
            } catch (Kwf_Assets_NotFoundException $e) {
                throw new Kwf_Exception_NotFound();
            }
        }
    }

    public function __construct($config = null)
    {
        $this->_config = $config;
    }

    public function getConfig()
    {
        return $this->_config;
    }

    /**
     * der host muss ich der CacheId vorkommen wegen Google Maps API keys
     * die pro domain unterschiedlich sein müssen
     */
    private function _getHostForCacheId()
    {
        if (isset($_SERVER['HTTP_HOST'])) {
            $host = $_SERVER['HTTP_HOST'];
        } else {
            $host = $this->_getConfig()->server->domain;
        }
        if (preg_match('#[^\.]+\.[^\.]+$#', $host, $m)) {
            $host = $m[0];
        }
        $host = str_replace(array('.', '-', ':'), array('', '', ''), $host);
        return $host;
    }

    public function getFileContents($file, $language = null)
    {
        $ret = array();
        if (substr($file, 0, 8) == 'dynamic/') {
            if (!preg_match('#^dynamic/([a-z0-9_:]+)/(([a-z0-9_]+)/)?([a-z0-9_:]+)$#i', $file, $m)) {
                throw new Kwf_Exception_NotFound();
            }
            $assetsType = $m[1];
            $rootComponent = $m[3];
            $m = explode(':', $m[4]);
            $assetClass = array_shift($m);
            $arguments = $m;
            if (!Kwf_Loader::isValidClass($assetClass) || !is_instance_of($assetClass, 'Kwf_Assets_Dynamic_Interface')) {
                throw new Kwf_Exception_NotFound();
            }
            if ($rootComponent && (!Kwf_Loader::isValidClass($rootComponent) || !is_instance_of($rootComponent, 'Kwc_Abstract'))) {
                throw new Kwf_Exception_NotFound();
            }
            $file = new $assetClass($this, $assetsType, $rootComponent, $arguments);
            $ret = array();
            $ret['contents'] = $file->getContents();
            $ret['mtime'] = $file->getMTime();
            $ret['mtimeFiles'] = $file->getMTimeFiles();
            if ($file->getType() == 'js') {
                $ret['mimeType'] = 'text/javascript; charset=utf8';
            } else if ($file->getType() == 'css' || $file->getType() == 'printcss') {
                $ret['mimeType'] = 'text/css; charset=utf8';
            } else {
                throw new Kwf_Exception("Unknown type");
            }
        } else if (substr($file, 0, 4) == 'all/') {
            $encoding = Kwf_Media_Output::getEncoding();
            $cacheId = str_replace(array('/', '.', ':'), '_', $file).'_enc'.$encoding.$this->_getHostForCacheId();
            $cache = Kwf_Assets_Cache::getInstance();
            $cacheData = $cache->load($cacheId);
            if ($cacheData) {
                if ($cacheData['maxFileMTime'] != $this->getDependencies()->getMaxFileMTime()) {
                    $cacheData = false;
                }
            }
            if (!$cacheData) {
                Kwf_Benchmark::count('load asset all');
                if (strpos($file, '?') !== false) {
                    $file = substr($file, 0, strpos($file, '?'));
                }
                if (!preg_match('#^all/([a-z0-9]+)/(([a-z0-9_]+)/)?([a-zA-Z_]+)/([a-z0-9_:]+)\\.(printcss|js|css)$#i', $file, $m)) {
                    throw new Kwf_Exception_NotFound("Invalid Url '$file'");
                }
                $section = $m[1];
                $rootComponent = $m[3];
                $language = $m[4];
                $assetsType = $m[5];
                $fileType = $m[6];
                if ($rootComponent) {
                    if (!Kwf_Loader::isValidClass($rootComponent) || !is_instance_of($rootComponent, 'Kwc_Abstract')) {
                        throw new Kwf_Exception_NotFound("Invalid root component '$rootComponent'");
                    } else {
                        Kwf_Component_Data_Root::setComponentClass($rootComponent);
                    }
                }

                if (substr($assetsType, -5) == 'Debug' && !$this->_getConfig()->debug->menu) {
                    throw new Kwf_Exception("Debug Assets are not avaliable as the debug menu is disabled");
                }

                $cacheData  = array();

                $cacheData['contents'] = '';
                foreach ($this->_getDep()->getAssetFiles($assetsType, $fileType, $section, $rootComponent) as $file) {
                    if (!(substr($file, 0, 7) == 'http://' || substr($file, 0, 8) == 'https://' || substr($file, 0, 1) == '/')) {
                        if (substr($file, 0, 8) == 'dynamic/') {
                            $arguments = explode(':', substr($file, 8));
                            $file = array_shift($arguments);
                            if (!Kwf_Loader::isValidClass($file) || !is_instance_of($file, 'Kwf_Assets_Dynamic_Interface')) {
                                throw new Kwf_Exception_NotFound();
                            }
                            $file = new $file($this, $assetsType, $rootComponent, $arguments);
                            if (!$file->getIncludeInAll()) continue;
                            $c = array();
                            $c['contents'] = $file->getContents();
                        } else {
                            try {
                                $c = $this->getFileContents($file, $language);
                            } catch (Kwf_Assets_NotFoundException $e) {
                                throw new Kwf_Exception($file.': '.$e->getMessage());
                            }
                        }
                        $cacheData['contents'] .=  $c['contents']."\n";
                    }
                }
                $cacheData['contents'] = $this->pack($cacheData['contents'], $fileType);
                $cacheData['contents'] = Kwf_Media_Output::encode($cacheData['contents'], $encoding);
                $cacheData['maxFileMTime'] = $this->getDependencies()->getMaxFileMTime();
                if ($fileType == 'js') {
                    $cacheData['mimeType'] = 'text/javascript; charset=utf8';
                } else if ($fileType == 'css' || $fileType == 'printcss') {
                    $cacheData['mimeType'] = 'text/css; charset=utf8';
                }

                //store list of generated all caches for clear-cache-watcher
                file_put_contents('cache/assets/generated-all', $cacheId."\n", FILE_APPEND);

                $cache->save($cacheData, $cacheId);
            }
            $ret['mtime'] = time();
            $ret['contents'] = $cacheData['contents'];
            $ret['mimeType'] = $cacheData['mimeType'];
            $ret['encoding'] = $encoding;
        } else {
            if (substr($file, -4)=='.gif') {
                $ret['mimeType'] = 'image/gif';
            } else if (substr($file, -4)=='.png') {
                $ret['mimeType'] = 'image/png';
            } else if (substr($file, -4)=='.jpg') {
                $ret['mimeType'] = 'image/jpeg';
            } else if (substr($file, -4)=='.mp4') {
                $ret['mimeType'] = 'video/mp4';
            } else if (substr($file, -5)=='.webm') {
                $ret['mimeType'] = 'video/webm';
            } else if (substr($file, -4)=='.css' || substr($file, -5)=='.scss') {
                $ret['mimeType'] = 'text/css; charset=utf-8';
            } else if (substr($file, -9)=='.printcss') {
                $ret['mimeType'] = 'text/css; charset=utf-8';
            } else if (substr($file, -3)=='.js') {
                $ret['mimeType'] = 'text/javascript; charset=utf-8';
            } else if (substr($file, -4)=='.swf') {
                $ret['mimeType'] = 'application/flash';
            } else if (substr($file, -4)=='.ico') {
                $ret['mimeType'] = 'image/x-icon';
            } else if (substr($file, -5)=='.html') {
                $ret['mimeType'] = 'text/html; charset=utf-8';
            } else if (substr($file, -4)=='.otf') { // für Schriften
                $ret['mimeType'] = 'application/octet-stream';
            } else if (substr($file, -4)=='.eot') { // für Schriften
                $ret['mimeType'] = 'application/vnd.ms-fontobject';
            } else if (substr($file, -4)=='.svg') { // für Schriften
                $ret['mimeType'] = 'image/svg+xml';
            } else if (substr($file, -4)=='.ttf') { // für Schriften
                $ret['mimeType'] = 'application/octet-stream';
            } else if (substr($file, -5)=='.woff') { // für Schriften
                $ret['mimeType'] = 'application/x-woff';
            } else if (substr($file, -4)=='.htc') { // für ie css3
                $ret['mimeType'] = 'text/x-component';
            } else if (substr($file, -4)=='.pdf') {
                $ret['mimeType'] = 'application/pdf';
            } else {
                throw new Kwf_Assets_NotFoundException("Invalid filetype ($file)");
            }

            if (substr($ret['mimeType'], 0, 5) == 'text/') { //nur texte cachen
                if (!$language) {
                    $language = Kwf_Trl::getInstance()->getTargetLanguage();
                }

                $cache = Kwf_Assets_Cache::getInstance();
                $section = substr($file, 0, strpos($file, '-'));
                if (!$section) $section = 'web';
                $cacheId  = 'fileContents'.$section;
                if (substr($ret['mimeType'], 0, 15) == 'text/javascript') {
                    //cache javascript per language for trl calls and host for eg. Kwf_Assets_GoogleMapsApiKey
                    $cacheId .= $language.$this->_getHostForCacheId();
                }
                $cacheId .= str_replace(array('/', '\\', '.', '-', ':'), '_', $file);
                $cacheData = $cache->load($cacheId);
                if ($cacheData) {
                    if ($cacheData['maxFileMTime'] != $this->getDependencies()->getMaxFileMTime()) {
                        $cacheData = false;
                    }
                }
                if (!$cacheData) {
                    Kwf_Benchmark::count('load asset');
                    $cacheData['contents'] = file_get_contents($this->_getDep()->getAssetPath($file));
                    $cacheData['mtimeFiles'] = array($this->_getDep()->getAssetPath($file));
                    if ((substr($file, 0, strlen($section)+5)==$section.'-ext/' || substr($file, 0, 4)=='ext/')
                        && substr($ret['mimeType'], 0, 5) == 'text/'
                    ) {
                        //hack um bei ext-css-dateien korrekte pfade für die bilder zu haben
                        $cacheData['contents'] = str_replace('../images/', '/assets/ext/resources/images/', $cacheData['contents']);
                    } else if ((substr($file, 0, strlen($section)+14)==$section.'-mediaelement/' || substr($file, 0, 13)=='mediaelement/')
                        && substr($ret['mimeType'], 0, 5) == 'text/'
                    ) {
                        //hack to get the correct paths for the mediaelement pictures
                        $cacheData['contents'] = str_replace('url(', 'url(/assets/mediaelement/build/', $cacheData['contents']);
                    }

                    $cacheData['contents'] = self::expandAssetVariables($cacheData['contents'], $section, $cacheData['mtimeFiles']);

                    $cacheData['maxFileMTime'] = $this->getDependencies()->getMaxFileMTime();
                    if (substr($ret['mimeType'], 0, 8) == 'text/css') {
                        $cssClass = $file;
                        if (substr($cssClass, 0, strlen($section)+5) == $section.'-web/') {
                            $cssClass = substr($cssClass, strlen($section)+5);
                        }
                        if (substr($cssClass, 0, strlen($section)+5) == $section.'-kwf/') {
                            $cssClass = substr($cssClass, strlen($section)+5);
                        }
                        if (substr($cssClass, 0, strlen($section)+6) == $section.'-vkwf/') {
                            $cssClass = substr($cssClass, strlen($section)+6);
                        }
                        if (substr($cssClass, 0, 11) == 'components/') {
                            $cssClass = substr($cssClass, 11);
                        }
                        if (substr($cssClass, 0, 7) == 'themes/') {
                            $cssClass = substr($cssClass, 7);
                        }
                        if (substr($cssClass, -4) == '.css') {
                            $cssClass = substr($cssClass, 0, -4);
                        }
                        if (substr($cssClass, -5) == '.scss') {
                            $cssClass = substr($cssClass, 0, -5);
                        }
                        if (substr($cssClass, -9) == '.printcss') {
                            $cssClass = substr($cssClass, 0, -9);
                        }
                        if (substr($cssClass, -10) == '/Component') {
                            $cssClass = substr($cssClass, 0, -10);
                        } else if (substr($cssClass, -7) == '/Master') {
                            $cssClass = substr($cssClass, 0, -7);
                            $cssClass = 'master'.$cssClass;
                        } else {
                            $cssClass = false;
                        }
                        if ($cssClass) {
                            $cssClass = str_replace('/', '', $cssClass);
                            $cssClass = strtolower(substr($cssClass, 0, 1)) . substr($cssClass, 1);
                            $cacheData['contents'] = str_replace('$cssClass', $cssClass, $cacheData['contents']);
                            $cacheData['contents'] = str_replace('.cssClass', '.'.$cssClass, $cacheData['contents']);
                        }

                        if (substr($file, -5)=='.scss') {
                            if (!$this->_scssParserOptions) {
                                $this->_scssParserOptions = array(
                                    'style' => 'compact',
                                    'cache' => false,
                                    'syntax' => 'scss',
                                    'debug' => true,
                                    'debug_info' => false,
                                    'load_path_functions' => array('Kwf_Util_SassParser::loadCallback'),
                                    'functions' => Kwf_Util_SassParser::getExtensionsFunctions(array('Compass', 'Susy', 'Kwf')),
                                    'extensions' => array('Compass', 'Susy', 'Kwf')
                                );
                            }
                            if (!$this->_scssParser) {
                                $this->_scssParser = new Kwf_Util_SassParser($this->_scssParserOptions);
                            }
                            $cacheData['contents'] = $this->_scssParser->toCss($cacheData['contents'], false);
                        }

                        if (Kwf_Config::getValue('assetsCacheUrl')) {
                            $url = Kwf_Config::getValue('assetsCacheUrl').'?web='.Kwf_Config::getValue('application.id').'&section='.Kwf_Setup::getConfigSection().'&url=';
                            $cacheData['contents'] = str_replace('url(\'/assets/', 'url(\''.$url.'assets/', $cacheData['contents']);
                            $cacheData['contents'] = str_replace('url(/assets/', 'url('.$url.'assets/', $cacheData['contents']);
                        } else if ($baseUrl = Kwf_Setup::getBaseUrl()) {
                            $cacheData['contents'] = preg_replace('#url\\((\s*[\'"]?)/assets/#', 'url($1'.$baseUrl.'/assets/', $cacheData['contents']);
                        }
                    }

                    if (substr($ret['mimeType'], 0, 15) == 'text/javascript') {
                        //Deprecated: sollte durch dynamische assets ersetzt werden
                        preg_match_all('#{([A-Za-z0-9_]+)::([A-Za-z0-9_]+)\\(\\)}#', $cacheData['contents'], $m);
                        foreach (array_keys($m[0]) as $i) {
                            $c = call_user_func(array($m[1][$i], $m[2][$i]), $this->_getDep(), $this);
                            $cacheData['contents'] = str_replace($m[0][$i], $c, $cacheData['contents']);
                            if (method_exists($m[1][$i], 'getMTimeFiles')) {
                               $cacheData['mtimeFiles'] = array_merge($cacheData['mtimeFiles'],
                                    call_user_func(array($m[1][$i], 'getMTimeFiles'), $this->_getDep()));
                            }
                        }

                        $cacheData['contents'] = str_replace(
                            '{$application.maxAssetsMTime}',
                            $this->getDependencies()->getMaxFileMTime(),
                            $cacheData['contents']);

                        $cacheData['contents'] = $this->_getJsLoader()->trlLoad($cacheData['contents'], $language);
                        $cacheData['contents'] = $this->_hlp($cacheData['contents'], $language);

                        if ($baseUrl = Kwf_Setup::getBaseUrl()) {
                            $cacheData['contents'] = preg_replace('#url\\((\s*[\'"]?)/assets/#', 'url($1'.$baseUrl.'/assets/', $cacheData['contents']);
                            $cacheData['contents'] = preg_replace('#([\'"])/(kwf|vkwf|admin|assets)/#', '$1'.$baseUrl.'/$2/', $cacheData['contents']);
                        }
                    }
                    $cache->save($cacheData, $cacheId);
                }
                $ret['contents'] = $cacheData['contents'];
                $ret['mtime'] = time();

            } else {
                $fx = substr($file, 0, strpos($file, '/'));
                if (substr($fx, 0, 3) == 'fx_') {
                    $cache = Kwf_Assets_Cache::getInstance();
                    $cacheId = 'fileContents'.str_replace(array('/', '.', '-', ':'), array('_', '_', '_', '_'), $file);
                    if (!$cacheData = $cache->load($cacheId)) {
                        if (substr($ret['mimeType'], 0, 6) != 'image/') {
                            throw new Kwf_Exception("Fx is only possible for images");
                        }
                        $im = new Imagick();
                        if (substr($file, -4)=='.ico') $im->setFormat('ico'); //required because imagick can't autodetect ico format
                        $im->readImage($this->_getDep()->getAssetPath($file));
                        $fx = explode('_', substr($fx, 3));
                        foreach ($fx as $i) {
                            $params = array();
                            if (($pos = strpos($i, '-')) !== false) {
                                $params = explode('-', substr($i, $pos + 1));
                                $i = substr($i, 0, $pos);
                            }
                            call_user_func(array('Kwf_Assets_Effects', $i), $im, $params);
                        }
                        $cacheData['mtime'] = filemtime($this->_getDep()->getAssetPath($file));
                        $cacheData['mtimeFiles'] = array($this->_getDep()->getAssetPath($file));
                        $cacheData['contents'] = $im->getImagesBlob();;
                        $im->destroy();
                        $cache->save($cacheData, $cacheId);
                    }
                    $ret['contents'] = $cacheData['contents'];
                    $ret['mtime'] = time();
                } else {
                    $ret['mtime'] = time();
                    $ret['contents'] = file_get_contents($this->_getDep()->getAssetPath($file));
                }
            }
        }

        return $ret;
    }

    public static function expandAssetVariables($contents, $section, &$mtimeFiles = array())
    {
        static $assetVariables = array();
        if (!isset($assetVariables[$section])) {
            $assetVariables[$section] = Kwf_Config::getValueArray('assetVariables');
            if (file_exists('assetVariables.ini')) {
                $mtimeFiles[] = 'assetVariables.ini';
                $cfg = new Zend_Config_Ini('assetVariables.ini', $section);
                $assetVariables[$section] = array_merge($assetVariables[$section], $cfg->toArray());
            }
        }
        foreach ($assetVariables[$section] as $k=>$i) {
            $contents = preg_replace('#\\$'.preg_quote($k).'([^a-z0-9A-Z])#', "$i\\1", $contents); //deprecated syntax
            $contents = str_replace('var('.$k.')', $i, $contents);
        }
        return $contents;
    }

    private function _getConfig()
    {
        if (!isset($this->_config)) {
            $this->_config = Kwf_Registry::get('config');
        }
        return $this->_config;
    }

    private function _getDep()
    {
        if (!isset($this->_dep)) {
            $this->_dep = new Kwf_Assets_Dependencies($this);
        }
        return $this->_dep;
    }

    public function getDependencies()
    {
        return $this->_getDep();
    }

    private function _hlp($contents, $language)
    {
        //TODO 1902 $language verwenden
        $matches = array();
        preg_match_all("#hlp\(['\"](.+?)['\"]\)#", $contents, $matches);
        foreach ($matches[0] as $key => $search) {
            $r = hlp($matches[1][$key]);
            $r = str_replace(array("\n", "\r", "'"), array('\n', '', "\\'"), $r);
            $contents = str_replace($search, "'" . $r . "'", $contents);
        }
        return $contents;
    }

    public function pack($contents, $fileType)
    {
        if ($fileType == 'js') {
            $contents = str_replace("\r", "\n", $contents);

            // remove comments
            $contents = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $contents);
            // deaktiviert wg. urls mit http:// in hilfetexten $contents = preg_replace('!//[^\n]*!', '', $contents);

            // remove tabs, spaces, newlines, etc. - funktioniert nicht - da fehlen hinundwider ;
            //$contents = str_replace(array("\r", "\n", "\t"), "", $contents);

            // multiple whitespaces
            $contents = str_replace("\t", " ", $contents);
            $contents = preg_replace('/(\n)\n+/', '$1', $contents);
            $contents = preg_replace('/(\n)\ +/', '$1', $contents);
            $contents = preg_replace('/(\ )\ +/', '$1', $contents);

        } else if ($fileType == 'css') {

            $contents = str_replace("\r", "\n", $contents);

            // remove comments
            $contents = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $contents);

            // multiple whitespaces
            $contents = str_replace("\t", " ", $contents);
            $contents = preg_replace('/(\n)\n+/', '$1', $contents);
            $contents = preg_replace('/(\n)\ +/', '$1', $contents);
            $contents = preg_replace('/(\ )\ +/', '$1', $contents);
        }
        return $contents;
    }

    private function _getJsLoader()
    {
        static $ret;
        if (!isset($ret)) $ret = new Kwf_Trl_JsLoader();
        return $ret;
    }
}
