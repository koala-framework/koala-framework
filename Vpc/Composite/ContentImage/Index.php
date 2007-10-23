<?php
class Vpc_Composite_ContentImage_Index extends Vpc_Abstract
{
    const NAME = 'Standard.ContentImage';
    protected $_settings = array(
        'contentClass'      => 'Vpc_Paragraphs_Index',
        'contentSettings'   => array(),
        'imageClass'        => 'Vpc_Basic_Image_Index',
        'imageSettings'     => array()
    );
    public $content;
    public $image;

    public function getTemplateVars()
    {
        $return = parent::getTemplateVars();
        $return['content'] = $this->content->getTemplateVars('');
        $return['image'] = $this->image->getTemplateVars('');
        $return['template'] = 'Composite/ContentImage.html';
        return $return;
    }

    protected function _init()
    {
        $contentClass = $this->_getClassFromSetting('contentClass', 'Vpc_Paragraphs_Index');
        $imageClass = $this->_getClassFromSetting('imageClass', 'Vpc_Basic_Image_Index');
        $this->content = $this->createComponent($contentClass, 1, $this->getSetting('contentSettings'));
        $this->image = $this->createComponent($imageClass, 2, $this->getSetting('imageSettings'));
    }

    public function getChildComponents()
    {
        return array($this->content, $this->image);
    }

}