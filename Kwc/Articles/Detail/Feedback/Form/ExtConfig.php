<?php
class Kwc_Articles_Detail_Feedback_Form_ExtConfig extends Kwf_Component_Abstract_ExtConfig_Abstract
{
    protected function _getConfig()
    {
        $config = $this->_getStandardConfig('kwf.autogrid');
        return array(
            'grid' => $config
        );
    }
}
