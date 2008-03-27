<?php
class Vpc_Posts_Admin extends Vpc_Admin
{
    public function getExtConfig()
    {
        return array_merge(parent::getExtConfig(), array(
            'xtype' => 'vps.autogrid'
        ));
    }

    public function setup()
    {
        $classes = Vpc_Abstract::getSetting($this->_class, 'childComponentClasses');
//         Vpc_Admin::getInstance($classes['details'])->setup();

        if (!$this->_tableExists('vpc_comments')) {
            $this->_db->query("CREATE TABLE IF NOT EXISTS `vpc_comments` (
                `id` int(11) NOT NULL,
                `component_id` varchar(255) NOT NULL,
                `visible` tinyint(1) NOT NULL default '1',
                `create_time` datetime NOT NULL,
                `name` varchar(255) NOT NULL,
                `email` varchar(255) NOT NULL,
                `content` text NOT NULL,
                PRIMARY KEY  (`id`),
                KEY `component_id` (`component_id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8");
        }
    }

    public function delete($componentId)
    {
        $classes = Vpc_Abstract::getSetting($this->_class, 'childComponentClasses');
//         Vpc_Admin::getInstance($classes['child'])->delete($componentId);
    }
}
