<?php
class Vpc_Posts_Detail_Edit_Form_Component extends Vpc_Posts_Write_Form_Component
{
    protected function _getPostsComponent()
    {
        return $this->getData()->parent->parent->parent;
    }

    protected function _initForm()
    {
        parent::_initForm();
        $this->_form->setId($this->getData()->parent->parent->parent->row->id);
    }
}
