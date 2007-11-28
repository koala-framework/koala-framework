<?php
class Vpc_Paragraphs_Admin extends Vpc_Admin
{
    public function getComponents()
    {
        return $this->getAvailableComponents('Vpc');
    }

    public function getControllerConfig()
    {
        $componentList = array();
        foreach ($this->getComponents() as $component) {
            $name = Vpc_Abstract::getSetting($component, 'componentName');
            $hide = Vpc_Abstract::getSetting($component, 'hideInParagraphs');
            if ($hide !== true && $name) {
                $str = '$componentList["' . str_replace('.', '"]["', $name) . '"] = "' . $component . '";';
                eval($str);
            }
        }
        return array('components' => $componentList);
    }

    public function getControllerClass()
    {
        return 'Vpc.Paragraphs.Panel';
    }

    public function setup()
    {
        $tablename = 'vpc_paragraphs';
        if (!$this->_tableExists($tablename)) {
          $this->_db->query("CREATE TABLE `$tablename` (
                  `id` int(10) unsigned NOT NULL auto_increment,
                  `page_id` int(10) unsigned NOT NULL,
                  `component_key` varchar(255) NOT NULL,
                  `component_class` varchar(255) NOT NULL,
                  `pos` smallint NOT NULL,
                  `visible` tinyint(4) NOT NULL
                   PRIMARY KEY  (`id`)
                     ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
        }
    }

    public function delete($pageId, $componentKey)
    {
        foreach ($this->_getRows($pageId, $componentKey) as $row) {
            $row->delete();
        }
    }
}