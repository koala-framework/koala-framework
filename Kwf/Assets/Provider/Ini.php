<?php
class Kwf_Assets_Provider_Ini extends Kwf_Assets_Provider_Abstract implements Serializable
{
    private $_config;
    private $_iniFile;

    public function __construct($iniFile)
    {
        $this->_iniFile = $iniFile;
    }

    //used only by IniNoFiles
    public function hasDependencyInConfig($dependencyName)
    {
        return isset($this->_config[$dependencyName]);
    }

    public function getDependency($dependencyName)
    {
        if (!isset($this->_config)) {
            $ini = new Kwf_Config_Ini($this->_iniFile, 'dependencies');
            $this->_config = $ini->toArray();
            unset($ini);
        }

        if (substr($dependencyName, -6) == 'IniDep') {
            //ini dep is own dependency as it might be defined in different file than files
            $dep = substr($dependencyName, 0, -6);

            if (!isset($this->_config[$dep]['dep'])) return null;

            $ret = array();
            foreach ($this->_config[$dep]['dep'] as $i) {
                if (!$i) continue;
                $d = $this->_providerList->findDependency(trim($i));
                if (!$d) {
                    throw new Kwf_Exception("Can't find dependency '$i'");
                }
                $ret[] = $d;
            }
        } else {
            if (!isset($this->_config[$dependencyName]['files'])) return null;
            $ret = array();

            //optional
            $dep = $this->_providerList->findDependency($dependencyName.'IniDep');
            if ($dep) {
                $ret[] = $dep;
            }

            $depFiles = $this->_config[$dependencyName]['files'];

            $files = array();
            foreach ($depFiles as $i) {
                if (!$i) continue;
                $i = Kwf_Assets_Dependency_File::createDependency(trim($i), $this->_providerList);
                if ($i instanceof Kwf_Assets_Dependency_File && $i->getFileNameWithType()) {
                    $files[$i->getFileNameWithType()] = true;
                } else if ($i instanceof Kwf_Assets_Dependency_Dependencies) {
                    //filter out dependencies that are already returned as individual files
                    //happens when using *
                    foreach (array(Kwf_Assets_Dependency_Abstract::DEPENDENCY_TYPE_REQUIRES, Kwf_Assets_Dependency_Abstract::DEPENDENCY_TYPE_USES) as $type) {
                        $deps = $i->getDependencies($type);
                        foreach ($deps as $k=>$j) {
                            if ($j instanceof Kwf_Assets_Dependency_File && $j->getFileNameWithType()) {
                                if (isset($files[$j->getFileNameWithType()])) {
                                    unset($deps[$k]);
                                }
                            }
                        }
                        $i->setDependencies($type, $deps);
                    }
                }
                $ret[] = $i;
            }
        }

        return new Kwf_Assets_Dependency_Dependencies($ret, $dependencyName);
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
