<?php
class Vps_Assets_Dependencies
{
    private $_files = array();
    private $_config;
    private $_dependenciesConfig;
    private $_processedDependencies = array();
    private $_processedComponents = array();
    /**
     * @param string Assets-Typ, Frontend od. Admin
     **/
    public function __construct()
    {
        $this->_config = Vps_Registry::get('config');
    }

    public function getAssetUrls($assetsType, $fileType = null)
    {
        $b = Vps_Benchmark::start();
        if ($this->_config->debug->menu) {
            $session = new Zend_Session_Namespace('debug');
            if (isset($session->enable) && $session->enable) {
                $assetsType .= 'Debug';
            }
        }
        $ret = array();
        if (!$this->_config->debug->assets->$fileType || (isset($session->$fileType) && !$session->$fileType)) {
            $v = $this->_config->application->version;
            $language = Zend_Registry::get('trl')->getTargetLanguage();
            $ret[] = "/assets/All$assetsType-$language.$fileType?v=$v";
            $allUsed = true;
        }

        foreach ($this->getAssetFiles($assetsType, $fileType) as $file) {
            if ($file instanceof Vps_Assets_Dynamic) {
                $file = $file->getFile();
            }
            if (substr($file, 0, 7) == 'http://' || substr($file, 0, 8) == 'https://' || substr($file, 0, 1) == '/') {
                $ret[] = $file;
            } else if (empty($allUsed)) {
                $ret[] = '/assets/'.$file;
            }
        }
        return $ret;
    }

    public function getAssetFiles($assetsType, $fileType = null)
    {
        if (!isset($this->_files[$assetsType])) {
            $cacheId = 'dependencies'.$assetsType;
            $cache = $this->_getCache();
            $mtime = $cache->test($cacheId);
            if (!$mtime) {
                Vps_Benchmark::info('Generate Dependencies Cache');
            }
            if (Vps_Registry::get('config')->debug->componentCache->checkComponentModification) {
                if (Zend_Controller_Front::getInstance() instanceof Vps_Controller_Front_Component) {
                    if ($mtime && $mtime < Vpc_Abstract::getSettingCacheMtime()) {
                        Vps_Benchmark::info('Regenerate Dependencies Cache (component settings changed)');
                        $mtime = false;
                    }
                }
                if ($mtime && $mtime < Vps_Registry::get('configMtime')) {
                    Vps_Benchmark::info('Regenerate Dependencies Cache (config.ini changed)');
                    $mtime = false;
                }
            }
            if (!$mtime) {
                $this->_files[$assetsType] = array();
                $assetsSection = $assetsType;
                if (!isset($this->_config->assets->$assetsType)) {
                    throw new Vps_Assets_NotFoundException("Unknown AssetsType '$assetsType'");
                }
                foreach ($this->_config->assets->$assetsType as $d=>$v) {
                    if ($v) {
                        $this->_processDependency($assetsType, $d);
                    }
                }
                $cache->save($this->_files[$assetsType], $cacheId);
            } else {
                $this->_files[$assetsType] = $cache->load($cacheId);
            }
        }

        if (is_null($fileType)) return $this->_files[$assetsType];

        $files = array();
        foreach ($this->_files[$assetsType] as $file) {
            if ((is_string($file) && substr($file, -strlen($fileType)) == $fileType)
                || ($file instanceof Vps_Assets_Dynamic && $file->getType() == $fileType)) {
                if (is_string($file) && substr($file, -strlen($fileType)-1) == " $fileType") {
                    //wenn asset hinten mit " js" aufhört das js abschneiden
                    //wird benötigt für googlemaps wo die js-dateien kein js am ende haben
                    $file = substr($file, 0, -strlen($fileType)-1);
                }
                $files[] = $file;
            }
        }

        //hack: übersetzung immer zuletzt anhängen
        if ($fileType == 'js') {
            $files[] = 'vps/Ext/ext-lang-en.js';
        }
        return $files;
    }

