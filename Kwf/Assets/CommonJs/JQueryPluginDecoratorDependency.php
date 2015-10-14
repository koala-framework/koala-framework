<?php
class Kwf_Assets_CommonJs_JQueryPluginDecoratorDependency extends Kwf_Assets_Dependency_Decorator_Abstract
{
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
}
