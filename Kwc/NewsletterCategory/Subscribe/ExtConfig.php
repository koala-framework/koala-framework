<?php
class Kwc_NewsletterCategory_Subscribe_ExtConfig extends Kwf_Component_Abstract_ExtConfig_Abstract
{
    protected function _getConfig()
    {
        $config = $this->_getStandardConfig('kwf.autogrid', 'Categories', trlKwf('Categories'), new Kwf_Asset('application_form'));
        return array(
            'categories' => $config
        );
    }
}
