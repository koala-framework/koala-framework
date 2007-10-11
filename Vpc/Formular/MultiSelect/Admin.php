<?php
class Vpc_Formular_MultiSelect_Admin extends Vpc_Admin
{
    public function setup()
    {
        $this->copyTemplate('Index.html', 'Formular/MultiSelect.html');

        $fields['type'] = 'varchar(20) NOT NULL';
        $this->createTable('vpc_formular_multiselect', $fields);

        $tablename = 'vpc_formular_multiselect_options';
        if (!in_array($tablename, $this->_db->listTables())) {
            $this->_db->query("CREATE TABLE `$tablename` (
                  `id` int(10) unsigned NOT NULL auto_increment,
                  `pos` smallint(6) NOT NULL,
                  `page_id` int(10) unsigned NOT NULL,
                  `component_key` varchar(255) NOT NULL,
                  `text` varchar(255) NOT NULL,
                  `checked` tinyint(4) NOT NULL,
                  PRIMARY KEY  (`id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
        }
    }

    public function delete($component)
    {
        $where = array();
        $where['page_id = ?'] = $component->getDbId();
        $where['component_key = ?'] = $component->getComponentKey();
        $table = new Vpc_Formular_MultiSelect_IndexModel(array('db'=>$this->_db));
        $table->delete($where);
        $table = new Vpc_Formular_MultiSelect_OptionsModel(array('db'=>$this->_db));
        $table->delete($where);
    }
}