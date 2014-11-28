<?php
class Kwc_Menu_Expanded_EditableItems_Generator extends Kwf_Component_Generator_Table
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

    protected function _duplicateRow($source, $parentTarget)
    {
        $model = $this->getModel();
        $select = $model->select()
            ->whereEquals('parent_component_id', $parentTarget->parent->componentId)
            ->whereEquals('filename', $source->row->filename);
        return $model->getRow($select);
    }
}
