<?php
class Vpc_Composite_Images_Admin extends Vpc_Admin
{
    public function getControllerConfig()
    {
        $classes = Vpc_Abstract::getSetting($this->getTable()->getComponentClass(), 'childComponentClasses');
        return array(
            'imageConfig' => Vpc_Admin::getConfig($classes['image'])
        );
    }

    public function getControllerClass()
    {
        return 'Vpc.Composite.Images.Panel';
    }
    
    public function setup()
    {
        $classes = Vpc_Abstract::getSetting($this->_class, 'childComponentClasses');
        Vpc_Admin::getInstance($classes['image'])->setup();

        if (!$this->_tableExists('vpc_composite_images')) {
            $sql = 'CREATE TABLE IF NOT EXISTS `vpc_composite_images` (
              `id` int(10) unsigned NOT NULL auto_increment,
              `page_id` int(10) unsigned NOT NULL,
              `component_key` varchar(255) NOT NULL,
              `pos` tinyint(4) NOT NULL,
              `visible` tinyint(4) NOT NULL,
              PRIMARY KEY  (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 ;';
            $this->_db->query($sql);
        }
    }

    public function delete($pageId, $componentKey)
    {
        foreach ($this->_getRows($pageId, $componentKey) as $row) {
            $row->delete();
        }
    }
}
