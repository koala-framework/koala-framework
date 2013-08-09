<?php
class Kwf_Assets_Dependency_TestDependencyDynamic extends Kwf_Assets_Dependency_Abstract
    implements Kwf_Assets_Dependency_UrlResolvableInterface
{
    public function getContents()
    {
        return "dynamic\n";
    }

    public function getMimeType()
    {
        return 'text/javascript; charset=utf-8';
    }

    public function getIncludeInPackage()
    {
        return false;
    }

    public function toUrlParameter()
    {
        return '';
    }

    public static function fromUrlParameter($class, $parameter)
    {
        return new $class();
    }

}
