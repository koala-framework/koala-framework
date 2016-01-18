<?php
class Kwf_Assets_Components_Dependency_Js extends Kwf_Assets_Components_Dependency_Abstract
{
    public function getMimeType()
    {
        return 'text/javascript';
    }

    public function isCommonJsEntry()
    {
        return true;
    }

    public function getContentsPacked($language)
    {
        $ret = Kwf_SourceMaps_SourceMap::createEmptyMap('');
        foreach ($this->_componentDependencies as $dep) {
            $c = $dep->getContentsPacked($language);
            if (Kwf_Config::getValue('application.uniquePrefix')) {
                $c->stringReplace('kwcBem--', $this->_getKwcClass().'--');
                $c->stringReplace('kwcBem__', $this->_getKwcClass().'__');
            } else {
                $c->stringReplace('kwcBem--', '');
                $c->stringReplace('kwcBem__', '');
            }
            $c->stringReplace('.kwcClass', '.'.$this->_getKwcClass());
            $ret->concat($c);
        }
        if ($this->getMimeType() == 'text/css') {
            $ret->setMimeType('text/css');
        } else {
            $ret->setMimeType('text/javascript');
        }
        return $ret;
    }
}
