<?php
class Kwf_Assets_CommonJs_Underscore_TemplateProvider extends Kwf_Assets_Provider_Abstract
{
    public function getDependency($dependencyName)
    {
        if (substr($dependencyName, -15) == '.underscore.tpl') {
            $ret = new Kwf_Assets_CommonJs_Underscore_TemplateDependency($dependencyName);
            $ret->addDependency(
                Kwf_Assets_Dependency_Abstract::DEPENDENCY_TYPE_COMMONJS,
                $this->_providerList->findDependency('underscore'),
                'underscore'
            );

            if (file_exists(substr($ret->getAbsoluteFileName(), 0, -15) . '.scss')) {
                $ret->addDependency(
                    Kwf_Assets_Dependency_Abstract::DEPENDENCY_TYPE_REQUIRES,
                    new Kwf_Assets_Dependency_File_Scss(substr($dependencyName, 0, -15) . '.scss')
                );
            }
            return $ret;
        }
        return null;
    }
}
