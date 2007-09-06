<?php
class Vpc_Formular_MultiSelect_Setup extends Vpc_Setup_Abstract
{
    public function setup()
    {
        $fields['type'] = 'varchar(20) NOT NULL';
        $this->createTable('vpc_formular_multiselect', $fields);
        
        $tablename = 'vpc_formular_multiselect_options';
        if (!$this->_tableExits($tablename)) {
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
    
    public function deleteEntry($pageId, $componentKey)
    {
        $where = array();
        $where['page_id = ?'] = $pageId;
        $where['component_key = ?'] = $componentKey;
        $table = new Vpc_Formular_MultiSelect_IndexModel(array('db'=>$this->_db));
        $table->delete($where);
        $table = new Vpc_Formular_MultiSelect_OptionsModel(array('db'=>$this->_db));
        $table->delete($where);
    }
}