<?php
abstract class Kwf_Assets_Components_Dependency_Abstract extends Kwf_Assets_Dependency_Abstract
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

    protected function _getKwcClass()
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

    public function __toString()
    {
        return $this->_dependencyName;
    }

    public function getIdentifier()
    {
        return $this->_dependencyName;
    }
}
