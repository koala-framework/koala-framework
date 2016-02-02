<?php
//very specialized assets provider
//if we need more of this kind thing of something more generic
class Kwf_Assets_Provider_ViewsUser extends Kwf_Assets_Provider_Abstract
{
    public function getDependency($dependencyName)
    {
        if ($dependencyName == 'ViewsUser') {
            $deps = array();
            foreach (glob(KWF_PATH.'/views/user/*.scss') as $f) {
                $f = 'kwf/'.substr($f, strlen(KWF_PATH)+1);
                $deps[] = Kwf_Assets_Dependency_File::createDependency($f, $this->_providerList);
            }
            if (file_exists('views/user')) {
                foreach (glob('views/user/*.scss') as $f) {
                    $deps[] = Kwf_Assets_Dependency_File::createDependency('web/'.$f, $this->_providerList);
                }
            }
            return new Kwf_Assets_Dependency_Dependencies($this->_providerList, $deps, $dependencyName);
        }
        return null;
    }
}
