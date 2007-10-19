<?php
class Vpc_Composite_Images_Admin extends Vpc_Admin
{
    public function getControllerClass()
    {
        return 'Vpc.Composite.Images.Index';
    }

    public function setup()
    {
        $this->copyTemplate('Index.html', 'Composite/Images.html');

        Vpc_Admin::getInstance('Vpc_Basic_Image_Index')->setup();

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

    public function delete($component)
    {
        foreach ($component->getChildComponents() as $c) {
            Vpc_Admin::getInstance($c)->delete($c);
        }
    }
}
