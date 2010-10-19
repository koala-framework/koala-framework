<?php
class Vpc_Abstract_List_Generator extends Vps_Component_Generator_Table
{
    protected function _formatSelect($parentData, $select)
    {
        $select = parent::_formatSelect($parentData, $select);
        if (!$select) return $select;
        if (!Vpc_Abstract::getSetting($this->getClass(), 'hasVisible')) {
            $select->ignoreVisible(true);
        }
        return $select;
    }
}
