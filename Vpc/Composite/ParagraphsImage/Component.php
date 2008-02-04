<?php
class Vpc_Composite_ParagraphsImage_Component extends Vpc_Abstract
{
    public $_paragraphs;
    public $_image;

    public static function getSettings()
    {
        return array_merge(parent::getSettings(), array(
            'componentName'     => 'ParagraphsImage',
            'childComponentClasses' => array(
                'paragraphs'   => 'Vpc_Paragraphs_Component',
                'image'        => 'Vpc_Basic_Image_Enlarge_Component'
            )
        ));
    }

    public function getTemplateVars()
    {
        $return = parent::getTemplateVars();
        $return['paragraphs'] = $this->getChildComponent('paragraphs')->getTemplateVars('');
        $return['image'] = $this->getChildComponent('image')->getTemplateVars('');
        return $return;
    }

    protected function getChildComponent($type)
    {
        if ($type == 'paragraphs') {
            if (!$this->_paragraphs) {
                $paragraphsClass = $this->_getClassFromSetting('paragraphs', 'Vpc_Paragraphs_Component');
                $this->_paragraphs = $this->createComponent($paragraphsClass, 'paragraphs');
            }
            return $this->_paragraphs;
        } else if ($type == 'image') {
            if (!$this->_image) {
                $imageClass = $this->_getClassFromSetting('image', 'Vpc_Basic_Image_Component');
                $this->_image = $this->createComponent($imageClass, 'image');
            }
            return $this->_image;
        }
        return null;
    }

    public function getChildComponents()
    {
        return array(
            $this->getChildComponent('paragraphs'),
            $this->getChildComponent('image')
        );
    }
}
