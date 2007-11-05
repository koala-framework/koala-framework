<?php
class Vpc_Formular_Admin extends Vpc_Paragraphs_Admin
{
    public function getComponents()
    {
        $c = $this->getAvailableComponents(VPS_PATH . '/Vpc/Formular/');
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
    }

    public function setup()
    {
        $this->copyTemplate('Template.html', 'Formular.html');

        $tablename = 'vpc_formular';
        if (!$this->_tableExits($tablename)) {
          $this->_db->query("CREATE TABLE `$tablename` (
                  `id` int(10) unsigned NOT NULL auto_increment,
                  `page_id` int(10) unsigned NOT NULL,
                  `component_key` varchar(255) NOT NULL,
                  `component_class` varchar(255) NOT NULL,
                  `pos` smallint NOT NULL,
                  `visible` tinyint(4) NOT NULL,
                  `name` varchar(255) NOT NULL,
                  `mandatory` tinyint(4) NOT NULL,
                  `no_cols` tinyint(4) NOT NULL,
                   PRIMARY KEY  (`id`)
                     ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;");
        }
    }
}