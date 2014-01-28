<?php
class Kwc_Columns_Generator extends Kwf_Component_Generator_Table
{
    protected function _getModel()
    {
        return Kwc_Columns_Component::getColumnsModel($this->_class);
    }
}
