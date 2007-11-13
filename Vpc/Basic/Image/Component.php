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
        
        if (isset($settings['dimension'][0])) {
            if (is_array($settings['dimension'][0])) {
                $settings['default']['width'] = $settings['dimension'][0][0];
                $settings['default']['height'] = $settings['dimension'][0][1];
            } else {
                $settings['default']['width'] = $settings['dimension'][0];
                $settings['default']['height'] = $settings['dimension'][1];
            }
        }
        if (!isset($settings['default']['scale']) || $settings['scale'] == '') {
            if (isset($settings['allow'][0])) {
                $settings['default']['scale'] = $settings['allow'][0];
            } else {
                $settings['default']['scale'] = Vps_Media_Image::SCALE_BESTFIT;
            }
        }
        return $settings;
    }
    
    public function getTemplateVars()
    {
        $size = $this->_row->getImageDimension(get_class($this));

        $return = parent::getTemplateVars();
        $return['url'] = $this->_row->getImageUrl(get_class($this));
        $return['width'] = $size['width'];
        $return['height'] = $size['height'];
        return $return;
    }
}