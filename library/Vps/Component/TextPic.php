<?php
class Vps_Component_TextPic extends Vps_Component_Abstract
{
    private $_textboxComponent;
    private $_picComponent;

    public function getTemplateVars($mode)
    {
        $this->_paragraphs = array();
        $componentKey = $this->getComponentKey();
        if($componentKey!='') $componentKey .= ".";

        $this->_textboxComponent = new Vps_Component_Textbox($this->_dao, $this->getComponentId(), $this->getPageKey(), $componentKey.'1');
        $this->_picComponent = new Vps_Component_Pic($this->_dao, $this->getComponentId(), $this->getPageKey(), $componentKey.'2');

        $ret = parent::getTemplateVars($mode);
        $ret['textbox'] = $this->_textboxComponent->getTemplateVars($mode);
        $ret['pic'] = $this->_picComponent->getTemplateVars($mode);
        $ret['template'] = 'TextPic.html';
        return $ret;
    }
    public function getComponentInfo()
    {
    	$info = parent::getComponentInfo();
    	$info += $this->_textboxComponent->getComponentInfo();
    	$info += $this->_picComponent->getComponentInfo();
    	return $info;
    }
}
