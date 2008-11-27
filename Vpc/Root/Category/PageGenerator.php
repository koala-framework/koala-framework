<?php
class Vpc_Root_Category_PageGenerator extends Vps_Component_Generator_Page
{
    protected function _getPageIds($parentData, $select)
    {
        $pageIds = parent::_getPageIds($parentData, $select);

        if ($parentData && is_instance_of($parentData->componentClass, 'Vpc_Root_Category_Component')) {
            if (isset($this->_pageCategory[$parentData->row->id])) {
                $pageIds = array_intersect($this->_pageCategory[$parentData->row->id], $pageIds);
            } else {
                $pageIds = array();
            }
        }

        return $pageIds;
    }

    protected function _getParentDataByRow($row)
    {
        $parentData = Vps_Component_Data_Root::getInstance()->getChildComponent('-' . $row['category']);
        if ($parentData->componentClass != $this->_class) return null;
        return $parentData;
    }
}
