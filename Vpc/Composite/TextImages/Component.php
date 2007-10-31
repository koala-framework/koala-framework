<?php
class Vpc_Composite_TextImages_Component extends Vpc_Abstract
{
    const NAME = 'Standard.TextImages';
    public $text;
    public $images;
    protected $_settings = array(
        'textClass'         => 'Vpc_Basic_Html_Component',
        'textSettings'      => array(),
        'imagesClass'       => 'Vpc_Composite_Images_Component',
        'imagesSettings'    => array(),
        'imageClass'        => 'Vpc_Basic_Image_Component',
        'imageSettings'     => array(),
        'image_position' => 'alternate' // 'left', 'right', 'alternate'
    );
    protected $_tablename = 'Vpc_Composite_TextImage_Model';

    public function getTemplateVars()
    {
        $return = parent::getTemplateVars();
        $return['text'] = $this->text->getTemplateVars('');
        $return['images'] = $this->images->getTemplateVars('');
        $return['imagePosition'] = $this->getSetting('image_position');
        $return['template'] = 'Composite/TextImages.html';
        return $return;
    }

    protected function _init()
    {
        $textClass = $this->_getClassFromSetting('textClass', 'Vpc_Basic_Html_Component');
        $imagesClass = $this->_getClassFromSetting('imagesClass', 'Vpc_Composite_Images_Component');
        $imagesSettings = $this->getSetting('imageSettings');
        if (!is_array($imagesSettings)) { $imagesSettings = array(); }
        $imagesSettings['imageClass'] = $this->getSetting('imageClass');
        $imagesSettings['imageSettings'] = $this->getSetting('imageSettings');
        $this->text = $this->createComponent($textClass, 1, $this->getSetting('textSettings'));
        $this->images = $this->createComponent($imagesClass, 2, $imagesSettings);
    }

    public function getChildComponents()
    {
        return array($this->text, $this->images);
    }

}