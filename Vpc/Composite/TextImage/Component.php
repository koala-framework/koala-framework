<?php
class Vpc_Composite_TextImage_Component extends Vpc_Abstract
{
    public $text;
    public $image;
    
    public static function getSettings()
    {
        return array_merge(parent::getSettings(), array(
            'componentName'     => 'Standard.TextImage',
            'tablename'         => 'Vpc_Composite_TextImage_Model',
            'textClass'         => 'Vpc_Basic_Text_Component',
            'imageClass'        => 'Vpc_Basic_Image_Component',
            'default'           => array(
                'image_position'    => 'alternate' // 'left', 'right', 'alternate'
            )
        ));
    }
    
    public function getTemplateVars()
    {
        $return = parent::getTemplateVars();
        $return['text'] = $this->text->getTemplateVars('');
        $return['image'] = $this->image->getTemplateVars('');
        $return['image_position'] = 'right'; // TODO
        return $return;
    }

    protected function _init()
    {
        $textClass = $this->_getClassFromSetting('textClass', 'Vpc_Basic_Text_Component');
        $imageClass = $this->_getClassFromSetting('imageClass', 'Vpc_Basic_Image_Component');
        $this->text = $this->createComponent($textClass, 1);
        $this->image = $this->createComponent($imageClass, 2);
    }

    public function getChildComponents()
    {
        return array($this->text, $this->image);
    }

}