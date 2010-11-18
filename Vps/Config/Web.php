<?php
require_once 'Vps/Config/Ini.php';

class Vps_Config_Web extends Vps_Config_Ini
{
    public static function getInstance($section)
    {
        static $instances = array();
        if (!isset($instances[$section])) {
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
            $instances[$section] = $ret;
        }
        return $instances[$section];
    }

    public static function getInstanceMtime($section)
    {
        require_once 'Vps/Config/Cache.php';
        $cache = Vps_Config_Cache::getInstance();
        return $cache->test('config_'.str_replace('-', '_', $section));
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

        $vpsSection = false;

        $webSection = $this->_getWebSection($webPath.'/application/config.ini', $section);
        $webConfig = new Vps_Config_Ini($webPath.'/application/config.ini', $webSection);
        if (!empty($webConfig->vpsConfigSection)) {
            $vpsSection = $webConfig->vpsConfigSection;
        } else {
            $vpsConfigFull = new Vps_Config_Ini($vpsPath.'/config.ini', null);
            if (isset($vpsConfigFull->$section)) {
                $vpsSection = $section;
            }
        }
        if (!$vpsSection) {
            require_once 'Vps/Exception.php';
            throw new Vps_Exception("Add either '$section' to vps/config.ini or set vpsConfigSection in web config.ini");
        }

        parent::__construct($vpsPath.'/config.ini', $vpsSection,
                        array('allowModifications'=>true));

        $fixes = array();
        if ($webSection != $section && ($this->server->host == 'vivid' || $this->server->host == 'vivid-test-server')) {
            $fixes = array(
                'libraryPath' => $this->libraryPath,
                'uploads' => $this->uploads,
                'serverUser' => $this->server->user,
                'serverHost' => $this->server->host,
                'serverDir' => $this->server->dir,
                'serverDomain' => $this->server->domain
            );
        }

        $this->_mergeWebConfig($webPath.'/application/config.ini', $section);

        if ($fixes) {
            $this->libraryPath = $fixes['libraryPath'];
            $this->uploads = $fixes['uploads'];
            $this->server->user = $fixes['serverUser'];
            $this->server->host = $fixes['serverHost'];
            $this->server->dir = $fixes['serverDir'];
            $this->server->domain = $fixes['serverDomain'];
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

        $this->server->dir = str_replace('%id%', $this->application->id, $this->server->dir);
        $this->server->domain = str_replace('%id%', $this->application->id, $this->server->domain);
        $this->uploads = str_replace('%id%', $this->application->id, $this->uploads);
    }

    private function _getWebSection($file, $section)
    {
        $webConfigFull = new Vps_Config_Ini($file, null);
        if (isset($webConfigFull->$section)) {
            $webSection = $section;
        } else if (isset($webConfigFull->vivid)) {
            $webSection = 'vivid';
        } else {
            $webSection = 'production';
        }
        return $webSection;
    }

    protected function _mergeWebConfig($path, $section)
    {
        $this->_mergeFile($path, $section);
    }

    protected final function _mergeFile($file, $section)
    {
        return self::mergeConfigs($this, new Vps_Config_Ini($file, $this->_getWebSection($file, $section)));
    }

    /**
     * Diesen Merge sollte eigentlich das Zend machen, aber das merged nicht so
     * wie wir das erwarten. Beispiel:
     *
     * Main Config:
     * bla.blubb[] = x
     * bla.blubb[] = y
     * bla.blubb[] = z
     *
     * Merge Config:
     * bla.blubb[] = a
     * bla.blubb[] = b
     *
     * Nach den Config-Section regeln würde man erwarten, dass nach dem mergen nur mehr
     * a und b drin steht. Tatsächlich merget Zend aber so, dass a, b, z überbleibt.
     * Zend überschreibt die Werte, was wir nicht wollen, deshalb dieses
     * händische mergen hier.
     */
    public static function mergeConfigs(Zend_Config $main, Zend_Config $merge)
    {
        // check if all keys are of type 'integer' and if so, only use merge config
        $everyKeyIsInteger = true;
        foreach($merge as $key => $item) {
            if (!is_int($key)) {
                $everyKeyIsInteger = false;
                break;
            }
        }
        if ($everyKeyIsInteger) {
            return $merge;
        }

        foreach($merge as $key => $item) {
            if(isset($main->$key)) {
                if($item instanceof Zend_Config && $main->$key instanceof Zend_Config) {
                    $main->$key = Vps_Config_Web::mergeConfigs(
                        $main->$key,
                        new Zend_Config($item->toArray(), !$main->readOnly())
                    );
                } else {
                    $main->$key = $item;
                }
            } else {
                if($item instanceof Zend_Config) {
                    $main->$key = new Zend_Config($item->toArray(), !$main->readOnly());
                } else {
                    $main->$key = $item;
                }
            }
        }
        return $main;
    }
}
