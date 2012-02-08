<?php
class Kwc_List_ChildPages_Teaser_Generator extends Kwf_Component_Generator_Table
{
    protected $_hasNumericIds = false;

    protected function _formatSelect($parentData, $select)
    {
        $select = parent::_formatSelect($parentData, $select);
        if (!$select) return $select;

        if (!$parentData) {
            throw new Kwf_Exception_NotYetImplemented();
        }
        $select->whereEquals('parent_component_id', $parentData->componentId);
        return $select;
    }

    protected function _formatConfig($parentData, $row)
    {
        $ret = parent::_formatConfig($parentData, $row);
        $ret['targetPage'] = Kwf_Component_Data_Root::getInstance()
            ->getComponentById($row->target_page_id);
        return $ret;
    }

    public function getDuplicateProgressSteps($source)
    {
        return 0;
    }

    public function duplicateChild($source, $parentTarget, Zend_ProgressBar $progressBar = null)
    {
        //don't duplicate children of this generator *here* because we don't know the new child ids yet
        //as they depend on the new pages (that might not yet exist at this point)
        return null;
    }
}
