<?php
class Kwf_Assets_Dependency_Decorator_StringReplace extends Kwf_Assets_Dependency_Decorator_Abstract
{
    private $_replacements;
    private $_identifier;
    public function __construct(Kwf_Assets_Dependency_Abstract $dep, array $replacements, $identifier)
    {
        parent::__construct($dep);
        $this->_replacements = $replacements;
        $this->_identifier = $identifier;
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

    public function getIdentifier()
    {
        return $this->_identifier;
    }
}
