<?php
class Kwf_Assets_Provider_ErrorHandler extends Kwf_Assets_Provider_Abstract
{
    public function getDependency($dependencyName)
    {
        if ($dependencyName == 'KwfErrorHandler') {
            $deps = array(
                $this->_providerList->findDependency('KwfErrorHandlerCore'),
                $this->_providerList->findDependency('KwfErrorHandlerLog')
            );
            return new Kwf_Assets_Dependency_Dependencies($this->_providerList, $deps, $dependencyName);
        }
        return null;
    }
}
