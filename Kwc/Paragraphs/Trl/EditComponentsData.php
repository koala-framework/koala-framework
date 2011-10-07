<?php
class Kwc_Paragraphs_Trl_EditComponentsData extends Kwc_Paragraphs_EditComponentsData
{
    protected function _getComponentClassByRow($row)
    {
        return $row->component_class;
    }
}
