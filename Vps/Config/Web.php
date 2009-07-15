<?php
require_once 'Zend/Config/Ini.php';

class Vps_Config_Web extends Zend_Config_Ini
{
    public static function getInstance($section)
    {
        require_once 'Vps/Config/Cache.php';
        $cache = Vps_Config_Cache::getInstance();
        $cacheId = 'config_'.str_replace('-', '_', $section);
        $configClass = Vps_Setup::$configClass;
        require_once str_replace('_', '/', $configClass).'.php';
        if(!$ret = $cache->load($cacheId)) {
            $ret = new $configClass($section);
            $mtime = time();
            $cache->save($ret, $cacheId);
        }
        return $ret;
    }

    public static function getInstanceMtime($section)
    {
        require_once 'Vps/Config/Cache.php';
        $cache = Vps_Config_Cache::getInstance();
        return $cache->test('config_'.$section);
    }

    public function __construct($section, $options = array())
    {
        if (isset($options['vpsPath'])) {
            $vpsPath = $options['vpsPath'];
        } else {
            $vpsPath = VPS_PATH;
        }
        if (isset($options['webPath'])) {
            $webPath = $options['webPath'];
        } else {
            $webPath = '.';
        }
        $vpsSection = 'vivid';

        $vpsConfigFull = new Zend_Config_Ini($vpsPath.'/config.ini', null);
        if (isset($vpsConfigFull->$section)) {
            $vpsSection = $section;
        }

        parent::__construct($vpsPath.'/config.ini', $vpsSection,
                        array('allowModifications'=>true));



        $this->_mergeWebConfig($webPath.'/application/config.ini', $section);

        $v = $this->application->vps->version;
        if (preg_match('#tags/vps/([^/]+)/config\\.ini#', $v, $m)) {
            $v = $m[1];
        } else if (preg_match('#branches/vps/([^/]+)/config\\.ini#', $v, $m)) {
            $v = 'Branch '.$m[1];
        } else if (preg_match('#trunk/vps/config\\.ini#', $v, $m)) {
            $v = 'Trunk';
        }
        $this->application->vps->version = $v;
        if (preg_match('/Revision: ([0-9]+)/', $this->application->vps->revision, $m)) {
            $this->application->vps->revision = (int)$m[1];
        }
        foreach ($this->path as $k=>$i) {
            $this->path->$k = str_replace(array('%libraryPath%', '%vpsPath%'),
                                            array($this->libraryPath, $vpsPath),
                                            $i);
        }
        foreach ($this->includepath as $k=>$i) {
            $this->includepath->$k = str_replace(array('%libraryPath%', '%vpsPath%'),
                                            array($this->libraryPath, $vpsPath),
                                            $i);
        }
    }

    protected function _mergeWebConfig($path, $section)
    {
        $this->_mergeFile($path, $section);
    }

    protected final function _mergeFile($file, $section)
    {
        $webConfigFull = new Zend_Config_Ini($file, null);
        if (isset($webConfigFull->$section)) {
            $webSection = $section;
        } else if (isset($webConfigFull->vivid)) {
            $webSection = 'vivid';
        } else {
            $webSection = 'production';
        }
        return $this->merge(new Zend_Config_Ini($file, $webSection));
    }
}
