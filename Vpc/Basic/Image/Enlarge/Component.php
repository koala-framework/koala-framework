<?php
class Vpc_Basic_Image_Enlarge_Component extends Vpc_Basic_Image_Component
{
    public $enlargeImage = null;

    public static function getSettings()
    {
        return array_merge(parent::getSettings(), array(
            'tablename' => 'Vpc_Basic_Image_Enlarge_Model',
            'childComponentClasses' => array(
                'enlarge' => 'Vpc_Basic_Image_Component',
            )
        ));
    }

    protected function _init()
    {
        $enlargeClass = $this->_getClassFromSetting('enlarge', 'Vpc_Basic_Image_Component');
        $this->enlargeImage = $this->createComponent($enlargeClass, 1);
    }

    public function getTemplateVars()
    {
        $return = parent::getTemplateVars();
        $return['enlarge'] = $this->enlargeImage->getTemplateVars();
        return $return;
    }

    public function getChildComponents()
    {
        return array($this->enlargeImage);
    }
}
