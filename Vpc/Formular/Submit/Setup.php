<?php
class Vpc_Formular_Submit_Setup extends Vpc_Setup_Abstract
{
    public function setup()
    {
       $fields['text'] = 'varchar(255) NOT NULL';
       $this->createTable('component_formular_submit', $fields);
    }

    public function deleteEntry($pageId, $componentKey)
    {
        $where = array();
        $where['page_id = ?'] = $pageId;
        $where['component_key = ?'] = $componentKey;
        $table = new Vpc_Formular_Submit_IndexModel(array('db'=>$this->_db));
        $table->delete($where);
    }
}