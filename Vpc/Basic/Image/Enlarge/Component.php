<?php
class Vpc_Basic_Image_Enlarge_Component extends Vpc_Basic_Image_Component
{
    protected $_smallImage;

    public static function getSettings()
    {
        return array_merge(parent::getSettings(), array(
            'componentName'     => trlVps('Image Enlarge'),
            'componentIcon'     => new Vps_Asset('imageEnlarge'),
            'tablename' => 'Vpc_Basic_Image_Enlarge_Model',
            'hasSmallImageComponent' => true,
            'childComponentClasses' => array(
                'smallImage' => 'Vpc_Basic_Image_Thumb_Component',
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

        // Small Image
        $row = $this->getData()->getChildComponent('-1');
        $vars = $row->getComponent()->getTemplateVars();

        if (!$vars['url'] || !$this->_getRow()->enlarge) {
            $size = $this->_getRow()->getImageDimensions(null, 'small');
            $vars['url'] = $this->_getRow()->getFileUrl(null, 'small');
            $vars['width'] = $size['width'];
            $vars['height'] = $size['height'];
        }
        $return['smallImage'] = $vars;

        $return['thumbMaxHeight'] = $vars['height'];

        return $return;
    }
    public function getImageRow()
    {
        return $this->_getRow();
    }
}
