<?php
class Vpc_Paragraphs_Trl_ExtConfig extends Vps_Component_Abstract_ExtConfig_Abstract
{
    protected function _getConfig()
    {
        $config = $this->_getStandardConfig('vpc.paragraphs');
        $config['previewWidth'] = $this->_getSetting('previewWidth');
        $config['showDelete'] = false;
        $config['showPosition'] = false;
        return array(
            'paragraphs' => $config
        );
    }
}