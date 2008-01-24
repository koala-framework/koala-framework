<?php
class Vpc_Basic_Image_Enlarge_Component extends Vpc_Basic_Image_Component
{
    public $smallImage = null;

    public static function getSettings()
    {
        return array_merge(parent::getSettings(), array(
            'componentName'     => 'Image Enlarge',
            'tablename' => 'Vpc_Basic_Image_Enlarge_Model',
            'hasSmallImageComponent' => true,
            'childComponentClasses' => array(
                'smallImage' => 'Vpc_Basic_Image_Component',
            ),
            'smallImageSettings' => array(
                'dimension'         => array(100, 100),
                'scale'             => Vps_Media_Image::SCALE_BESTFIT
            ),
            'assets' => array('files'=>array('vps/Vpc/Basic/Image/Enlarge/Component.js'),
            'dep' => array('ExtCore', 'ExtWindow'))
        ));
    }

    protected function _init()
    {
        $enlargeClass = $this->_getClassFromSetting('smallImage', 'Vpc_Basic_Image_Component');
        $this->smallImage = $this->createComponent($enlargeClass, 1);
    }

    public function getTemplateVars()
    {
        $return = parent::getTemplateVars();
        $vars = $this->smallImage->getTemplateVars();
        if (!$vars['url']) {
            $url = $this->_row->getFileUrl(null, 'small');
            $size = $this->_row->getImageDimensions(null, 'small');
            $vars = array(
                'url' => $url,
                'width' => $size['width'],
                'height' => $size['height']
            );
        } else {
            $vars = array(
                'url' => $vars['url'],
                'width' => $vars['width'],
                'height' => $vars['height']
            );
        }
        $return['smallImage'] = $vars;
        return $return;
    }

    public function getChildComponents()
    {
        return array($this->smallImage);
    }
}
