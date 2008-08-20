<?php
class Vpc_Basic_Image_Component extends Vpc_Abstract
{
    public static function getSettings()
    {
        $ret = array_merge(parent::getSettings(), array(
            'componentName'     => trlVps('Image'),
            'componentIcon'     => new Vps_Asset('picture'),
            'tablename'         => 'Vpc_Basic_Image_Model',

            'dimensions'        => array(300, 200, Vps_Media_Image::SCALE_BESTFIT), // Leeres Array -> freie Wahl, array(width, height), array(array(width, height), null: Bild in Originalgröße)
            'ouputDimensions'   => array('mini'  => array(20, 20, Vps_Media_Image::SCALE_BESTFIT),
                                         'thumb' => array(100, 100, Vps_Media_Image::SCALE_BESTFIT)),

            'editComment'       => false,
            'editFilename'      => false,
            'allowBlank'        => true,
            'default'           => array(
                'filename'   => 'filename'
            ),
            'extensions'        => array('jpg'),
            'pdfMaxWidth'       => 0,
            'imgCssClass'       => '',
            'type'              => 'default',
            'emptyImage'        => null
        ));
        $ret['assetsAdmin']['dep'][] = 'VpsSwfUpload';
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['row'] = $this->_getRow();
        if (Vpc_Abstract::getSetting(get_class($this), 'editComment')) {
            $ret['comment'] = $this->_getRow()->comment;
        }
        $ret['imgCssClass'] = $this->_getSetting('imgCssClass');
        $ret['type'] = $this->_getSetting('type');
        return $ret;
    }

    public function getImageUrl($type = 'default')
    {
        return $this->_getRow()->getFileUrl(null, $type);
    }

    public function getImageDimensions($type = 'default')
    {
        return $this->_getRow()->getImageDimensions(null, $type);
    }

    //für Pdf
    public function getImageRow()
    {
        return $this->_getRow();
    }
}
