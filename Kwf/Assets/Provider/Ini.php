<?php
class Kwf_Assets_Provider_Ini extends Kwf_Assets_Provider_Abstract implements Serializable
{
    private $_config;
    private $_iniFile;

    public function __construct($iniFile)
    {
        $this->_iniFile = $iniFile;
    }

    public function getDependency($dependencyName)
    {
        if (!isset($this->_config)) {
            $ini = new Kwf_Config_Ini($this->_iniFile, 'dependencies');
            $this->_config = $ini->toArray();
            unset($ini);
        }

        if (!isset($this->_config[$dependencyName])) return null;
        $dep = $this->_config[$dependencyName];
        $ret = array();
        if (isset($dep['dep'])) {
            foreach ($dep['dep'] as $i) {
                $ret[] = trim($i);
            }
        }

        $files = array();
        if (isset($dep['files'])) {
            foreach ($dep['files'] as $i) {
                $i = Kwf_Assets_Dependency_File::createDependency(trim($i));
                if ($i instanceof Kwf_Assets_Dependency_File && $i->getFileName()) {
                    $files[] = $i->getFileName();
                } else if ($i instanceof Kwf_Assets_Dependency_Dependencies) {
                    //filter out dependencies that are already returned as individual files
                    //happens when using *
                    $deps = $i->getDependencies();
                    foreach ($deps as $k=>$j) {
                        if ($j instanceof Kwf_Assets_Dependency_File && $j->getFileName()) {
                            if (in_array($j->getFileName(), $files)) {
                                unset($deps[$k]);
                            }
                        }
                    }
                    $i->setDependencies($deps);
                }
                $ret[] = $i;
            }
        }
        return $ret;
    }

    public function serialize()
    {
        $ret = array();
        foreach (get_object_vars($this) as $k=>$i) {
            if ($k == '_config') { //don't serialize config, re-read lazily if required
                continue;
            }
            $ret[$k] = $i;
        }
        return serialize($ret);
    }

    public function unserialize($serialized)
    {
        foreach (unserialize($serialized) as $k=>$i) {
            $this->$k = $i;
        }
    }
}
