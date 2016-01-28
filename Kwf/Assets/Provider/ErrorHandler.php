<?php
class Kwf_Assets_Provider_ErrorHandler extends Kwf_Assets_Provider_Abstract
{
    public function getDependency($dependencyName)
    {
        if ($dependencyName == 'KwfErrorHandler') {
            $deps = array();
            $deps[] = $this->_providerList->findDependency('KwfErrorHandlerCore');
            if (Kwf_Exception_Logger_Abstract::getInstance() instanceof Kwf_Exception_Logger_Raven) {
                $deps[] = new Kwf_Assets_Dependency_Dynamic_RavenJsDsn($this->_providerList);
                $deps[] = $this->_providerList->findDependency('KwfErrorHandlerRaven');
            } else {
                $deps[] = $this->_providerList->findDependency('KwfErrorHandlerLog');
            }
            return new Kwf_Assets_Dependency_Dependencies($this->_providerList, $deps, $dependencyName);
        }
        return null;
    }
}
