<?php
class Vpc_Composite_TextImages_Component extends Vpc_Abstract
{
    public $text;
    public $images;

    public static function getSettings()
    {
        return array_merge(parent::getSettings(), array(
            'componentName'     => 'TextImages',
            'tablename'         => 'Vpc_Composite_TextImage_Model',
            'childComponentClasses' => array(
                'text'         => 'Vpc_Basic_Html_Component',
                'images'       => 'Vpc_Composite_Images_Component',
            ),
            'default'           => array(
                'image_position'    => 'alternate' // 'left', 'right', 'alternate'
            )
        ));
    }
    
    public function getTemplateVars()
    {
        $return = parent::getTemplateVars();
        $return['text'] = $this->text->getTemplateVars('');
        $return['images'] = $this->images->getTemplateVars('');
        $return['imagePosition'] = $this->_row->image_position;
        return $return;
    }

    protected function _init()
    {
        $textClass = $this->_getClassFromSetting('text', 'Vpc_Basic_Html_Component');
        $imagesClass = $this->_getClassFromSetting('images', 'Vpc_Composite_Images_Component');
        $this->text = $this->createComponent($textClass, 'text');
        $this->images = $this->createComponent($imagesClass, 'images');
    }

    public function getChildComponents()
    {
        return array($this->text, $this->images);
    }

}
