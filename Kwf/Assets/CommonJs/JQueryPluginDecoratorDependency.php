<?php
class Kwf_Assets_CommonJs_JQueryPluginDecoratorDependency extends Kwf_Assets_Dependency_Abstract
{
    private $_dep;
    public function __construct(Kwf_Assets_Dependency_Abstract $dep)
    {
        $this->_dep = $dep;
        parent::__construct();
    }

    private function _getPrependCode()
    {
        return "var jQuery = require('jQuery');\n";
    }

    public function getContents($language)
    {
        $ret = $this->_dep->getContents($language);
        $ret = $this->_getPrependCode().$ret;
        return $ret;
    }

    public function getContentsPacked($language)
    {
        $c = $this->_dep->getContentsPacked($language);
        $ret = Kwf_SourceMaps_SourceMap::createEmptyMap($this->_getPrependCode());
        $ret->concat($c);
        return $ret;
    }

    public function getMimeType()
    {
        return $this->_dep->getMimeType();
    }

    public function getMTime()
    {
        return $this->_dep->getMTime();
    }

    public function warmupCaches()
    {
        return $this->_dep->warmupCaches();
    }

    public function __toString()
    {
        return 'JQueryPluginDecorator('.$this->_dep->__toString().')';
    }
}
