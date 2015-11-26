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

    public function getContentsPacked($language)
    {
        $ret = $this->_dep->getContentsPacked($language);
        $ret = clone $ret;
        foreach ($this->_getReplacements() as $k=>$i) {
            $ret->stringReplace($k, $i);
        }
        return $ret;
    }
}
