<?php
class Vpc_List_ChildPages_Teaser_ChildVisibilityData extends Vps_Data_Abstract
{
    private $_childOwnModel = null;
    private $_componentId = null;

    public function __construct($childOwnModel, $componentId)
    {
        $this->_childOwnModel = $childOwnModel;
        $this->_componentId = $componentId;
    }

    public function load($row)
    {
        $m = Vps_Model_Abstract::getInstance($this->_childOwnModel);
        $childRow = $m->getRow($this->_componentId.'-'.$row->id);
        if (!$childRow) return 0;
        return $childRow->visible;
    }
}
