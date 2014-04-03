<?php
class Kwf_Assets_Ext4_AclControllerProvider extends Kwf_Assets_Provider_Abstract
{
    public function getDependency($dependencyName)
    {
        if ($dependencyName == 'Ext4AclController') {
            $deps = array();
            foreach (Kwf_Acl::getInstance()->getAllResources() as $r) {
                if ($r instanceof Kwf_Acl_Resource_MenuExt4) {
                    $cls = 'App.controller.'.$r->getExtController();
                    $d = $this->_providerList->findDependency($cls);
                    if (!$d) {
                        throw new Kwf_Exception("Didn't find dependency '$cls'.");
                    }
                    $deps[] = $d;
                }
            }
            if ($deps) {
                array_unshift($deps, $this->_providerList->findDependency('Ext4.app.Application'));
            }
            return new Kwf_Assets_Dependency_Dependencies($deps, $dependencyName);
        }
        return null;
    }
}
