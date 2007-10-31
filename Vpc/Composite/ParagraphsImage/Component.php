<?php
class Vpc_Composite_ParagraphsImage_Component extends Vpc_Abstract
{
    const NAME = 'Standard.ParagraphsImage';
    protected $_settings = array(
        'paragraphsClass'      => 'Vpc_Paragraphs_Component',
        'paragraphsSettings'   => array(),
        'imageClass'        => 'Vpc_Basic_Image_Component',
        'imageSettings'     => array()
    );
    public $paragraphs;
    public $image;

    public function getTemplateVars()
    {
        $return = parent::getTemplateVars();
        $return['paragraphs'] = $this->paragraphs->getTemplateVars('');
        $return['image'] = $this->image->getTemplateVars('');
        $return['template'] = 'Composite/ParagraphsImage.html';
        return $return;
    }

    protected function _init()
    {
        $paragraphsClass = $this->_getClassFromSetting('paragraphsClass', 'Vpc_Paragraphs_Component');
        $imageClass = $this->_getClassFromSetting('imageClass', 'Vpc_Basic_Image_Component');
        $this->paragraphs = $this->createComponent($paragraphsClass, 1, $this->getSetting('paragraphsSettings'));
        $this->image = $this->createComponent($imageClass, 2, $this->getSetting('imageSettings'));
    }

    public function getChildComponents()
    {
        return array($this->paragraphs, $this->image);
    }

}