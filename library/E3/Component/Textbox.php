<?php
class E3_Component_Textbox extends E3_Component_Abstract
{
    public function getTemplateVars()
    {
        $rowset = $this->_dao->getTable('E3_Dao_Textbox')
                ->find($this->getComponentId());
        if ($rowset->count() == 1) {
        	$content = $rowset->current()->content;
        } else {
        	$content = '';
        }
        $ret['id'] = $this->getComponentId();
        $ret['content'] = $content;
       	$ret['template'] = 'Textbox.html';
        /*
        if (isset($_GET["mode"]) && $_GET["mode"] == 'edit') {
        	$ret['template'] = '../../library/E3/Component/Textbox.html';
        } else {
	       	$ret['template'] = 'Textbox.html';
        }
        */
        return $ret;
    }
}
