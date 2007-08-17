<?php
class Vpc_Formular_Select_Setup extends Vpc_Setup_Abstract
{
    public function setup()
    {
        $fields['type'] = 'varchar(20) NOT NULL';
        $this->createTable('component_formular_select', $fields);

        $tablename = 'component_formular_select_options';
        if (!$this->_tableExits($tablename)) {
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
    
    public function deleteEntry($pageId, $componentKey)
    {
        $where = array();
        $where['page_id = ?'] = $pageId;
        $where['component_key = ?'] = $componentKey;
        $table = new Vpc_Formular_Select_IndexModel(array('db'=>$this->_db));
        $table->delete($where);
        $table = new Vpc_Formular_Select_OptionsModel(array('db'=>$this->_db));
        $table->delete($where);
    }
}