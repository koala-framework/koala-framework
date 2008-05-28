<?php
class Vpc_Basic_Image_Component extends Vpc_Abstract
{
    public static function getSettings()
    {
        $settings = array_merge(parent::getSettings(), array(
            'componentName'     => trlVps('Image'),
            'componentIcon'     => new Vps_Asset('picture'),
            'tablename'         => 'Vpc_Basic_Image_Model',
            'dimension'         => array(300, 200), // Leeres Array -> freie Wahl, array(width, height), array(array(width, height), null: Bild in Originalgröße)
            'allow'             => array(Vps_Media_Image::SCALE_BESTFIT),
            'ouputDimensions'   => array('mini'  => array(20, 20, Vps_Media_Image::SCALE_BESTFIT),
                                         'thumb' => array(100, 100, Vps_Media_Image::SCALE_BESTFIT)),
            'editComment'       => false,
            'editFilename'      => false,
            'allowBlank'        => true,
            'default'           => array(
                'filename'   => 'filename'
            ),
            'extensions'        => array('jpg'),
            'scale'             => array()
        ));
        return $settings;
    }

    public function getTemplateVars()
    {
        $size = $this->_getRow()->getImageDimension();

        $return = parent::getTemplateVars();
        $return['url'] = $this->_getRow()->getFileUrl();
        $return['width'] = $size['width'];
        $return['height'] = $size['height'];
        if (Vpc_Abstract::getSetting(get_class($this), 'editComment')) {
            $return['comment'] = $this->_getRow()->comment;
        }
        $return['height'] = $size['height'];
        return $return;
    }

    public function getImageUrl($type = 'default')
    {
        return $this->_getRow()->getFileUrl(null, $type);
    }

    public function getImageDimension()
    {
        return $this->_getRow()->getImageDimension();
    }

    //für Pdf
    public function getImageRow()
    {
        return $this->_getRow();
    }
}
