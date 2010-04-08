<?php
abstract class Vpc_Menu_Abstract_Trl_Component extends Vpc_Chained_Trl_Component
{
    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret = array_merge($ret, $this->getData()->chained->getComponent()->getTemplateVars());
        return $ret;
    }

    protected function _getChainedComponent($component)
    {
        $ret = Vpc_Chained_Trl_Component::getChainedByMaster($component, $this->getData());
        if ($ret) {
            if (isset($component->current)) $ret->current = $component->current;
            if (isset($component->class)) $ret->class = $component->class;
        }
        return $ret;
    }
}
