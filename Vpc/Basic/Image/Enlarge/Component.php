<?php
class Vpc_Basic_Image_Enlarge_Component extends Vpc_Basic_Image_Component
{
    protected $_smallImage;

    public static function getSettings()
    {
        return array_merge(parent::getSettings(), array(
            'componentName'     => 'Image Enlarge',
            'componentIcon'     => new Vps_Asset('imageEnlarge'),
            'tablename' => 'Vpc_Basic_Image_Enlarge_Model',
            'hasSmallImageComponent' => true,
            'childComponentClasses' => array(
                'smallImage' => 'Vpc_Basic_Image_Component',
            ),
            'smallImageSettings' => array(
                'dimension'         => array(100, 100),
                'scale'             => Vps_Media_Image::SCALE_BESTFIT
            ),
            'dimension' => array(640, 480),
            'assets' => array(
                'files'=>array('vps/Vpc/Basic/Image/Enlarge/Component.js'),
                'dep' => array('ExtCore')
            ),
            'editComment' => true
        ));
    }

    public function getTemplateVars()
    {
        $return = parent::getTemplateVars();
        $vars = $this->getChildComponent()->getTemplateVars();
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

        $return['thumbMaxHeight'] = $this->_getSetting('smallImageSettings');
        $return['thumbMaxHeight'] = $return['thumbMaxHeight']['dimension'][1];

        $return['smallImage'] = $vars;
        return $return;
    }

    protected function getChildComponent()
    {
        if (!$this->_smallImage) {
            $enlargeClass = $this->_getClassFromSetting('smallImage', 'Vpc_Basic_Image_Component');
            $this->_smallImage = $this->createComponent($enlargeClass, 1);
        }
        return $this->_smallImage;
    }

    public function getChildComponents()
    {
        return array($this->getChildComponent());
    }
}
