<?php
class Vpc_Formular_Admin extends Vpc_Paragraphs_Admin
{
    public function getComponents()
    {
        return array_merge($this->getAvailableComponents(VPS_PATH . '/Vpc/Formular'),
                            $this->getAvailableComponents('Vpc'));
    }
/*
    public function getComponents()
    {
        $components = array();
        foreach ($c as $key => $val) {
            if ($key != 'Formular') {
                $key = str_replace('Formular.', '', $key);
                $components[$key] = $val;
            }
        }
        asort($components);
        foreach (parent::getComponents() as $key => $val) {
            if ($key != 'Formular') {
                $components['Nicht Formular.' . $key] = $val;
            }
        }
        return $components;
    }*/

    public function setup()
    {
        /*
        $tablename = 'vpc_formular';
        if (!$this->_tableExists($tablename)) {
          $this->_db->query("CREATE TABLE `$tablename` (
                  `id` int(10) unsigned NOT NULL auto_increment,
                  `component_id` varchar(255) NOT NULL,
                  `component_class` varchar(255) NOT NULL,
                  `pos` smallint NOT NULL,
                  `visible` tinyint(4) NOT NULL,
                  `field_label` varchar(255) NOT NULL,
                  `mandatory` tinyint(4) NOT NULL,
                  `no_cols` tinyint(4) NOT NULL,
                   PRIMARY KEY  (`id`)
                     ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
        }
        */
    }
}
