<?php
require_once 'Zend/Config/Ini.php';

class Vps_Config_Web extends Zend_Config_Ini
{
    public function __construct($section)
    {
        $vpsSection = 'vivid';

        $vpsConfigFull = new Zend_Config_Ini(VPS_PATH.'/config.ini', null);
        if (isset($vpsConfigFull->$section)) {
            $vpsSection = $section;
        }

        parent::__construct(VPS_PATH.'/config.ini', $vpsSection,
                        array('allowModifications'=>true));

        $this->_mergeWebConfig($section);

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
                                            array($this->libraryPath, VPS_PATH),
                                            $i);
        }
        foreach ($this->includepath as $k=>$i) {
            $this->includepath->$k = str_replace(array('%libraryPath%', '%vpsPath%'),
                                            array($this->libraryPath, VPS_PATH),
                                            $i);
        }
    }

    protected function _mergeWebConfig($section)
    {
        $this->_mergeFile('application/config.ini', $section);
    }

    protected final function _mergeFile($file, $section)
    {
        $webSection = 'vivid';
        $webConfigFull = new Zend_Config_Ini($file, null);
        if (isset($webConfigFull->$section)) {
            $webSection = $section;
        }
        $webConfig = new Zend_Config_Ini($file, $webSection);
        return $this->merge($webConfig);
    }
}
