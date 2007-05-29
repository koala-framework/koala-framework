<?php
class Vps_Component_Decorator_Color_Color extends Vps_Component_Decorator_Abstract
{
    protected $_decorated;

    public function getTemplateVars($mode)
    {
        $ret = parent::getTemplateVars($mode);
        $ret['decorated'] = $this->_component->getTemplateVars($mode);
        $ret['color'] = 'blue';
        $ret['template'] = 'Decorator.html';
        return $ret;
    }
    
}
