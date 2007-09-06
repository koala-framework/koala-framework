<?php
class Vpc_Formular_Checkbox_Setup extends Vpc_Setup_Abstract
{
    public function setup()
    {
        $fields['value'] = 'varchar(255) NOT NULL';
        $fields['text'] = 'varchar(255) NOT NULL';
        $fields['checked'] = 'tinyint(4) NOT NULL';
        $this->createTable('vpc_formular_checkbox', $fields);
    }

    public function deleteEntry($pageId, $componentKey)
    {
        $where = array();
        $where['page_id = ?'] = $pageId;
        $where['component_key = ?'] = $componentKey;
        $table = new Vpc_Formular_Checkbox_IndexModel(array('db'=>$this->_db));
        $table->delete($where);
    }
}