<?php
class Kwc_Chained_Trl_GeneratorEvents_Abstract extends Kwf_Component_Generator_Events
{
    protected function _getChainedGenerator()
    {
        $class = Kwc_Abstract::getSetting($this->_class, 'masterComponentClass');
        $generatorKey = $this->_getGenerator()->getGeneratorKey();
        return Kwf_Component_Generator_Abstract::getInstance($class, $generatorKey);;
    }
}
