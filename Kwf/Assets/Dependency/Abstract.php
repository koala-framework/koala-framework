<?php
abstract class Kwf_Assets_Dependency_Abstract
{
    const DEPENDENCY_TYPE_ALL = 'all';
    const DEPENDENCY_TYPE_REQUIRES = 'requires';
    const DEPENDENCY_TYPE_USES = 'uses';

    public function getContents()
    {
        return null;
    }

    public function getContentsPacked($language)
    {
        return $this->getContents($language);
    }

    public function getDependencies($type)
    {
        return array();
    }

    public function getMimeType()
    {
        return null;
    }

    public function getWatchFiles()
    {
        return array();
    }
    public function getIncludeInPackage()
    {
        return true;
    }

    public function getMTime()
    {
        return null;
    }

    public function __toString()
    {
        return get_class($this);
    }

    public function toDebug()
    {
        return get_class($this).': '.$this->__toString()."\n";
    }
}
