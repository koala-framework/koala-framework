<?php
abstract class Kwf_Assets_Dependency_Abstract
{
    public function getContents()
    {
        return null;
    }

    public function getDependencies()
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
}
