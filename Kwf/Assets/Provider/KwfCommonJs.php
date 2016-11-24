<?php
class Kwf_Assets_Provider_KwfCommonJs extends Kwf_Assets_Provider_Abstract
{
    public function getDependency($dependencyName)
    {
        if (substr($dependencyName, 0, 4) == 'kwf/') {
            $dependencyName = substr($dependencyName, 4);
            if (file_exists(KWF_PATH.'/commonjs/'.$dependencyName.'.js')) {
                $ret = new Kwf_Assets_Dependency_File_Js($this->_providerList, 'kwf/commonjs/'.$dependencyName.'.js');
                return $ret;
            }
        } else if (substr($dependencyName, 0, 4) == 'web/') {
            $dependencyName = substr($dependencyName, 4);
            if (substr($dependencyName, -5) == '.scss' && file_exists('./'.$dependencyName)) {
                $ret = new Kwf_Assets_Dependency_File_Scss($this->_providerList, 'web/' . $dependencyName);
                return $ret;
            } else if (substr($dependencyName, -4) == '.css' && file_exists('./'.$dependencyName)) {
                $ret = new Kwf_Assets_Dependency_File_Css($this->_providerList, 'web/' . $dependencyName);
                return $ret;
            } else if (file_exists('./'.$dependencyName.'.js')) {
                $ret = new Kwf_Assets_Dependency_File_Js($this->_providerList, 'web/'.$dependencyName.'.js');
                return $ret;
            }
        }
    }
}
