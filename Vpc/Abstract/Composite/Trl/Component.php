<?php
class Vpc_Abstract_Composite_Trl_Component extends Vpc_Chained_Trl_Component
{
    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        foreach ($this->getData()->getChildComponents(/*array('generator' => 'child')*/) as $c) {
            $ret[$c->id] = $c;
        }
        return $ret;
    }
}
