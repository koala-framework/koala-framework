<?php
class Vpc_Formular_Select_Admin extends Vpc_Admin
{
    public function setup()
    {
        $this->copyTemplate('Template.html', 'Formular/Select.html');

        $fields['type'] = 'varchar(20) NOT NULL';
        $this->createFormTable('vpc_formular_select', $fields);

        $tablename = 'vpc_formular_select_options';
        if (!in_array($tablename, $this->_db->listTables())) {
            $this->_db->query("CREATE TABLE `$tablename` (
                `id` int(11) NOT NULL auto_increment,
                `component_id` varchar(255) NOT NULL,
                `pos` smallint(6) NOT NULL,
                `text` varchar(255) NOT NULL,
                `checked` tinyint(4) NOT NULL,
                PRIMARY KEY  (`id`),
                KEY `KEY` (`component_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 ;");
        }
    }

    public function deleteEntry($component)
    {
        $where = array();
        $where['component_id = ?'] = $component->getId();
        $table = new Vpc_Formular_Select_Model(array('db'=>$this->_db));
        $table->delete($where);
        $table = new Vpc_Formular_Select_OptionsModel(array('db'=>$this->_db));
        $table->delete($where);
    }
}
