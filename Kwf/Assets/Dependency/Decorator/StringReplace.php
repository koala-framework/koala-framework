<?php
class Kwf_Assets_Dependency_Decorator_StringReplace extends Kwf_Assets_Dependency_Decorator_Abstract
{
    private $_replacements;
    public function __construct(Kwf_Assets_Dependency_Abstract $dep, array $replacements)
    {
        parent::__construct($dep);
        $this->_replacements = $replacements;
    }

    protected function _getReplacements()
    {
        return $this->_replacements;
    }

    public function getContents($language)
    {
        $ret = $this->_dep->getContents($language);
        foreach ($this->_getReplacements() as $k=>$i) {
            $ret = str_replace($k, $i, $Ret);
        }
        return $ret;
    }

    public function getContentsPacked($language)
    {
        $ret = $this->_dep->getContentsPacked($language);
        foreach ($this->_getReplacements() as $k=>$i) {
            $ret->stringReplace($k, $i);
        }
        return $ret;
    }
}
