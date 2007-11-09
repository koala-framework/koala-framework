<?php
class Vpc_Composite_TextImage_Admin extends Vpc_Admin
{
    public function setup()
    {
        $tClass = Vpc_Abstract::getSetting($class, 'textClass');
        Vpc_Admin::getInstance($tClass)->setup();
        
        $iClass = Vpc_Abstract::getSetting($class, 'imageClass');
        Vpc_Admin::getInstance($iClass)->setup();

        $fields['image_position'] = "enum('left', 'right', 'alternate') default NULL";
        $fields['enlarge'] = 'tinyint(3) NOT NULL';
        $this->createFormTable('vpc_composite_textimage', $fields);
    }

    public function delete($class, $pageId, $componentKey)
    {
        $tClass = Vpc_Abstract::getSetting($class, 'textClass');
        Vpc_Admin::getInstance($tClass)->delete($tClass, $pageId, $componentKey . '-1');
        
        $iClass = Vpc_Abstract::getSetting($class, 'imageClass');
        Vpc_Admin::getInstance($iClass)->delete($iClass, $pageId, $componentKey . '-2');
        
        parent::delete($class, $pageId, $componentKey);
    }
}
