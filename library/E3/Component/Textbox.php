<?php
class E3_Component_Textbox extends E3_Component_Abstract
{
    public function getTemplateVars()
    {
        $row = $this->_dao->getTable('E3_Dao_Textbox')
                ->find($this->getComponentId());
        $ret['content'] = $row->content;
        $ret['template'] = 'Textbox.html';
        return $ret;
    }
}
