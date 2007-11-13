<?php
class Vpc_Composite_TextImage_Admin extends Vpc_Admin
{
    public function setup()
    {
        $tClass = Vpc_Abstract::getSetting($this->_class, 'textClass');
        Vpc_Admin::getInstance($tClass)->setup();
        
        $iClass = Vpc_Abstract::getSetting($this->_class, 'imageClass');
        Vpc_Admin::getInstance($iClass)->setup();

        $fields['image_position'] = "enum('left', 'right', 'alternate') default NULL";
        $this->createFormTable('vpc_composite_textimage', $fields);
    }

    public function delete($pageId, $componentKey)
    {
        $tClass = Vpc_Abstract::getSetting($this->_class, 'textClass');
        Vpc_Admin::getInstance($tClass)->delete($pageId, $componentKey . '-1');
        
        $iClass = Vpc_Abstract::getSetting($this->_class, 'imageClass');
        Vpc_Admin::getInstance($iClass)->delete($pageId, $componentKey . '-2');
        
        parent::delete($pageId, $componentKey);
    }
}
