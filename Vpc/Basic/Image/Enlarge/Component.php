<?php
class Vpc_Basic_Image_Enlarge_Component extends Vpc_Basic_Image_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlVps('Image Enlarge');
        $ret['componentIcon'] = new Vps_Asset('imageEnlarge');
        $ret['tablename'] = 'Vpc_Basic_Image_Enlarge_Model';
        $ret['hasSmallImageComponent'] = true;
        $ret['childComponentClasses']['smallImage'] = 'Vpc_Basic_Image_Thumb_Component';
        $ret['dimension'] = array(640, 480);
        $ret['assets']['files'][] = 'vps/Vpc/Basic/Image/Enlarge/Component.js';
        $ret['assets']['dep'][] = 'ExtCore';
        $ret['editComment'] = true;
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['smallImage'] = $this->getSmallImage();
        return $ret;
    }

    //wird uA. verwendet in Vpc_Composite_ImagesEnlarge_Component
    public function getSmallImage()
    {
        $row = $this->getData()->getChildComponent('-smallImage')
                            ->getComponent()->getImageRow();
        $ret = array();
        $ret['row'] = $row;
        if (!$this->_getSetting('hasSmallImageComponent') ||
            !$row->getFileUrl() ||
            !$this->_getRow()->enlarge)
        {
            $ret['url'] = $this->_getRow()->getFileUrl(null, 'small');
            $size = $this->_getRow()->getImageDimensions(null, 'small');
        } else {
            $ret['url'] = $this->_getRow()->getFileUrl();
            $size = $this->_getRow()->getImageDimensions();
        }
        $ret['width'] = $size['width'];
        $ret['height'] = $size['height'];
        return $ret;
    }
}
