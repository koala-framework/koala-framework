<?php
class Kwc_Paragraphs_Trl_ExtConfig extends Kwf_Component_Abstract_ExtConfig_Abstract
{
    protected function _getConfig()
    {
        $config = $this->_getStandardConfig('kwc.paragraphs');
        $config['showDelete'] = false;
        $config['showPosition'] = false;
        $config['needsComponentPanel'] = true;
        $config['generatorProperties'] = array();
        return array(
            'paragraphs' => $config
        );
    }
}
