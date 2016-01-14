<?php
class Kwf_Assets_Provider_KwfCommonJs extends Kwf_Assets_Provider_Abstract
{
    public function getDependency($dependencyName)
    {
        if (substr($dependencyName, 0, 4) == 'kwf/') {
            $dependencyName = substr($dependencyName, 4);
            if (file_exists(KWF_PATH.'/commonjs/'.$dependencyName.'.js')) {
                $ret = new Kwf_Assets_Dependency_File_Js('kwf/commonjs/'.$dependencyName.'.js');
                return $ret;
            }
        } else if (substr($dependencyName, 0, 13) == 'web/commonjs/') {
            $dependencyName = substr($dependencyName, 13);
            if (file_exists('./commonjs/'.$dependencyName.'.js')) {
                $ret = new Kwf_Assets_Dependency_File_Js('web/commonjs/'.$dependencyName.'.js');
                return $ret;
            }
        }
    }
}
