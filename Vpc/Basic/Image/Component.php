<?php
class Vpc_Basic_Image_Component extends Vpc_Abstract
{
    public static function getSettings()
    {
        $settings = array_merge(parent::getSettings(), array(
            'componentName'     => 'Standard.Image',
            'tablename'         => 'Vpc_Basic_Image_Model',
            'extensions'        => array('jpg', 'gif', 'png'),
            'dimension'         => array(300, 200), // Leeres Array -> freie Wahl, array(width, height), array(array(width, height), ...)
            'scale'             => array(Vps_Media_Image::SCALE_BESTFIT),
            'editFilename'      => true,
            'allowBlank'        => true,
            'default'           => array(
                'filename'   => 'filename'
            )
        ));
        return $settings;
    }
    
    public function getTemplateVars()
    {
        $size = $this->_row->getImageDimension();

        $return = parent::getTemplateVars();
        $return['url'] = $this->_row->getImageUrl();
        $return['width'] = $size['width'];
        $return['height'] = $size['height'];
        return $return;
    }
}