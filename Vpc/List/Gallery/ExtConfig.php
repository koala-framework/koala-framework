<?php
class Vpc_List_Gallery_ExtConfig extends Vps_Component_Abstract_ExtConfig_Abstract
{
    private $_listExtConfig;
    public function __construct($class)
    {
        parent::__construct($class);

        $this->_listExtConfig = new Vpc_Abstract_List_ExtConfigList($this->_class);
    }

    protected function _getConfig()
    {
        $config = $this->_getStandardConfig('vps.tabpanel', null);
        $config['activeTab'] = 0;
        $config['tabs'] = array();

        $config['tabs']['settings'] = $this->_getStandardConfig('vps.autoform', 'Index', trlVps('Settings'));

        $listConfig = $this->_listExtConfig->_getConfig();
        $config['tabs']['images'] = $listConfig['list'];

        return array('tabs' => $config);
    }
}
