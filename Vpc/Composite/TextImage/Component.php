<?php
class Vpc_Composite_TextImage_Component extends Vpc_Abstract
{
    const NAME = 'Standard.TextImage';
    public $text;
    public $image;
    public $imagebig;
    protected $_settings = array(
        'textClass'         => 'Vpc_Basic_Html_Component',
        'textSettings'      => array(),
        'imageClass'        => 'Vpc_Basic_Image_Component',
        'imageSettings'     => array(),
        'image_position'    => 'alternate' // 'left', 'right', 'alternate'
    );
    protected $_tablename = 'Vpc_Composite_TextImage_Model';

    public function getTemplateVars()
    {
        $return = parent::getTemplateVars();
        $return['text'] = $this->text->getTemplateVars('');
        $return['image'] = $this->image->getTemplateVars('');
        $return['image_position'] = 'right'; // TODO
        $return['enlarge'] = $this->getSetting('enlarge');
        $return['template'] = 'Composite/TextImage.html';
        return $return;
    }

    protected function _init()
    {
        $textClass = $this->_getClassFromSetting('textClass', 'Vpc_Basic_Html_Component');
        $imageClass = $this->_getClassFromSetting('imageClass', 'Vpc_Basic_Image_Component');
        $this->text = $this->createComponent($textClass, 1, $this->getSetting('textSettings'));
        $this->image = $this->createComponent($imageClass, 2, $this->getSetting('imageSettings'));
    }

    public function getChildComponents()
    {
        return array($this->text, $this->image);
    }

}