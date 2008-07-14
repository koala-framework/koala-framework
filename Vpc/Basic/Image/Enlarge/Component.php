<?php
class Vpc_Basic_Image_Enlarge_Component extends Vpc_Basic_Image_Component
{
    private $_smallImageRow;

    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlVps('Image Enlarge');
        $ret['componentIcon'] = new Vps_Asset('imageEnlarge');
        $ret['tablename'] = 'Vpc_Basic_Image_Enlarge_Model';
        $ret['hasSmallImageComponent'] = true;
        $ret['fullSizeDownloadable'] = true;
        $ret['generators']['smallImage'] = array(
            'class' => 'Vps_Component_Generator_Static',
            'component' => 'Vpc_Basic_Image_Thumb_Component'
        );
        $ret['dimensions'] = array(640, 480);
        $ret['assets']['files'][] = 'vps/Vpc/Basic/Image/Enlarge/Component.js';
        $ret['assets']['dep'][] = 'ExtCore';
        $ret['editComment'] = true;
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['url'] = $ret['row']->getFileUrl();
        $size = $ret['row']->getImageDimensions();
        $ret['width'] = $size['width'];
        $ret['height'] = $size['height'];
        $ret['smallImage'] = $this->getSmallImage();

        $ret['fullSizeUrl'] = false;
        if ($this->_getSetting('fullSizeDownloadable')) {
            $ret['fullSizeUrl'] = $this->_getRow()->getFileUrl(null, 'original');
        }
        return $ret;
    }

    private function _getSmallImageRow()
    {
        if (!isset($this->_smallImageRow)) {
            $this->_smallImageRow = $this->getData()->getChildComponent('-smallImage')
                                        ->getComponent()->getImageRow();
        }
        return $this->_smallImageRow;
    }

    //wird uA. verwendet in Vpc_Composite_ImagesEnlarge_Component
    public function getSmallImage()
    {
        $ret = array();

        if (!$this->_getSetting('hasSmallImageComponent')
            || !$this->_getRow()->enlarge
            || !$this->_getSmallImageRow()->getFileUrl())
        {
            $ret['url'] = $this->_getRow()->getFileUrl(null, 'small');
            $size = $this->_getRow()->getImageDimensions(null, 'small');
        } else {
            $ret['row'] = $this->_getSmallImageRow();
            $ret['url'] = $ret['row']->getFileUrl();
            $size = $ret['row']->getImageDimensions();
        }
        $ret['width'] = $size['width'];
        $ret['height'] = $size['height'];
        return $ret;
    }
}
