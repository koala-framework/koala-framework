<?php
class Vpc_Posts_Post_Edit_Form_Form extends Vpc_Posts_Write_Form_Form
{
    protected function _getRowByParentRow($parentRow)
    {
        return $this->getPostRow();
    }
}
