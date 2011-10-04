<?php
class Vpc_Paragraphs_Trl_EditComponentsData extends Vpc_Paragraphs_EditComponentsData
{
    protected function _getComponentClassByRow($row)
    {
        return $row->component_class;
    }
}
