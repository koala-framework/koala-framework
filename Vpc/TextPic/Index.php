<?php
/**
 * @package Vpc
 * @subpackage Components
 */
class Vpc_TextPic_Index extends Vpc_Abstract
{
    private $_textboxComponent;
    private $_picComponent;

    protected function setup()
    {
        $this->_textboxComponent = $this->createComponent('Vpc_Textbox', 0, '1');
        $this->_picComponent = $this->createComponent('Vpc_Pic', 0, '2');
    }
    
    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['textbox'] = $this->_textboxComponent->getTemplateVars();
        $ret['pic'] = $this->_picComponent->getTemplateVars();
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

    public function getChildComponents()
    {
        $this->setup();
        $return[$this->_textboxComponent->getId()] = $this->_textboxComponent;
        $return[$this->_picComponent->getId()] = $this->_picComponent;
        return $return;
    }

}
