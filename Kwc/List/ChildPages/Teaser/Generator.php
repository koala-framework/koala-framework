<?php
class Kwc_List_ChildPages_Teaser_Generator extends Kwf_Component_Generator_Table
{
    protected $_hasNumericIds = false;
    protected $_idColumn = 'child_id';
    protected $_useComponentId = true;

    protected function _formatConfig($parentData, $row)
    {
        $ret = parent::_formatConfig($parentData, $row);
        $ret['targetPage'] = Kwf_Component_Data_Root::getInstance()
            ->getComponentByDbId($row->target_page_id, array('subroot'=>$parentData, 'limit'=>1));

        if (!$ret['targetPage']) return null; //can happen if page was deleted but entry still exists

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
