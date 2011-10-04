<?php
class Vpc_NewsletterCategory_Subscribe_ExtConfig extends Vps_Component_Abstract_ExtConfig_Abstract
{
    protected function _getConfig()
    {
        $config = $this->_getStandardConfig('vps.autogrid', 'Categories', trlVps('Categories'), new Vps_Asset('application_form'));
        return array(
            'categories' => $config
        );
    }
}
