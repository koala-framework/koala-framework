<?php
class Vpc_Simple_Text_Setup extends Vpc_Setup_Abstract 
{
    public function setup()
    {        
        $this->copyTemplate('Simple/Text.html');

        $fields['content'] = 'text NOT NULL';
        $this->createTable('vpc_text', $fields);       
    }
    
    public function deleteEntry($pageId, $componentKey)
    {
        $where = array();
        $where['page_id = ?'] = $pageId;
        $where['component_key = ?'] = $componentKey;
        $table = new Vpc_Simple_Text_IndexModel(array('db'=>$this->_db));
        $table->delete($where);
    }
}