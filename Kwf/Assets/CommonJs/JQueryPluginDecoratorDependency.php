<?php
class Kwf_Assets_CommonJs_JQueryPluginDecoratorDependency extends Kwf_Assets_Dependency_Decorator_Abstract
{
    private function _getPrependCode()
    {
        return "var jQuery = require('jQuery');\n";
    }

    public function getContentsPacked()
    {
        $c = $this->_dep->getContentsPacked();
        $ret = Kwf_SourceMaps_SourceMap::createEmptyMap($this->_getPrependCode());
        $ret->concat($c);
        return $ret;
    }

    public function getIdentifier()
    {
        return 'JQueryPlugin('.$this->_dep->getIdentifier().')';
    }

}
