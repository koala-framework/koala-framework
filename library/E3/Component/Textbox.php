<?php
require_once 'E3/Component/Abstract.php';

class E3_Component_Textbox extends E3_Component_Abstract
{
    public function getTemplateVars()
    {
        $row = $this->_dao->getModel('E3_Model_Textbox')
                ->find($this->getComponentId());
        return array("content" => $row->content);
    }
}
