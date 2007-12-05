<?php
class Vpc_Basic_Link_Component_Component extends Vpc_Basic_Link_Component
{
    protected $_targetComponent;
    protected $_rel;
    protected $_param;
    
    public function setTargetComponent(Vpc_Abstract $component)
    {
        $this->_targetComponent = $component;
    }
    
    public function setRel($rel)
    {
        $this->_rel = $rel;
    }
    
    public function setParam($param)
    {
        $this->_param = $param;
    }
    
    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $target = $this->_targetComponent;
        $ret['href'] = $target->getUrl();
        $ret['param'] = $this->_param;
        $ret['rel'] = $this->_rel;
        return $ret;
    }
}
