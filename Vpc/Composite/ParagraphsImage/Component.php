<?php
class Vpc_Composite_ParagraphsImage_Component extends Vpc_Abstract
{
    public $paragraphs;
    public $image;

    public static function getSettings()
    {
        return array_merge(parent::getSettings(), array(
            'componentName'     => 'Standard.ParagraphsImage',
            'childComponentClasses' => array(
                'paragraphs'   => 'Vpc_Paragraphs_Component',
                'image'        => 'Vpc_Basic_Image_Enlarge_Component'
            )
        ));
    }

    public function getTemplateVars()
    {
        $return = parent::getTemplateVars();
        $return['paragraphs'] = $this->paragraphs->getTemplateVars('');
        $return['image'] = $this->image->getTemplateVars('');
        return $return;
    }

    protected function _init()
    {
        $paragraphsClass = $this->_getClassFromSetting('paragraphs', 'Vpc_Paragraphs_Component');
        $imageClass = $this->_getClassFromSetting('image', 'Vpc_Basic_Image_Component');
        $this->paragraphs = $this->createComponent($paragraphsClass, 'paragraphs');
        $this->image = $this->createComponent($imageClass, 'image');
    }

    public function getChildComponents()
    {
        return array($this->paragraphs, $this->image);
    }

}
