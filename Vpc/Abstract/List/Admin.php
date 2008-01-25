<?php
class Vpc_Abstract_List_Admin extends Vpc_Admin
{
    public function getControllerConfig()
    {
        $classes = Vpc_Abstract::getSetting($this->_class, 'childComponentClasses');
        return array(
            'childConfig' => Vpc_Admin::getConfig($classes['child'])
        );
    }

    public function getControllerClass()
    {
        return 'Vpc.Abstract.List.Panel';
    }

    public function setup()
    {
        $classes = Vpc_Abstract::getSetting($this->_class, 'childComponentClasses');
        Vpc_Admin::getInstance($classes['child'])->setup();

        if (!$this->_tableExists('vpc_composite_list')) {
            $sql = 'CREATE TABLE IF NOT EXISTS `vpc_composite_list` (
              `id` int(10) unsigned NOT NULL auto_increment,
              `component_id` varchar(255) NOT NULL,
              `component_class` varchar(255) NOT NULL,
              `pos` tinyint(4) NOT NULL,
              `visible` tinyint(4) NOT NULL,
              PRIMARY KEY  (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 ;';
            $this->_db->query($sql);
        }
    }

    public function delete($componentId)
    {
        foreach ($this->_getRows($componentId) as $row) {
            $row->delete();
        }
    }
}
