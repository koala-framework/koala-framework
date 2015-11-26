<?php
class Kwf_Assets_Components_Dependency_Abstract extends Kwf_Assets_Dependency_Abstract
{
    protected $_componentClass;
    protected $_componentDependencies;
    protected $_isMaster;

    public function __construct($componentClass, array $componentDependencies, $isMaster, $dependencyName)
    {
        $this->_componentClass = $componentClass;
        $this->_componentDependencies = $componentDependencies;
        $this->_isMaster = $isMaster;
        $this->_dependencyName = $dependencyName;
    }

    private function _getKwcClass()
    {
        $kwcClass = Kwf_Component_Abstract::formatRootElementClass($this->_componentClass, '');
        if ($this->_isMaster) $kwcClass .= 'Master';
        if (Kwf_Config::getValue('application.uniquePrefix')) {
            $kwcClass = str_replace('kwfUp-', Kwf_Config::getValue('application.uniquePrefix').'-', $kwcClass);
        } else {
            $kwcClass = str_replace('kwfUp-', '', $kwcClass);
        }
        return $kwcClass;
    }

    public function getContentsSourceString()
    {
        $ret = '';
        foreach ($this->_componentDependencies as $dep) {
            $ret .= $dep->getContentsSourceString()."\n";
        }
        return $ret;
    }

    public function getContentsSource()
    {
        $ret = '';
        foreach ($this->_componentDependencies as $dep) {
            $s = $dep->getContentsSource();
            if ($s['type'] == 'file') {
                $ret .= file_get_contents($s['file'])."\n";
            } else if ($s['type'] == 'contents') {
                $ret .= $s['contents']."\n";
            } else {
                throw new Kwf_Exception_NotYetImplemented();
            }
        }
        return array(
            'type' => 'contents',
            'contents' => $ret,
        );
    }

    public function usesLanguage()
    {
        $ret = false;
        foreach ($this->_componentDependencies as $dep) {
            if ($dep->usesLanguage()) {
                $ret = true;
            }
        }
        return $ret;
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

    public function __toString()
    {
        return $this->_dependencyName;
    }

    public function getIdentifier()
    {
        return $this->_dependencyName;
    }
}
