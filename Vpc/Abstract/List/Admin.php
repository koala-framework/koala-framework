<?php
class Vpc_Abstract_List_Admin extends Vpc_Admin
{
    public function getExtConfig()
    {
        $class = Vpc_Abstract::getChildComponentClass($this->_class, 'child');
        $childConfig = array_values(Vpc_Admin::getInstance($class)->getExtConfig());
        if (count($childConfig) > 1) {
            //wenn das mal benötigt wird möglicherwesie mit tabs
            throw new Vps_Exception("Vpc_Abstract_List can only have childs with one Controller '$class'");
        }

        return array(
            'list' => array(
                'xtype'=>'vpc.list',
                'controllerUrl' => $this->getControllerUrl(),
                'title' => trlVps('Edit {0}', $this->_getSetting('componentName')),
                'icon' => $this->_getSetting('componentIcon')->__toString(),
                'childConfig'=>$childConfig[0]
            )
        );
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
