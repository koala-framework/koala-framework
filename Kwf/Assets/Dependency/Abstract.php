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

    public function getRecursiveFiles()
    {
        $it = new RecursiveIteratorIterator(new Kwf_Assets_Dependency_Iterator_UniqueFilter(new Kwf_Assets_Dependency_Iterator_Recursive($this, Kwf_Assets_Dependency_Abstract::DEPENDENCY_TYPE_ALL)), RecursiveIteratorIterator::CHILD_FIRST);
        $array = array();
        if ($this instanceof Kwf_Assets_Dependency_File) {
            $array[] = $this;
        }
        foreach ($it as $i) {
            if ($i instanceof Kwf_Assets_Dependency_File && $i !== $this) {
                $array[] = $i;
            }
        }
        return $array;
    }

    public function printDebugTree()
    {
        $this->_printDebugTree($this);
    }

    private function _printDebugTree($dep, $indent=0, &$processed=array())
    {
        echo $dep->toDebug();
        if (in_array($dep, $processed, true)) {
            echo str_repeat(' ', ($indent+1)*2)."(recursion)\n";
            return;
        }
        $processed[] = $dep;
        foreach ($dep->getDependencies(Kwf_Assets_Dependency_Abstract::DEPENDENCY_TYPE_REQUIRES) as $i) {
            echo str_repeat(' ', $indent*2);
            echo 'requires ';
            $this->_printDebugTree($i, $indent+1, $processed);
        }
        foreach ($dep->getDependencies(Kwf_Assets_Dependency_Abstract::DEPENDENCY_TYPE_USES) as $i) {
            echo str_repeat(' ', $indent*2);
            echo 'uses ';
            $this->_printDebugTree($i, $indent+1, $processed);
        }
    }
}
