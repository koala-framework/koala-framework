<?php
class Kwf_Assets_Components_Dependency_Css extends Kwf_Assets_Components_Dependency_Abstract
{
    public function getMimeType()
    {
        return 'text/css';
    }

    public function getContentsPacked($language)
    {
        $ret = Kwf_SourceMaps_SourceMap::createEmptyMap('');
        foreach ($this->_componentDependencies as $dep) {
            $c = $dep->getContentsPacked($language);

            $replacements = array();
            if (Kwf_Config::getValue('application.uniquePrefix')) {
                $replacements['kwcBem--'] = $this->_getKwcClass().'--';
                $replacements['kwcBem__'] = $this->_getKwcClass().'__';
            } else {
                $replacements['kwcBem--'] = '';
                $replacements['kwcBem__'] = '';
            }
            $replacements['kwcClass'] = $this->_getKwcClass();
            $filter = new Kwf_Assets_Filter_Css_SelectorReplace($replacements);
            $c = $filter->filter($c);

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
