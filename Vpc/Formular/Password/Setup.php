<?php
class Vpc_Formular_Password_Setup extends Vpc_Setup_Abstract
{
    public function setup()
    {
        $fields['maxlength'] = 'smallint (6) NOT NULL';
        $fields['width'] = 'smallint(6) NOT NULL';
        $this->createTable("vpc_formular_password", $fields);
    }

    public function deleteEntry($pageId, $componentKey)
    {
        $where = array();
        $where['page_id = ?'] = $pageId;
        $where['component_key = ?'] = $componentKey;
        $table = new Vpc_Formular_Password_IndexModel(array('db'=>$this->_db));
        $table->delete($where);
    }
}