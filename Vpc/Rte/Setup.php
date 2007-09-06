<?php
class Vpc_Rte_Setup extends Vpc_Setup_Abstract
{
    public function setup()
    {
        $this->copyTemplate('Rte.html');

        $fields['text'] = 'text NOT NULL';
        $this->createTable('vpc_rte', $fields);
    }

    public function deleteEntry($pageId, $componentKey)
    {
        $where = array();
        $where['page_id = ?'] = $pageId;
        $where['component_key = ?'] = $componentKey;
        $table = new Vpc_Rte_IndexModel(array('db'=>$this->_db));
        $table->delete($where);
    }
}