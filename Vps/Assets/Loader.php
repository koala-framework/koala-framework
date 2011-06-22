<?php
class Vps_Assets_Loader
{
    /**
     * @var Vps_Assets_Dependencies
     */
    private $_dep = null;
    private $_config = null;

    static public function load()
    {
        if (!isset($_SERVER['REQUEST_URI'])) return;
        require_once 'Vps/Loader.php';
        if (substr($_SERVER['REQUEST_URI'], 0, 8)=='/assets/') {
            $url = substr($_SERVER['REQUEST_URI'], 8);
            if (strpos($url, '?') !== false) {
                $url = substr($url, 0, strpos($url, '?'));
            }

            try {
                $l = new self();
                Vps_Media_Output::output($l->getFileContents($url));
            } catch (Vps_Assets_NotFoundException $e) {
                throw new Vps_Exception_NotFound();
            }
        }
    }

    public function __construct($config = null)
    {
        if (!$config) $config = Vps_Registry::get('config');
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
                throw new Vps_Exception_NotFound();
            }
            $assetsType = $m[1];
            $rootComponent = $m[3];
            $m = explode(':', $m[4]);
            $assetClass = array_shift($m);
            $arguments = $m;
            if (!class_exists($assetClass) || !is_instance_of($assetClass, 'Vps_Assets_Dynamic_Interface')) {
                throw new Vps_Exception_NotFound();
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
                throw new Vps_Exception("Unknown type");
            }
        } else if (substr($file, 0, 4) == 'all/') {
            $encoding = Vps_Media_Output::getEncoding();
            $cacheId = md5($file.$encoding.$this->_getHostForCacheId());
            $cache = Vps_Assets_Cache::getInstance();
            $cacheData = $cache->load($cacheId);
            if ($cacheData) {
                if ($cacheData['maxFileMTime'] != $this->getDependencies()->getMaxFileMTime()) {
                    $cacheData = false;
                }
            }
            if (!$cacheData) {
                Vps_Benchmark::count('load asset all');
                if (strpos($file, '?') !== false) {
                    $file = substr($file, 0, strpos($file, '?'));
                }
                if (!preg_match('#^all/([a-z0-9]+)/(([a-z0-9_]+)/)?([a-z]+)/([a-z0-9_:]+)\\.(printcss|js|css)$#i', $file, $m)) {
                    throw new Vps_Exception_NotFound("Invalid Url '$file'");
                }
                $section = $m[1];
                $rootComponent = $m[3];
                $language = $m[4];
                $assetsType = $m[5];
                $fileType = $m[6];
                Vps_Component_Data_Root::setComponentClass($rootComponent);

                if (substr($assetsType, -5) == 'Debug' && !$this->_getConfig()->debug->menu) {
                    throw new Vps_Exception("Debug Assets are not avaliable as the debug menu is disabled");
                }

                $cacheData  = array();

                $cacheData['contents'] = '';
                $cacheData['mtimeFiles'] = array();
                foreach ($this->_getDep()->getAssetFiles($assetsType, $fileType, $section, $rootComponent) as $file) {
                    if (!(substr($file, 0, 7) == 'http://' || substr($file, 0, 8) == 'https://' || substr($file, 0, 1) == '/')) {
                        if (substr($file, 0, 8) == 'dynamic/') {
                            $arguments = explode(':', substr($file, 8));
                            $file = array_shift($arguments);
                            if (!is_instance_of($file, 'Vps_Assets_Dynamic_Interface')) {
                                throw new Vps_Exception_NotFound();
                            }
                            $file = new $file($this, $assetsType, $rootComponent, $arguments);
                            if (!$file->getIncludeInAll()) continue;
                            $c = array();
                            $c['contents'] = $file->getContents();
                            $c['mtimeFiles'] = $file->getMTimeFiles();
                        } else {
                            try {
                                $c = $this->getFileContents($file, $language);
                            } catch (Vps_Assets_NotFoundException $e) {
                                throw new Vps_Exception($file.': '.$e->getMessage());
                            }
                        }
                        $cacheData['contents'] .=  $c['contents']."\n";
                        $cacheData['mtimeFiles'] = array_merge($cacheData['mtimeFiles'], $c['mtimeFiles']);
                    }
                }
                $cacheData['contents'] = $this->pack($cacheData['contents'], $fileType);
                $cacheData['contents'] = Vps_Media_Output::encode($cacheData['contents'], $encoding);
                $cacheData['maxFileMTime'] = $this->getDependencies()->getMaxFileMTime();
                if ($fileType == 'js') {
                    $cacheData['mimeType'] = 'text/javascript; charset=utf8';
                } else if ($fileType == 'css' || $fileType == 'printcss') {
                    $cacheData['mimeType'] = 'text/css; charset=utf8';
                }
                $cache->save($cacheData, $cacheId);
            }
            $ret['mtime'] = $cacheData['mtime'];
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
            } else if (substr($file, -4)=='.css') {
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
            } else {
                throw new Vps_Assets_NotFoundException("Invalid filetype ($file)");
            }

            if (substr($ret['mimeType'], 0, 5) == 'text/') { //nur texte cachen
                if (!$language) {
                    $language = Vps_Trl::getInstance()->getTargetLanguage();
                }

                $cache = Vps_Assets_Cache::getInstance();
                $section = substr($file, 0, strpos($file, '-'));
                if (!$section) $section = 'web';
                $cacheId = 'fileContents'.$language.$section.$this->_getHostForCacheId().
                    str_replace(array('/', '.', '-', ':'), array('_', '_', '_', '_'), $file).
                    Vps_Component_Data_Root::getComponentClass();
                $cacheData = $cache->load($cacheId);
                if ($cacheData) {
                    if ($cacheData['maxFileMTime'] != $this->getDependencies()->getMaxFileMTime()) {
                        $cacheData = false;
                    }
                }
                if (!$cacheData) {
                    Vps_Benchmark::count('load asset');
                    $cacheData['contents'] = file_get_contents($this->_getDep()->getAssetPath($file));
                    $cacheData['mtimeFiles'] = array($this->_getDep()->getAssetPath($file));
                    if ((substr($file, 0, strlen($section)+5)==$section.'-ext/' || substr($file, 0, 4)=='ext/')
                        && substr($ret['mimeType'], 0, 5) == 'text/'
                    ) {
                        //hack um bei ext-css-dateien korrekte pfade für die bilder zu haben
                        $cacheData['contents'] = str_replace('../images/', '/assets/ext/resources/images/', $cacheData['contents']);
                    }

                    if (file_exists('application/assetVariables.ini')) {
                        $cacheData['mtimeFiles'][] = 'application/assetVariables.ini';
                        static $assetVariables = array();
                        if (!isset($assetVariables[$section])) {
                            $assetVariables[$section] = new Zend_Config_Ini('application/assetVariables.ini', $section);
                        }
                        foreach ($assetVariables[$section] as $k=>$i) {
                            $cacheData['contents'] = preg_replace('#\\$'.preg_quote($k).'([^a-z0-9A-Z])#', "$i\\1", $cacheData['contents']);
                            $cacheData['contents'] = str_replace('var('.$k.')', $i, $cacheData['contents']);
                        }
                    }
                    $cacheData['maxFileMTime'] = $this->getDependencies()->getMaxFileMTime();
                    if (substr($ret['mimeType'], 0, 8) == 'text/css') {
                        $cssClass = $file;
                        if (substr($cssClass, 0, strlen($section)+5) == $section.'-web/') {
                            $cssClass = substr($cssClass, strlen($section)+5);
                        }
                        if (substr($cssClass, 0, strlen($section)+5) == $section.'-vps/') {
                            $cssClass = substr($cssClass, strlen($section)+5);
                        }
                        if (substr($cssClass, -4) == '.css') {
                            $cssClass = substr($cssClass, 0, -4);
                        }
                        if (substr($cssClass, -9) == '.printcss') {
                            $cssClass = substr($cssClass, 0, -9);
                        }
                        if (substr($cssClass, -10) == '/Component') {
                            $cssClass = substr($cssClass, 0, -10);
                        }
                        $cssClass = str_replace('/', '', $cssClass);
                        $cssClass = strtolower(substr($cssClass, 0, 1)) . substr($cssClass, 1);
                        $cacheData['contents'] = str_replace('$cssClass', $cssClass, $cacheData['contents']);
                        $cacheData['contents'] = str_replace('.cssClass', '.'.$cssClass, $cacheData['contents']);
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
                    }
                    $cache->save($cacheData, $cacheId);
                }
                $ret['contents'] = $cacheData['contents'];
                $ret['mtime'] = $cacheData['mtime'];
                $ret['mtimeFiles'] = $cacheData['mtimeFiles'];

            } else {
                $fx = substr($file, 0, strpos($file, '/'));
                if (substr($fx, 0, 3) == 'fx_') {
                    $cache = Vps_Assets_Cache::getInstance();
                    $cacheId = 'fileContents'.str_replace(array('/', '.', '-', ':'), array('_', '_', '_', '_'), $file);
                    if (!$cacheData = $cache->load($cacheId)) {
                        if (substr($ret['mimeType'], 0, 6) != 'image/') {
                            throw new Vps_Exception("Fx is only possible for images");
                        }
                        $im = new Imagick();
                        $im->readImage($this->_getDep()->getAssetPath($file));
                        $fx = explode('_', substr($fx, 3));
                        foreach ($fx as $i) {
                            call_user_func(array('Vps_Assets_Effects', $i), $im);
                        }
                        $cacheData['mtime'] = filemtime($this->_getDep()->getAssetPath($file));
                        $cacheData['mtimeFiles'] = array($this->_getDep()->getAssetPath($file));
                        $cacheData['contents'] = $im->getImageBlob();;
                        $im->destroy();
                        $cache->save($cacheData, $cacheId);
                    }
                    $ret['contents'] = $cacheData['contents'];
                    $ret['mtime'] = $cacheData['mtime'];
                    $ret['mtimeFiles'] = $cacheData['mtimeFiles'];
                } else {
                    $ret['mtime'] = filemtime($this->_getDep()->getAssetPath($file));
                    $ret['mtimeFiles'] = array($this->_getDep()->getAssetPath($file));
                    $ret['contents'] = file_get_contents($this->_getDep()->getAssetPath($file));
                }
            }
        }

        return $ret;
    }

    private function _getConfig()
    {
        if (!isset($this->_config)) {
            $this->_config = Vps_Registry::get('config');
        }
        return $this->_config;
    }

    private function _getDep()
    {
        if (!isset($this->_dep)) {
            $this->_dep = new Vps_Assets_Dependencies($this);
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
        if (!isset($ret)) $ret = new Vps_Trl_JsLoader();
        return $ret;
    }
}
