<?php
class Kwc_Paragraphs_Trl_EditComponentsData extends Kwf_Data_Kwc_EditComponents
{
    protected function _getComponentClassByRow($row)
    {
        return $row->component_class;
    }
}
