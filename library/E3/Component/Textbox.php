<?php
class E3_Component_Textbox extends E3_Component_Abstract
{
    public function getTemplateVars()
    {
        $rowset = $this->_dao->getTable('E3_Dao_Textbox')
                ->find($this->getComponentId(), $this->getPageKey(), $this->getComponentKey());
        if ($rowset->count() == 1) {
        	$content = $rowset->current()->content;
        } else {
        	$content = '';
        }
        $ret['id'] = $this->getId();
        $ret['content'] = $content;
       	$ret['template'] = 'Textbox.html';

        return $ret;
    }
}
