<?php
class Kwc_Articles_Directory_Generator extends Kwf_Component_Generator_Page_Table
{
    protected function _formatSelect($parentData, $select)
    {
        $ret = parent::_formatSelect($parentData, $select);
        if (!$ret) return $ret;

        $ignoreVisible = $select && $select->getPart(Kwf_Component_Select::IGNORE_VISIBLE);
        if (!$ignoreVisible) {
            if (!Kwf_Component_Data_Root::getShowInvisible()) {
                $ret->where('date <= CURDATE()');
            }
        }

        $ret->whereEquals('deleted', 0);
        return $ret;
    }
}
