<?php
class Vpc_Formular_Textarea_Setup extends Vpc_Setup_Abstract
{
    public function setup()
    {
       $fields['cols'] = 'smallint(6) NOT NULL';
       $fields['rows'] = 'smallint(6) NOT NULL';
       $this->createTable('vpc_formular_textarea', $fields);
    }
    
    public function deleteEntry($pageId, $componentKey)
    {
        $where = array();
        $where['page_id = ?'] = $pageId;
        $where['component_key = ?'] = $componentKey;
        $table = new Vpc_Formular_Textarea_IndexModel(array('db'=>$this->_db));
        $table->delete($where);
    }
}