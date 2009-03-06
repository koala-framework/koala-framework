<?php
class Vpc_Abstract_List_Admin extends Vpc_Admin
{
    public function getExtConfig()
    {
        $class = Vpc_Abstract::getChildComponentClass($this->_class, 'child');
        $childConfig = Vpc_Admin::getInstance($class)->getExtConfig();

        return array_merge(parent::getExtConfig(), array(
            'xtype'=>'vpc.list',
            'childConfig'=>$childConfig
        ));
    }
    public function setup()
    {
        $class = Vpc_Abstract::getChildComponentClass($this->_class, 'child');
        Vpc_Admin::getInstance($class)->setup();

        if (!$this->_tableExists('vpc_composite_list')) {
            $sql = 'CREATE TABLE IF NOT EXISTS `vpc_composite_list` (
              `id` int(10) unsigned NOT NULL auto_increment,
              `component_id` varchar(255) NOT NULL,
              `component_class` varchar(255) NOT NULL,
              `pos` tinyint(4) NOT NULL,
              `visible` tinyint(4) NOT NULL,
              PRIMARY KEY  (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 ;';
            Vps_Registry::get('db')->query($sql);
        }
    }

}
