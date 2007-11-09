<?php
class Vpc_Composite_Images_Admin extends Vpc_Admin
{
    public function getControllerConfig($class)
    {
        $imageClass = Vpc_Abstract::getSetting($class, 'imageClass');
        return array(
            'imageConfig' => Vpc_Admin::getConfig($imageClass)
        );
    }

    public function getControllerClass()
    {
        return 'Vpc.Composite.Images.Panel';
    }
    
    public function setup()
    {
        Vpc_Admin::getInstance('Vpc_Basic_Image_Component')->setup();

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

    public function delete($class, $pageId, $componentKey)
    {
        foreach ($this->_getRows($class, $pageId, $componentKey) as $row) {
            $imageClass = Vpc_Abstract::getSetting($class, 'imageClass');
            $admin = Vpc_Admin::getInstance($imageClass);
            $admin->delete($imageClass, $pageId, $componentKey . '-' . $row->id);
            $row->delete();
        }
    }
}