    private function _pack($contents, $fileType)
    {
        if ($fileType == 'js') {
            $contents = str_replace("\r", "\n", $contents);

            // remove comments
            $contents = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $contents);
            $contents = preg_replace('!//[^\n]*!', '', $contents);

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

    private function _getDependenciesConfig()
    {
        if (!isset($this->_dependenciesConfig)) {
            $this->_dependenciesConfig = new Zend_Config_Ini(VPS_PATH.'/config.ini', 'dependencies',
                                                array('allowModifications'=>true));
            $this->_dependenciesConfig->merge(new Zend_Config_Ini('application/config.ini', 'dependencies'));
        }
        return $this->_dependenciesConfig;
    }

    private function _processDependency($assetsType, $dependency)
    {
        if (in_array($assetsType.$dependency, $this->_processedDependencies)) return;
        $this->_processedDependencies[] = $assetsType.$dependency;
        if ($dependency == 'Components' || $dependency == 'ComponentsAdmin') {
            $rootComonent = $this->_config->vpc->rootComponent;
            $this->_processComponentDependency($assetsType, $rootComonent, $dependency == 'ComponentsAdmin');
            foreach ($this->_config->vpc->masterComponents as $c) {
                if ($c) {
                    $this->_processComponentDependency($assetsType, $c, $dependency == 'ComponentsAdmin');
                }
            }
            return;
        }
        if (!isset($this->_getDependenciesConfig()->$dependency)) {
            throw new Vps_Exception("Can't resolve dependency '$dependency'.");
        }
        $deps = $this->_getDependenciesConfig()->$dependency;

        if (isset($deps->dep)) {
            foreach ($deps->dep as $d) {
                $this->_processDependency($assetsType, $d);
            }
        }

        if (isset($deps->files)) {
            foreach ($deps->files as $file) {
                $this->_processDependencyFile($assetsType, $file);
            }
        }
        return;
    }
    private function _processComponentDependency($assetsType, $class, $includeAdminAssets)
    {
        if (in_array($assetsType.$class.$includeAdminAssets, $this->_processedComponents)) return;

        $assets = Vpc_Abstract::getSetting($class, 'assets');
        $assetsAdmin = array();
        if ($includeAdminAssets) {
            $assetsAdmin = Vpc_Abstract::getSetting($class, 'assetsAdmin');
        }
        $this->_processedComponents[] = $assetsType.$class.$includeAdminAssets;
        if (isset($assets['dep'])) {
            foreach ($assets['dep'] as $dep) {
                $this->_processDependency($assetsType, $dep);
            }
        }
        if (isset($assetsAdmin['dep'])) {
            foreach ($assetsAdmin['dep'] as $dep) {
                $this->_processDependency($assetsType, $dep);
            }
        }
        if (isset($assets['files'])) {
            foreach ($assets['files'] as $file) {
                $this->_processDependencyFile($assetsType, $file);
            }
        }
        if (isset($assetsAdmin['files'])) {
            foreach ($assetsAdmin['files'] as $file) {
                $this->_processDependencyFile($assetsType, $file);
            }
        }

        //alle css-dateien der vererbungshierache includieren
        $componentCssFiles = array();

        foreach (Vpc_Abstract::getParentClasses($class) as $c) {
            $curClass = $c;
            if (substr($curClass, -10) == '_Component') {
                $curClass = substr($curClass, 0, -10);
            }
            $curClass =  $curClass . '_Component';
            $file = str_replace('_', DIRECTORY_SEPARATOR, $curClass) . '.css';
            foreach ($this->_config->path as $type=>$dir) {
                if ($dir == '.') $dir = getcwd();
                $path = $dir . '/' . $file;
                if (is_file($path)) {
                    $f = $type . '/' . $file;
                    if (!in_array($f, $this->_files[$assetsType])) {
                        $componentCssFiles[] = $f;
                    }
                    break;
                }
            }
        }
        //reverse damit css von weiter unten in der vererbungshierachie überschreibt
        $this->_files[$assetsType] = array_merge($this->_files[$assetsType], array_reverse($componentCssFiles));

        $classes = Vpc_Abstract::getChildComponentClasses($class);
        $classes = array_merge($classes, Vpc_Abstract::getSetting($class, 'plugins'));
        foreach ($classes as $class) {
            if ($class) {
                $this->_processComponentDependency($assetsType, $class, $includeAdminAssets);
            }
        }
    }

    private function _processDependencyFile($assetsType, $file)
    {
        if (is_string($file) && substr($file, -2)=="/*") {
            $pathType = substr($file, 0, strpos($file, '/'));
            if (!isset($this->_config->path->$pathType)) {
                throw new Vps_Exception(trlVps("Assets-Path-Type '{0}' not found in config.", $pathType));
            }
            $file = substr($file, strpos($file, '/')); //pathtype abschneiden
            $file = substr($file, 0, -1); //* abschneiden
            $path = $this->_config->path->$pathType.$file;
            if (!file_exists($path)) {
                throw new Vps_Exception(trlVps("Path '{0}' does not exist.", $path));
            }
            $DirIterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path));
            foreach ($DirIterator as $file) {
                if (!preg_match('#/\\.svn/#', $file->getPathname())
                    && (substr($file->getPathname(), -3) == '.js'
                        || substr($file->getPathname(), -4) == '.css')) {
                    $f = $file->getPathname();
                    $f = substr($f, strlen($this->_config->path->$pathType));
                    $f = $pathType . $f;
                    if (!in_array($f, $this->_files[$assetsType])) {
                        $this->_files[$assetsType][] = $f;
                    }
                }
            }
        } else {
            if (!in_array($file, $this->_files[$assetsType])) {
                $this->_files[$assetsType][] = $file;
            }
        }
    }
    public function getAssetPath($url)
    {
        if (file_exists($url)) return $url;
        $paths = $this->_config->path;
        $type = substr($url, 0, strpos($url, '/'));
        $url = substr($url, strpos($url, '/')+1);
        if (!isset($paths->$type)) {
            throw new Vps_Assets_NotFoundException("Assets-Path-Type '$type' for url '$url' not found in config.");
        }
        $p = $paths->$type;
        if ($p == 'VPS_PATH') $p = VPS_PATH;
        if (!file_exists($p.'/'.$url)) {
            throw new Vps_Assets_NotFoundException("Assets '$url' not found");
        }
        return $p.'/'.$url;
    }

    public function getFileContents($file, $language)
    {
        $ret = array();
        if ($file == 'AllRteStyles.css') {
            $ret = Vpc_Basic_Text_StylesModel::getStylesContents();
        } else if (preg_match('#^All([a-z]+)\\-([a-z]+).(js|css)$#i', $file, $m)) {

            $section = $m[1];
            if (substr($section, -5) == 'Debug' && !$this->_config->debug->menu) {
                throw new Vps_Exception("Debug Assets are not avaliable as the debug menu is disabled");
            }
            $language = $m[2];
            if ($m[3] == 'js') {
                $fileType = 'js';
            } else {
                $fileType = 'css';
            }

            $encoding = Vps_Media_Output::getEncoding();
            $cache = $this->_getCache();
            $cacheId = $fileType.$encoding.$section.Vps_Setup::getConfigSection().$language;

            $cacheData = $cache->load($cacheId);
            if ($cacheData && $cacheData['version'] != $this->_config->application->version) {
                $cacheData = false;
            }
            if (!$cacheData) {
                $cacheData  = array();

                $cacheData['contents'] = '';
                $cacheData['mtimeFiles'] = array();
                foreach ($this->getAssetFiles($section, $fileType) as $file) {
                    if ($file instanceof Vps_Assets_Dynamic) {
                        $file = $file->getFile();
                    }
                    if (!(substr($file, 0, 7) == 'http://' || substr($file, 0, 8) == 'https://' || substr($file, 0, 1) == '/')) {
                        $c = $this->getFileContents($file, $language);
                        $cacheData['contents'] .=  $c['contents']."\n";
                        $cacheData['mtimeFiles'] = array_merge($cacheData['mtimeFiles'], $c['mtimeFiles']);
                    }
                }
                $cacheData['contents'] = $this->_pack($cacheData['contents'], $fileType);
                $cacheData['contents'] = Vps_Media_Output::encode($cacheData['contents'], $encoding);
                $cacheData['version'] = $this->_config->application->version;
                if ($fileType == 'js') {
                    $cacheData['mimeType'] = 'text/javascript; charset=utf8';
                } else if ($fileType == 'css') {
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
            } else if (substr($file, -3)=='.js') {
                $ret['mimeType'] = 'text/javascript; charset=utf-8';
            } else if (substr($file, -4)=='.swf') {
                $ret['mimeType'] = 'application/flash';
            } else if (substr($file, -4)=='.ico') {
                $ret['mimeType'] = 'image/x-icon';
            } else if (substr($file, -5)=='.html') {
                $ret['mimeType'] = 'text/html; charset=utf-8';
            } else {
                throw new Vps_Assets_NotFoundException("Invalid filetype");
            }

            if (substr($ret['mimeType'], 0, 5) == 'text/') { //nur texte cachen

                $cache = $this->_getCache();
                $cacheId = 'fileContents'.$language.str_replace(array('/', '.', '-'), array('_', '_', '_'), $file);
                if (!$cacheData = $cache->load($cacheId)) {
                    $cacheData['contents'] = file_get_contents($this->getAssetPath($file));
                    $cacheData['mtimeFiles'] = array($this->getAssetPath($file));
                    if (substr($file, 0, 4)=='ext/' && substr($ret['mimeType'], 0, 5) == 'text/') {
                        //hack um bei ext-css-dateien korrekte pfade für die bilder zu haben
                        $cacheData['contents'] = str_replace('../images/', '/assets/ext/resources/images/', $cacheData['contents']);
                    }

                    if (substr($ret['mimeType'], 0, 8) == 'text/css') {
                        static $cssConfig;
                        if (!isset($cssConfig)) {
                            try {
                                $cssConfig = new Zend_Config_Ini('application/config.ini', 'css');
                            } catch (Zend_Config_Exception $e) {
                                $cssConfig = array();
                            }
                        }
                        foreach ($cssConfig as $k=>$i) {
                            $cacheData['contents'] = preg_replace('#\\$'.preg_quote($k).'([^a-z0-9A-Z])#', "$i\\1", $cacheData['contents']);
                        }
                    }

                    if (substr($ret['mimeType'], 0, 15) == 'text/javascript') {
                        preg_match_all('#{([A-Za-z0-9_]+)::([A-Za-z0-9_]+)\\(\\)}#', $cacheData['contents'], $m);
                        foreach (array_keys($m[0]) as $i) {
                            $c = call_user_func(array($m[1][$i], $m[2][$i]), $this);
                            $cacheData['contents'] = str_replace($m[0][$i], $c, $cacheData['contents']);
                            if (method_exists($m[1][$i], 'getMTimeFiles')) {
                               $cacheData['mtimeFiles'] = array_merge($cacheData['mtimeFiles'],
                                    call_user_func(array($m[1][$i], 'getMTimeFiles'), $this));
                            }
                        }

                        $version = Vps_Registry::get('config')->application->version;
                        $cacheData['contents'] = str_replace('{$application.version}', $version, $cacheData['contents']);

                        $cacheData['contents'] = $this->_trl($cacheData['contents'], $language);
                        $cacheData['contents'] = $this->_hlp($cacheData['contents'], $language);
                    }
                    $cache->save($cacheData, $cacheId);
                }
                $ret['contents'] = $cacheData['contents'];
                $ret['mtime'] = $cacheData['mtime'];
                $ret['mtimeFiles'] = $cacheData['mtimeFiles'];

            } else {
                $cacheData = false;
                $ret['contents'] = file_get_contents($this->getAssetPath($file));
                $ret['mtime'] = filemtime($this->getAssetPath($file));
                $ret['mtimeFiles'] = array($this->getAssetPath($file));
            }
        }

        return $ret;
    }

    private function _hlp($contents, $language)
    {
        //TODO 1902 $language verwenden
        $matches = array();
        preg_match_all("#hlp\('(.*)'\)#", $contents, $matches);
        foreach ($matches[0] as $key => $search) {
            $r = hlp($matches[1][$key]);
            $r = str_replace(array("\n", "\r", "'"), array('\n', '', "\\'"), $r);
            $contents = str_replace($search, "'" . $r . "'", $contents);
        }
        return $contents;
    }

    private function _trl($contents, $language)
    {
        //TODO 1902 $language verwenden
        $elements = Zend_Registry::get('trl')->parse($contents);

        foreach ($elements as $i=>$trlelement) {
            $values = array();

            if ($trlelement['source'] == Vps_Trl::SOURCE_VPS) {
                $mode = "Vps";
            } else  {
                $mode = '';
            }

            //TODO: vereinfachen
            if ($trlelement['type'] == 'trl') {
                $values['before'] = $trlelement['before'];
                $values['tochange'] = $trlelement['text'];
                $method = $trlelement['type'].$mode;
                $values['now'] = $method($values['tochange']);
                $values['now'] = str_replace($values['tochange'], $values['now'], $values['before']);
                $values['now'] = str_replace($method, "trl", $values['now']);

            } else if ($trlelement['type'] == 'trlc') {
                $values = array();
                $values['context'] = $trlelement['context'];
                $values['before'] = $trlelement['before'];
                $values['tochange'] = $trlelement['text'];
                $method = $trlelement['type'].$mode;
                $values['now'] = $method($values['context'] ,$values['tochange']);
                $values['now'] = str_replace($values['tochange'], $values['now'], $values['before']);
                $values['now'] = str_replace($method, 'trl', $values['now']);
                $values['now'] = str_replace('\''.$values['context'].'\', ', '', $values['now']);
                $values['now'] = str_replace('"'.$values['context'].'", ', '', $values['now']);

            } else if ($trlelement['type'] == 'trlp') {
                $values['before'] = $trlelement['before'];
                $values['single'] = $trlelement['text'];
                $values['plural'] = $trlelement['plural'];

                $newValues = Zend_Registry::get('trl')->getTrlpValues(null, $values['single'],
                                            $values['plural'], $trlelement['source'] );

                $method = $trlelement['type'].$mode;
                $values['now'] = str_replace($values['single'], $newValues['single'], $values['before']);
                $values['now'] = str_replace($values['plural'], $newValues['plural'], $values['now']);
                $values['now'] = str_replace($method, 'trlp', $values['now']);

            } else if ($trlelement['type'] == 'trlcp') {
                $values = array();
                $values['before'] = $trlelement['before'];
                $values['context'] = $trlelement['context'];
                $values['single'] = $trlelement['text'];
                $values['plural'] = $trlelement['plural'];

                $newValues = Zend_Registry::get('trl')->getTrlpValues($values['context'],
                            $values['single'], $values['plural'], $trlelement['source'] );

                $method = 'trlcp'.$mode;
                $values['now'] = str_replace($values['single'], $newValues['single'], $values['before']);
                $values['now'] = str_replace($values['plural'], $newValues['plural'], $values['now']);
                $values['now'] = str_replace("\"".$values['context']."\",", "", $values['now']);
                $values['now'] = str_replace('\''.$values['context'].'\',', "", $values['now']);
                $values['now'] = str_replace($method, 'trlp', $values['now']);
            }
            $contents = str_replace($values['before'], $values['now'], $contents);
        }
        return $contents;
    }

    private function _getCache()
    {
        static $cache;
        if (!isset($cache)) {
            $cache = new Vps_Assets_Cache();
        }
        return $cache;
    }
}
