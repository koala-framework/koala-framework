<?php
class Kwc_Blog_Comments_QuickWrite_Form_Component extends Kwc_Posts_Write_Form_Component
{
    protected function _getSettingsRow()
    {
        return $this->_getPostsComponent()->getComponent()->getRow();
    }
}
