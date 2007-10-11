<?php
class Vpc_Formular_Select_Admin extends Vpc_Admin
{
    public function setup()
    {
        $this->copyTemplate('Index.html', 'Formular/Select.html');

        $fields['type'] = 'varchar(20) NOT NULL';
        $this->createTable('vpc_formular_select', $fields);

        $tablename = 'vpc_formular_select_options';
        if (!in_array($tablename, $this->_db->listTables())) {
            $this->_db->query("CREATE TABLE `$tablename` (
                `id` int(11) NOT NULL auto_increment,
                `page_id` int(11) NOT NULL,
                `component_key` varchar(255) NOT NULL,
                `pos` smallint(6) NOT NULL,
                `text` varchar(255) NOT NULL,
                `checked` tinyint(4) NOT NULL,
                PRIMARY KEY  (`id`),
                KEY `KEY` (`page_id`,`component_key`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 ;");
        }
    }

    public function deleteEntry($component)
    {
        $where = array();
        $where['page_id = ?'] = $component->getDbId();
        $where['component_key = ?'] = $component->getComponentKey();
        $table = new Vpc_Formular_Select_IndexModel(array('db'=>$this->_db));
        $table->delete($where);
        $table = new Vpc_Formular_Select_OptionsModel(array('db'=>$this->_db));
        $table->delete($where);
    }
}