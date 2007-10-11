<?php
class Vpc_Paragraphs_Admin extends Vpc_Admin
{
    public function getComponents()
    {
        return $this->getAvailableComponents('Vpc/');
    }

    public function getControllerConfig($component)
    {
        $componentList = array();
        foreach ($this->getComponents() as $name => $component) {
            $str = '$componentList["' . str_replace('.', '"]["', $name) . '"] = "' . $component . '";';
            eval($str);
        }
        return array('components' => $componentList);
    }

    public function getControllerClass()
    {
        return 'Vpc.Paragraphs.Index';
    }

    public function setup()
    {
        $this->copyTemplate('Index.html', 'Paragraphs.html');

        $tablename = 'vpc_paragraphs';
        if (!in_array($tablename, $this->_db->listTables())) {
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

    public function delete(Vpc_Abstract $component)
    {
        // Komponenten löschen
        foreach ($component->getChildComponents() as $cc) {
            Vpc_Admin::getInstance($component)->delete($component);
        }
        // Einträge in Tabelle löschen
        $where = array();
        $where['page_id = ?'] = $component->getDbId();
        $where['component_key = ?'] = $component->getComponentKey();
        foreach ($component->getTable()->fetchAll($where) as $row) {
            $row->delete();
        }
    }
}