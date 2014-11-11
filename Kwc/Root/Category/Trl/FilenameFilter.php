<?php
class Kwc_Root_Category_Trl_FilenameFilter extends Kwc_Root_Category_FilenameFilter
{
    protected function _getParentPage($row)
    {
        return Kwf_Component_Data_Root::getInstance()
            ->getComponentById($this->_getComponentId($row), array('ignoreVisible' => true))
            ->parent
            ->getPseudoPageOrRoot();
    }

    protected function _getComponentId($row)
    {
        return $row->component_id;
    }
}
